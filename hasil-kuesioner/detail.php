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
$getPage = "&i=".$id;
$queryDataKuesioner = $db->query("SELECT * FROM hasil_kuesioner WHERE id_data_kuesioner = '{$id}'");
if($queryDataKuesioner->num_rows == 0) {
    redirect('hasil-kuesioner');
}
$search = clearInput($_GET['search'] ?? '');

$_whereOR = [];
$_whereAND = [];

// Kondisi dengan logika OR
if (!empty($search)) {
    $_whereOR[] = "pengunjung.nama LIKE '%{$search}%'";
    $_whereOR[] = "transaksi.nama_grup LIKE '%{$search}%'";
}

$_whereAND[] = "hasil_kuesioner.id_data_kuesioner = '{$id}'";

// Gabungkan kondisi OR dan AND
$whereClause = '';
if (count($_whereOR) > 0 || count($_whereAND) > 0) {
    $orClause = count($_whereOR) > 0 ? '(' . implode(' OR ', $_whereOR) . ')' : '';
    $andClause = count($_whereAND) > 0 ? implode(' AND ', $_whereAND) : '';

    if (!empty($orClause) && !empty($andClause)) {
        $whereClause = "WHERE {$orClause} AND {$andClause}";
    } elseif (!empty($orClause)) {
        $whereClause = "WHERE {$orClause}";
    } elseif (!empty($andClause)) {
        $whereClause = "WHERE {$andClause}";
    }
}

$limitPerPage = 10;
$currentPage = intval(clearInput($_GET['p'] ?? '1'));
$offset = ($currentPage > 1) ? ($currentPage * $limitPerPage) - $limitPerPage : 0;

$queryAllData = $db->query("SELECT hasil_kuesioner.id FROM hasil_kuesioner INNER JOIN pengunjung ON hasil_kuesioner.id_pengunjung = pengunjung.id INNER JOIN transaksi ON transaksi.id = pengunjung.id_transaksi {$whereClause}");
$countAllData = $queryAllData->num_rows;
$totalPage = ceil($countAllData / $limitPerPage);

$queryHasilKuesioner = $db->query("
    SELECT
    hasil_kuesioner.id as id,
    pengunjung.nama as nama,
    transaksi.nama_grup as nama_grup
    FROM hasil_kuesioner
    INNER JOIN pengunjung
    ON hasil_kuesioner.id_pengunjung = pengunjung.id
    INNER JOIN transaksi
    ON transaksi.id = pengunjung.id_transaksi
    {$whereClause}
    ORDER BY hasil_kuesioner.id DESC
    LIMIT {$offset}, {$limitPerPage}
");

$breadcrumbItems = [
    [ 
        'label' => 'Hasil Kuesioner',
        'href' => 'hasil-kuesioner' 
    ],
    [ 
        'label' => 'Detail Kuesioner', 
    ],
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
                                    Nama Pengunjung
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Nama Grup
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
                                <td class="px-6 py-4 text-center" colspan="3">Tidak Ada Data</td>
                            </tr>

                        <?php 
                        else:
                            while($dataHasilKuesioner = $queryHasilKuesioner->fetch_object()) :
                        ?>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <?= $dataHasilKuesioner->nama ?>
                                </th>
                                <td class="px-6 py-4">
                                    <?= $dataHasilKuesioner->nama_grup ?>
                                </td>
                                <td class="px-6 py-4 flex items-center">
                                    <a
                                        class="font-medium text-blue-300 hover:text-blue-500 transition cursor-pointer me-1"
                                        data-tooltip-target="tooltip-detail-<?= $dataHasilKuesioner->id ?>"
                                        href="<?= baseUrl('hasil-kuesioner/jawaban.php?i='.$dataHasilKuesioner->id) ?>">
                                        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>
                                            <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-detail-<?= $dataHasilKuesioner->id ?>" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                                        Detail Jawaban
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