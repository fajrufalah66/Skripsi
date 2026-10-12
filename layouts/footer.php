    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js" integrity="sha512-u3fPA7V8qQmhBPNT5quvaXVa1mnnLSXUep5PS1qo5NRzHwG19aHmNJnj1Q8hpA/nBWZtZD4r4AX6YOt5ynLN2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
    <script src="<?= baseUrl('assets/app.js') ?>"></script>

    <?php include 'alert.php'; ?>

    <script>
        function confirmDelete(url) {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: 'Anda yakin ingin menghapus data berikut?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "Ya, hapus!"

            }).then( ({ isConfirmed }) => {
                if(isConfirmed) {
                    location.href = url;
                }
            });
        }
    </script>
</body>
</html>