<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="shortcut icon" href="<?= baseUrl('assets/images/logo.jpg') ?>" type="image/x-icon">
    <title>Cikadongdong River Tubing</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'false',
        };
    </script>

    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="text-slate-600 bg-gray-200 pb-6">
    <?php if(isAuthenticated()) : ?>
        <nav class="bg-white border-gray-200 dark:bg-gray-900">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                <div class="flex items-center gap-4">
                    <a href="<?= baseUrl('') ?>" class="flex items-center space-x-2 md:me-4 rtl:space-x-reverse">
                        <img src="<?= baseUrl('assets/images/logo.jpg') ?>" class="h-10" alt="Logo" />
                    </a>
                    <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1">
                        <ul class="flex flex-col font-medium py-4 px-2 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-4 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                            <?php
                            $baseClasses = 'block py-2 px-3 md:p-0 rounded';
                            $hoverClasses = 'hover:bg-gray-100 md:hover:bg-transparent md:hover:text-sky-700 md:dark:hover:text-sky-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700';
                            $activeClasses = 'text-white bg-sky-700 md:bg-transparent md:text-sky-700 md:dark:text-sky-500';
                            $inactiveClasses = 'text-gray-900 dark:text-white';

                            function renderMenuItem($url, $label, $isActive, $baseClasses, $hoverClasses, $activeClasses, $inactiveClasses) {
                                $classes = $baseClasses . ' ' . ($isActive ? $activeClasses : $inactiveClasses . ' ' . $hoverClasses);
                                return "<li><a href='" . baseUrl($url) . "' class='" . $classes . "'>" . $label . "</a></li>";
                            }

                            $menuItems = [
                                'Ketua Pengelola' => [
                                    ['url' => 'master-pernyataan/', 'label' => 'Master Pernyataan'],
                                    ['url' => 'master-kuesioner/', 'label' => 'Master Kuesioner'],
                                    ['url' => 'hasil-kuesioner/', 'label' => 'Hasil Kuesioner'],
                                    ['url' => 'kritik-saran/', 'label' => 'Kritik Dan Saran'],
                                ],
                                'default' => [
                                    ['url' => 'data-transaksi', 'label' => 'Data Transaksi'],
                                ]
                            ];

                            $currentLabel = $breadcrumbItems[0]['label'] ?? '';
                            $userMenu = $menuItems[$user->jabatan] ?? $menuItems['default'];
                            ?>

                            <li>
                                <a href="<?= baseUrl('') ?>" class="<?= $baseClasses . ' ' . ($currentLabel === '' ? $activeClasses : $inactiveClasses . ' ' . $hoverClasses) ?>">Beranda</a>
                            </li>

                            <?php foreach ($userMenu as $item): ?>
                                <?= renderMenuItem(
                                    $item['url'],
                                    $item['label'],
                                    $currentLabel === $item['label'],
                                    $baseClasses,
                                    $hoverClasses,
                                    $activeClasses,
                                    $inactiveClasses
                                ) ?>
                            <?php endforeach; ?>

                        </ul>
                    </div>
                </div>
                <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                    <a href="<?= baseUrl('auth/logout.php') ?>" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Logout</a>
                    <button data-collapse-toggle="navbar-cta" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-cta" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                        </svg>
                    </button>
                </div>
                <div class="items-center justify-between hidden w-full" id="navbar-cta">
                    <ul class="flex flex-col font-medium py-4 px-2 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-4 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                        <li>
                            <a href="<?= baseUrl('') ?>" class="<?= $baseClasses . ' ' . ($currentLabel === '' ? $activeClasses : $inactiveClasses . ' ' . $hoverClasses) ?>">Beranda</a>
                        </li>

                        <?php foreach ($userMenu as $item): ?>
                            <?= renderMenuItem(
                                $item['url'],
                                $item['label'],
                                $currentLabel === $item['label'],
                                $baseClasses,
                                $hoverClasses,
                                $activeClasses,
                                $inactiveClasses
                            ) ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="p-4 space-y-4 max-w-screen-xl mx-auto">
    <?php endif ?>