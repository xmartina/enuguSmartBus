<?php

$fontfamily = "Inter";
$appTitle = "Enugu Smart Bus Admin";
$favicon = base_url('public/newadmin/assets/top-bar-logo.png');
$local_session = \Config\Services::session();

if ($local_session->has('fontfamily')) {
    $fontfamily = $local_session->fontfamily;
}

if ($local_session->has('favicon')) {
    $favicon = $local_session->favicon;
}

if ($local_session->has('apptitle')) {
    $appTitle = $local_session->apptitle;
}

if (isset($pageheading)) {
    $appTitle = sprintf('%s - %s', $pageheading, $appTitle);
}
?>

<title><?= esc($appTitle) ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?= esc($favicon) ?>" sizes="32x32">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="<?= base_url('public/css/bootstrap/bootstrap.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('public/css/metisMenu/metisMenu.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('public/css/fontawesome/css/all.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('public/css/typicons/src/typicons.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('public/css/themify-icons/themify-icons.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('public/css/clockpicker.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('public/css/datepicker.css'); ?>">
<link rel="stylesheet" href="<?= base_url('public/datatables/datatables.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('public/datatables/Buttons-2.0.1/css/buttons.bootstrap5.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('public/datatables/Buttons-2.0.1/css/buttons.dataTables.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('public/css/sumoselect.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('public/jstree/dist/themes/default/style.min.css'); ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css">
<link rel="stylesheet" href="<?= base_url('public/newadmin/styles.css'); ?>">

<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'primary-blue': '#1f2b6c',
                    'primary-green': '#27c840',
                    'dark-green': '#22b038',
                    'light-green': '#0f9918',
                    'dark-blue': '#001447',
                    background: '#F1F8FF',
                    overlay: '#00131ac4',
                },
                fontFamily: {
                    inter: ['Inter', 'sans-serif'],
                    poppins: ['Poppins', 'sans-serif'],
                },
            },
        },
    };
</script>
