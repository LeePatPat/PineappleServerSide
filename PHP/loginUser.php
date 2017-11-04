<?php

include_once './userdbfuncs.php';
include_once './phpValidation.php';

$email = isset($_POST["email"]) ? makeSafe(trim($_POST["email"])) : "";
$password = isset($_POST["password"]) ? makeSafe(trim($_POST["password"])) : "";

if(validEmail($email) && validPassword($password)){
    if(!login($email, $password)){
        echo '<script>alert("E-mail or password does not exist.")</script>';
        header("Location:/pineapple");
        return false;
    }else{
        echo '<script>alert("You have logged in!")</script>';
        /*TODO Successful login*/
        echo 'You have logged in!';
        header("Location:/pineapple/createGame.php");
        return true;
    }
}else{
    echo '<script>alert("Either username or email is black/invalid")</script>';
    header("Location:/~syb14178/mad/testPine/index.php");
    return false;
}

?>