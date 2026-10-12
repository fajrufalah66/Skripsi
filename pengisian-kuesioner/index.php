<?php

use Ramsey\Uuid\Uuid;
require './../config/app.php';
require './../vendor/autoload.php';

if(empty($_COOKIE['device_id'])) {
    setcookie(
        'device_id',
        Uuid::uuid4(),
        time() + (10 * 365 * 24 * 60 * 60)
    );

    $hasilKuesioner = null;
} else {
    $deviceId = $_COOKIE['device_id'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['validasi'])) {
    $idTransaksi = clearInput($_POST['id_transaksi'] ?? '');
    $namaPengunjung = clearInput($_POST['nama_pengunjung'] ?? '');

    if (empty($idTransaksi) || empty($namaPengunjung)) {
        alertMessage('error', 'Semua kolom wajib diisi!');
        redirect('pengisian-kuesioner');
        exit;
    }

    try {
        $cekTransaksi = $db->query("SELECT id FROM transaksi WHERE id = '$idTransaksi'");

        if($cekTransaksi->num_rows == 0) {
            alertMessage('error', 'ID Transaksi Tidak Ditemukan!');
            redirect('pengisian-kuesioner');
            exit;
        }

        // $cekTransaksiPengunjung = $db->query("SELECT id FROM pengunjung WHERE device_id = '".$_COOKIE['device_id']."'");

        // if($cekTransaksiPengunjung->num_rows == 1) {
        //     alertMessage('error', 'Kuesioner Pada ID Transaksi Tersebut Sudah Pernah Terisi!');
        //     redirect('pengisian-kuesioner');
        //     exit;
        // }
        
        // if($db->query("SELECT id FROM pengunjung WHERE id_transaksi = '$idTransaksi' AND device_id = '".$_COOKIE['device_id']."'")->num_rows == 1) {
        
        // } else {
            
        // }
        
        $db->query("INSERT INTO pengunjung (id_transaksi, nama, device_id) VALUES ('{$idTransaksi}', '{$namaPengunjung}', '{$_COOKIE['device_id']}')");
        alertMessage('success', 'Validasi Berhasil!');
        $_SESSION['validasi'] = true;
        $_SESSION['id_transaksi'] = $idTransaksi;
        redirect('pengisian-kuesioner');
        exit;
        
    } catch(Exception $e) {
        alertMessage('error', 'Terjadi sebuah kesalahan');
    }
}

$kuesioner = null;
if(isset($_SESSION['validasi'])) {
    $cekKuesioner = $db->query("SELECT * FROM data_kuesioner ORDER BY created_at DESC LIMIT 1");
    $kuesioner = $cekKuesioner->fetch_object();
    $idTransaksi = $_SESSION['id_transaksi'];
}
?>
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
    
    <?php if(!isset($_SESSION['validasi'])): ?>
    <div class="h-screen flex justify-center items-center">
        <div class="max-w-md mx-auto w-full">
            <div class="flex items-center bg-gray-50 rounded-md shadow-lg overflow-hidden w-full">
                <div class="flex flex-col justify-between p-8 w-full">
                    <img src="<?= baseUrl('assets/images/logo.jpg') ?>" class="w-20 mx-auto" alt="">
                    <h3 class="text-center font-bold text-2xl text-gray-900 mt-4">ISI KUESIONER</h3>
                    <form method="POST" class="mt-4 space-y-4">
                        <div>
                            <input
                                class="bg-gray-50 border-gray-300 rounded-lg h-11 text-gray-900 text-sm focus:ring-sky-500 focus:border-sky-500 block w-full py-2.5"
                                type="text"
                                name="id_transaksi"
                                id="id_transaksi"
                                placeholder="Masukkan ID Transaksi"
                                required />
                        </div>
                
                        <div>
                            <input
                                class="bg-gray-50 border-gray-300 rounded-lg h-11 text-gray-900 text-sm focus:ring-sky-500 focus:border-sky-500 block w-full py-2.5"
                                type="text"
                                name="nama_pengunjung"
                                id="nama_pengunjung"
                                placeholder="Masukkan Nama Anda"
                                required />
                        </div>

                        <button
                            type="submit"
                            name="validasi"
                            class="block text-white bg-blue-500 transition hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-500 font-medium rounded-md text-sm w-full px-5 py-2.5 text-center">
                            Validasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif ?>

    <div class="flex justify-center items-center py-10 px-4">
        <div class="max-w-2xl mx-auto w-full space-y-4">
            <div class="relative py-3 px-4 border border-gray-200 dark:border-gray-600 flex justify-center items-center bg-white rounded-md">
                <h3 class="font-semibold text-gray-900 dark:text-white">Kuesioner Wisata Alam Cikadongdong River Tubing</h3>
            </div>

            <div id="controls-carousel" class="relative w-full my-4" data-carousel="slide">
                <!-- Carousel wrapper -->
                <div class="relative h-52 md:h-96 overflow-hidden rounded-lg">
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <img src="<?= baseUrl('assets/images/carousel/gambar1.jpg') ?>" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                    </div>
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <img src="<?= baseUrl('assets/images/carousel/gambar2.jpg') ?>" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                    </div>
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <img src="<?= baseUrl('assets/images/carousel/gambar4.jpg') ?>" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                    </div>
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <img src="<?= baseUrl('assets/images/carousel/gambar5.jpeg') ?>" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                    </div>
                </div>

                <!-- Slider controls -->
                <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white group-focus:outline-none">
                        <svg class="w-4 h-4 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
                        </svg>
                        <span class="sr-only">Previous</span>
                    </span>
                </button>
                <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white group-focus:outline-none">
                        <svg class="w-4 h-4 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="sr-only">Next</span>
                    </span>
                </button>
            </div>

            <div class="relative grid w-full bg-white rounded-md shadow dark:bg-gray-800">
                <div class="p-4 flex justify-center gap-4 gap-x-12 items-center">
                    <a href="<?= baseUrl('pengisian-kuesioner/kuesioner.php?i='.$idTransaksi) ?>" class="flex flex-col gap-y-2 justify-center text-center items-center">
                        <svg class="w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.03v13m0-13c-2.819-.831-4.715-1.076-8.029-1.023A.99.99 0 0 0 3 6v11c0 .563.466 1.014 1.03 1.007 3.122-.043 5.018.212 7.97 1.023m0-13c2.819-.831 4.715-1.076 8.029-1.023A.99.99 0 0 1 21 6v11c0 .563-.466 1.014-1.03 1.007-3.122-.043-5.018.212-7.97 1.023"/>
                        </svg>
                        <p class="text-sm">Kuesioner</p>
                    </a>
                    <a href="<?= baseUrl('pengisian-kuesioner/kritik-saran.php?i='.$idTransaksi) ?>" class="flex flex-col gap-y-2 justify-center text-center items-center">
                        <svg class="w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 10.5h.01m-4.01 0h.01M8 10.5h.01M5 5h14a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1h-6.6a1 1 0 0 0-.69.275l-2.866 2.723A.5.5 0 0 1 8 18.635V17a1 1 0 0 0-1-1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/>
                        </svg>
                        <p class="text-sm">Kritik Dan Saran</p>
                    </a>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js" integrity="sha512-u3fPA7V8qQmhBPNT5quvaXVa1mnnLSXUep5PS1qo5NRzHwG19aHmNJnj1Q8hpA/nBWZtZD4r4AX6YOt5ynLN2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
    <script src="<?= baseUrl('assets/app.js') ?>"></script>

    <?php include './../layouts/alert.php'; ?>

    <script>
        function confirmDelete(url) {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: 'Anda yakin ingin menghapus data berikut?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "Ya, hapus!"

            }).then( ({ isConfirmed }) => {
                if(isConfirmed) {
                    location.href = url;
                }
            });
        }
    </script>
</body>
</html>