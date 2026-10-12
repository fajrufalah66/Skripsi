<?php
require './../config/app.php';

if(!isAuthenticated()) {
    redirect('auth/login.php');
} else if(!checkIsRole('Ketua Pengelola')) {
    alertMessage('error', 'Akses tidak diizinkan');
    redirect('');
}

$id = $db->real_escape_string($_GET['i'] ?? '');
$db->query("UPDATE data_kuesioner SET status = 'Publish' WHERE id = '{$id}'");

alertMessage('success', 'Kuesioner Berhasil Di Publish!');
redirect('master-kuesioner');