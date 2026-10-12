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
$search = clearInput($_GET['search'] ?? '');

$_whereOR = [];

if (!empty($search)) {
    $_whereOR[] = "id LIKE '%{$search}%'";
    $_whereOR[] = "tanggal LIKE '%{$search}%'";
    $_whereOR[] = "nomor_hp LIKE '%{$search}%'";
    $_whereOR[] = "nama_grup LIKE '%{$search}%'";
    $_whereOR[] = "jumlah LIKE '%{$search}%'";
    $_whereOR[] = "paket LIKE '%{$search}%'";
    $_whereOR[] = "pembayaran LIKE '%{$search}%'";
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

$queryAllData = $db->query("SELECT id FROM transaksi {$whereClause}");
$countAllData = $queryAllData->num_rows;
$totalPage = ceil($countAllData / $limitPerPage);

$queryTransaksi = $db->query("
    SELECT *
    FROM transaksi
    {$whereClause}
    ORDER BY created_at DESC
    LIMIT {$offset}, {$limitPerPage}
");

$breadcrumbItems = [
    [ 'label' => 'Data Transaksi' ]
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
                <a href="<?= baseUrl('data-transaksi/add.php') ?>" class="text-white bg-sky-700 hover:bg-sky-800 focus:ring-4 focus:outline-none focus:ring-sky-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:ring-sky-800">Tambah Data</a>
            </div>
        </div>
        <div class="overflow-hidden">
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    ID Transaksi
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Tanggal
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Nomor HP
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Nama Grup
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Jumlah
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Paket
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Pembayaran
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
                        <?php if($queryTransaksi->num_rows == 0) : ?>
                            <tr class="odd:bg-white even:bg-gray-50 border-b">
                                <td class="px-6 py-4 text-center" colspan="8">Tidak Ada Data</td>
                            </tr>

                        <?php 
                        else:
                            while($dataTransaksi = $queryTransaksi->fetch_object()) :
                        ?>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <?= $dataTransaksi->id ?>
                                </th>
                                <td class="px-6 py-4">
                                    <?= $dataTransaksi->tanggal ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $dataTransaksi->nomor_hp ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $dataTransaksi->nama_grup ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $dataTransaksi->jumlah ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $dataTransaksi->paket ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $dataTransaksi->pembayaran ?>
                                </td>
                                <td class="px-6 py-4 flex items-center">
                                    <a
                                        class="font-medium text-amber-300 hover:text-amber-500 transition cursor-pointer"
                                        data-tooltip-target="tooltip-edit-<?= $dataTransaksi->id ?>"
                                        href="<?= baseUrl('data-transaksi/edit.php?i=' . $dataTransaksi->id) ?>">
                                        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z" clip-rule="evenodd"/>
                                            <path fill-rule="evenodd" d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-edit-<?= $dataTransaksi->id ?>" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                        Edit
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>

                                    <?php if($dataTransaksi->pembayaran == 'Belum Lunas') : ?>

                                    <a
                                        class="font-medium text-green-300 hover:text-green-500 transition cursor-pointer"
                                        data-tooltip-target="tooltip-selesai-<?= $dataTransaksi->id ?>"
                                        href="javascript:;"
                                        onclick="confirmUpdatePembayaran('<?= baseUrl('data-transaksi/ubah-status-pembayaran.php?i=' . $dataTransaksi->id) ?>')">
                                        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-selesai-<?= $dataTransaksi->id ?>" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                        Terima Pembayaran
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>

                                    <a
                                        class="font-medium text-rose-300 hover:text-rose-500 transition cursor-pointer"
                                        data-tooltip-target="tooltip-delete-1"
                                        href="javascript:;"
                                        onclick="confirmDelete('<?= baseUrl('data-transaksi/delete.php?i=' . $dataTransaksi->id) ?>')">
                                        <svg class="w-6 h-6 text-red-300 hover:text-red-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-delete-1" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                        Hapus
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
    function confirmUpdatePembayaran(url) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: 'Apakah anda ingin mengubah status pembayaran menjadi Lunas?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "Ya!"
        }).then(({ isConfirmed }) => {
            if (isConfirmed) {
                Swal.fire({
                    title: 'Sedang memproses...',
                    text: 'Harap tunggu sebentar.',
                    icon: 'info',
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                setTimeout(() => {
                    location.href = url;
                }, 2000);
            }
        });
    }

</script>