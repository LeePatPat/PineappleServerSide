<?php
if(session_status() == PHP_SESSION_NONE)
    session_start();
if($_SESSION["loggedIn"]==false|| !isset($_SESSION["loggedIn"]))
    header("Location:/~syb14178/mad/testPine/index.php");

var_dump($_SESSION);
?>


<html>
    <head>
        <title>Create game</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>

        <h1> Create Game </h1>
        
        <form action="PHP/newGame.php" method="GET">
            Player 1: <?php echo $_SESSION['username']; ?></br>
            <input type="hidden" name="user1" value="<?php echo $_SESSION['username']; ?>"/></br>
            <input type="text" name="user2" placeholder="user2"/></br>
            <input type="text" name="user3" placeholder="user3 (optional)"/></br>
            <input type="number" name="pointLimit" placeholder="Point Limit"/></br>
            <input type="submit" name="submit" /></br>
        </form>
        
    </body>
    
    <script>
    </script>
</html>
