<?php echo $this->extend('template/admin/main') ?>

<?php
$formatCurrency = function ($value) use ($currencySymbol) {
    $amount = is_numeric($value) ? (float) $value : 0;
    return $currencySymbol . number_format($amount, 2);
};

$cardStats = $cardStats ?? [];
$transactionFlow = $transactionFlow ?? ['labels' => [], 'income' => [], 'expense' => []];
$paymentMethodChart = $paymentMethodChart ?? ['labels' => [], 'data' => []];
$recentTransactions = $recentTransactions ?? [];
$gatewayPerformance = $gatewayPerformance ?? [];
$topCustomers = $topCustomers ?? [];
$walletSummary = $walletSummary ?? ['totalBalance' => 0, 'walletCount' => 0, 'lastActivity' => null];
?>

<?php echo $this->section('content') ?>
<div class="space-y-8">
    <section class="bg-gradient-to-r from-primary-blue via-dark-blue to-primary-blue text-white rounded-3xl p-6 lg:p-10 shadow-lg relative overflow-hidden">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <p class="text-sm uppercase tracking-[0.35em] text-white/70">Payment Control Center</p>
                <h1 class="text-3xl lg:text-4xl font-semibold mt-2">Monitor OvoPay traffic & smart wallet flows</h1>
                <p class="mt-4 text-white/80 max-w-2xl">Track real-time settlement states, gateway uptime, wallet cash-ins, and settlement failures in one panel.</p>
                <div class="mt-6 flex flex-wrap gap-3 text-sm">
                    <a href="<?= esc($portalUrl) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full hover:bg-white/20 transition">
                        <i class="ri-external-link-line"></i>
                        Go to OvoPay Console
                    </a>
                    <span class="inline-flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full">
                        <span class="size-2 rounded-full bg-primary-green animate-pulse"></span>
                        Live monitoring enabled
                    </span>
                </div>
            </div>
            <img src="<?= base_url('public/newadmin/assets/cards.png') ?>" alt="Payment" class="w-40 lg:w-60 opacity-90">
        </div>
        <img src="<?= base_url('public/newadmin/assets/route-2.png') ?>" alt="pattern" class="absolute inset-y-0 right-0 w-1/2 opacity-20 pointer-events-none hidden lg:block">
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Deposits</p>
                    <p class="text-2xl font-semibold text-dark-blue mt-2"><?= $formatCurrency($cardStats['totalDeposits'] ?? 0) ?></p>
                    <p class="text-xs text-green-600 mt-1">Wallet + ticket settlements</p>
                </div>
                <div class="bg-primary-blue/10 text-primary-blue p-3 rounded-xl">
                    <i class="ri-download-2-line text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending Transactions</p>
                    <p class="text-2xl font-semibold text-dark-blue mt-2"><?= number_format($cardStats['pendingTransactions'] ?? 0) ?></p>
                    <p class="text-xs text-orange-500 mt-1">Awaiting external confirmation</p>
                </div>
                <div class="bg-orange-100 text-orange-500 p-3 rounded-xl">
                    <i class="ri-time-line text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Successful Payments</p>
                    <p class="text-2xl font-semibold text-dark-blue mt-2"><?= number_format($cardStats['successfulPayments'] ?? 0) ?></p>
                    <p class="text-xs text-green-600 mt-1">Tickets settled today</p>
                </div>
                <div class="bg-green-100 text-green-600 p-3 rounded-xl">
                    <i class="ri-checkbox-circle-line text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Failed Transactions</p>
                    <p class="text-2xl font-semibold text-dark-blue mt-2"><?= number_format($cardStats['failedTransactions'] ?? 0) ?></p>
                    <p class="text-xs text-red-500 mt-1">Requires dispute workflow</p>
                </div>
                <div class="bg-red-100 text-red-500 p-3 rounded-xl">
                    <i class="ri-close-circle-line text-xl"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-dark-blue">Transaction Flow</h2>
                    <p class="text-sm text-gray-500">Income vs expenses (current year)</p>
                </div>
            </div>
            <div class="p-5">
                <canvas id="transactionChart" height="300"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-dark-blue">Payment Methods</h2>
                    <p class="text-sm text-gray-500">Share by processor</p>
                </div>
            </div>
            <div class="p-5">
                <?php if (!empty($paymentMethodChart['data'])) : ?>
                    <canvas id="paymentChart" height="260"></canvas>
                <?php else : ?>
                    <p class="text-sm text-gray-500">No payment data recorded yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-gray-100 shadow-sm">
        <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-dark-blue">Recent Transactions</h2>
                <p class="text-sm text-gray-500">Ledger entries from Accounts module</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500">Reference</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500">Detail</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500">Amount</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500">Type</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if (!empty($recentTransactions)) : ?>
                        <?php foreach ($recentTransactions as $transaction) : ?>
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs text-gray-500">#<?= esc($transaction->id) ?></td>
                                <td class="px-4 py-3 text-gray-800"><?= esc($transaction->detail ?? 'Transaction') ?></td>
                                <td class="px-4 py-3 font-semibold <?= ($transaction->type ?? '') === 'expense' ? 'text-red-600' : 'text-green-600' ?>">
                                    <?= ($transaction->type ?? '') === 'expense' ? '-' : '+' ?><?= $formatCurrency($transaction->amount ?? 0) ?>
                                </td>
                                <td class="px-4 py-3 text-xs uppercase text-gray-500"><?= esc($transaction->type ?? 'income') ?></td>
                                <?php $createdAt = isset($transaction->created_at) ? strtotime($transaction->created_at) : null; ?>
                                <td class="px-4 py-3 text-gray-500 text-xs">
                                    <?= $createdAt ? esc(date('d M Y H:i', $createdAt)) : '--' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-gray-500">No transactions logged yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-lg font-semibold text-dark-blue mb-4">Payment Gateway Performance</h2>
            <div class="space-y-4">
                <?php if (!empty($gatewayPerformance)) : ?>
                    <?php foreach ($gatewayPerformance as $gateway) : ?>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-dark-blue"><?= esc($gateway->gateway ?? 'Gateway') ?></p>
                                <p class="text-xs text-gray-500">
                                    <?= $formatCurrency($gateway->total_amount ?? 0) ?> processed · <?= number_format($gateway->total_transactions ?? 0) ?> tx
                                </p>
                            </div>
                            <span class="text-primary-green font-semibold text-sm">Live</span>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="text-sm text-gray-500">No gateway totals available.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-lg font-semibold text-dark-blue mb-4">Top Customers</h2>
            <div class="space-y-4">
                <?php if (!empty($topCustomers)) : ?>
                    <?php foreach ($topCustomers as $customer) : ?>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-dark-blue"><?= esc($customer->name ?? 'Customer') ?></p>
                                <p class="text-xs text-gray-500"><?= number_format($customer->total_transactions ?? 0) ?> transactions</p>
                            </div>
                            <p class="font-semibold text-dark-blue"><?= $formatCurrency($customer->total_amount ?? 0) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="text-sm text-gray-500">No customer insights available.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-lg font-semibold text-dark-blue mb-4">Wallet Balance Overview</h2>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span>Total wallet balance</span>
                    <strong><?= $formatCurrency($walletSummary['totalBalance'] ?? 0) ?></strong>
                </div>
                <div class="flex items-center justify-between">
                    <span>Active wallets</span>
                    <strong><?= number_format($walletSummary['walletCount'] ?? 0) ?></strong>
                </div>
                <div class="flex items-center justify-between">
                    <span>Last funding activity</span>
                    <?php $lastActivity = $walletSummary['lastActivity'] ?? null; ?>
                    <strong><?= $lastActivity ? esc(date('d M Y H:i', strtotime($lastActivity))) : '—' ?></strong>
                </div>
            </div>
        </div>
    </section>
</div>
<?php echo $this->endSection() ?>

<?php echo $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const transactionFlow = <?= json_encode($transactionFlow, JSON_NUMERIC_CHECK) ?>;
    const paymentMethodChart = <?= json_encode($paymentMethodChart, JSON_NUMERIC_CHECK) ?>;

    const transactionCtx = document.getElementById('transactionChart');
    if (transactionCtx && transactionFlow.labels.length) {
        new Chart(transactionCtx, {
            type: 'line',
            data: {
                labels: transactionFlow.labels,
                datasets: [
                    {
                        label: 'Income',
                        data: transactionFlow.income,
                        borderColor: '#27c840',
                        backgroundColor: 'rgba(39, 200, 64, 0.15)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                    },
                    {
                        label: 'Expense',
                        data: transactionFlow.expense,
                        borderColor: '#1f2b6c',
                        backgroundColor: 'rgba(31, 43, 108, 0.15)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '<?= $currencySymbol ?>' + value
                        }
                    }
                }
            }
        });
    }

    const paymentCtx = document.getElementById('paymentChart');
    if (paymentCtx && paymentMethodChart.labels.length) {
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: paymentMethodChart.labels,
                datasets: [{
                    data: paymentMethodChart.data,
                    backgroundColor: ['#1f2b6c', '#27c840', '#0f9918', '#22b038', '#F97316', '#EC4899'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }
</script>
<?php echo $this->endSection() ?>
