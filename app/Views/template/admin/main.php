<!DOCTYPE html>
<html lang="en">

<head>
    <?= $this->include('common/newadmin-css') ?>
    <?= $this->include('common/headerjs') ?>
    <?= $this->renderSection('css') ?>
</head>

<body class="bg-background font-inter min-h-screen">
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-40 z-40 hidden lg:hidden"></div>
    <div class="min-h-screen flex">
        <?= $this->include('template/admin/sidebar') ?>

        <div class="flex-1 flex flex-col min-h-screen">
            <?= $this->include('template/admin/navbar') ?>
            <main class="flex-1 overflow-y-auto bg-background">
                <div class="p-4 lg:p-6 space-y-6">
                    <?= $this->include('template/admin/body') ?>
                    <?= $this->include('common/message') ?>
                    <?= $this->renderSection('content') ?>
                </div>
            </main>
        </div>
    </div>

    <?= $this->include('common/js') ?>
    <script src="<?= base_url('public/newadmin/script.js') ?>"></script>
    <?= $this->renderSection('js') ?>
</body>

</html>