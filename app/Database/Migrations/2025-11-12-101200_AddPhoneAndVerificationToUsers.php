<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhoneAndVerificationToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'unique' => true,
            ],
            'phone_verified' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'phone_verification_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'phone_verification_expires' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'phone');
        $this->forge->dropColumn('users', 'phone_verified');
        $this->forge->dropColumn('users', 'phone_verification_code');
        $this->forge->dropColumn('users', 'phone_verification_expires');
    }
}
