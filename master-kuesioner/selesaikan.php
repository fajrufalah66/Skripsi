<?php
require './../config/app.php';

if(!isAuthenticated()) {
    redirect('auth/login.php');
} else if(!checkIsRole('Ketua Pengelola')) {
    alertMessage('error', 'Akses tidak diizinkan');
    redirect('');
}

$id = $db->real_escape_string($_GET['i'] ?? '');

function getPointJawaban($jawaban) {
    switch($jawaban) {
        case 'SS':
            return 5;
        case 'S':
            return 4;
        case 'CS':
            return 3;
        case 'TS':
            return 2;
        default:
            return 1;
    }
}

function rekomendasiPerbaikan($jawabanKuesioner, $gap) {
    if($gap > 0) {
        return 'Sangat Baik ' . $jawabanKuesioner['nama_pernyataan'];
    } else {
        return $jawabanKuesioner['rekomendasi_perbaikan_pernyataan'];
    }
}

$queryJawabanKuesioner = $db->query("
        SELECT jawaban_kuesioner.*, data_pernyataan.pernyataan as nama_pernyataan, data_pernyataan.rekomendasi_perbaikan AS rekomendasi_perbaikan_pernyataan
        FROM jawaban_kuesioner
        JOIN hasil_kuesioner ON hasil_kuesioner.id = jawaban_kuesioner.id_hasil_kuesioner
        JOIN data_pernyataan ON data_pernyataan.id = jawaban_kuesioner.id_data_pernyataan
        WHERE hasil_kuesioner.id_data_kuesioner = '{$id}'
        ORDER BY id_data_pernyataan ASC
    ");

    $tmpResult = [];
    while($jawabanKuesioner = $queryJawabanKuesioner->fetch_assoc()) {
        $jawabanHarapan = getPointJawaban($jawabanKuesioner['jawaban_harapan']);
        $jawabanKenyataan = getPointJawaban($jawabanKuesioner['jawaban_kenyataan']);

        if(isset($tmpResult[ $jawabanKuesioner['id_data_pernyataan'] ])) {
            $tmpResult[ $jawabanKuesioner['id_data_pernyataan'] ]['jawaban_harapan'][] = $jawabanHarapan;
            $tmpResult[ $jawabanKuesioner['id_data_pernyataan'] ]['jawaban_kenyataan'][] = $jawabanKenyataan;

        } else {
            $jawabanKuesioner['jawaban_harapan'] = [$jawabanHarapan];
            $jawabanKuesioner['jawaban_kenyataan'] = [$jawabanKenyataan];
            $tmpResult[$jawabanKuesioner['id_data_pernyataan']] = $jawabanKuesioner;
        }
    }

    foreach($tmpResult as $item) {
        $jawabanHarapan = array_sum($item['jawaban_harapan']) / count($item['jawaban_harapan']);
        $jawabanKenyataan = array_sum($item['jawaban_kenyataan']) / count($item['jawaban_kenyataan']);
        $gap = $jawabanKenyataan - $jawabanHarapan;

        $rekomendasiPerbaikan = rekomendasiPerbaikan($item, $gap);
        $db->query("UPDATE jawaban_kuesioner SET gap = '{$gap}', rekomendasi_perbaikan = '{$rekomendasiPerbaikan}' WHERE id = '{$item['id']}'");
    }

    $db->query("UPDATE data_kuesioner SET status = 'Selesai' WHERE id = '{$id}'");
    alertMessage('success', 'Kuesioner berhasil di selesaikan, hasil jawaban telah dihitung!');
    redirect('master-kuesioner');