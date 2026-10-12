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
$queryKuesioner = $db->query("SELECT * FROM data_kuesioner WHERE id = '{$id}'");
if($queryKuesioner->num_rows == 0) {
    redirect('master-kuesioner');
}

$dataKuesioner = $queryKuesioner->fetch_object();

$breadcrumbItems = [
    [ 
        'label' => 'Master Kuesioner',
        'href' => 'master-kuesioner' 
    ],
    [ 
        'label' => 'Lihat Pernyataan' 
    ],
];

include './../layouts/header.php';
include './../components/breadcrumb.php';
?>
        
    <div class="relative grid w-full bg-white rounded-none md:rounded-md shadow dark:bg-gray-800">
        <div class="py-3 px-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
            <h3 class="font-semibold text-sm text-gray-900 dark:text-white">List Pernyataan <?= $dataKuesioner->nama_kuesioner ?></h3>
        </div>
        
        <div class="overflow-hidden rounded-b-md">
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <tbody>
                    <?php
                        $dimensiLayanans = json_decode($dataKuesioner->dimensi_layanan);
                        foreach ($dimensiLayanans as $dimensiLayanan) :
                    ?>
                        <tr class="text-gray-700 uppercase bg-gray-50 text-xs">
                            <th scope="col" class="px-4 py-3">
                                <div class="flex items-center">
                                    <?= $dimensiLayanan ?>
                                </div>
                            </th>
                        </tr>
                    <?php
                            $cekPernyataan = $db->query("SELECT * FROM data_pernyataan WHERE dimensi_layanan = '$dimensiLayanan' AND status = 'Aktif'");
                            while ($dataPernyataan = $cekPernyataan->fetch_object()) :
                    ?>
                        <tr class="bg-white border-b">
                            <td class="px-4 py-3">
                                <?= $dataPernyataan->pernyataan ?>
                            </td>
                        </tr>
                    <?php
                            endwhile;
                        endforeach;
                    ?>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include './../layouts/footer.php'; ?>