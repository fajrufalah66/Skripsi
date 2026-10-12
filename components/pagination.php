<?php
$toData = $offset + $limitPerPage;
$toData = $toData > $countAllData ? $countAllData : $toData;
?>

    <div class="flex justify-between items-center mt-4">
        <p class="text-gray-500 font-light text-sm">
            Showing <span class="font-semibold text-gray-800"><?= $offset == 0 ? 1 : $offset ?> - <?= $toData ?></span> of <span class="font-semibold text-gray-800"><?= $countAllData ?></span> rows
        </p>

        <div class="flex items-center gap-x-4">
            <a
                href="<?= $currentPage == 1 ? 'javascript:;' : (isset($getPage) ? '?p=' . ($currentPage - 1) . $getPage : '?p=' . ($currentPage - 1)) ?>"
                class="w-9 h-9 flex justify-center items-center rounded-full transition text-white 
                <?= $currentPage > 1 ? 'bg-sky-700 hover:bg-sky-800 hover:text-white' : 'bg-sky-600 opacity-75 cursor-default' ?>">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                </svg>
            </a>

            <div class="text-gray-800">
                <?= $currentPage ?>
            </div>

            <a
                href="<?= $currentPage == $totalPage ? 'javascript:;' : (isset($getPage) ? '?p=' . ($currentPage + 1) . $getPage : '?p=' . ($currentPage + 1)) ?>"
                class="w-9 h-9 flex justify-center items-center rounded-full transition text-white
                <?= $currentPage < $totalPage ? 'bg-sky-700 hover:bg-sky-800 hover:text-white' : 'bg-sky-600 opacity-75 cursor-default' ?>">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>