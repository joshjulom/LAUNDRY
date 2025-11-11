<?php

namespace App\Controllers;

use App\Models\UserModel;

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

        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            $session->setFlashdata('errors', $this->validator->getErrors());
            $session->setFlashdata('old', $this->request->getPost());
            return redirect()->back();
        }

        $userModel = new UserModel();
        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'customer', // Default role for registration
        ];

        if ($userModel->insert($data)) {
            $session->setFlashdata('success', 'Registration successful! Please login.');
            return redirect()->to(site_url('login'));
        } else {
            $session->setFlashdata('error', 'Registration failed. Please try again.');
            return redirect()->back();
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'));
    }
}
