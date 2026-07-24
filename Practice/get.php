<?php 
   echo $_GET["username"] . "<br>";
   echo $_GET["password"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$_GET</title>

    <form action="get.php" method="get">
        <input type="text" name="username" placeholder="Enter your username" required>
        <input type="password" name="password" placeholder="Enter your password" required>
        <button type="submit">Submit</button>
    </form>
</head>
<body>
    
</body>
</html>