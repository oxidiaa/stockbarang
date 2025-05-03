<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'function.php';

// Fungsi untuk menampilkan pesan error
function showLoginError($message) {
    $_SESSION['login_error'] = $message;
}

// Fungsi untuk redirect dengan pesan
function redirectWithMessage($location, $message, $type = 'error') {
    $_SESSION['login_' . $type] = $message;
    header("location:$location");
    exit();
}

if (isset($_POST['login'])) {
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];

    if(empty($email) || empty($password)) {
        showLoginError("Email dan password harus diisi");
        header('location:login.php');
        exit();
    }

    // Menggunakan prepared statement untuk mencegah SQL injection
    $stmt = $conn->prepare("SELECT * FROM login WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        // Verifikasi password menggunakan password_verify jika menggunakan hash
        // Untuk sementara menggunakan perbandingan langsung karena sistem yang ada
        if ($password === $data['password']) {
            // Set session dengan data yang diperlukan
            $_SESSION['log'] = true;
            $_SESSION['email'] = $data['email'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['user_id'] = $data['id']; // Tambahkan user_id jika ada
            $_SESSION['last_activity'] = time();

            // Redirect ke halaman beranda untuk semua user
            redirectWithMessage('index.php', 'Selamat datang!', 'success');
        } else {
            showLoginError("Password salah!");
            header('location:login.php');
            exit();
        }
    } else {
        showLoginError("Email tidak ditemukan!");
        header('location:login.php');
        exit();
    }
    $stmt->close();
}

// Cek jika user sudah login
if (isset($_SESSION['log'])) {
    // Cek session timeout (30 menit)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        session_unset();
        session_destroy();
        redirectWithMessage('login.php', 'Sesi anda telah berakhir. Silakan login kembali.');
    }
    
    $_SESSION['last_activity'] = time();
    
    // Redirect ke halaman beranda untuk semua user
    header('location:index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MetalArt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .success-message {
            color: #28a745;
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height:100vh;">
    <div class="card p-4 shadow" style="width: 400px;">
        <h3 class="text-center mb-4">Login MetalArt</h3>
        
        <?php if(isset($_SESSION['login_error'])): ?>
            <div class="error-message text-center">
                <?php 
                echo $_SESSION['login_error'];
                unset($_SESSION['login_error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['login_success'])): ?>
            <div class="success-message text-center">
                <?php 
                echo $_SESSION['login_success'];
                unset($_SESSION['login_success']);
                ?>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"/>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required/>
            </div>
            <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
