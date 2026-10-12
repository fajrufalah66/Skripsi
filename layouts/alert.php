<?php if(isset($_SESSION['alert'])) : ?>
    <script>
        const type = `<?= $_SESSION['alert']['icon'] ?>`;
        const message = `<?= $_SESSION['alert']['message'] ?>`;

        Swal.fire({
            title: type === 'success' ? 'Berhasil!' : 'Gagal',
            text: message,
            icon: type,
        });
    </script>
<?php 
unset($_SESSION['alert']);
endif; 
?>