<?php
session_start();  // Ensure session is started

// Import DB config file
require_once 'config/Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Establish DB connection
    try {
        $dbCon = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
        $dbCon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $th) {
        echo "<div id='alert-message' class='alert alert-danger'>Connection failed: " . htmlspecialchars($th->getMessage()) . "</div>";
        echo "<script>
                setTimeout(function() {
                    var alertBox = document.getElementById('alert-message');
                    if (alertBox) { alertBox.style.display = 'none'; }
                }, 2000);
              </script>";
        exit;
    }

    // Check if the email exists in the database
    $stmt = $dbCon->prepare("SELECT * FROM login WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            header("Location: bird-app.php");
            exit;
        } else {
            echo "<div id='alert-message' class='alert alert-danger'>Incorrect password. Please try again.</div>";
            echo "<script>
                    setTimeout(function() {
                        var alertBox = document.getElementById('alert-message');
                        if (alertBox) { alertBox.style.display = 'none'; }
                    }, 2000);
                  </script>";
        }
    } else {
        echo "<div id='alert-message' class='alert alert-warning'>Email not found. Please register first.</div>";
        echo "<script>
                setTimeout(function() {
                    var alertBox = document.getElementById('alert-message');
                    if (alertBox) { alertBox.style.display = 'none'; }
                }, 2000);
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avicast_Login</title>
    <link rel="stylesheet" href="style0.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <header>
        <h1>Welcome to AVICAST</h1>
    </header>

    <div class="forms-container">
        <div class="wrapper" id="login-form">
            <form action="login.php" method="POST">
                <h1>Login</h1>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required />
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required />
                </div>
                <div class="remember-forgot">
                    <label>
                        <input type="checkbox" name="remember_me" /> Remember me
                    </label>
                    <a href="Google.com">Forgot Password?</a>
                </div>
                <button type="submit" class="button">Login</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>