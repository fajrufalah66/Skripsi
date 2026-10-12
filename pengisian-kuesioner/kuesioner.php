<?php

require './../config/app.php';
require './../vendor/autoload.php';

if(empty($_COOKIE['device_id'])) {
    redirect('pengisian-kuesioner');
}

$idTransaksi = clearInput($_GET['i'] ?? '');
$cekTransaksiPengunjung = $db->query("SELECT id FROM pengunjung WHERE id_transaksi = '$idTransaksi' AND device_id = '".$_COOKIE['device_id']."'");
$dataPengunjung = $cekTransaksiPengunjung->fetch_object();

$cekKuesioner = $db->query("SELECT * FROM data_kuesioner ORDER BY created_at DESC LIMIT 1");
$kuesioner = $cekKuesioner->fetch_object();

$deviceId = $_COOKIE['device_id'];
$hasilKuesioner = $db->query("SELECT * FROM hasil_kuesioner JOIN pengunjung ON pengunjung.id = hasil_kuesioner.id_pengunjung WHERE pengunjung.device_id = '{$deviceId}' AND hasil_kuesioner.id_data_kuesioner = '{$kuesioner->id}'")->fetch_object();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $tglPengisian = date('Y-m-d');
        $db->query("INSERT INTO hasil_kuesioner (id_data_kuesioner, id_pengunjung, tgl_pengisian) VALUES ('{$kuesioner->id}', '{$dataPengunjung->id}', '{$tglPengisian}')");
        $idHasilKuesioner = $db->insert_id;

        $errors = [];

        if (empty($_POST['jawaban_harapan']) || count($_POST['jawaban_harapan']) < $jumlahPertanyaan) {
            $errors[] = "Harap isi semua jawaban.";
        }

        if (empty($_POST['jawaban_kenyataan']) || count($_POST['jawaban_kenyataan']) < $jumlahPertanyaan) {
            $errors[] = "Harap isi semua jawaban.";
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                alertMessage('error', $error);
            }

            redirect('pengisian-kuesioner/kuesioner.php?i='.$idTransaksi);
            exit;
        }

        foreach($_POST['jawaban_harapan'] as $key => $jawabanHarapan) {
            $jawabanKenyataan = $_POST['jawaban_kenyataan'][$key];

            $db->query("INSERT INTO jawaban_kuesioner (id_hasil_kuesioner, id_data_pernyataan, jawaban_harapan, jawaban_kenyataan) VALUES ('{$idHasilKuesioner}', '{$key}', '{$jawabanHarapan}', '{$jawabanKenyataan}')");
        }

        alertMessage('success', 'Berhasil menyimpan response kuesioner, terimakasih atas jawaban anda!');
    } catch(Exception $e) {
        redirect('pengisian-kuesioner/kuesioner.php?i='.$idTransaksi);
    }

    redirect('pengisian-kuesioner');
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

            <?php if(!is_null($hasilKuesioner)) : ?>
                <div class="flex items-center bg-gray-50 rounded-md shadow-lg overflow-hidden w-full">
                    <div class="flex flex-col justify-between p-8 w-full">
                        <svg class="w-20 h-20 text-blue-500 mx-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>
                        </svg>
                        <p class="text-gray-700 mt-2 text-center">Jawaban response kuesioner anda telah di submit!</p>
                    </div>
                </div>
            <?php elseif($kuesioner->status == 'Draft') : ?>
                <div class="flex items-center bg-gray-50 rounded-md shadow-lg overflow-hidden w-full">
                    <div class="flex flex-col justify-between p-8 w-full">
                        <svg class="w-20 h-20 text-yellow-400 mx-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <p class="text-gray-700 mt-2 text-center">Kuesioner berikut masih dalam draft, harap tunggu beberapa saat hingga kuesioner sudah di publish!</p>
                    </div>
                </div>
            <?php elseif($kuesioner->status == 'Selesai') : ?>
                <div class="flex items-center bg-gray-50 rounded-md shadow-lg overflow-hidden w-full">
                    <div class="flex flex-col justify-between p-8 w-full">
                        <svg class="w-20 h-20 text-green-400 mx-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 1 0-18c1.052 0 2.062.18 3 .512M7 9.577l3.923 3.923 8.5-8.5M17 14v6m-3-3h6"/>
                        </svg>
                        <p class="text-gray-700 mt-2 text-center">Kuesioner berikut sudah selesai, terimakasih atas partisipasi anda dalam pengisian kuesioner!</p>
                    </div>
                </div>
            <?php else : ?>
                <form method="POST">
                    <div class="relative grid w-full bg-white rounded-md shadow dark:bg-gray-800">
                        <div class="py-3 px-4 border-b border-gray-200 dark:border-gray-600 flex justify-center items-center">
                            <h3 class="font-semibold text-gray-900 dark:text-white"><?= $kuesioner->nama_kuesioner ?></h3>
                        </div>
                        <div class="space-y-4 p-4">
                            <?php
                                $dimensiLayanans = json_decode($kuesioner->dimensi_layanan);
                                $no = 1;
                                foreach ($dimensiLayanans as $dimensiLayanan) :
                                    $cekPernyataan = $db->query("SELECT * FROM data_pernyataan WHERE dimensi_layanan = '$dimensiLayanan' AND status = 'Aktif'");
                                    while ($dataPernyataan = $cekPernyataan->fetch_object()) :
                            ?>
                                    <div>
                                        <h1 class="font-gray-800 text-sm font-semibold mb-2"><?= $no ?>. <?= $dataPernyataan->pernyataan ?></h1>

                                        <div class="grid grid-cols-2">
                                            <div class="col-span-1">
                                                <p class="mb-2 text-sm">Jawaban Harapan</p>

                                                <div class="flex items-center mb-2">
                                                    <input id="jawaban-harapan-<?= $no ?>-1" type="radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                                        value="STS" name="jawaban_harapan[<?= $dataPernyataan->id ?>]">
                                                    <label for="jawaban-harapan-<?= $no ?>-1" class="ms-2 text-xs font-medium text-gray-900">Sangat Tidak Setuju</label>
                                                </div>

                                                <div class="flex items-center mb-2">
                                                    <input id="jawaban-harapan-<?= $no ?>-2" type="radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                                        value="TS" name="jawaban_harapan[<?= $dataPernyataan->id ?>]">
                                                    <label for="jawaban-harapan-<?= $no ?>-2" class="ms-2 text-xs font-medium text-gray-900">Tidak Setuju</label>
                                                </div>

                                                <div class="flex items-center mb-2">
                                                    <input id="jawaban-harapan-<?= $no ?>-3" type="radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                                        value="CS" name="jawaban_harapan[<?= $dataPernyataan->id ?>]">
                                                    <label for="jawaban-harapan-<?= $no ?>-3" class="ms-2 text-xs font-medium text-gray-900">Cukup Setuju</label>
                                                </div>

                                                <div class="flex items-center mb-2">
                                                    <input id="jawaban-harapan-<?= $no ?>-4" type="radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                                        value="S" name="jawaban_harapan[<?= $dataPernyataan->id ?>]">
                                                    <label for="jawaban-harapan-<?= $no ?>-4" class="ms-2 text-xs font-medium text-gray-900">Setuju</label>
                                                </div>

                                                <div class="flex items-center mb-2">
                                                    <input id="jawaban-harapan-<?= $no ?>-5" type="radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                                        value="SS" name="jawaban_harapan[<?= $dataPernyataan->id ?>]">
                                                    <label for="jawaban-harapan-<?= $no ?>-5" class="ms-2 text-xs font-medium text-gray-900">Sangat Setuju</label>
                                                </div>
                                            </div>

                                            <div class="col-span-1">
                                                <p class="mb-2 text-sm">Jawaban Kenyataan</p>

                                                <div class="flex items-center mb-2">
                                                    <input id="jawaban-kenyataan-<?= $no ?>-1" type="radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                                        value="STS" name="jawaban_kenyataan[<?= $dataPernyataan->id ?>]">
                                                    <label for="jawaban-kenyataan-<?= $no ?>-1" class="ms-2 text-xs font-medium text-gray-900">Sangat Tidak Setuju</label>
                                                </div>

                                                <div class="flex items-center mb-2">
                                                    <input id="jawaban-kenyataan-<?= $no ?>-2" type="radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                                        value="TS" name="jawaban_kenyataan[<?= $dataPernyataan->id ?>]">
                                                    <label for="jawaban-kenyataan-<?= $no ?>-2" class="ms-2 text-xs font-medium text-gray-900">Tidak Setuju</label>
                                                </div>

                                                <div class="flex items-center mb-2">
                                                    <input id="jawaban-kenyataan-<?= $no ?>-3" type="radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                                        value="CS" name="jawaban_kenyataan[<?= $dataPernyataan->id ?>]">
                                                    <label for="jawaban-kenyataan-<?= $no ?>-3" class="ms-2 text-xs font-medium text-gray-900">Cukup Setuju</label>
                                                </div>

                                                <div class="flex items-center mb-2">
                                                    <input id="jawaban-kenyataan-<?= $no ?>-4" type="radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                                        value="S" name="jawaban_kenyataan[<?= $dataPernyataan->id ?>]">
                                                    <label for="jawaban-kenyataan-<?= $no ?>-4" class="ms-2 text-xs font-medium text-gray-900">Setuju</label>
                                                </div>

                                                <div class="flex items-center mb-2">
                                                    <input id="jawaban-kenyataan-<?= $no ?>-5" type="radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                                        value="SS" name="jawaban_kenyataan[<?= $dataPernyataan->id ?>]">
                                                    <label for="jawaban-kenyataan-<?= $no ?>-5" class="ms-2 text-xs font-medium text-gray-900">Sangat Setuju</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                    $no++;
                                    endwhile;
                                endforeach;
                            ?>
                        </div>
                        <div class="py-3 px-4 border-t border-gray-200 dark:border-gray-600 flex justify-between items-center">
                            <button
                                type="submit"
                                name="kirim_jawaban"
                                class="block text-white bg-blue-500 transition hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-500 font-medium rounded-md text-sm px-5 py-2.5 text-center mx-auto">
                                Kirim Jawaban
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif ?>
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