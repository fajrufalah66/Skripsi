<?php
function baseUrl(string $route) {
    global $config;
    return $config['APP_URL'] . '/' . $route;
}

function isAuthenticated() {
    return isset($_SESSION['userId']);
}

function alertMessage(string $icon, string $message) {
    $_SESSION['alert'] = [
        'icon' => $icon,
        'message' => $message
    ];
}

function checkIsRole(string $role) {
    global $db;
    if(empty($_SESSION['userId'])) return false;
    
    $user = $db->query("SELECT jabatan FROM pengguna WHERE id = '{$_SESSION['userId']}'")->fetch_object();
    return $user && $user->jabatan == $role;
}

function redirect(string $route) {
    header('Location: ' . baseUrl($route));
    exit;
}

function clearInput(string $input) {
    global $db;
    return $db->real_escape_string(trim($input));
}

function selectedOption(string $value, string $needle) {
    return $value == $needle ? 'selected' : '';
}

function formatDateTimeIndo(string $dateTime) {
    $month = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ];

    $expDateTime = explode(' ', $dateTime);
    $expDate = explode('-', $expDateTime[0]);
    $expTime = explode(':', $expDateTime[1]);
    return $expDate[2] . ' ' . $month[$expDate[1]] . ' ' . $expDate[0] . ', ' . $expTime[0] . ':' . $expTime[1];
}

function formatDateIndo(string $dateTime) {
    $month = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ];

    $expDateTime = explode(' ', $dateTime);
    $expDate = explode('-', $expDateTime[0]);
    return $expDate[2] . ' ' . $month[$expDate[1]] . ' ' . $expDate[0];
}

function formatMonthIndo(string $dateTime) {
    $month = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ];

    $expDateTime = explode(' ', $dateTime);
    $expDate = explode('-', $expDateTime[0]);
    return $month[$expDate[1]] . ' ' . $expDate[0];
}

function generateTransactionId($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function sendWhatsAppMessage($params) {
    $url = "https://wapisender.id/api/v5/message/text";
    
    $data = [
	'api_key' => getenv('WHATSAPP_API_KEY') ?: '',
	'device_key' => getenv('WHATSAPP_DEVICE_KEY') ?: '',
        'destination' => $params['destination'],
        'message' => $params['message'],
    ];
    
    if (isset($params['quoted_id']) && isset($params['quoted_message']) && isset($params['quoted_participant'])) {
        $data['quoted_id'] = $params['quoted_id'];
        $data['quoted_message'] = $params['quoted_message'];
        $data['quoted_participant'] = $params['quoted_participant'];
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
