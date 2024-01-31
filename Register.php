<?php
include "Connect.php";

$br = "";
$successMessage = "";
$errorMessage = "";

$maxAttempts = 10;
$lockoutTime = 60;

session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Check CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF Token Validation Failed");
    }

    if ($_POST['action'] === 'register') {
        $username = $_POST["username"];
        $password = $_POST["password"];

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
            $profilePicture = $_FILES['profile_picture'];
            $allowedExtensions = ['jpg', 'jpeg'];
            $fileExtension = pathinfo($profilePicture['name'], PATHINFO_EXTENSION);

            if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                $errorMessage = "Invalid file format. Only JPG/JPEG files are allowed.";
            } elseif ($profilePicture['size'] > 64 * 1024) { // 64 KiB limit
                $errorMessage = "File size exceeds the maximum limit (64 KiB).";
            } else {
                $profilePictureData = file_get_contents($profilePicture['tmp_name']);
                $profilePictureData = mysqli_real_escape_string($conn, $profilePictureData);
            }
        } else {
            $profilePictureData = NULL;
        }

        $sql_check_username = "SELECT COUNT(*) AS count FROM altchatuser_info WHERE user_username = '$username'";
        $result_check_username = mysqli_query($conn, $sql_check_username);
        $row = mysqli_fetch_assoc($result_check_username);

        if ($row['count'] > 0) {
            $errorMessage = "Username already exists. Please choose another.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql_insert_user = "INSERT INTO altchatuser_info (user_username, user_password, user_profilepicture) VALUES ('$username', '$hashedPassword', '$profilePictureData')";
            if (mysqli_query($conn, $sql_insert_user)) {
                $successMessage = "Registration successful. You can now login.";
            } else {
                $errorMessage = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="css/Stylesheet.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Libre+Franklin&display=swap">
</head>
<body>
    <div class="loginform">
        <h2>AltChat Registration</h2>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <label for="reg-username">Username: </label>
            <input type="text" id="reg-username" name="username" required><br><br>
            <label for="reg-password">Password: </label>
            <input type="password" id="reg-password" name="password" required><br><br>

            <label for="profile-picture">Profile Picture (JPG/JPEG, max 64 KiB): </label>
            <input type="file" id="profile-picture" name="profile_picture" accept="image/jpeg"><br><br>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="register">

            <input type="submit" name="Register" value="Register">
            <?php echo $br; ?>
            <?php echo $errorMessage; ?>
            <?php echo $successMessage; ?>
        </form>

        <p>Already have an account? <a href="Index.php">Login here</a>.</p>
    </div>
</body>
</html>
