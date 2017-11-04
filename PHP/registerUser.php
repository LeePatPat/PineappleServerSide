<?php

include_once './userdbfuncs.php';
include_once './phpValidation.php';

$email = isset($_POST["email"]) ? makeSafe(trim($_POST["email"])) : "";
$username = isset($_POST["username"]) ? makeSafe(trim($_POST["username"])) : "";
$password = isset($_POST["password"]) ? makeSafe(trim($_POST["password"])) : "";

if($email !== "" && $username !== ""){
    if(!registerUser($email, $password, $username)){
        echo '<script>alert("Could not add account. Perhaps the email or username is already taken.")</script>';
        return false;
    }else{
        echo '<script>alert("Account has been registered!")</script>';
        header("Location: /pineapple/login.php");
        return true;
    }
}else{
    echo '<script>alert("Either username or email is black/invalid")</script>';
    return false;
}
