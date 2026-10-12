    <nav class="flex px-5 py-4 text-gray-700 border border-gray-200 rounded-md shadow bg-white mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="<?= baseUrl('') ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    Beranda
                </a>
            </li>

            <?php foreach($breadcrumbItems as $item) : ?>
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>

                        <?php if(isset($item['href'])) : ?>
                            <a class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2" href="<?= baseUrl($item['href']) ?>"><?= $item['label'] ?></a>

                        <?php else : ?>
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2"><?= $item['label'] ?></span>                            
                        <?php endif; ?>

                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>