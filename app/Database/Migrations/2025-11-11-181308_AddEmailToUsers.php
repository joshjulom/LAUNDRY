<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => true,
                'after' => 'username',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'email');
    }
}
