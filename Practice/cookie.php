<?php 
    setcookie('fave_color', 'yellow', time() + 8600 * 7, '/');
    setcookie('fave_food', 'Cooking ina mo', time() + 8600 * 5, '/');
    setcookie('fave_movie', 'Spiderman Brand New Day', time() + 8600 * 3, '/');
    
    if (isset($_COOKIE['fave_color']) ?? false) {
        echo "Your favorite color is: " . $_COOKIE['fave_color'] . "<br>";
    } else {
        echo "No favorite color set.<br>";
    }

?>