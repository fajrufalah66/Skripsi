<?php
require './../config/app.php';

if (!isAuthenticated()) {
    redirect('auth/login.php');
}

if (!checkIsRole('Admin')) {
    alertMessage('error', 'Akses tidak diizinkan');
    redirect('');
}

$id = $db->real_escape_string($_GET['i'] ?? '');
$dataTransaksi = $db->query("SELECT * FROM transaksi WHERE id = '{$id}'")->fetch_object();

if ($dataTransaksi) {
    $db->query("UPDATE transaksi SET pembayaran = 'Lunas' WHERE id = '{$id}'");

    $nomor_hp = preg_replace('/^0/', '62', $dataTransaksi->nomor_hp);

    $params = [
        'destination' => $nomor_hp,
        'message' => "🎉 Halo Sobat Petualang! 🎉

Terima kasih sudah menikmati pengalaman seru di Cikadongdong River Tubing! 🏞🌊 Kami ingin mendengar pendapat dan saran Anda agar kami bisa terus meningkatkan layanan kami.

📝 Mohon luangkan sedikit waktu untuk mengisi kuesioner singkat ini. Tidak akan memakan waktu lama, tetapi masukan Anda sangat berharga bagi kami!

🔖 ID Transaksi: *{$id}*

🔗 Klik di sini untuk mengisi kuesioner: " . baseUrl('pengisian-kuesioner') . "

📌 Catatan: Harap masukkan ID Transaksi Anda pada kuesioner sebagai validasi. Tanpa ID Transaksi, jawaban tidak dapat kami proses.

📢 Mohon bantuannya juga untuk membagikan kuesioner ini ke teman-teman di grup Anda yang ikut berpetualang bersama di Cikadongdong River Tubing! Semakin banyak masukan, semakin baik kami bisa meningkatkan pengalaman untuk Anda semua. 🙌

Terima kasih, dan semoga kita bertemu lagi di petualangan berikutnya!💙🚣‍♂"
    ];

    $response = sendWhatsAppMessage($params);

    if (isset($response['status']) && $response['status'] === 'ok') {
        alertMessage('success', 'Status Pembayaran Berhasil Diubah Menjadi Lunas!');
    } else {
        alertMessage('error', 'Gagal mengirim pesan WhatsApp.');
    }
} else {
    alertMessage('error', 'Transaksi tidak ditemukan.');
}

redirect('data-transaksi');
