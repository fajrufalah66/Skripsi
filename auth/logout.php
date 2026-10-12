<?php
require './../config/app.php';

unset($_SESSION['userId']);
session_destroy();
session_reset();

alertMessage('success', 'Logout berhasil!');
redirect('auth/login.php');