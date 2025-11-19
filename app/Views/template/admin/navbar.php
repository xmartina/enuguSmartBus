<?php 
$sessiondata = \Config\Services::session();
$roleLabel = $sessiondata->get('role_name');
if (empty($roleLabel)) {
    $fallback = lang('Localize.admin');
    $roleLabel = ($fallback !== 'Localize.admin') ? $fallback : 'Admin';
}
?>

<header class="sticky top-0 z-30 bg-white shadow-sm px-4 py-3 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between border-b border-gray-100">
    <div class="flex items-center gap-3 w-full lg:w-auto">
        <button id="menuButton" class="lg:hidden p-2 rounded-lg bg-primary-blue text-white" aria-label="Open sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
        <div class="flex-1">
            <div class="flex items-center gap-2 bg-background border border-gray-200 rounded-full px-3 py-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input type="search" placeholder="Search modules, trips or agents" class="w-full bg-transparent text-sm focus:outline-none" />
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3 justify-end w-full lg:w-auto lg:gap-4">
        <div class="hidden lg:block">
            <?= view_cell('\\App\\Libraries\\Language::getAllLanguage') ?>
        </div>
        <button class="p-2 rounded-full bg-background" aria-label="Notifications">
            <img src="<?= base_url('public/newadmin/assets/bell.png') ?>" alt="Bell" class="w-6 h-6">
        </button>
        <div class="flex items-center bg-background rounded-full px-3 py-2 gap-2 lg:gap-3">
            <img src="<?= esc($sessiondata->get('profile_pic')) ?>" alt="Profile" class="w-8 h-8 lg:w-10 lg:h-10 rounded-full object-cover">
            <div class="hidden sm:block">
                <p class="text-sm font-semibold text-dark-blue"><?= esc($sessiondata->get('first_name') . ' ' . $sessiondata->get('last_name')) ?></p>
                <span class="text-xs text-gray-500"><?= esc($roleLabel) ?></span>
            </div>
        </div>
        <a href="<?= base_url(route_to('auth-logout')) ?>" class="px-3 py-2 lg:px-4 text-xs lg:text-sm font-semibold text-white bg-primary-green rounded-lg hover:bg-dark-green transition-colors"><?= lang('Localize.sign_out') ?></a>
    </div>
</header>

<input type="hidden" name="baseurl" id="baseurl" value="<?= base_url(); ?>">
