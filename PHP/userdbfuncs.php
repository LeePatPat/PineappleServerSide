<?php
if(session_status() == PHP_SESSION_NONE) session_start();

include_once 'dbinfo.php';

function getDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
/*
 * Closes the database connection of connection($conn)
 */
function closeDB($conn) {
    $conn->close();
}

/*
 * Register user with email, username and password (hashed for server storage)
 */
function registerUser($Email, $Password, $username){
    if(!accountExists($Email, $username))  {//account doesnt exist
        $conn = getDB();
        $Password = hashPassword($Password);
        $sql = "INSERT INTO users(id, email, username, password) VALUES 
        (NULL,'".$Email."','".$username."','".$Password."')";
        if(!$conn->query($sql)){
            echo $conn->error;
            closeDB($conn);
            return false;
        }
        echo "Account created!";
        closeDB($conn);
        return true;
    }
    echo "Username or password exists :P";
    closeDB($conn);
    return false;
}
function hashPassword($Password) { // Call this during registration as well as during login iff hash has changed
    $Password =  password_hash($Password, PASSWORD_DEFAULT, ['cost' => 12]);
    return $Password;
}

function accountExists($email, $username){
    return (emailExists($email) || usernameExists($username));
}

function emailExists($email){
    $conn = getDB();
    $sql  = "SELECT email FROM users WHERE email='".$email."'";
    $result = $conn->query($sql)->num_rows;
    closeDB($conn);
    return ($result >= 1);
}

function usernameExists($username){
    $conn = getDB();
    $sql = "SELECT username FROM users WHERE username='".$username."'";
    $result = $conn->query($sql)->num_rows;
    closeDB($conn);
    return ($result >= 1);
}

function login($Email, $Password) {
    $conn = getDB();
    $sql = "SELECT username, id, password FROM users WHERE Email='".$Email."'";
    if($r = $conn->query($sql)) {
        $result = $r->fetch_assoc(); //maybe mysql_fetch_assoc() ?
        $password = $result['password'];
        $id = $result['id'];
        if(password_verify($Password, $password)) {
            if (password_needs_rehash($password, PASSWORD_DEFAULT, ['cost' => 12])) {
                $password = hashPassword($Password);
                $sql = "UPDATE users SET password='".$password."' WHERE id='".$id."'";
                if(!$conn->query($sql))
                    echo "<script>alert('something has gone wrong logging in');</script>";
            }
            closeDB($conn);
            $_SESSION['loggedIn'] = true;
            $_SESSION['username'] = $result['username'];
            $_SESSION['email'] = $Email;
            return true;
        } 
        else {
            closeDB($conn);
            echo "No match :(";
            return false;
        }
    }
}

//can probably remove this
function getProfile($email){
    $conn = getDB();
    $SQL = "SELECT * from users WHERE FBEmail='".$email."'";
    if($r = $conn->query($SQL)) {
        $result = $r->fetch_assoc();
        closeDB($conn);
        return $result;
    }
    closeDB($conn);
}

/*
 * returns ID of the given username
 */
function getId($username){
    $conn = getDB();
    $sql = "SELECT id FROM users WHERE username='".$username."'";
    if($r = $conn->query($sql)) {
        $result = $r->fetch_assoc();
        if($r->num_rows == 0) return false;
        closeDB($conn);
        return $result['id'];
    }
    closeDB($conn);
    return false;
}

/*TODO write updatePassword ? */