
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Page</title>
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
        h1{
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .design{
            width:20%;
            border: 3px solid #73AD21;
            padding: 10px;
            text-align: center;
            background-color: white;
            border-radius: 8px;
        }
        a{
            font-weight: bold; 
            color: #1d72b8;
        }
    </style>
    <script>
    $(document).ready(function() {
        $('#loginLink').on('click', function(event) {
            event.preventDefault();
            $.ajax({
                url: 'login.php',
                method: 'GET',
                success: function(html) {
                    $('body').html(html);
                }
            });
        });
    });
    
    $(document).ready(function() {
        $('#registerLink').on('click', function(event) {
            event.preventDefault();
            $.ajax({
                url: 'register.php',
                method: 'GET',
                success: function(html) {
                    $('body').html(html);
                }
            });
        });
    });
</script>
</head>
<body>
    <form class="design" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
        <h1>Welcome to Start Page</h1>
        <p>If you already have an Account; then <a href="#" id="loginLink">Login</a>.</p>
        <p>if you are new; then <a href="#" id="registerLink">Register</a>.</p>
    </form>    
</body>
</html>
