<?php
echo '<script>alert("We are in logout");</script>';
if(session_status() == PHP_SESSION_NONE)
    session_start();
session_unset();
header("Location:/pineapple");

?>