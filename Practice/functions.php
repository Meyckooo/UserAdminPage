<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Functions PHP</title>
</head>
<body>
    <?php
        function happybirthday($name, $age, $year){
            echo "Happy birthday, $name! You are $age and were born in $year. <br> <br>";
        }
        // Invoke the function
        happybirthday("John", 30, 1993);

        function addition($num1, $num2){
            return $num1 + $num2;
        }
        // Invoke the function
        $result = addition(5, 10);
        echo "The sum is: " . $result;

        function hypotenuse($a, $b){
            $c = sqrt($a * $a + $b * $b);
            return $c;
        }
        
        // Invoke the function
        echo "<br> <br>" . hypotenuse(3, 4);
        
    ?>
</body>
</html>