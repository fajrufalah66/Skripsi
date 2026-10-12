<?php
require './../config/app.php';

if(isAuthenticated()) {
    redirect('');
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $db->real_escape_string($_POST['username']);
    $password = $db->real_escape_string($_POST['password']);

    try {
        $user = $db->query("SELECT id, password FROM pengguna WHERE username = '{$username}'")->fetch_object();
        if(!$user || !password_verify($password, $user->password)) {
            alertMessage('error', 'Username atau password salah');
        
        } else {
            $_SESSION['userId'] = $user->id;
            redirect('');
        }

    } catch(Exception $e) {
        alertMessage('error', 'Terjadi sebuah kesalahan');
    }
}

include './../layouts/header.php';
?>

    <div class="h-screen flex justify-center items-center">
        <div class="max-w-md mx-auto w-full">
            <div class="flex items-center bg-gray-50 rounded-md shadow-lg overflow-hidden w-full">
                <div class="flex flex-col justify-between p-8 w-full">
                    <img src="<?= baseUrl('assets/images/logo.jpg') ?>" class="w-20 mx-auto" alt="">
                    <form method="POST" class="mt-4 space-y-4">
                        <div>
                            <label
                                class="block text-sm/6 font-medium text-gray-900 mb-1"
                                for="username">
                                Username
                            </label>
                            <input
                                class="bg-gray-50 border-gray-300 rounded-lg h-11 text-gray-900 text-sm focus:ring-sky-500 focus:border-sky-500 block w-full py-2.5"
                                type="text"
                                name="username"
                                id="username"
                                autocomplete="username"
                                placeholder="Masukkan Username"
                                required />
                        </div>
                
                        <div>
                            <label
                                class="block text-sm/6 font-medium text-gray-900 mb-1"
                                for="password">
                                Password
                            </label>
                            <input
                                class="bg-gray-50 border-gray-300 rounded-lg h-11 text-gray-900 text-sm focus:ring-sky-500 focus:border-sky-500 block w-full py-2.5"
                                type="password"
                                name="password"
                                id="password"
                                autocomplete="current-password"
                                placeholder="Masukkan Password"
                                required />
                        </div>

                        <button
                            type="submit"
                            class="block text-white bg-sky-500 transition hover:bg-sky-600 focus:ring-4 focus:outline-none focus:ring-sky-500 font-medium rounded-md text-sm w-full px-5 py-2.5 text-center">
                            Log In
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php include './../layouts/footer.php'; ?>