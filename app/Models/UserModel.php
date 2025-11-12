<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'username', 'password', 'role', 'email', 'phone',
        'phone_verified', 'phone_verification_code', 'phone_verification_expires'
    ];
}
