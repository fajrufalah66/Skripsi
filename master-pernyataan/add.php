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
    $dimensiLayanan = clearInput($_POST['dimensi_layanan'] ?? '');
    $pernyataan = clearInput($_POST['pernyataan'] ?? '');
    $rekomendasiPerbaikan = clearInput($_POST['rekomendasi_perbaikan'] ?? '');
    $status = clearInput($_POST['status'] ?? '');

    if (empty($dimensiLayanan) || empty($pernyataan) || empty($rekomendasiPerbaikan) || empty($status)) {
        alertMessage('error', 'Semua kolom wajib diisi!');
        redirect('master-pernyataan/add.php');
        exit;
    }

    try {
        $db->query("INSERT INTO data_pernyataan (dimensi_layanan, pernyataan, rekomendasi_perbaikan, status) VALUES ('{$dimensiLayanan}', '{$pernyataan}', '{$rekomendasiPerbaikan}', '{$status}')");
        alertMessage('success', 'Berhasil menambahkan pernyataan!');
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
        'label' => 'Tambah Pernyataan' 
    ],
];

include './../layouts/header.php';
include './../components/breadcrumb.php';
?>
        
    <div class="relative grid w-full bg-white rounded-none md:rounded-md shadow dark:bg-gray-800">
        <div class="py-3 px-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
            <h3 class="font-semibold text-sm text-gray-900 dark:text-white">Form Tambah Pernyataan</h3>
        </div>
        <form class="p-4 space-y-4 w-4/6" method="POST">

            <div class="flex gap-x-4 items-center">
                <label for="dimensi_layanan" class="text-gray-600 text-sm font-medium w-2/4">Dimensi Layanan</label>
                <div class="w-full">
                    <select
                        id="dimensi_layanan"
                        name="dimensi_layanan"
                        class="bg-gray-50 border-gray-300 text-gray-600 rounded-lg h-11 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 px-3"
                        required>
                        <option value="" selected disabled>Pilih Dimensi Layanan</option>
                        <option value="Tangibels">Tangibels</option>
                        <option value="Realibility">Realibility</option>
                        <option value="Empathy">Empathy</option>
                        <option value="Assurance">Assurance</option>
                        <option value="Responsiveness">Responsiveness</option>
                    </select>
                </div>
            </div>

            <div id="deskripsi_dimensi_layanan" class="hidden gap-x-4 items-center">
                <label for="pernyataan" class="text-gray-600 text-sm font-medium w-2/4">Deskripsi</label>
                <div class="w-full">
                    <p class="text-xs" id="deskripsi"></p>
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
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
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

<script>
    $('#dimensi_layanan').change(function () {
        const dimensiLayanan = $(this).val();
        const deskripsiDimensiLayanan = $('#deskripsi_dimensi_layanan');

        const deskripsiMapping = {
            'Tangibels': 'Tangibels merupakan salah satu dimensi dalam metode SERVQUAL yang mengacu pada aspek fisik dan materi yang dapat diamati secara langsung oleh pelanggan. Tangibel mencakup fasilitas fisik, perlengkapan, penampilan karyawan, dan media komunikasi yang digunakan oleh suatu organisasi dalam memberikan layanan kepada pengunjung.',
            'Realibility': 'Reliability dalam konteks kualitas layanan mengacu pada kemampuan penyedia layanan untuk memberikan layanan yang andal, konsisten, dan sesuai dengan apa yang telah dijanjikan kepada pengunjung.',
            'Responsiveness': 'Responsiveness dalam konteks kualitas layanan mengacu pada kesediaan dan kemampuan penyedia layanan untuk membantu pengunjung, memberikan layanan secara cepat, serta menangani permintaan atau masalah mereka dengan tanggap.',
            'Assurance': 'Assurance dalam konteks kualitas layanan mengacu pada kemampuan penyedia layanan untuk menanamkan rasa percaya dan keyakinan kepada pengunjung melalui kompetensi, keramahan, serta sikap profesional dari guide.',
            'Empathy': 'Empathy dalam konteks kualitas layanan mengacu pada kemampuan penyedia layanan untuk memahami dan peduli terhadap kebutuhan, harapan, serta perasaan pengunjung.'
        };

        const deskripsiDimensi = deskripsiMapping[dimensiLayanan] || '';

        deskripsiDimensiLayanan
            .removeClass('hidden')
            .addClass('flex');

        $('#deskripsi').html(deskripsiDimensi);
    });

</script>