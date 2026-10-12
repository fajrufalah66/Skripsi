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
$search = clearInput($_GET['search'] ?? '');

$_whereOR = [];

// Kondisi dengan logika OR
if (!empty($search)) {
    $_whereOR[] = "data_kuesioner.nama_kuesioner LIKE '%{$search}%'";
}

// Gabungkan kondisi OR dan AND
$whereClause = '';
if (count($_whereOR) > 0) {
    $orClause = count($_whereOR) > 0 ? '(' . implode(' OR ', $_whereOR) . ')' : '';
    $whereClause = "WHERE {$orClause}";
}

$limitPerPage = 10;
$currentPage = intval(clearInput($_GET['p'] ?? '1'));
$offset = ($currentPage > 1) ? ($currentPage * $limitPerPage) - $limitPerPage : 0;

$queryAllData = $db->query("SELECT hasil_kuesioner.id FROM hasil_kuesioner INNER JOIN data_kuesioner ON data_kuesioner.id = hasil_kuesioner.id_data_kuesioner GROUP BY nama_kuesioner {$whereClause}");
$countAllData = $queryAllData->num_rows;
$totalPage = ceil($countAllData / $limitPerPage);

$queryHasilKuesioner = $db->query("
    SELECT *
    FROM hasil_kuesioner
    INNER JOIN
    data_kuesioner
    ON
    data_kuesioner.id = hasil_kuesioner.id_data_kuesioner
    {$whereClause}
    GROUP BY nama_kuesioner
    ORDER BY hasil_kuesioner.id DESC
    LIMIT {$offset}, {$limitPerPage}
");

$breadcrumbItems = [
    [ 'label' => 'Hasil Kuesioner' ]
];

include './../layouts/header.php';
include './../components/breadcrumb.php';
?>

    <div class="relative grid w-full bg-white rounded-none md:rounded-md shadow dark:bg-gray-800">
        <div class="p-4 border-b border-gray-200 dark:border-gray-600 grid grid-cols-1 md:grid-cols-2 items-center gap-4">
            <form id="form-filter" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input
                    type="text"
                    id="search"
                    name="search"
                    placeholder="Cari"
                    value="<?= $search ?>"
                    onchange="submitFilter()"
                    class="w-full rounded-md border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary/70 focus:outline-none focus:ring-1 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary/70"
                    required />
                </div>
            </form>
        </div>
        <div class="overflow-hidden">
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Nama Kuesioner
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Aksi
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($queryHasilKuesioner->num_rows == 0) : ?>
                            <tr class="odd:bg-white even:bg-gray-50 border-b">
                                <td class="px-6 py-4 text-center" colspan="2">Tidak Ada Data</td>
                            </tr>

                        <?php 
                        else:
                            while($dataHasilKuesioner = $queryHasilKuesioner->fetch_object()) :
                        ?>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <?= $dataHasilKuesioner->nama_kuesioner ?>
                                </th>
                                <td class="px-6 py-4 flex items-center">
                                    <a
                                        class="font-medium text-green-300 hover:text-green-500 transition cursor-pointer me-1"
                                        data-tooltip-target="tooltip-detail-<?= $dataHasilKuesioner->id_data_kuesioner ?>"
                                        href="<?= baseUrl('hasil-kuesioner/detail.php?i='.$dataHasilKuesioner->id_data_kuesioner) ?>">
                                        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v5m-3 0h6M4 11h16M5 15h14a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1Z"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-detail-<?= $dataHasilKuesioner->id_data_kuesioner ?>" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                                        Detail Kuesioner
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>

                                    <a
                                        class="font-medium text-blue-300 hover:text-blue-500 transition cursor-pointer me-1"
                                        data-tooltip-target="tooltip-rekomendasi-<?= $dataHasilKuesioner->id_data_kuesioner ?>"
                                        href="<?= baseUrl('hasil-kuesioner/rekomendasi.php?i='.$dataHasilKuesioner->id_data_kuesioner) ?>">
                                        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 9h5m3 0h2M7 12h2m3 0h5M5 5h14a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1h-6.616a1 1 0 0 0-.67.257l-2.88 2.592A.5.5 0 0 1 8 18.477V17a1 1 0 0 0-1-1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-rekomendasi-<?= $dataHasilKuesioner->id_data_kuesioner ?>" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                                        Rekomendasi Perbaikan
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="px-4 pb-4">
                <?php include './../components/pagination.php'; ?>
            </div>
        </div>
    </div>

    <script>
        function submitFilter() {
            $('#form-filter').submit();
        }
    </script>

<?php include './../layouts/footer.php'; ?>