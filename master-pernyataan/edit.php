<?php
require './../config/app.php';

if(!isAuthenticated()) {
    redirect('auth/login.php');
} else if(!checkIsRole('Ketua Pengelola')) {
    alertMessage('error', 'Akses tidak diizinkan');
    redirect('');
}

$userId = $_SESSION['userId'];
$user = $db->query("SELECT * FROM pengguna WHERE id = '{$userId}'")->fetch_object();

$id = clearInput($_GET['i'] ?? '');
$queryPernyataan = $db->query("SELECT * FROM data_pernyataan WHERE id = '{$id}'");
if($queryPernyataan->num_rows == 0) {
    redirect('master-pernyataan');
}

$dataPernyataan = $queryPernyataan->fetch_object();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dimensiLayanan = clearInput($_POST['dimensi_layanan'] ?? '');
    $pernyataan = clearInput($_POST['pernyataan'] ?? '');
    $rekomendasiPerbaikan = clearInput($_POST['rekomendasi_perbaikan'] ?? '');
    $status = clearInput($_POST['status'] ?? '');

    if (empty($id) || empty($dimensiLayanan) || empty($pernyataan) || empty($rekomendasiPerbaikan) || empty($status)) {
        alertMessage('error', 'Semua kolom wajib diisi!');
        redirect('master-pernyataan/edit.php?i='.$id);
        exit;
    }

    try {
        $db->query("UPDATE data_pernyataan SET dimensi_layanan = '{$dimensiLayanan}', pernyataan = '{$pernyataan}', rekomendasi_perbaikan = '{$rekomendasiPerbaikan}', status = '{$status}' WHERE id = '{$id}'");
        alertMessage('success', 'Perubahan berhasil disimpan!');
        redirect('master-pernyataan');
    } catch(Exception $e) {
        alertMessage('error', 'Terjadi sebuah kesalahan');
    }
}

$breadcrumbItems = [
    [ 
        'label' => 'Master Pernyataan',
        'href' => 'master-pernyataan' 
    ],
    [ 
        'label' => 'Edit Pernyataan' 
    ],
];

include './../layouts/header.php';
include './../components/breadcrumb.php';
?>
        
    <div class="relative grid w-full bg-white rounded-none md:rounded-md shadow dark:bg-gray-800">
        <div class="py-3 px-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
            <h3 class="font-semibold text-sm text-gray-900 dark:text-white">Form Edit Pernyataan</h3>
        </div>
        <form method="POST" class="p-4 space-y-4 w-4/6">

            <div class="flex gap-x-4 items-center">
                <label for="dimensi_layanan" class="text-gray-600 text-sm font-medium w-2/4">Dimensi Layanan</label>
                <div class="w-full">
                    <select
                        id="dimensi_layanan"
                        name="dimensi_layanan"
                        class="bg-gray-50 border-gray-300 text-gray-600 rounded-lg h-11 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 px-3"
                        required>
                        <option value="" selected disabled>Pilih Dimensi Layanan</option>
                        <option value="Tangibels" <?= selectedOption('Tangibels', $dataPernyataan->dimensi_layanan) ?>>Tangibels</option>
                        <option value="Realibility" <?= selectedOption('Realibility', $dataPernyataan->dimensi_layanan) ?>>Realibility</option>
                        <option value="Empathy" <?= selectedOption('Empathy', $dataPernyataan->dimensi_layanan) ?>>Empathy</option>
                        <option value="Assurance" <?= selectedOption('Assurance', $dataPernyataan->dimensi_layanan) ?>>Assurance</option>
                        <option value="Responsiveness" <?= selectedOption('Responsiveness', $dataPernyataan->dimensi_layanan) ?>>Responsiveness</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-x-4 items-center">
                <label for="pernyataan" class="text-gray-600 text-sm font-medium w-2/4">Pernyataan</label>
                <div class="w-full">
                    <input
                        type="text"
                        id="pernyataan"
                        name="pernyataan"
                        class="bg-gray-50 border-gray-300 rounded-lg h-11 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5"
                        placeholder="Masukkan Pernyataan"
                        value="<?= $dataPernyataan->pernyataan ?>"
                        required />
                </div>
            </div>

            <div class="flex gap-x-4 items-center">
                <label for="rekomendasi_perbaikan" class="text-gray-600 text-sm font-medium w-2/4">Rekomendasi Perbaikan</label>
                <div class="w-full">
                    <input
                        type="text"
                        id="rekomendasi_perbaikan"
                        name="rekomendasi_perbaikan"
                        class="bg-gray-50 border-gray-300 rounded-lg h-11 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5"
                        placeholder="Masukkan Rekomendasi Perbaikan"
                        value="<?= $dataPernyataan->rekomendasi_perbaikan ?>"
                        required />
                </div>
            </div>

            <div class="flex gap-x-4 items-center">
                <label for="status" class="text-gray-600 text-sm font-medium w-2/4">Status</label>
                <div class="w-full">
                    <select
                        id="status"
                        name="status"
                        class="bg-gray-50 border-gray-300 text-gray-600 rounded-lg h-11 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 px-3"
                        required>
                        <option value="" selected disabled>Pilih Status</option>
                        <option value="Aktif" <?= selectedOption('Aktif', $dataPernyataan->status) ?>>Aktif</option>
                        <option value="Tidak Aktif" <?= selectedOption('Tidak Aktif', $dataPernyataan->status) ?>>Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="<?= baseUrl('master-pernyataan') ?>"
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