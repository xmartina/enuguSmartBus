<?php echo $this->extend('template/admin/main') ?>

<?php
$local_session = \Config\Services::session();
$currencySymbol = $currencySymbol ?? ($local_session->get('currency_symbol') ?? '₦');
$cards = $dashboardCards ?? [];
$formatCurrency = function ($amount) use ($currencySymbol) {
    $numeric = is_numeric($amount) ? (float) $amount : 0;
    return $currencySymbol . number_format($numeric, 2);
};
?>

<?php echo $this->section('content') ?>

<?php if ($read_dashboard) : ?>
    <div class="space-y-8">
        <section class="bg-gradient-to-r from-primary-blue via-dark-blue to-primary-blue text-white rounded-3xl p-6 lg:p-10 shadow-lg overflow-hidden relative">
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-white/70">Control Center</p>
                    <h1 class="text-3xl lg:text-4xl font-semibold mt-2">Real-time Enugu Smart Bus Intelligence</h1>
                    <p class="mt-4 text-white/80 max-w-2xl">Monitor bookings, wallets, refunds, and operational KPIs in one place. All widgets refresh automatically as new trips and payment events arrive.</p>
                    <div class="mt-6 flex flex-wrap gap-4 text-sm">
                        <span class="inline-flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full">
                            <span class="size-2 rounded-full bg-primary-green animate-pulse"></span>
                            Payment API heartbeat: <strong><?= esc($paymentGatewayStatus['status'] ?? 'Unknown') ?></strong>
                        </span>
                        <span class="inline-flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full">
                            Last check: <?= esc($paymentGatewayStatus['checked_at'] ?? '') ?>
                        </span>
                    </div>
                </div>
                <img src="<?= base_url('public/newadmin/assets/hero-banner.png') ?>" alt="Dashboard hero" class="w-full max-w-xl pointer-events-none" />
            </div>
            <div class="absolute inset-y-0 right-0 opacity-20 pointer-events-none hidden lg:block">
                <img src="<?= base_url('public/newadmin/assets/route-1.png') ?>" alt="pattern" class="h-full object-cover" />
            </div>
        </section>

        <?php
        $cardItems = [
            [
                'label' => 'Registered Passengers',
                'value' => number_format($cards['registeredPassengers'] ?? $totalpassanger ?? 0),
                'icon'  => 'total-registered.png',
                'meta'  => 'Live total'
            ],
            [
                'label' => 'Trips Today',
                'value' => number_format($cards['tripsToday'] ?? $todaytrip ?? 0),
                'icon'  => 'total-trip.png',
                'meta'  => 'Trips dispatched'
            ],
            [
                'label' => 'Amount Paid Today',
                'value' => $formatCurrency($cards['amountPaidToday'] ?? $totalmoney ?? 0),
                'icon'  => 'total-amount-paid.png',
                'meta'  => 'Confirmed payments'
            ],
            [
                'label' => 'Wallet Deposits',
                'value' => $formatCurrency($cards['walletDeposits'] ?? 0),
                'icon'  => 'total-deposit.png',
                'meta'  => 'Wallet balance'
            ],
            [
                'label' => 'Ticket Bookings Today',
                'value' => number_format($cards['ticketBookingsToday'] ?? $todaybooking ?? 0),
                'icon'  => 'total-ticket-booking.png',
                'meta'  => 'Sold seats'
            ],
            [
                'label' => 'Total Booking Amount',
                'value' => $formatCurrency($cards['totalBookingAmount'] ?? 0),
                'icon'  => 'total-booking-amount.png',
                'meta'  => 'All-time'
            ],
            [
                'label' => 'Refund Requests',
                'value' => number_format($cards['refundRequests'] ?? 0),
                'icon'  => 'total-refund.png',
                'meta'  => 'Awaiting action'
            ],
            [
                'label' => 'Active Staff',
                'value' => number_format($cards['staffCount'] ?? 0),
                'icon'  => 'total-staff.png',
                'meta'  => 'System users'
            ],
        ];
        ?>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <?php foreach ($cardItems as $item) : ?>
                <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm">
                    <div class="bg-background rounded-2xl p-3">
                        <img src="<?= base_url('public/newadmin/assets/' . $item['icon']) ?>" alt="<?= esc($item['label']) ?>" class="w-9 h-9">
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500"><?= esc($item['label']) ?></p>
                        <p class="text-2xl font-semibold text-dark-blue mt-1"><?= esc($item['value']) ?></p>
                        <p class="text-xs text-gray-400 mt-1"><?= esc($item['meta']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="grid gap-6 lg:grid-cols-3">
            <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-dark-blue">Yearly Income vs Expense</h2>
                        <p class="text-sm text-gray-500">Aggregate view across the current fiscal years.</p>
                    </div>
                    <span class="text-xs text-gray-400">Auto-updated</span>
                </div>
                <div id="apexMixedChart" class="h-[340px]"></div>
            </div>
            <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-dark-blue">Weekly Income vs Expense</h2>
                        <p class="text-sm text-gray-500">Last 7 operational days.</p>
                    </div>
                </div>
                <div id="timelineChart" class="h-[340px]"></div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-3">
            <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-dark-blue">Payment Method Distribution</h2>
                        <p class="text-sm text-gray-500">Wallets, POS, transfers</p>
                    </div>
                </div>
                <div id="monochromeChart" class="h-[320px]"></div>
            </div>
            <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-dark-blue">Monthly Income vs Expense</h2>
                        <p class="text-sm text-gray-500">January - December comparison.</p>
                    </div>
                </div>
                <div id="lineChart" class="h-[320px]"></div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-3">
            <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-dark-blue">Monthly Ticket Bookings</h2>
                        <p class="text-sm text-gray-500">Seat confirmations by month.</p>
                    </div>
                </div>
                <div id="gradientLineArea" class="h-[320px]"></div>
            </div>
            <?php if ($local_session->get('role_id') == 1) : ?>
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-dark-blue">Agent Ticket Booking</h2>
                            <p class="text-sm text-gray-500">Top performing agents.</p>
                        </div>
                    </div>
                    <div id="barChart" class="h-[320px]"></div>
                </div>
            <?php endif; ?>
        </section>

        <section class="grid gap-6 lg:grid-cols-3">
            <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-dark-blue">Payment Gateway</h2>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?= ($paymentGatewayStatus['status'] ?? '') === 'Active' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                        <?= esc($paymentGatewayStatus['status'] ?? 'Unknown') ?>
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-2"><?= esc($paymentGatewayStatus['description'] ?? 'Awaiting heartbeat status') ?></p>
                <div class="mt-4 text-sm text-gray-500">
                    <p>Last checked: <strong><?= esc($paymentGatewayStatus['checked_at'] ?? '—') ?></strong></p>
                    <p>Endpoint: portal.enugusmartbus.com</p>
                </div>
                <div class="mt-6 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span>Transactions logged</span>
                        <strong><?= number_format($paymentAnalytics['totalProcessed'] ?? 0) ?></strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Total value</span>
                        <strong><?= $formatCurrency($paymentAnalytics['totalValue'] ?? 0) ?></strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Average ticket</span>
                        <strong><?= $formatCurrency($paymentAnalytics['averageTransaction'] ?? 0) ?></strong>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-dark-blue">Recent Transactions</h2>
                        <p class="text-sm text-gray-500">Latest entries from Accounts ledger.</p>
                    </div>
                </div>
                <div class="flex flex-col gap-4">
                    <?php if (!empty($recentTransactions)) : ?>
                        <?php foreach ($recentTransactions as $transaction) : ?>
                            <div class="flex flex-wrap items-center justify-between gap-3 border border-gray-100 rounded-2xl px-4 py-3">
                                <div>
                                    <p class="text-sm font-semibold text-dark-blue"><?= esc($transaction->detail ?? 'Transaction') ?></p>
                                    <p class="text-xs text-gray-500"><?= esc(date('d M Y H:i', strtotime($transaction->created_at ?? 'now'))) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-base font-semibold <?= ($transaction->type ?? '') === 'expense' ? 'text-red-500' : 'text-primary-green' ?>">
                                        <?= ($transaction->type ?? '') === 'expense' ? '-' : '+' ?><?= $formatCurrency($transaction->amount ?? 0) ?>
                                    </p>
                                    <p class="text-xs text-gray-500 uppercase"><?= esc($transaction->type ?? 'income') ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="text-sm text-gray-500">No transactions recorded yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-dark-blue">Refund Center</h2>
                <p class="text-sm text-gray-500">Monitor refund approvals and pending requests.</p>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span>Total requests</span>
                        <strong><?= number_format($refundAnalytics['total'] ?? 0) ?></strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Approved</span>
                        <strong><?= number_format($refundAnalytics['approved'] ?? 0) ?></strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Pending</span>
                        <strong><?= number_format($refundAnalytics['pending'] ?? 0) ?></strong>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-dark-blue">Dispute Management</h2>
                <p class="text-sm text-gray-500">Cancelations & dispute queue.</p>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span>Open disputes</span>
                        <strong><?= number_format($disputeAnalytics['open'] ?? 0) ?></strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Resolved</span>
                        <strong><?= number_format($disputeAnalytics['resolved'] ?? 0) ?></strong>
                    </div>
                </div>
            </div>
        </section>

        <script>
            var incomeLabel = "<?= esc(lang('Localize.income')) ?>";
            var expenseLabel = "<?= esc(lang('Localize.expense')) ?>";
            var saturday = "<?= esc(lang('Localize.saturday')) ?>";
            var sunday = "<?= esc(lang('Localize.sunday')) ?>";
            var monday = "<?= esc(lang('Localize.monday')) ?>";
            var tuesday = "<?= esc(lang('Localize.tuesday')) ?>";
            var wednesday = "<?= esc(lang('Localize.wednesday')) ?>";
            var thursday = "<?= esc(lang('Localize.thursday')) ?>";
            var friday = "<?= esc(lang('Localize.friday')) ?>";
            var difference = "<?= esc(lang('Localize.difference')) ?>";
            var totalPaid = "<?= esc(lang('Localize.total') . ' ' . lang('Localize.paid')) ?>";
            var totalExpece = "<?= esc(lang('Localize.total') . ' ' . lang('Localize.expense')) ?>";
            var january = "<?= esc(lang('Localize.january')) ?>";
            var february = "<?= esc(lang('Localize.february')) ?>";
            var march = "<?= esc(lang('Localize.march')) ?>";
            var april = "<?= esc(lang('Localize.april')) ?>";
            var may = "<?= esc(lang('Localize.may')) ?>";
            var june = "<?= esc(lang('Localize.june')) ?>";
            var july = "<?= esc(lang('Localize.july')) ?>";
            var august = "<?= esc(lang('Localize.august')) ?>";
            var september = "<?= esc(lang('Localize.september')) ?>";
            var october = "<?= esc(lang('Localize.october')) ?>";
            var november = "<?= esc(lang('Localize.november')) ?>";
            var december = "<?= esc(lang('Localize.december')) ?>";
        </script>

        <input type="hidden" id="year" value="<?= esc($year ?? '', 'attr') ?>">
        <input type="hidden" id="yearincome" value="<?= esc($income ?? '', 'attr') ?>">
        <input type="hidden" id="yearexpense" value="<?= esc($expense ?? '', 'attr') ?>">
        <input type="hidden" id="monthincome" value="<?= esc($monthincome ?? '', 'attr') ?>">
        <input type="hidden" id="monthexpense" value="<?= esc($monthexpense ?? '', 'attr') ?>">
        <input type="hidden" id="weekincome" value="<?= esc($weekincome ?? '', 'attr') ?>">
        <input type="hidden" id="weekexpense" value="<?= esc($weekexpense ?? '', 'attr') ?>">
        <input type="hidden" id="paylable" value="<?= esc($paylable ?? '', 'attr') ?>">
        <input type="hidden" id="paydata" value="<?= esc($paydata ?? '', 'attr') ?>">
        <input type="hidden" id="agentlable" value="<?= esc($agentLable ?? '', 'attr') ?>">
        <input type="hidden" id="agentamount" value="<?= esc($agentAmount ?? '', 'attr') ?>">
        <input type="hidden" id="ticketbook" value="<?= esc($bookticket ?? '', 'attr') ?>">
    </div>
<?php else : ?>
    <div class="bg-white rounded-3xl border border-red-200 text-center p-10">
        <h3 class="text-2xl font-semibold text-red-600 mb-2"><?= esc($dashboard_denied_text) ?></h3>
        <p class="text-gray-500">Please contact the system administrator to enable dashboard permissions for this role.</p>
    </div>
<?php endif; ?>

<?php echo $this->endSection() ?>

<?php echo $this->section('js') ?>
<script src="<?= base_url('public/apexcharts/dist/apexcharts.min.js'); ?>"></script>
<script src="<?= base_url('public/js/newchart.js'); ?>"></script>
<?php echo $this->endSection() ?>