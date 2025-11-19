<!DOCTYPE html>
<html lang="en">

<head>
    <?= $this->include('common/newadmin-css') ?>
    <?= $this->include('common/headerjs') ?>
    <?= $this->renderSection('css') ?>
</head>

<body class="bg-background font-inter min-h-screen">
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-40 z-40 hidden"></div>
    
    <div class="min-h-screen flex">
        <?= $this->include('template/admin/sidebar') ?>

        <div class="flex-1 flex flex-col min-h-screen lg:ml-64">
            <?= $this->include('template/admin/navbar') ?>
            
            <main class="flex-1 overflow-y-auto bg-background pt-4 lg:pt-6 pb-6">
                <div class="px-4 lg:px-6 space-y-6">
                    <?= $this->include('template/admin/body') ?>
                    <?= $this->include('common/message') ?>
                    <div class="content-section">
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?= $this->include('common/js') ?>
    <script src="<?= base_url('public/newadmin/script.js') ?>"></script>
    <?= $this->renderSection('js') ?>
</body>

</html>
