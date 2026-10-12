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
$dimensiLayanan = clearInput($_GET['dimensi_layanan'] ?? '');
$status = clearInput($_GET['status'] ?? '');

$_whereOR = [];
$_whereAND = [];

// Kondisi dengan logika OR
if (!empty($search)) {
    $_whereOR[] = "pernyataan LIKE '%{$search}%'";
}

// Kondisi dengan logika AND
if (!empty($dimensiLayanan)) {
    $_whereAND[] = "dimensi_layanan = '{$dimensiLayanan}'";
}
if (!empty($status)) {
    $_whereAND[] = "status = '{$status}'";
}

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

$queryAllData = $db->query("SELECT id FROM data_pernyataan {$whereClause}");
$countAllData = $queryAllData->num_rows;
$totalPage = ceil($countAllData / $limitPerPage);

$queryPernyataan = $db->query("
    SELECT *
    FROM data_pernyataan
    {$whereClause}
    ORDER BY id DESC
    LIMIT {$offset}, {$limitPerPage}
");


$breadcrumbItems = [
    [ 'label' => 'Master Pernyataan' ]
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
                    placeholder="Cari Pernyataan"
                    value="<?= $search ?>"
                    onchange="submitFilter()"
                    class="w-full rounded-md border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary/70 focus:outline-none focus:ring-1 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary/70"/>
                </div>
                <div>
                    <div class="relative">
                        <select
                            for="select"
                            id="dimensi_layanan"
                            name="dimensi_layanan"
                            onchange="submitFilter()"
                            class="appearance-none w-full rounded-md border border-gray-300 bg-gray-50 p-2.5 pr-10 text-sm text-gray-900 focus:border-primary/70 focus:outline-none focus:ring-1 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary/70">
                            <option value="" selected disabled>Pilih Dimensi Layanan</option>
                            <option value="" <?= selectedOption('', $dimensiLayanan) ?>>Semua</option>
                            <option value="Tangibels" <?= selectedOption('Tangibels', $dimensiLayanan) ?>>Tangibels</option>
                            <option value="Realibility" <?= selectedOption('Realibility', $dimensiLayanan) ?>>Realibility</option>
                            <option value="Empathy" <?= selectedOption('Empathy', $dimensiLayanan) ?>>Empathy</option>
                            <option value="Assurance" <?= selectedOption('Assurance', $dimensiLayanan) ?>>Assurance</option>
                            <option value="Responsiveness" <?= selectedOption('Responsiveness', $dimensiLayanan) ?>>Responsiveness</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <ion-icon name="chevron-down-outline" class="w-4 h-4 text-black dark:text-white"></ion-icon>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="relative">
                        <select
                            for="select"
                            id="status"
                            name="status"
                            onchange="submitFilter()"
                            class="appearance-none w-full rounded-md border border-gray-300 bg-gray-50 p-2.5 pr-10 text-sm text-gray-900 focus:border-primary/70 focus:outline-none focus:ring-1 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary/70">
                            <option value="" selected disabled>Pilih Status</option>
                            <option value="" <?= selectedOption('', $status) ?>>Semua</option>
                            <option value="Aktif" <?= selectedOption('Aktif', $status) ?>>Aktif</option>
                            <option value="Tidak Aktif" <?= selectedOption('Tidak Aktif', $status) ?>>Tidak Aktif</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <ion-icon name="chevron-down-outline" class="w-4 h-4 text-black dark:text-white"></ion-icon>
                        </div>
                    </div>
                </div>
            </form>
            <div class="justify-self-end">
                <a href="<?= baseUrl('master-pernyataan/add.php') ?>" class="text-white bg-sky-700 hover:bg-sky-800 focus:ring-4 focus:outline-none focus:ring-sky-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:ring-sky-800">Tambah Data</a>
            </div>
        </div>
        <div class="overflow-hidden">
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Dimensi Layanan
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Pernyataan
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Status
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($queryPernyataan->num_rows == 0) : ?>
                            <tr class="odd:bg-white even:bg-gray-50 border-b">
                                <td class="px-6 py-4 text-center" colspan="4">Tidak Ada Data</td>
                            </tr>

                        <?php 
                        else:
                            while($dataPernyataan = $queryPernyataan->fetch_object()) :
                        ?>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <?= $dataPernyataan->dimensi_layanan ?>
                                </th>
                                <td class="px-6 py-4">
                                    <?= $dataPernyataan->pernyataan ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $dataPernyataan->status ?>
                                </td>
                                <td class="px-6 py-4 flex items-center">
                                    <a
                                        class="font-medium text-amber-300 hover:text-amber-500 transition cursor-pointer"
                                        data-tooltip-target="tooltip-edit-<?= $dataPernyataan->id ?>"
                                        href="<?= baseUrl('master-pernyataan/edit.php?i=' . $dataPernyataan->id) ?>">
                                        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z" clip-rule="evenodd"/>
                                            <path fill-rule="evenodd" d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-edit-<?= $dataPernyataan->id ?>" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                        Edit
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