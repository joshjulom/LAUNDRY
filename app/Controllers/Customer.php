<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ComplaintModel;

class Customer extends BaseController
{
    public function index()
    {
        return view('customer/dashboard', ['title' => 'Customer Dashboard']);
    }

    public function orders()
    {
        $userId = session()->get('id');
        $orderModel = new OrderModel();
        $orders = $orderModel->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll();
        return view('customer/orders', [
            'title' => 'My Orders',
            'orders' => $orders,
        ]);
    }

    public function orderDetail($orderId)
    {
        $userId = session()->get('id');
        $orderModel = new OrderModel();
        $order = $orderModel->where('id', $orderId)->where('user_id', $userId)->first();
        if (!$order) {
            return redirect()->to(site_url('customer/orders'))->with('error', 'Order not found');
        }
        return view('customer/order_detail', [
            'title' => 'Order Detail',
            'order' => $order,
        ]);
    }

    public function complaint()
    {
        return view('customer/complaint', ['title' => 'Submit Complaint']);
    }

    public function addOrder()
    {
        return view('customer/add_order', ['title' => 'Create New Order']);
    }

    public function addOrderPost()
    {
        $userId = session()->get('id');
        $orderModel = new OrderModel();

        $data = [
            'user_id' => $userId,
            'status' => 'pending',
            'total_price' => $this->request->getPost('total_price'),
            'due_date' => $this->request->getPost('due_date'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($orderModel->insert($data)) {
            // Send order confirmation email
            $emailService = new \App\Libraries\EmailService();
            $orderData = $orderModel->find($orderModel->getInsertID());
            $userModel = new \App\Models\UserModel();
            $userData = $userModel->find($userId);

            $emailService->sendOrderConfirmation($orderData, $userData);

            return redirect()->to(site_url('customer/orders'))
                ->with('success', 'Order created successfully! A confirmation email has been sent.');
        } else {
            return redirect()->back()->with('error', 'Failed to create order');
        }
    }

    public function complaintPost()
    {
        $userId = session()->get('id');
        $model = new ComplaintModel();
        $data = [
            'order_id' => $this->request->getPost('order_id'),
            'user_id' => $userId,
            'description' => $this->request->getPost('description'),
            'status' => 'open',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $model->insert($data);
        return redirect()->to(site_url('customer/dashboard'))
            ->with('success', 'Complaint submitted');
    }
}
