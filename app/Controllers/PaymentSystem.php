<?php

namespace App\Controllers;

use App\Libraries\Chart;
use Modules\Account\Models\AccountModel;
use Modules\Paymethod\Models\GatewaytotalModel;
use Modules\Ticket\Models\TicketModel;

class PaymentSystem extends BaseController
{
    protected Chart $chart;
    protected AccountModel $accountModel;
    protected TicketModel $ticketModel;
    protected GatewaytotalModel $gatewayTotalModel;
    protected $db;

    public function __construct()
    {
        $this->chart = new Chart();
        $this->accountModel = new AccountModel();
        $this->ticketModel = new TicketModel();
        $this->gatewayTotalModel = new GatewaytotalModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'module' => 'Payment System',
            'title' => 'Payment System Overview',
            'pageheading' => 'Payment System',
            'cardStats' => $this->getCardStats(),
            'transactionFlow' => $this->buildTransactionFlow(),
            'paymentMethodChart' => $this->buildPaymentMethodChart(),
            'recentTransactions' => $this->getRecentTransactions(),
            'gatewayPerformance' => $this->getGatewayPerformance(),
            'topCustomers' => $this->getTopCustomers(),
            'walletSummary' => $this->getWalletSummary(),
            'portalUrl' => 'https://portal.enugusmartbus.com',
            'currencySymbol' => session()->get('currency_symbol') ?? '₦',
        ];

        return view('template/admin/payment_system', $data);
    }

    protected function getCardStats(): array
    {
        return [
            'totalDeposits' => $this->sumWalletBalances(),
            'pendingTransactions' => $this->countTickets(function ($builder) {
                $builder->where('payment_status !=', 'paid')->where('cancel_status', '0');
            }),
            'successfulPayments' => $this->countTickets(function ($builder) {
                $builder->where('payment_status', 'paid');
            }),
            'failedTransactions' => $this->countTickets(function ($builder) {
                $builder->groupStart()
                    ->where('cancel_status', '1')
                    ->orWhere('refund', '1')
                    ->groupEnd();
            }),
        ];
    }

    protected function buildTransactionFlow(): array
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $income = $this->chart->monthIncome();
        $expense = $this->chart->monthExpense();

        return [
            'labels' => $months,
            'income' => $this->mapMonthSeries($income),
            'expense' => $this->mapMonthSeries($expense),
        ];
    }

    protected function buildPaymentMethodChart(): array
    {
        $raw = $this->chart->payTypeChart();
        $labels = array_keys($raw);
        $data = array_map(static fn ($value) => (float) $value, array_values($raw));

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    protected function getRecentTransactions(): array
    {
        $builder = $this->accountModel->builder();
        $builder->where('deleted_at', null)->orderBy('created_at', 'DESC')->limit(8);
        $this->applyUserScope($builder, 'system_user_id');

        return $builder->get()->getResult();
    }

    protected function getGatewayPerformance(): array
    {
        $builder = $this->gatewayTotalModel->builder();
        $builder->select('paymentgateways.name as gateway')
            ->select('COUNT(gatewaytotals.id) as total_transactions')
            ->select('COALESCE(SUM(gatewaytotals.amount), 0) as total_amount')
            ->join('paymentgateways', 'paymentgateways.id = gatewaytotals.gateway_id', 'left')
            ->where('gatewaytotals.deleted_at', null)
            ->groupBy('gatewaytotals.gateway_id')
            ->orderBy('total_amount', 'DESC');
        $this->applyUserScope($builder, 'gatewaytotals.user_id');

        return $builder->get()->getResult();
    }

    protected function getTopCustomers(): array
    {
        $builder = $this->db->table('tickets');
        $builder->select('tickets.bookby_user_id as user_id')
            ->select('COALESCE(CONCAT(MAX(user_details.first_name), " ", MAX(user_details.last_name)), MAX(users.login_email), CONCAT("Passenger #", MAX(tickets.passanger_id))) AS name')
            ->select('COUNT(tickets.id) as total_transactions')
            ->select('COALESCE(SUM(tickets.paidamount), 0) as total_amount')
            ->join('users', 'users.id = tickets.bookby_user_id', 'left')
            ->join('user_details', 'user_details.user_id = users.id', 'left')
            ->where('tickets.deleted_at', null)
            ->where('tickets.payment_status', 'paid')
            ->groupBy('tickets.bookby_user_id')
            ->orderBy('total_amount', 'DESC')
            ->limit(4);
        $this->applyUserScope($builder, 'tickets.bookby_user_id');

        return $builder->get()->getResult();
    }

    protected function getWalletSummary(): array
    {
        $builder = $this->db->table('wallets');
        $builder->selectSum('balance', 'total_balance')
            ->selectCount('id', 'wallet_count')
            ->selectMax('updated_at', 'last_activity')
            ->where('deleted_at', null);
        $this->applyUserScope($builder, 'user_id');

        $row = $builder->get()->getRow();

        return [
            'totalBalance' => $row ? (float) ($row->total_balance ?? 0) : 0.0,
            'walletCount' => $row ? (int) ($row->wallet_count ?? 0) : 0,
            'lastActivity' => $row->last_activity ?? null,
        ];
    }

    protected function sumWalletBalances(): float
    {
        $builder = $this->db->table('wallets')->selectSum('balance')->where('deleted_at', null);
        $this->applyUserScope($builder, 'user_id');
        $row = $builder->get()->getRow();
        return $row ? (float) $row->balance : 0.0;
    }

    protected function countTickets(callable $callback): int
    {
        $builder = $this->db->table('tickets')->where('deleted_at', null);
        $this->applyUserScope($builder, 'bookby_user_id');
        $callback($builder);
        return (int) $builder->countAllResults();
    }

    protected function mapMonthSeries(array $data): array
    {
        $series = [];
        for ($month = 1; $month <= 12; $month++) {
            $series[] = isset($data[$month])
                ? (float) $data[$month]
                : (float) ($data[(string) $month] ?? 0);
        }
        return $series;
    }

    protected function applyUserScope($builder, string $column)
    {
        $roleId = (int) (session()->get('role_id') ?? 1);
        if ($roleId !== 1) {
            $builder->where($column, session()->get('user_id'));
        }

        return $builder;
    }
}
