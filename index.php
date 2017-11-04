<?php
if(session_status() == PHP_SESSION_NONE)
    session_start();

//var_dump($_SESSION);echo "</br>";

//REMOVE PLZ
include_once 'PHP/userdbfuncs.php';
?>

<html>
    <head>
        <title>Home</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <?php
            if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] == false){
                echo '<a href="register.php">Register</a>';
                echo '</br>';
                echo '<a href="login.php">Login</a>';
            }else{
                echo '<a href="createGame.php">Create Game</a></br>';
                echo '<a href="logout.php">Log Out</a>';
            }
        ?>
    </body>
    
    <?php
//    $test = getUsernameFromEmail("leepaterson1996@hotmail.com");
//    echo $test["username"];
//    var_dump($test);
    ?>
</html>
