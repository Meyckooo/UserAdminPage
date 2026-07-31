<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkboxes in PHP</title>
</head>
<body>
            
    <form action="" method="post">
        <label><input type="checkbox" name="fruits[]" value="Apple"> Apple</label><br>
        <label><input type="checkbox" name="fruits[]" value="Banana"> Banana</label><br>
        <label><input type="checkbox" name="fruits[]" value="Cherry"> Cherry</label><br>
        <button type="submit" name="submit">Submit</button>
    </form>

    <?php
        if(isset($_POST["submit"])){
            if(!empty($_POST["fruits"])){
                $selectedFruits = $_POST["fruits"];
                echo "You selected: " . implode(", ", $selectedFruits);
            } else {
                echo "No fruits selected.";
            }
        }
    ?>
</body>
</html>