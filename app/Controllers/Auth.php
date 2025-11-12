<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\SmsService;

class Auth extends BaseController
{
    public function login()
    {
        // just load the login view
        return view('auth/login'); // your file is Views/auth/login.php
    }

    public function loginPost()
    {
        helper(['form']);
        $session = session();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            $session->set([
                'id'       => $user['id'],
                'username' => $user['username'],
                'role'     => $user['role'],
                'isLoggedIn' => true
            ]);

            if ($user['role'] === 'admin') {
                return redirect()->to(site_url('admin/dashboard'));
            } elseif ($user['role'] === 'staff') {
                return redirect()->to(site_url('staff/dashboard'));
            } elseif ($user['role'] === 'customer') {
                return redirect()->to(site_url('customer/dashboard'));
            }
        }

        $session->setFlashdata('error', 'Invalid username or password');
        return redirect()->to(site_url('login'));
    }

    public function register()
    {
        return view('auth/register');
    }

    public function registerPost()
    {
        helper(['form']);
        $session = session();

        // Defensive: check whether users table has 'phone' column (migration may not have run)
        $db = \Config\Database::connect();
        $hasPhone = false;
        try {
            $fields = $db->getFieldData('users');
            foreach ($fields as $f) {
                if (isset($f->name) && $f->name === 'phone') {
                    $hasPhone = true;
                    break;
                }
            }
        } catch (\Throwable $e) {
            // table may not exist or other DB error; treat as no phone support
            $hasPhone = false;
        }

        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
        ];

        if ($hasPhone) {
            // only validate phone if the column exists
            $rules['phone'] = 'required|regex_match[/^[0-9]{7,15}$/]|is_unique[users.phone]';
        }

        if (!$this->validate($rules)) {
            $session->setFlashdata('errors', $this->validator->getErrors());
            $session->setFlashdata('old', $this->request->getPost());
            return redirect()->back();
        }

        if ($hasPhone) {
            // Do NOT create user yet. Store registration data in session temporarily
            $regData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'phone' => $this->request->getPost('phone'),
            ];

            // generate verification code and expiry (10 minutes)
            $code = random_int(100000, 999999);
            $expiry = date('Y-m-d H:i:s', time() + 600);

            $session->set('pending_registration', $regData);
            $session->set('pending_verification_code', (string)$code);
            $session->set('pending_verification_expires', $expiry);

            // Send SMS via SmsService
            try {
                $sms = new SmsService();
                $message = "Your verification code is: {$code}";
                $sms->send($this->request->getPost('phone'), $message);
            } catch (\Exception $e) {
                log_message('error', 'SMS send error: ' . $e->getMessage());
                $session->setFlashdata('error', 'Failed to send verification code. Please try again.');
                return redirect()->back();
            }

            // Redirect to verification page
            return redirect()->to(site_url('register/verify'));
        } else {
            // If phone column does not exist, create user immediately (legacy behavior)
            $userModel = new UserModel();
            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role' => 'customer', // Default role for registration
            ];

            if ($userModel->insert($data)) {
                $session->setFlashdata('success', 'Registration successful! Please log in.');
                return redirect()->to(site_url('login'));
            } else {
                $session->setFlashdata('error', 'Registration failed. Please try again.');
                return redirect()->back();
            }
        }
    }

    /**
     * Show verification form
     */
    public function registerVerify()
    {
        $session = session();
        $pendingReg = $session->get('pending_registration');
        if (empty($pendingReg)) {
            return redirect()->to(site_url('register'));
        }

        return view('auth/register_verify');
    }

    /**
     * Handle verification submission
     */
    public function registerVerifyPost()
    {
        $session = session();
        $pendingReg = $session->get('pending_registration');
        $pendingCode = $session->get('pending_verification_code');
        $pendingExpires = $session->get('pending_verification_expires');

        if (empty($pendingReg) || empty($pendingCode)) {
            return redirect()->to(site_url('register'));
        }

        $code = $this->request->getPost('code');
        if (empty($code)) {
            $session->setFlashdata('error', 'Please enter the verification code');
            return redirect()->back();
        }

        // Check if code matches
        if ($code !== $pendingCode) {
            $session->setFlashdata('error', 'Invalid verification code');
            return redirect()->back();
        }

        // Check if code has expired
        if (!empty($pendingExpires) && strtotime($pendingExpires) < time()) {
            $session->setFlashdata('error', 'Verification code expired. Please register again.');
            // Clear the pending data
            $session->remove('pending_registration');
            $session->remove('pending_verification_code');
            $session->remove('pending_verification_expires');
            return redirect()->to(site_url('register'));
        }

        // Code is valid! Now create the user account
        $userModel = new UserModel();
        $userData = [
            'username' => $pendingReg['username'],
            'email' => $pendingReg['email'],
            'password' => password_hash($pendingReg['password'], PASSWORD_DEFAULT),
            'role' => 'customer',
            'phone' => $pendingReg['phone'],
            'phone_verified' => 1,
        ];

        if ($userModel->insert($userData)) {
            $userId = $userModel->getInsertID();

            // Log the user in
            $session->set([
                'id' => $userId,
                'username' => $pendingReg['username'],
                'role' => 'customer',
                'isLoggedIn' => true,
            ]);

            // Clear pending registration data
            $session->remove('pending_registration');
            $session->remove('pending_verification_code');
            $session->remove('pending_verification_expires');

            $session->setFlashdata('success', 'Registration verified successfully! Welcome!');
            return redirect()->to(site_url('customer/dashboard'));
        } else {
            $session->setFlashdata('error', 'Failed to create account. Please try again.');
            return redirect()->back();
        }
    }

    /**
     * Resend verification code
     */
    public function resendVerification()
    {
        $session = session();
        $pendingReg = $session->get('pending_registration');

        if (empty($pendingReg)) {
            return redirect()->to(site_url('register'));
        }

        // Generate new code
        $code = random_int(100000, 999999);
        $expiry = date('Y-m-d H:i:s', time() + 600);

        $session->set('pending_verification_code', (string)$code);
        $session->set('pending_verification_expires', $expiry);

        try {
            $sms = new SmsService();
            $message = "Your verification code is: {$code}";
            $sms->send($pendingReg['phone'], $message);
            $session->setFlashdata('success', 'Verification code resent to ' . $pendingReg['phone']);
        } catch (\Exception $e) {
            log_message('error', 'SMS resend error: ' . $e->getMessage());
            $session->setFlashdata('error', 'Failed to resend code. Please try again.');
        }

        return redirect()->to(site_url('register/verify'));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'));
    }
}
