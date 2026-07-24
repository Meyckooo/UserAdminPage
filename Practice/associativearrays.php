<?php 
    $capitals = [
        "USA" => "Washington D.C.", 
        "France" => "Paris", 
        "Germany" => "Berlin", 
        "Italy" => "Rome", 
        "Spain" => "Madrid",
        "Philippines" => "Manila",
    ];
    $capital = $capitals[$_POST['country']] ?? '';
    echo $capital;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Associative Array</title>

    <form action="demo.php" method="post">
        <label>Enter a country:</label>
        <input type="text" name="country" placeholder="Enter your country" required>
        <button type="submit">Submit</button>
    </form>
</head>
<body>
    
</body>
</html>