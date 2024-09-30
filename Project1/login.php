<?php
    include("database.php");
    
    if(isset($_POST["login"])){       
        $username = $_POST["username"];
        $password = $_POST["password"];
        $message1 = "Missing Username or Password!";
        $message2 = "Wrong Username or password!";        

        if(empty($username) || empty($password)){       
            echo "<script type='text/javascript'>alert('$message1');</script>";
        } else{
            $sql = "SELECT * FROM registeredusers WHERE username = ?";
            $stmt = mysqli_prepare($conn,$sql);

            if($stmt){
                mysqli_stmt_bind_param($stmt, 's', $username);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if($user = mysqli_fetch_assoc($result)){

                    //$hash1 = password_hash($password, PASSWORD_DEFAULT);
                    //$hashedPasswordFromDatabase = $user['password'];
                    //$saltFromDatabase = $user['salt'];                    
                    //$passwordWithSalt = $saltFromDatabase . $password;

                    if (password_verify($password, $user['password'])) {
                        session_start();
                        $_SESSION["username"] = $username;
                        $_SESSION["firstname"] = $user['firstname'];
                        $_SESSION["place"] = $user['place'];
                        $_SESSION["lastname"] = $user['lastname'];
                        header("Location: userpage.php");
                        exit();
                    } else {
                        echo "<script type='text/javascript'>alert('$message2');</script>";
                    }
                } else {
                    echo "<script type='text/javascript'>alert('$message2');</script>";
                }
                mysqli_stmt_close($stmt); 
            }
        }
     }
     mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In Page</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!--<link rel="stylesheet" href="css/style.css">-->
    <script>
    $(document).ready(function(){
        $('#gobackLink').on('click', function(event){
            event.preventDefault();
            $.ajax({
                url:'start.php',
                method: 'GET',
                success: function(html){
                    $('body').html(html);
                }
            });
        });
    });
    </script>
    <style>
        body{
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
            }
        .login-design{
            width:20%;
            border: 3px solid #007fff;
            padding: 10px;
            text-align: center;
            background-color: white;
            border-radius: 8px;
            }  
        a   {
            font-weight: bold; 
            color: #1d72b8;
            }
        label{
            font-weight: bold;
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
            }
    </style>
</head>
<body>
    <form class="login-design" id="login" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
    <p style="font-size: 20px;"><b>Enter your login data:</b></p>
        <label>Username: </label>
        <input type="text" name="username" placeholder="Jon123"><br>
        <label>Password: </label>
        <input type="password" name="password"><br><br>
        <input type="submit" name="login" id="login" value="Login">
         or <a href="#" id="gobackLink">go back</a> 
    </form>    
</body>
</html> 

