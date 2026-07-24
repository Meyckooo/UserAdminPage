<?php 
   echo $_POST["username"] . "<br>";
   echo $_POST["password"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$_POST</title>

    <form action="post.php" method="post">
        <input type="text" name="username" placeholder="Enter your username" required>
        <input type="password" name="password" placeholder="Enter your password" required>
        <button type="submit">Submit</button>
    </form>
</head>
<body>
    
</body>
</html>