<?php
    include("database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body{
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
        }
        .register-design{
            width:20%;
            border: 3px solid #ff7f7f;
            padding: 10px;
            text-align: center;
            background-color: white;
            border-radius: 8px;
        }   
        #goBack{
            font-weight: bold; 
            color: #1d72b8;
        }
        table td{
            padding: 5px;
        }   
        label{
            font-weight: bold;
        }
        table{
            width: 100%;
        }
        input[type="text"], input[type="password"]{
            padding: 4px;
            margin: 5px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"]{
            background-color: #41c5b8;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            border: 1px solid black;
            font-weight: bold;
        }    
        #regiLoginButton{
            background-color: #28deeb;
            border: 1px solid black;
            color: white;
            padding: 9px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;            
        }
        #registrationSuccessful{
            position:absolute;
            top:30px;
            background-color: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            font-weight: bold;
            width: 100%; 
        }
    </style>
    <script>
    $(document).ready(function(){
        $('#goBack').on('click', function(event){
            event.preventDefault();
            $.ajax({
                url:'start.php',
                method: 'GET',
                success: function(html){
                    $('body').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching register page:', error);
                }
            });
        });
    });
    $(document).ready(function(){
        $('#regiLoginButton').on('click', function(event){
            event.preventDefault();
            $.ajax({
                url:'login.php',
                method: 'GET',
                success: function(html){
                    $('body').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching register page:', error);
                }
            });
        });
    });
    </script>
</head>
<body>
    
    <form class="register-design" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
        <p style="font-size: 20px;"><b>Register</b></p>
        <table>
                <tr>
                    <td><label for="firstname">Firstname:</label></td>
                    <td><input type="text" id="firstname" name="firstname" required pattern="[a-zA-Z]+" placeholder="Jon"></td>
                </tr>
                <tr>
                    <td><label for="lastname">Lastname:</label></td>
                    <td><input type="text" id="lastname" name="lastname" required pattern="[a-zA-Z]+" placeholder="Kennedy"></td>
                </tr>
                <tr>
                    <td><label for="username">Username:</label></td>
                    <td><input type="text" id="username" name="username" required placeholder="Jon123"></td>
                </tr>
                <tr>
                    <td><label for="email">Email:</label></td>
                    <td><input type="text" id="email" name="email" required placeholder="marc99@gmail.com" ></td>
                </tr>
                <tr>
                    <td><label for="password">Password:</label></td>
                    <td><input type="password" id="password" name="password" required></td>
                </tr>
                <tr>
                    <td><label for="repeatpassword">Repeat Password:</label></td>
                    <td><input type="password" id="repeatpassword" name="repeatpassword" required minlength="6"></td>
                </tr>
                <tr>
                    <td><label for="place">Where do you live:</label></td>
                    <td><input type="text" id="place" name="place" required pattern="[a-zA-Z]+" placeholder="City"></td>
                </tr>
                <tr>
                    <td ><a href="#" id="goBack">Go back</a></td>
                    <td><input type="submit" name="register" value="Register"> <a href="#" id="regiLoginButton">Login</a></td>  
                </tr>
        </table>       
    </form>
</body>
</html>

<?php

    $message1 = "This is invalid Email";
    $message2 = "Username or Email already taken";
    $message3 = "Your repeated password is wrong";

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])){

        $firstname = $_POST["firstname"];
        $lastname = $_POST["lastname"];  
        $username = $_POST["username"]; 
        $email = $_POST["email"];
        $place = $_POST["place"];
        $password = $_POST["password"]; 
        $repatpassword = $_POST["repeatpassword"]; 
        $salt = random_bytes(16);
        $salt = bin2hex($salt);

        $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
            if(filter_var($emailB, FILTER_VALIDATE_EMAIL) === false || $emailB != $email){
                echo "<script type='text/javascript'>alert('$message1');</script>";
                exit();
            } 
                               
            if($repatpassword == $password){
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT); 
                //$hash1 = password_hash("555" . $hash1, PASSWORD_DEFAULT);
                $sql = "INSERT INTO registeredusers (firstname, lastname, username, email, password, place, salt)
                VALUES ('$firstname', '$lastname', '$username', '$email', '$hashedPassword', '$place', '$salt')";
               
                try{
                    mysqli_query($conn, $sql);                                    
                    echo "<div id='registrationSuccessful'>Registration successful! You can now log in.</div>";
                }
                catch(mysqli_sql_exception) {
                    echo "<script type='text/javascript'>alert('$message2');</script>";
                }
            } else{
                echo "<script type='text/javascript'>alert('$message3');</script>";
            }
    }   
    mysqli_close($conn);           
?>
