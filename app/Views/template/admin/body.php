<?php
$sessiondata = \Config\Services::session(); // Needed for Point 5
$uri = service('uri');
$menuname = $uri->getSegment(3);

?>



<?php if ($menuname == "admin" || $menuname == "driver") : ?>
    <!-- Dashboard uses custom hero -->
<?php else : ?>
    <div class="bg-white border border-gray-100 rounded-2xl px-5 py-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-gray-400">Module</p>
        <h1 class="text-xl font-semibold text-dark-blue mb-1"><?php echo esc(!empty($pageheading) ? $pageheading : ($title ?? '')); ?></h1>
        <?php if (!empty($module)) : ?>
            <p class="text-sm text-gray-500"><?php echo esc($module); ?></p>
        <?php endif; ?>
    </div>
<?php endif ?>