<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter Sanitize</title>
</head>
<body>
    
    <form action="filter-sanitize.php" method="post">
        <input type="text" name="name" placeholder="Name:">
        <input type="text" name="age" placeholder="Age">
        <input type="text" name="email" placeholder="Email">
        <button type="submit" name="submit" value="submit">Submit</button>
    </form>
    <?php
            // Filter and Sanitize are used to validate and sanitize user input data.
            
            // Filter Sanitize
           if(isset($_POST['submit'])){
                $username = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);
                $age = filter_input(INPUT_POST, "age", FILTER_SANITIZE_NUMBER_INT);
                $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
                echo "The name is: " . $username;
           }


        //  Filter Validate
           if(isset($_POST['submit'])){
                $age = filter_input(INPUT_POST, "age", FILTER_VALIDATE_INT);
                $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);

                if(empty($age)){
                    echo "<br>The number is invalid";
                }else {
                    echo "<br> You are {$age} years old";
                }

                if(empty($email)){
                    echo "<br>The email is invalid";
                }else {
                    echo "<br>The email is: " . $email;
                }
           }
    ?>
</body>
</html>