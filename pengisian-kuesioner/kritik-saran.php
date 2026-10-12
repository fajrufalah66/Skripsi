<?php

require './../config/app.php';
require './../vendor/autoload.php';

if(empty($_COOKIE['device_id'])) {
    redirect('pengisian-kuesioner');
}

$idTransaksi = clearInput($_GET['i'] ?? '');
$cekTransaksiPengunjung = $db->query("SELECT * FROM pengunjung WHERE id_transaksi = '$idTransaksi' AND device_id = '".$_COOKIE['device_id']."'");
$dataPengunjung = $cekTransaksiPengunjung->fetch_object();

if($dataPengunjung->device_id <> $_COOKIE['device_id']) {
    redirect('pengisian-kuesioner');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $saran = clearInput($_POST['saran'] ?? '');
    $keluhan = clearInput($_POST['keluhan'] ?? '');

    if (empty($saran) || empty($keluhan)) {
        alertMessage('error', 'Semua kolom wajib diisi!');
        redirect('pengisian-kuesioner/kritik-saran.php?i='.$idTransaksi);
        exit;
    }

    try {
        $cekTransaksi = $db->query("SELECT id FROM transaksi WHERE id = '$idTransaksi'");

        if($cekTransaksi->num_rows == 0) {
            alertMessage('error', 'ID Transaksi Tidak Ditemukan!');
            redirect('pengisian-kuesioner');
            exit;
        }

        $cekKritikSaranPengunjung = $db->query("SELECT id FROM kritik_saran WHERE id_pengunjung = '$dataPengunjung->id'");

        if($cekKritikSaranPengunjung->num_rows == 1) {
            alertMessage('error', 'Anda Sudah Mengisi Form Kritik Dan Saran!');
            redirect('pengisian-kuesioner/kritik-saran.php?i='.$idTransaksi);
            exit;
        }

        $db->query("INSERT INTO kritik_saran (id_pengunjung, saran, keluhan) VALUES ('{$dataPengunjung->id}', '{$saran}', '{$keluhan}')");
        alertMessage('success', 'Terima Kasih Telah Mengisi Kritik Dan Saran!');
        redirect('pengisian-kuesioner');
        exit;
        
    } catch(Exception $e) {
        alertMessage('error', 'Terjadi sebuah kesalahan');
    }
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

    <div class="flex justify-center items-center py-10 px-4">
        <div class="max-w-2xl mx-auto w-full space-y-4">
            <div class="relative grid w-full bg-white rounded-md shadow dark:bg-gray-800">
                <div class="py-3 px-4 border-b border-gray-200 dark:border-gray-600 flex justify-center items-center">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Kritik Dan Saran</h3>
                </div>
                <form method="POST" class="p-4 space-y-4">

                    <div class="flex gap-x-4 items-center">
                        <label for="saran" class="text-gray-600 text-sm font-medium w-2/4">Saran</label>
                        <div class="w-full">
                            <input
                                type="text"
                                id="saran"
                                name="saran"
                                class="bg-gray-50 border-gray-300 rounded-lg h-11 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5"
                                placeholder="Masukkan Saran"
                                required />
                        </div>
                    </div>

                    <div class="flex gap-x-4">
                        <label for="keluhan" class="text-gray-600 text-sm font-medium w-2/4">Keluhan</label>
                        <div class="w-full">
                            <textarea
                                type="text"
                                id="keluhan"
                                name="keluhan"
                                class="bg-gray-50 border-gray-300 rounded-lg h-32 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5"
                                placeholder="Masukkan Keluhan"
                                required></textarea>
                        </div>
                    </div>

                    <div class="flex justify-center pt-8">
                        <a href="<?= baseUrl('pengisian-kuesioner') ?>"
                            class="block text-white bg-red-500 transition hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-md text-sm px-5 py-2.5 text-center me-3">
                            Batal
                        </a>

                        <button type="submit"
                            class="block text-white bg-green-500 transition hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-md text-sm px-5 py-2.5 text-center disabled:opacity-75">
                            Simpan
                        </button>
                    </div>
                </form>
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