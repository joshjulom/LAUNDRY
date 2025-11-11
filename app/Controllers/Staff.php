<?php

namespace App\Controllers;

use App\Models\OrderAssignmentModel;
use App\Models\OrderModel;
use App\Models\OrderStatusHistoryModel;
use App\Models\StaffIssueModel;
use App\Models\UserModel;
use App\Libraries\EmailService;

class Staff extends BaseController
{
    public function index()
    {
        return view('staff/dashboard', ['title' => 'Staff Dashboard']);
    }

    public function orders()
    {
        $session = session();
        $staffId = $session->get('id');

        $assignmentModel = new OrderAssignmentModel();
        $orderModel = new OrderModel();

        // First try to get assigned orders
        $assignments = $assignmentModel
            ->where('staff_id', $staffId)
            ->where('active', 1)
            ->findAll();

        $orderIds = array_map(static function ($a) { return $a['order_id']; }, $assignments);
        $orders = [];
        if (!empty($orderIds)) {
            $orders = $orderModel->whereIn('id', $orderIds)->findAll();
        }

        // If no assigned orders, show all orders for staff to assign themselves
        if (empty($orders)) {
            $orders = $orderModel->findAll();
        }

        return view('staff/orders', [
            'title' => 'Orders',
            'orders' => $orders,
            'assignments' => $assignments,
        ]);
    }

    public function updateStatus($orderId)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);
        if (!$order) {
            return redirect()->to(site_url('staff/orders'))->with('error', 'Order not found');
        }
        return view('staff/update_status', [
            'title' => 'Update Order Status',
            'order' => $order,
            'statuses' => ['pending','washing','ready','delivered'],
        ]);
    }

    public function updateStatusPost($orderId)
    {
        $session = session();
        $staffId = $session->get('id');

        $orderModel = new OrderModel();
        $historyModel = new OrderStatusHistoryModel();
        $userModel = new UserModel();

        $order = $orderModel->find($orderId);
        if (!$order) {
            return redirect()->to(site_url('staff/orders'))->with('error', 'Order not found');
        }

        $newStatus = $this->request->getPost('status');
        $note = $this->request->getPost('note');

        if (!in_array($newStatus, ['pending','washing','ready','delivered'], true)) {
            return redirect()->back()->with('error', 'Invalid status');
        }

        $oldStatus = $order['status'];

        $orderModel->update($orderId, ['status' => $newStatus]);
        $historyModel->insert([
            'order_id' => $orderId,
            'changed_by' => $staffId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'note' => $note,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Send email notification to customer
        $user = $userModel->find($order['user_id']);
        if ($user) {
            $emailService = new EmailService();
            $emailService->sendOrderStatusUpdate($order, $user, $oldStatus, $newStatus);
        }

        return redirect()->to(site_url('staff/orders'))->with('success', 'Status updated');
    }

    public function reportIssue()
    {
        return view('staff/report_issue', [
            'title' => 'Report Issue',
        ]);
    }

    public function reportIssuePost()
    {
        $session = session();
        $staffId = $session->get('id');

        $issueModel = new StaffIssueModel();

        $data = [
            'order_id' => $this->request->getPost('order_id') ?: null,
            'reported_by' => $staffId,
            'category' => $this->request->getPost('category') ?: 'other',
            'description' => $this->request->getPost('description'),
            'status' => 'open',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $issueModel->insert($data);

        return redirect()->to(site_url('staff/dashboard'))->with('success', 'Issue reported');
    }

    public function chat()
    {
        $session = session();
        $role = $session->get('role');
        if ($role !== 'staff') {
            return redirect()->to('/');
        }
        return view('chat/index', [
            'title' => 'Staff Chat',
            'role' => $role,
            'username' => $session->get('username'),
            'sendUrl' => site_url('chat/send'),
            'messagesUrl' => site_url('chat/messages')
        ]);
    }
}
