<?php
$base_path = './';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/media.css">
</head>

<body>
    <?php include $base_path . 'includes/sidebar.php'; ?>
    <div id="banner">
        <div class="wrapper">
            <div class="bnr_con">
                <div class="bnr_info">
                    <h1>Grow With "MJAC" <span>Dashboard Features</span></h1>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dignissimos amet dicta, distinctio sapiente, inventore eveniet porro exercitationem aut repellendus unde deleniti ut fugiat debitis iure sunt nulla ea maxime qui.</p>
                    <a class="global_btn" href="#">Click Here</a>
                </div>
                <div class="bnr_image">
                    <figure><img src="assets/images/slider-right-img.png" alt=""></figure>
                </div>
            </div>
        </div>
    </div>
    <div id="main">
        <div class="main_con">
            
        </div>
    </div>

    <script src="<?php echo $base_path; ?>/assets/js/modal_item.js"></script>
    <script src="<?php echo $base_path; ?>assets/js/sidebar_button.js"></script>
    <script src="assets/js/sweetalert.js"></script>

</body>

</html>