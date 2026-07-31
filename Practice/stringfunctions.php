<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>String Functions PHP</title>
</head>
<body>
    <?php
            $username = "John Doe";
            $username2 = "Ivan Smith";
            
            // String length
            $length = strlen($username);
            echo "The length of the username is: " . $length;

            // String to uppercase -> Convert a string to uppercase
            $uppercase = strtoupper($username); 
            echo "<br> <br>The username in uppercase is: " . $uppercase;

            //String to Lowercase -> Convert a string to lowercase
            $lowercase = strtolower($username);
            echo "<br> <br>The username in lowercase is: " . $lowercase;

            //String to Trim -> Remove whitespace from the beginning and end of a string
            $trimmed = trim($username);
            echo "<br> <br>The trimmed username is: " . $trimmed;

            //String to Replace -> Replace all occurrences of a search string with a replacement string
            $replaced = str_replace("Doe", "Smith", $username);
            echo "<br> <br>The replaced username is: " . $replaced;

            //String to Reverse -> Reverse a string
            $reversed = strrev($username);
            echo "<br> <br>The reversed username is: " . $reversed;

            //String to Shuffle -> Randomly shuffle the characters in a string
            $shuffled = str_shuffle($username);
            echo "<br> <br>The shuffled username is: " . $shuffled;

            //String to strcmp -> Compare two strings 
            //if the result is 1 it means the comparison is false and if the result is 0 the comparison is true
            $comparison = strcmp($username , $username2);
            echo "<br> <br>The comparison result is: " . $comparison;

    ?>
</body>
</html>