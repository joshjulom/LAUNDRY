<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomerSampleSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Create customer user
        $customerData = [
            'username'   => 'customer',
            'password'   => password_hash('123456', PASSWORD_BCRYPT),
            'role'       => 'customer',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $db->table('users')->insert($customerData);
        $customerId = (int) $db->insertID();

        // Seed a few orders for this customer
        $orders = [
            [
                'user_id'     => $customerId,
                'status'      => 'pending',
                'total_price' => '15.00',
                'barcode'     => 'LAU-00001001',
                'due_date'    => date('Y-m-d H:i:s', strtotime('+2 days')),
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'     => $customerId,
                'status'      => 'washing',
                'total_price' => '22.50',
                'barcode'     => 'LAU-00001002',
                'due_date'    => date('Y-m-d H:i:s', strtotime('+1 day')),
                'created_at'  => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
            [
                'user_id'     => $customerId,
                'status'      => 'ready',
                'total_price' => '30.00',
                'barcode'     => 'LAU-00001003',
                'due_date'    => date('Y-m-d H:i:s', strtotime('+3 days')),
                'created_at'  => date('Y-m-d H:i:s', strtotime('-2 days')),
            ],
        ];
        $db->table('orders')->insertBatch($orders);
    }
}
