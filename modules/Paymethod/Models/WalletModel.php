<?php

namespace Modules\Wallet\Models;

use CodeIgniter\Model;

class WalletModel extends Model
{
    protected $table = 'wallets';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'balance', 'updated_at', 'created_at'];

    /**
     * 🔹 Log a wallet transaction (credit or debit)
     *
     * @param int    $userId
     * @param float  $amount   Positive for credit, negative for debit
     * @param string $description
     * @param string|null $reference
     * @return bool
     */
    public function insertTransaction($userId, $amount, $description = '', $reference = null)
    {
        $db = \Config\Database::connect();

        // Determine transaction type
        $type = ($amount >= 0) ? 'credit' : 'debit';

        $data = [
            'user_id'     => $userId,
            'amount'      => abs($amount),
            'type'        => $type,
            'description' => $description,
            'reference'   => $reference ?? strtoupper(uniqid('TXN')),
            'created_at'  => date('Y-m-d H:i:s')
        ];

        return $db->table('wallet_transactions')->insert($data);
    }

    /**
     * 🔹 Get wallet transaction history
     */
    public function getTransactions($userId, $limit = 20)
    {
        return $this->db->table('wallet_transactions')
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }
}
