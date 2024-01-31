<?php
include "Connect.php";

$br = "";
$errorMessage = "";

$maxAttempts = 6;
$lockoutTime = 60;

session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF Token Validation Failed");
    }

    if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $maxAttempts && (time() - $_SESSION['last_attempt_time']) < $lockoutTime) {
        $errorMessage = "Too many login attempts. Please try again later.";
        $br = "<br>";
    } else {
        $sql_les = "SELECT * FROM altchatuser_info";
        $resultat_les = mysqli_query($conn, $sql_les);
        $users = mysqli_fetch_all($resultat_les, MYSQLI_ASSOC);

        $username = $_POST["username"];
        $password = $_POST["password"];

        foreach ($users as $user) {
            if ($username == $user["user_username"] && password_verify($password, $user["user_password"])) {
                // Passwords match
                if (in_array($username, $admins)) {
                    session_start();
                    $_SESSION["redirect_allowed"] = true;
                    header("Location: Adminpanel.php");
                    exit();
                } else {
                    header("Location: Temp.php");
                    exit();
                }
            }
        }        

        if (!isset($_SESSION['login_attempts']) || (time() - $_SESSION['last_attempt_time']) >= $lockoutTime) {
            $_SESSION['login_attempts'] = 1;
        } else {
            $_SESSION['login_attempts']++;
        }
        $_SESSION['last_attempt_time'] = time();

        $errorMessage = "Feil brukernavn eller passord!";
        $br = "<br>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/Stylesheet.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Libre+Franklin&display=swap">
</head>
<body>
<button class="top-right-button"><a href="Register.php">Registrer Deg</a></button>

    <div class="loginform">
        <h2>AltChat Login</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="username">Username: </label>
            <input type="text" id="username" name="username" required><br><br>
            <label for="password">Password: </label>
            <input type="password" id="password" name="password" required><br><br>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <input type="submit" name="Login" value="Login">
            <?php echo $br; ?>
            <?php echo $br; ?>
            <?php echo $errorMessage; ?>
        </form>
    </div>
</body>
</html>
