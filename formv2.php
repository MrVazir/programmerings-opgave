<?php
session_start();
$_SESSION['message'] = '';
$mysqli = new mysqli("localhost", "root", "mypass123", "accounts_complete");

//Formen bliver sendt med POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //Begge passwords er ens.
    if ($_POST['password'] == $_POST['confirmpassword']) {
        
        //set alle post variables
        $username = $mysqli->real_escape_string($_POST['username']);
        $email = $mysqli->real_escape_string($_POST['email']);
        $password = md5($_POST['password']); //md5 has password for security
        $avatar_path = $mysqli->real_escape_string('images/'.$_FILES['avatar']['name']);
        
        //File type kan kun være billede formater.
        if (preg_match("!image!",$_FILES['avatar']['type'])) {
            
            //Kopir billeder til images folder.
            if (copy($_FILES['avatar']['tmp_name'], $avatar_path)){
                
                //set session variables
                $_SESSION['username'] = $username;
                $_SESSION['avatar'] = $avatar_path;

                //Inset user data til vores database.
                $sql = "INSERT INTO users (username, email, password, avatar) "
                        . "VALUES ('$username', '$email', '$password', '$avatar_path')";
                
                //Hvis queryen er success, send bruger videre til welcome.php page, done!
                if ($mysqli->query($sql) === true){
                    $_SESSION['message'] = "Registration succesful! Added $username to the database!";
                    header("location: welcome.php");
                }
                else {
                    $_SESSION['message'] = 'Brugern kunne ikke blive addet til databasen';
                }
                $mysqli->close();          
            }
            else {
                $_SESSION['message'] = 'File upload failed';
            }
        }
        else {
            $_SESSION['message'] = 'Kun upload GIF, JPG eller PNG billeder';
        }
    }
    else {
        $_SESSION['message'] = 'Passwords passer ikke med hinanden';
    }
}
?>