<?php

if(isset($_POST['signup-submit'])){
    require 'dbhandler.php';
    $username = $_POST['uname'];
    $email = $_POST['email'];
    $passw = $_POST['pwd'];
    $passw2 = $_POST['con-pwd'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    if($passw !== $passw2){
        header("Location:../signup.php?error=diffPasswords");
        exit();
    }
    else{
        $sql = "SELECT uname FROM users WHERE uname=?";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)){
            header("Location:../signup.php?error=SQLinjection");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $check = mysqli_stmt_num_rows($stmt);
            if($check > 0){
                header("Location:../signup.php?error=UsernameTaken");
                exit();
            }
            else{
                $sql = "INSERT INTO users (lname, fname, email, uname, password) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if(!mysqli_stmt_prepare($stmt, $sql)){
                    header("Location:../signup.php?error=SQLinjection");
                    exit();
                }
                else{
                    $hashed = password_hash($passw, PASSWORD_BCRYPT);
                    mysqli_stmt_bind_param($stmt, "sssss", $lname, $fname, $email, $username, $hashed);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_store_result($stmt);

                    $sqlImg = "INSERT INTO profiles (uname, fname) VALUES ('$username', '$fname')";
                    mysqli_query($conn, $sqlImg);
                    header("Location:../signup.php?signup=success");
                    exit();
                }
            }
        }
        mysqli_close($stmt);
        mysqli_close($conn);
    }
}else{
    header("Location:../signup.php");
    exit();
}