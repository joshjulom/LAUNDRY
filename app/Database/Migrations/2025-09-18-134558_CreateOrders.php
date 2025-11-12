<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrders extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT','unsigned' => true,'auto_increment' => true],
            'user_id'     => ['type' => 'INT','unsigned' => true],
            'status'      => ['type' => 'ENUM','constraint' => ['pending','washing','ready','delivered'],'default' => 'pending'],
            'total_price' => ['type' => 'DECIMAL','constraint' => '10,2'],
            'barcode'     => ['type' => 'VARCHAR','constraint' => 255,'null' => true],
            'due_date'    => ['type' => 'DATETIME','null' => true],
            'created_at'  => ['type' => 'DATETIME','null' => true],
        ]);
        $this->forge->addKey('id', true);
        // make barcode searchable and unique when present
        $this->forge->addKey('barcode', false, true);
        $this->forge->addForeignKey('user_id','users','id','CASCADE','CASCADE');
        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}
