<?php
/*
 * Php file that generates output upon creation of a new game.
 * url = something.com/newGame.php?user1=#&user2=#&user3=#&pointLimit=#
 * params: _POST: username1,username2,username3,pointLiimt - User IDs
 */
include_once 'gamefuncs.php';
include_once 'phpValidation.php';

$user1 = isset($_GET["user1"]) ? makeSafe(trim($_GET["user1"])) : "";
$user2 = isset($_GET["user2"]) ? makeSafe(trim($_GET["user2"])) : "";
$user3 = isset($_GET["user3"]) ? makeSafe(trim($_GET["user3"])) : "";
$pointLimit = isset($_GET["pointLimit"]) ? makeSafe(trim($_GET["pointLimit"])) : "";
//You can have a game with 2 or 3 players, thus id3 does not need to be set

if($user1 == $user2 || $user2 == $user3 || $user1 == $user3)
    exit("Error: Two or more of the users have the same username");

if(!checkUsersExist($user1, $user2, $user3)) //stop script and print error message
    exit("Error: Player(s) does not exist. Cannot create game.");
if($pointLimit == 0)
    exit("Cannot create a game with no point limit.");

if($user1!=="" && $user2!==""){    
    $gameId = 0;
    
    if($user3!==""){ //create 3 player game
        if(($gameId = createThreePlayerGame($user1, $user2, $user3, $pointLimit)) == false)
            exit("Could not create three player game.");
    }else{
        if(($gameId = createTwoPlayerGame($user1, $user2, $pointLimit)) == false)
            exit("Could not create two player game.");
    }
    
    //deal hands
    if($gameId !== 0)
        dealHand($gameId);
    else
        exit('Could not deal hand on game initialisation');
    
}else{
    exit("Could not create game. Please contant the administrator");
}
?>