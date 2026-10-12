<?php
require './../config/app.php';

if(!isAuthenticated()) {
    redirect('auth/login.php');
} else if(!checkIsRole('Admin')) {
    alertMessage('error', 'Akses tidak diizinkan');
    redirect('');
}

$id = $db->real_escape_string($_GET['i'] ?? '');
$db->query("DELETE FROM transaksi WHERE id = '{$id}'");

alertMessage('success', 'Data transaksi berhasil dihapus!');
redirect('data-transaksi');