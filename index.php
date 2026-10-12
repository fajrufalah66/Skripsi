<?php
require './config/app.php';

if(!isAuthenticated()) {
    redirect('auth/login.php');
}

$userId = $_SESSION['userId'];
$user = $db->query("SELECT * FROM pengguna WHERE id = '{$userId}'")->fetch_object();

$breadcrumbItems = [];

include './layouts/header.php';
include './components/breadcrumb.php';
?>

    

<?php include './layouts/footer.php'; ?>