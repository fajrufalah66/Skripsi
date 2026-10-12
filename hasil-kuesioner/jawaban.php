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
$queryDataKuesioner = $db->query("SELECT * FROM jawaban_kuesioner WHERE id_hasil_kuesioner = '{$id}'");
if($queryDataKuesioner->num_rows == 0) {
    redirect('hasil-kuesioner');
}
$search = clearInput($_GET['search'] ?? '');

$_whereOR = [];
$_whereAND = [];

// Kondisi dengan logika OR
if (!empty($search)) {
    $_whereOR[] = "data_pernyataan.pernyataan LIKE '%{$search}%'";
}

$_whereAND[] = "jawaban_kuesioner.id_hasil_kuesioner = '{$id}'";

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

$queryAllData = $db->query("SELECT jawaban_kuesioner.id FROM jawaban_kuesioner INNER JOIN data_pernyataan ON jawaban_kuesioner.id_data_pernyataan = data_pernyataan.id {$whereClause}");
$countAllData = $queryAllData->num_rows;
$totalPage = ceil($countAllData / $limitPerPage);

$queryHasilKuesioner = $db->query("
    SELECT
    data_pernyataan.pernyataan as nama_pernyataan,
    jawaban_kuesioner.jawaban_kenyataan as jawaban_kenyataan,
    jawaban_kuesioner.jawaban_harapan as jawaban_harapan
    FROM jawaban_kuesioner
    INNER JOIN data_pernyataan
    ON jawaban_kuesioner.id_data_pernyataan = data_pernyataan.id
    {$whereClause}
    ORDER BY jawaban_kuesioner.id DESC
    LIMIT {$offset}, {$limitPerPage}
");

$breadcrumbItems = [
    [ 
        'label' => 'Hasil Kuesioner',
        'href' => 'hasil-kuesioner' 
    ],
    [ 
        'label' => 'Detail Kuesioner',
        'href' => 'hasil-kuesioner/detail.php?i='.$id 
    ],
    [ 
        'label' => 'Detail Jawaban', 
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
                                    Pernyataan
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Jawaban Kenyataan
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Jawaban Harapan
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
                                    <?= $dataHasilKuesioner->nama_pernyataan ?>
                                </th>
                                <td class="px-6 py-4">
                                    <?= $dataHasilKuesioner->jawaban_kenyataan ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $dataHasilKuesioner->jawaban_harapan ?>
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