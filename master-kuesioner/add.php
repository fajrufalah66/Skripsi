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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaKuesioner = clearInput($_POST['nama_kuesioner'] ?? '');
    $dimensiLayanan = $_POST['dimensi_layanan'] ?? [];

    if (empty($namaKuesioner)) {
        alertMessage('error', 'Nama kuesioner tidak boleh kosong!');
        redirect('master-kuesioner/add.php');
        exit;
    }

    if (empty($dimensiLayanan)) {
        alertMessage('error', 'Dimensi layanan harus dipilih!');
        redirect('master-kuesioner/add.php');
        exit;
    }

    $cekKuesioner = $db->query("SELECT id FROM data_kuesioner WHERE status IN ('Draft', 'Publish')");
    if($cekKuesioner->num_rows == 1) {
        alertMessage('error', 'Tidak dapat menambahkan kuesioner baru dikarenakan masih ada kuesioner dengan status Draft atau Publish!');
        redirect('master-kuesioner');
        exit;
    }

    $dimensiLayananJson = json_encode($dimensiLayanan);

    try {
        $db->query("INSERT INTO data_kuesioner (nama_kuesioner, dimensi_layanan) VALUES ('{$namaKuesioner}', '{$dimensiLayananJson}')");
        alertMessage('success', 'Berhasil menambahkan kuesioner!');
        redirect('master-kuesioner');
        
    } catch(Exception $e) {
        alertMessage('error', 'Terjadi sebuah kesalahan');
    }
}

$breadcrumbItems = [
    [ 
        'label' => 'Master Kuesioner',
        'href' => 'master-kuesioner' 
    ],
    [ 
        'label' => 'Tambah Kuesioner' 
    ],
];

include './../layouts/header.php';
include './../components/breadcrumb.php';
?>
        
    <div class="relative grid w-full bg-white rounded-none md:rounded-md shadow dark:bg-gray-800">
        <div class="py-3 px-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
            <h3 class="font-semibold text-sm text-gray-900 dark:text-white">Form Tambah Kuesioner</h3>
        </div>
        <form method="POST" class="p-4 space-y-4 w-4/6">

            <div class="flex gap-x-4 items-center">
                <label for="nama_kuesioner" class="text-gray-600 text-sm font-medium w-2/6">Nama Kuesioner</label>
                <div class="w-full">
                    <input
                        type="text"
                        id="nama_kuesioner"
                        name="nama_kuesioner"
                        class="bg-gray-50 border-gray-300 rounded-lg h-11 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5"
                        placeholder="Masukkan Nama Kuesioner"
                        required />
                </div>
            </div>

            <div class="flex gap-x-4 items-center">
                <label class="text-gray-600 text-sm font-medium w-2/6">Dimensi Layanan</label>
                <div class="flex flex-wrap gap-4 w-full">
                    <?php foreach (['Tangibels', 'Realibility', 'Empathy', 'Assurance', 'Responsiveness'] as $key => $dimensi): ?>
                    <div class="flex items-center">
                        <input id="checkbox-<?= $key ?>" name="dimensi_layanan[]" type="checkbox" value="<?= $dimensi ?>"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="checkbox-<?= $key ?>" class="ml-2 text-sm font-medium text-gray-900"><?= $dimensi ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <a href="<?= baseUrl('master-kuesioner') ?>"
                    class="block text-white bg-red-500 transition hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-md text-sm px-5 py-2.5 text-center me-3">
                    Batal
                </a>
                <button type="submit"
                    class="block text-white bg-green-500 transition hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-md text-sm px-5 py-2.5 text-center">
                    Simpan
                </button>
            </div>
        </form>

    </div>

<?php include './../layouts/footer.php'; ?>