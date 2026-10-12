<?php
require './../config/app.php';

if(!isAuthenticated()) {
    redirect('auth/login.php');
} else if(!checkIsRole('Admin')) {
    alertMessage('error', 'Akses tidak diizinkan');
    redirect('');
}

$userId = $_SESSION['userId'];
$user = $db->query("SELECT * FROM pengguna WHERE id = '{$userId}'")->fetch_object();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = clearInput($_POST['tanggal'] ?? '');
    $nomorHp = clearInput($_POST['nomor_hp'] ?? '');
    $namaGrup = clearInput($_POST['nama_grup'] ?? '');
    $jumlah = clearInput($_POST['jumlah'] ?? '');
    $paket = clearInput($_POST['paket'] ?? '');
    $pembayaran = 'Belum Lunas';

    if (empty($tanggal) || empty($nomorHp) || empty($namaGrup) || empty($jumlah) || empty($paket) || empty($pembayaran)) {
        alertMessage('error', 'Semua kolom wajib diisi!');
        redirect('data-transaksi/add.php');
        exit;
    }

    $idTransaksi = generateTransactionId();

    try {
        $db->query("INSERT INTO transaksi (id, tanggal, nomor_hp, nama_grup, jumlah, paket, pembayaran) VALUES ('{$idTransaksi}', '{$tanggal}', '{$nomorHp}', '{$namaGrup}', '{$jumlah}', '{$paket}', '{$pembayaran}')");
        alertMessage('success', 'Berhasil menambahkan transaksi!');
        redirect('data-transaksi');
        
    } catch(Exception $e) {
        alertMessage('error', 'Terjadi sebuah kesalahan');
    }
}

$breadcrumbItems = [
    [ 
        'label' => 'Data Transaksi',
        'href' => 'data-transaksi' 
    ],
    [ 
        'label' => 'Tambah Transaksi', 
    ],
];

include './../layouts/header.php';
include './../components/breadcrumb.php';
?>
        
    <div class="relative grid w-full bg-white rounded-none md:rounded-md shadow dark:bg-gray-800">
        <div class="py-3 px-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
            <h3 class="font-semibold text-sm text-gray-900 dark:text-white">Form Tambah Transaksi</h3>
        </div>
        <form class="p-4 space-y-4 w-4/6" method="POST">

            <div class="flex gap-x-4 items-center">
                <label for="default-datepicker" class="text-gray-600 text-sm font-medium w-2/4">Tanggal</label>
                <div class="w-full relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                        </svg>
                    </div>
                    <input datepicker datepicker-format="yyyy-mm-dd" id="default-datepicker" type="text" name="tanggal" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 h-11 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date" readonly>
                </div>
            </div>

            <div class="flex gap-x-4 items-center">
                <label for="nomor_hp" class="text-gray-600 text-sm font-medium w-2/4">Nomor HP</label>
                <div class="w-full">
                    <input
                        type="text"
                        id="nomor_hp"
                        name="nomor_hp"
                        class="bg-gray-50 border-gray-300 rounded-lg h-11 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5"
                        placeholder="Masukkan Nomor HP"
                        required />
                </div>
            </div>

            <div class="flex gap-x-4 items-center">
                <label for="nama_grup" class="text-gray-600 text-sm font-medium w-2/4">Nama Group</label>
                <div class="w-full">
                    <input
                        type="text"
                        id="nama_grup"
                        name="nama_grup"
                        class="bg-gray-50 border-gray-300 rounded-lg h-11 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5"
                        placeholder="Masukkan Nama Group"
                        required />
                </div>
            </div>

            <div class="flex gap-x-4 items-center">
                <label for="jumlah" class="text-gray-600 text-sm font-medium w-2/4">Jumlah</label>
                <div class="w-full">
                    <input
                        type="number"
                        id="jumlah"
                        name="jumlah"
                        class="bg-gray-50 border-gray-300 rounded-lg h-11 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5"
                        placeholder="Masukkan Jumlah"
                        required />
                </div>
            </div>

            <div class="flex gap-x-4 items-center">
                <label for="paket" class="text-gray-600 text-sm font-medium w-2/4">Paket</label>
                <div class="w-full">
                    <select
                        id="paket"
                        name="paket"
                        class="bg-gray-50 border-gray-300 text-gray-600 rounded-lg h-11 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 px-3"
                        required>
                        <option value="" selected disabled>Pilih Paket</option>
                        <option value="Extreme">Extreme</option>
                        <option value="Regular">Regular</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="<?= baseUrl('data-transaksi') ?>"
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

<?php include './../layouts/footer.php'; ?>