<?php

use App\Libraries\Rolepermission;

$uri = service('uri');
$menuname = $uri->getSegment(3);
$submenupath = implode('/', array_slice($uri->getSegments(), 3));
$currentPath = trim($uri->getPath(), '/');
$paymentSystemActive = strpos($currentPath, 'payment-system') !== false;
$manageMapActive = strpos($currentPath, 'driver-locations') !== false;

$rolepermissionLibrary = new Rolepermission();
$sessiondata = \Config\Services::session();
?>

<aside id="sidebar" class="fixed left-0 top-0 h-full w-full lg:w-64 bg-white shadow-xl transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-50 lg:z-auto overflow-y-auto">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <a href="<?= base_url(route_to('admin-home')) ?>" class="flex items-center gap-3">
            <img src="<?= esc($sessiondata->get('logo')) ?>" alt="Admin Logo" class="h-12 w-12 object-contain rounded-full">
            <div>
                <p class="text-sm text-gray-500">Enugu Smart Bus</p>
                <p class="text-lg font-semibold text-dark-blue">Admin Console</p>
            </div>
        </a>
        <button id="closeButton" class="lg:hidden p-2 rounded-full hover:bg-gray-100" aria-label="Close sidebar">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <div class="px-5 py-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <img src="<?= esc($sessiondata->get('profile_pic')) ?>" alt="Profile" class="h-12 w-12 rounded-full object-cover border-2 border-primary-green">
            <div>
                <p class="text-base font-semibold text-dark-blue"><?= esc($sessiondata->get('first_name') . ' ' . $sessiondata->get('last_name')) ?></p>
                <p class="text-xs text-gray-500 uppercase tracking-wide"><?= esc($sessiondata->get('role_name') ?? 'Administrator') ?></p>
            </div>
        </div>
    </div>

    <nav class="px-4 py-6">
        <ul class="metismenu text-sm font-semibold text-gray-600 space-y-1">
            <?php if ($rolepermissionLibrary->read("dashboard")) : ?>
                <li class="<?= ($menuname == 'admin') ? 'mm-active bg-primary-blue text-white rounded-lg' : 'rounded-lg' ?>">
                    <a href="<?= base_url(route_to('admin-home')) ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= ($menuname == 'admin') ? 'text-white' : 'hover:bg-gray-100' ?>">
                        <img src="<?= base_url('public/newadmin/assets/dashboard.png') ?>" class="w-5 h-5" alt="Dashboard" />
                        <span><?= lang('Localize.dashboard') ?></span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($rolepermissionLibrary->read("driver_dashboard")) : ?>
                <li class="<?= ($menuname == 'driver') ? 'mm-active' : '' ?>">
                    <a href="<?= base_url(route_to('driver-home')) ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                        <img src="<?= base_url('public/newadmin/assets/user-icon.png') ?>" class="w-5 h-5" alt="Driver" />
                        <span><?= lang('Localize.driver') . ' ' . lang('Localize.dashboard') ?></span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="<?= $manageMapActive ? 'mm-active' : '' ?>">
                <a href="<?= base_url('admin/driver-locations') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                    <img src="<?= base_url('public/newadmin/assets/map-icon.png') ?>" class="w-5 h-5" alt="Map" />
                    <span>Manage Map</span>
                </a>
            </li>

            <li class="<?= $paymentSystemActive ? 'mm-active' : '' ?>">
                <a href="<?= base_url(route_to('admin-payment-system')) ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $paymentSystemActive ? 'bg-primary-blue text-white' : 'hover:bg-gray-100' ?>">
                    <img src="<?= base_url('public/newadmin/assets/cards.png') ?>" class="w-5 h-5" alt="Payments" />
                    <span>Payment System</span>
                </a>
            </li>

            <?php
            $moduleConfigMenuFiles = glob(ROOTPATH . 'modules/*/Config/Menu.php');
            if ($moduleConfigMenuFiles) {
                sort($moduleConfigMenuFiles);
                foreach ($moduleConfigMenuFiles as $value) {
                    if (file_exists($value)) {
                        @include($value);
                    }
                }
            }
            ?>
        </ul>
    </nav>
</aside>