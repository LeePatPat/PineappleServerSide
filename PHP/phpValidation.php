<?php

/**
 *  This file contains all serverside validation
 */
include_once './userdbfuncs.php';

//Make variables safe to validate and insert into DB
function makeSafe($var){
    $conn = getDB();
    $var = $conn->real_escape_string(strip_tags($var));
    $conn->close();
    return ($var);
}

function validUsername($username){      //check not just whitespace
    return ($username !== "" || preg_match('/\s/',$username));
}

function validPassword($password){
    return ($password !== "" || preg_match('/\s/',$password));
}

function validEmail($email){
    return (filter_var($email, FILTER_VALIDATE_EMAIL));
}
