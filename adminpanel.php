<?php 
include "Connect.php";

$sql_les = "SELECT * FROM altchatuser_info";

$resultat_les = mysqli_query($conn, $sql_les);

$users = mysqli_fetch_all($resultat_les, MYSQLI_ASSOC);?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
<table> 
    <tr>
    <th>id</th>
    <th>navn</th>
    <th>passord</th>
    <th>tidlagetpå</th>
    </tr>
    <?php  foreach ($users as $x) {
        echo "<tr>";
        echo "<td>";
        echo $x["user_id"];
        echo "</td>";
        echo "<td>";
        echo $x["user_username"];
        echo "</td>";
        echo "<td>";
        echo $x["user_password"];
        echo "</td>";
        echo "<td>";
        echo $x["user_timecreatedat"];
        echo "</td>";
        echo "</tr>";
    } ?>
    
    </table>
</body>
</html>