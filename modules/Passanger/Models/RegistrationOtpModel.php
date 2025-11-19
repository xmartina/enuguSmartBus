<?php

namespace Modules\Passanger\Models;

use CodeIgniter\Model;

class RegistrationOtpModel extends Model
{
    protected $table = 'registration_otps';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'email',
        'otp_hash',
        'expires_at',
        'attempts',
        'verified',
        'verification_token',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $returnType = 'array';
}
