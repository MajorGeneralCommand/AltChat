<?php
include "connect.php";

$br = "";
$errorMessage = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql_les = "SELECT * FROM user_info";
    $resultat_les = mysqli_query($conn, $sql_les);
    $users = mysqli_fetch_all($resultat_les, MYSQLI_ASSOC);

    $username = $_POST["username"];
    $password = $_POST["password"];

    foreach ($users as $user) {
        if ($username == $user["user_username"] && $password == $user["user_password"]) {
            if (in_array($username, $admins)) {
              header("Location: adminpanel.php");
              exit();
            }
            else {
              header("Location: temp.php");
              exit();  
            }
            
        }
    }

    $errorMessage = "Feil brukernavn eller passord!";
    $br = "<br>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/stylesheet.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Libre+Franklin&display=swap">
</head>
<body>
    <div class="loginform">
        <h2>AltChat Login</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="username">Username: </label>
            <input type="text" id="username" name="username" required><br><br>
            <label for="password">Password: </label>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" name="Login" value="Login">
            <?php echo $br; ?>
            <?php echo $br; ?>
            <?php echo $errorMessage; ?>
        </form>
    </div>
</body>
</html>