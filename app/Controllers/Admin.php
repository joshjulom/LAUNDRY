<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OrderModel;
use App\Models\ComplaintModel;
use App\Models\InventoryModel;

class Admin extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $orderModel = new OrderModel();
        $complaintModel = new ComplaintModel();
        
        $data = [
            'title' => 'Admin Dashboard',
            'total_users' => $userModel->countAll(),
            'total_orders' => $orderModel->countAll(),
            'pending_orders' => $orderModel->where('status', 'pending')->countAllResults(),
            'open_complaints' => $complaintModel->where('status', 'open')->countAllResults()
        ];
        
        return view('admin/dashboard', $data);
    }

    public function users()
    {
        $userModel = new UserModel();
        $users = $userModel->findAll();
        
        return view('admin/users', [
            'title' => 'Manage Users',
            'users' => $users
        ]);
    }

    public function orders()
    {
        $orderModel = new OrderModel();
        $userModel = new UserModel();
        
        $orders = $orderModel->select('orders.*, users.username')
                            ->join('users', 'users.id = orders.user_id')
                            ->orderBy('orders.created_at', 'DESC')
                            ->findAll();
        
        return view('admin/orders', [
            'title' => 'Orders',
            'orders' => $orders
        ]);
    }

    public function inventory()
    {
        $inventoryModel = new InventoryModel();
        $items = $inventoryModel->findAll();
        
        return view('admin/inventory', [
            'title' => 'Inventory',
            'items' => $items
        ]);
    }

    public function complaints()
    {
        $complaintModel = new ComplaintModel();
        $userModel = new UserModel();
        
        $complaints = $complaintModel->select('complaints.*, users.username, orders.id as order_number')
                                    ->join('users', 'users.id = complaints.user_id')
                                    ->join('orders', 'orders.id = complaints.order_id', 'left')
                                    ->orderBy('complaints.created_at', 'DESC')
                                    ->findAll();
        
        return view('admin/complaints', [
            'title' => 'Complaints',
            'complaints' => $complaints
        ]);
    }

    public function addUser()
    {
        if ($this->request->getMethod() === 'POST') {
            $userModel = new UserModel();
            
            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
                'role' => $this->request->getPost('role'),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            if ($userModel->insert($data)) {
                return redirect()->to(site_url('admin/users'))->with('success', 'User added successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to add user');
            }
        }
        
        return view('admin/add_user', ['title' => 'Add User']);
    }

    public function addInventoryItem()
    {
        if ($this->request->getMethod() === 'POST') {
            $inventoryModel = new InventoryModel();
            
            $data = [
                'item_name' => $this->request->getPost('item_name'),
                'quantity' => $this->request->getPost('quantity'),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            if ($inventoryModel->insert($data)) {
                return redirect()->to(site_url('admin/inventory'))->with('success', 'Item added successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to add item');
            }
        }
        
        return view('admin/add_inventory_item', ['title' => 'Add Inventory Item']);
    }

    public function updateInventoryItem($id)
    {
        $inventoryModel = new InventoryModel();
        
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'item_name' => $this->request->getPost('item_name'),
                'quantity' => $this->request->getPost('quantity')
            ];
            
            if ($inventoryModel->update($id, $data)) {
                return redirect()->to(site_url('admin/inventory'))->with('success', 'Item updated successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to update item');
            }
        }
        
        $item = $inventoryModel->find($id);
        if (!$item) {
            return redirect()->to(site_url('admin/inventory'))->with('error', 'Item not found');
        }
        
        return view('admin/edit_inventory_item', [
            'title' => 'Edit Inventory Item',
            'item' => $item
        ]);
    }

    public function deleteInventoryItem($id)
    {
        $inventoryModel = new InventoryModel();
        
        if ($inventoryModel->delete($id)) {
            return redirect()->to(site_url('admin/inventory'))->with('success', 'Item deleted successfully');
        } else {
            return redirect()->to(site_url('admin/inventory'))->with('error', 'Failed to delete item');
        }
    }

    public function resolveComplaint($id)
    {
        $complaintModel = new ComplaintModel();

        $data = ['status' => 'resolved'];

        if ($complaintModel->update($id, $data)) {
            return redirect()->to(site_url('admin/complaints'))->with('success', 'Complaint resolved successfully');
        } else {
            return redirect()->to(site_url('admin/complaints'))->with('error', 'Failed to resolve complaint');
        }
    }

    public function generateBarcode($orderId)
    {
        $barcodeService = new \App\Libraries\BarcodeService();
        $barcodeHtml = $barcodeService->getBarcodeHtml($orderId);

        return $this->response->setBody($barcodeHtml);
    }
}
