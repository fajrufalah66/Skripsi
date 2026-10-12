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

if (!empty($search)) {
    $_whereOR[] = "nama_kuesioner LIKE '%{$search}%'";
    $_whereOR[] = "dimensi_layanan LIKE '%{$search}%'";
    $_whereOR[] = "status LIKE '%{$search}%'";
}

$whereClause = '';
if (count($_whereOR) > 0) {
    $orClause = count($_whereOR) > 0 ? '(' . implode(' OR ', $_whereOR) . ')' : '';

    if (!empty($orClause)) {
        $whereClause = "WHERE {$orClause}";
    }
}

$limitPerPage = 10;
$currentPage = intval(clearInput($_GET['p'] ?? '1'));
$offset = ($currentPage > 1) ? ($currentPage * $limitPerPage) - $limitPerPage : 0;

$queryAllData = $db->query("SELECT id FROM data_kuesioner {$whereClause}");
$countAllData = $queryAllData->num_rows;
$totalPage = ceil($countAllData / $limitPerPage);

$queryKuesioner = $db->query("
    SELECT *
    FROM data_kuesioner
    {$whereClause}
    ORDER BY created_at DESC
    LIMIT {$offset}, {$limitPerPage}
");

$breadcrumbItems = [
    [ 'label' => 'Master Kuesioner' ]
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
                    placeholder="Search"
                    value="<?= $search ?>"
                    onchange="submitFilter()"
                    class="w-full rounded-md border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary/70 focus:outline-none focus:ring-1 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary/70"/>
                </div>
            </form>
            <div class="justify-self-end">
                <a href="<?= baseUrl('master-kuesioner/add.php') ?>" class="text-white bg-sky-700 hover:bg-sky-800 focus:ring-4 focus:outline-none focus:ring-sky-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:ring-sky-800">Tambah Data</a>
            </div>
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
                                    Dimensi Layanan
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Status
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
                        <?php if($queryKuesioner->num_rows == 0) : ?>
                            <tr class="odd:bg-white even:bg-gray-50 border-b">
                                <td class="px-6 py-4 text-center" colspan="4">Tidak Ada Data</td>
                            </tr>

                        <?php 
                        else:
                            while($dataKuesioner = $queryKuesioner->fetch_object()) :
                        ?>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <?= $dataKuesioner->nama_kuesioner ?>
                                </th>
                                <td class="px-6 py-4">
                                    <?= $dataKuesioner->dimensi_layanan ? implode(', ', json_decode($dataKuesioner->dimensi_layanan)) : 'Tidak ada dimensi layanan' ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $dataKuesioner->status ?>
                                </td>
                                <td class="px-6 py-4 flex items-center">
                                    <a
                                        class="font-medium text-gray-500 hover:text-gray-700 transition cursor-pointer mr-2"
                                        data-tooltip-target="tooltip-pernyataan-<?= $dataKuesioner->id ?>"
                                        href="<?= baseUrl('master-kuesioner/lihat-pernyataan.php?i=' . $dataKuesioner->id) ?>">
                                        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>
                                            <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-pernyataan-<?= $dataKuesioner->id ?>" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                        Lihat Pernyataan
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                    <?php if($dataKuesioner->status == 'Draft') : ?>
                                    <a
                                        class="font-medium text-blue-300 hover:text-blue-500 transition cursor-pointer"
                                        data-tooltip-target="tooltip-publish-<?= $dataKuesioner->id ?>"
                                        href="javascript:;"
                                        onclick="publishKuesioner('<?= baseUrl('master-kuesioner/publish.php?i=' . $dataKuesioner->id) ?>')">
                                        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v9m-5 0H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2M8 9l4-5 4 5m1 8h.01"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-publish-<?= $dataKuesioner->id ?>" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                        Publish Kuesioner
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                    <?php elseif($dataKuesioner->status == 'Publish') : ?>
                                    <a
                                        class="font-medium text-green-300 hover:text-green-500 transition cursor-pointer"
                                        data-tooltip-target="tooltip-selesaikan-<?= $dataKuesioner->id ?>"
                                        href="javascript:;"
                                        onclick="selesaikanKuesioner('<?= baseUrl('master-kuesioner/selesaikan.php?i=' . $dataKuesioner->id) ?>')">
                                        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-selesaikan-<?= $dataKuesioner->id ?>" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                        Selesaikan Kuesioner
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                    <?php else : ?>
                                        <a
                                            class="font-medium text-amber-300 hover:text-amber-500 transition cursor-pointer"
                                            data-tooltip-target="tooltip-selesai-<?= $dataKuesioner->id ?>"
                                            href="javascript:;">
                                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                                            </svg>
                                        </a>
                                        <div id="tooltip-selesai-<?= $dataKuesioner->id ?>" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                            Kuesioner Sudah Selesai
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    <?php endif ?>

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
    

<?php include './../layouts/footer.php'; ?>

<script>
    function publishKuesioner(url) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: 'Apakah anda ingin publish kuesioner berikut?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "Ya, publish!"

        }).then( ({ isConfirmed }) => {
            if(isConfirmed) {
                location.href = url;
            }
        });
    }

    function selesaikanKuesioner(url) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: 'Apakah anda ingin menyelesaikan kuesioner berikut?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "Ya, selesaikan!"

        }).then( ({ isConfirmed }) => {
            if(isConfirmed) {
                location.href = url;
            }
        });
    }
</script>