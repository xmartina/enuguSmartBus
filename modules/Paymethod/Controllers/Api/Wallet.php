<?php

namespace Modules\Paymethod\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Modules\Ticket\Models\TicketModel;
use Modules\Ticket\Models\PartialpaidModel;
use Modules\User\Models\UserModel;
use Modules\Paymethod\Models\WalletModel;

class Wallet extends BaseController
{
    use ResponseTrait;

    protected $ticketModel;
    protected $partialpaidModel;
    protected $userModel;
    protected $walletModel;
    protected $db;

    public function __construct()
    {
        $this->ticketModel = new TicketModel();
        $this->partialpaidModel = new PartialpaidModel();
        $this->userModel = new UserModel();
        $this->walletModel = new WalletModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Pay for booking using wallet
     * POST JSON: { "user_id": 12, "booking_id": "BK12345", "amount": 1500 }
     */
    public function pay()
    {
        $req = $this->request->getJSON(true);

        $userId = $req['user_id'] ?? null;
        $bookingId = $req['booking_id'] ?? null;
        $amount = floatval($req['amount'] ?? 0);

        if (!$userId || !$bookingId || !$amount) {
            return $this->failValidationErrors('Missing required fields: user_id, booking_id, amount');
        }

        // Fetch wallet record
        $wallet = $this->walletModel->where('user_id', $userId)->first();

        if (!$wallet) {
            return $this->failNotFound('Wallet not found for this user.');
        }

        if ($wallet->balance < $amount) {
            return $this->fail('Insufficient wallet balance.');
        }

        // Fetch booking
        $ticket = $this->ticketModel->where('booking_id', $bookingId)->first();
        if (!$ticket) {
            return $this->failNotFound('Booking not found.');
        }

        if ($ticket->payment_status === 'paid') {
            return $this->fail('This booking has already been paid.');
        }

        // Begin transaction
        $this->db->transStart();

        // Deduct wallet balance
        $newBalance = $wallet->balance - $amount;
        $this->walletModel->update($wallet->id, ['balance' => $newBalance]);

        // Update ticket as paid
        $this->ticketModel->where('booking_id', $bookingId)->set([
            'payment_status' => 'paid',
            'pay_method_id'  => 11, // 11 = wallet payment (custom id)
            'pay_type_id'    => 3,
            'payment_detail' => 'Wallet Payment',
            'updated_at'     => date('Y-m-d H:i:s')
        ])->update();

        // Update partial payment if exists
        $this->partialpaidModel
            ->where('booking_id', $bookingId)
            ->set([
                'pay_type_id'    => 3,
                'pay_method_id'  => 11,
                'payment_detail' => 'Wallet Payment'
            ])->update();

        // Record transaction
        $this->walletModel->insertTransaction($userId, -$amount, "Wallet payment for booking #$bookingId");

        // Commit DB
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->failServerError('Failed to process wallet payment.');
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Wallet payment successful.',
            'data' => [
                'user_id' => $userId,
                'booking_id' => $bookingId,
                'amount' => $amount,
                'balance_now' => $newBalance
            ]
        ]);
    }

    /**
     * 💰 Credit wallet (manual or via top-up)
     * POST: { "user_id": 12, "amount": 2000 }
     */
    public function credit()
    {
        $req = $this->request->getJSON(true);
        $userId = $req['user_id'] ?? null;
        $amount = floatval($req['amount'] ?? 0);

        if (!$userId || $amount <= 0) {
            return $this->failValidationErrors('Missing or invalid user_id/amount');
        }

        $wallet = $this->walletModel->where('user_id', $userId)->first();

        if (!$wallet) {
            $this->walletModel->insert(['user_id' => $userId, 'balance' => $amount]);
        } else {
            $newBalance = $wallet->balance + $amount;
            $this->walletModel->update($wallet->id, ['balance' => $newBalance]);
        }

        $this->walletModel->insertTransaction($userId, $amount, "Wallet top-up");

        return $this->respond(['status' => 'success', 'message' => 'Wallet credited successfully']);
    }

    /**
     * Check wallet balance
     * GET /modules/api/v1/paymethods/wallet/balance/{user_id}
     */
    public function balance($userId = null)
    {
        if (!$userId) {
            return $this->failValidationErrors('Missing user_id');
        }

        $wallet = $this->walletModel->where('user_id', $userId)->first();
        $balance = $wallet ? $wallet->balance : 0.0;

        return $this->respond(['status' => 'success', 'balance' => $balance]);
    }
    
     public function bookWithWallet()
    {
        $req = $this->request->getJSON(true);

        $userId = $req['user_id'] ?? null;
        $tripId = $req['trip_id'] ?? null;

        if (!$userId || !$tripId) {
            return $this->failValidationErrors('Missing user_id or trip_id');
        }

        $trip = $this->tripModel->find($tripId);
        if (!$trip) {
            return $this->failNotFound('Trip not found');
        }

        $fare = (float) $trip['fare_amount'];
        $wallet = $this->walletModel->where('user_id', $userId)->first();

        if (!$wallet || $wallet['balance'] < $fare) {
            return $this->fail('Insufficient wallet balance');
        }

        $this->db->transStart();

        // Deduct fare
        $this->walletModel->update($wallet['id'], [
            'balance' => $wallet['balance'] - $fare,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Create ticket record
        $ticketId = "QR" . strtoupper(substr(md5(uniqid()), 0, 8));
        $this->ticketModel->insert([
            'booking_id' => $ticketId,
            'passanger_id' => $userId,
            'trip_id' => $tripId,
            'pay_method_id' => 9, // wallet
            'payment_status' => 'paid',
            'payment_detail' => 'Wallet QR Booking',
            'amount' => $fare,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->fail('Failed to book trip');
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Ride booked successfully using wallet',
            'ticket_id' => $ticketId,
            'remaining_balance' => $wallet['balance'] - $fare
        ]);
    }

}
