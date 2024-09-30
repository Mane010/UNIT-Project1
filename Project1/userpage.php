<?php
    include("database.php");
    session_start();
// Cookie    
    $cookieName = $_SESSION["username"];
    $cookieValue = "Welcome back";
    setcookie($cookieName, $cookieValue, time() + (86400 * 1), "/");
    
    
// Logout
    //var_dump($_SESSION);
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logout"])){
        session_unset();
        session_destroy();
        header("Location: start.php");
        exit();  
    }
// Weather

    $apiKey = "7537fcfb62194269b24110738241009";
    $city = $_SESSION["place"];

    $weatherUrl = "http://api.weatherapi.com/v1/current.json?q=" . $city . "&key=" . $apiKey;

    $weatherData = @file_get_contents($weatherUrl);
    $date = "";
    $temperature = "";
    if ($weatherData === FALSE) {
        //echo "Error: Unable to fetch weather data.<br>";
    } else {
        $weather = json_decode($weatherData, true);

        //Debug: Print the API response
        //echo "<pre>";
        //print_r($weather);
        //echo "</pre>";

        if (isset($weather['current'])) {
            $temperature = $weather['current']['temp_c'];
            $dateandtime = $weather['current']['last_updated'];
        } else {
            echo "Error: " . (isset($weather['error']) ? $weather['error']['message'] : 'Unknown error') . "<br>";
            $temperature = "N/A";
        }
    }
// Change Password - User 
    if(isset($_POST["changepassword"])){
        $message1 = "Password changed!";
        $message2 = "Passwords dont match";
        if($_POST["password"] === $_POST["repeatpassword"]){
            $password = $_POST["password"];
            $message1 = "Password changed!";
            $message2 = "Passwords don't match";
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE registeredusers SET password = '$hashedPassword' WHERE username = '" . $_SESSION["username"] . "'";

            if(mysqli_query($conn, $sql)){
                echo "<script type='text/javascript'>alert('$message1');</script>";
            }            
        }
        else{
            echo "<script type='text/javascript'>alert('$message2');</script>";
        }
    }
// Delete Account- User

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deleteAcc"])) {
        
        $message1 = "Your account is deleted";
        $sql_deleteacc = "DELETE FROM registeredusers WHERE username = '" . $_SESSION["username"] . "'";
    
        if (mysqli_query($conn, $sql_deleteacc)) {
            
            setcookie($cookieName, $cookieValue, time() - 3600, "/");
            echo "<script type='text/javascript'>
                    alert('" . htmlspecialchars($message1, ENT_QUOTES, 'UTF-8') . "');
                    window.location.href = 'start.php';
                  </script>";  
        }
    }    

// Termins
    if ($_SERVER["REQUEST_METHOD"] == "POST") {        
        
    // Create Termin
    if (isset($_POST["create_termin"])) {
        
        $message = "This user doesn't exist.";
        $message2 = "You can't assign yourself !";
        $zugriffUser = $_POST["zugriffUser"];
        $date2 = $_POST["termin_date"];
        $time2 = $_POST["termin_time"];
        $description = $_POST["description"];
        
        if (!empty($zugriffUser)) {
            
            if($zugriffUser != $_SESSION['username']){
                $checkUsername = $_POST["zugriffUser"];
                $sqlcheck = "SELECT id FROM registeredusers WHERE username = ?";
                $stmt2 = mysqli_prepare($conn, $sqlcheck);
                mysqli_stmt_bind_param($stmt2, 's', $checkUsername);
                mysqli_stmt_execute($stmt2);
                $result = mysqli_stmt_get_result($stmt2);

                if (mysqli_num_rows($result) > 0) {
                    
                    $sql = "INSERT INTO termins (username, termin_date, termin_time, description, zugriffUser) VALUES (?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, 'sssss', $_SESSION['username'], $date2, $time2, $description, $zugriffUser);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    echo "<script type='text/javascript'>alert('" . addslashes($message) . "');</script>";       
                }
                mysqli_stmt_close($stmt2);
            } else{
                echo "<script type='text/javascript'>alert('" . addslashes($message2) . "');</script>";
            }
        } else {
            
            $sql = "INSERT INTO termins (username, termin_date, termin_time, description, zugriffUser) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssss', $_SESSION['username'], $date2, $time2, $description, $zugriffUser);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // Update Termin
        if (isset($_POST["update_termin"])) {
            $id = $_POST["id"];
            $date2 = $_POST["termin_date"];
            $time2 = $_POST["termin_time"];
            $description = $_POST["description"];
            $sql = "UPDATE termins SET termin_date=?, termin_time=?, description=? WHERE id=? AND username=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssis', $date2, $time2, $description, $id, $_SESSION['username']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    // Delete Termin
        if (isset($_POST["delete_termin"])) {
            $id = $_POST["id"];
            $sql = "DELETE FROM termins WHERE id=? AND username=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'is', $id, $_SESSION['username']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // Fetch all termins 
    $sql = "SELECT * FROM termins WHERE username=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $_SESSION['username']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $termins = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    // Fetch only zugriff termins
    $zugriffUser = isset($_SESSION["username"]) ? $_SESSION["username"] : '';
    $sql2 = "SELECT * FROM termins WHERE zugriffUser=?";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, 's', $zugriffUser);
    mysqli_stmt_execute($stmt2);
    $result2 = mysqli_stmt_get_result($stmt2);
    $zugriffTermins = mysqli_fetch_all($result2, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt2);

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>      
    <style>
        body{
            margin: 0;
            height: 100vh;
            background-color: #f0f0f0;
            position: relative;
        }
        #logoutButton{
            background-color: #e34234;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            position: absolute;
            top: 90px;
            right: 40px;
        }
        #login-session{
            padding: 5px;
            position: absolute;
            top: 0;
            right: 0;
            border: 3px solid gray;
            width: 150px;
            font-size: 25px;
            height: 123px;
        }
        #div2{
            position:absolute;
            top: 150px;
            left:35%;
            background-color: white;
            border: 3px solid gray;
            border-radius: 4px;
            padding: 5px;
        }
        p{            
            font-size: 20px;
        }
        #editButton{
            border: 2px solid gray;
            border-radius: 4px;
            color: white;
            background-color: #F2B344;
            cursor: pointer;
            padding: 4px;
            font-size: 22px;  
            width:70px;          
        } 
        h1{
            font-size: 23px;
            
        }
        table{
            padding:6px;
            font-size: 20px;          
            background-color: lightgray;            
            border-collapse: separate;
            border-spacing: 6px;
            
        }
        #changepasswordButton{
            border: 2px solid gray;
            border-radius: 4px;
            color: white;
            background-color: #F2B344;
            cursor: pointer;
            padding: 4px;
            font-size: 17px;  
            font-weight: bold;            
        }
        #deleteButton{
            margin-left: 8px;
            background-color: #c42721;
            border-radius: 20px;
            padding:10px;
            color:white;
        }
        #terminForm {
            margin-top: 20px;
            position:absolute;
            top:630px;
            left:35%;
            background-color: #b6e0f2;
            border: 3px solid gray;
            border-radius: 4px;
            padding: 5px;            
        }
        .termin-table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .termin-table th, .termin-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .termin-table th {
            background-color: #f2f2f2;
        }
        .termin-table td input {
            width:
            100%;
        }   
        #alertSuccess{            
            background-color: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 30px;
            text-align: center;
            font-weight: bold;
            width: 100%; 
        }
        #hoverText {
            display: none;
            position: absolute;
            background-color: #333;
            color: white;
            padding: 10px;
            border-radius: 5px;            
        }
    
            
    </style>
    <script>
        /*
            $(document).on('submit', '#createTermin', function(e){
                e.preventDefault();

                var formData = new FormData(this);
                formData.append("create_termin", true);

                $.ajax({
                    type: "POST",
                    url: "userpage.php",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response){
                        var res = $.parseJSON(response);
                        if(res.status == 200){

                            $('#createTermin')[0].reset();
                            $('#terminForm').load(location.href +  " #terminForm");
                        }
                    }
                });
            });
        */
 
        function showFullText(event) {
            // Get the input field and its value (description from PHP)
            var inputField = event.target;
            var inputValue = inputField.value;

            // Get the hover text div and set its content to the input value (description)
            var hoverDiv = document.getElementById("hoverText");
            hoverDiv.innerText = inputValue;

            // Position the hover text div near the input field
            var rect = inputField.getBoundingClientRect();
            hoverDiv.style.top = (rect.bottom + window.scrollY) + "px";
            hoverDiv.style.left = rect.left + "px";

            // Show the hover text div
            hoverDiv.style.display = "block";

            // Hide the hover text when the mouse leaves the input field
            inputField.onmouseout = function() {
                hoverDiv.style.display = "none";
            };
        }
    </script>
</head>
<body>        
<!-- Logged in as and logout button -->
    <div id="login-session" ><b>Logged in as:  </b><?php echo htmlspecialchars($_SESSION["firstname"] . ' ' . $_SESSION["lastname"]); ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
        <input type="submit" id="logoutButton" name="logout" value="Log out">
        </form>
    </div>
<!-- Hello and weather -->
    <div id="div2">
        <p style="font-size: 30px; font-weight: bold; text-align: center">Hello, <?php echo htmlspecialchars($_SESSION["firstname"]); ?>!</p>

        <?php
            if(isset($weather['current'])){
        ?>

        <p>In place where you live - 
            <b>(<?php echo htmlspecialchars($_SESSION["place"]); ?>)</b>
            is right now <b><?php echo $temperature ?></b>°.
            <br>Date and Time: <b><?php echo $dateandtime?></b>
        </p>

        <?php 
        } else{
        ?>
        
        <p>We couldn't find any informations about weather in place where you live.</p>

        <?php 
        }
        ?>
        <?php 
        if($_SESSION["username"] == "admin"){ 
        ?>    
        <p>Edit Users:    <a href="crud.php"><button id="editButton">Edit</button></a></p>

        <?php 
        } 
        ?> 
<!-- Reset your password -->
        <form id="changePassForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
            <table class="table">
                    <td><h1>Reset your Password</h1></td>
                <tr>
                    <td><label for="password">New Password:</label></td>
                    <td><input type="password" id="password" name="password" required></td>
                </tr>
                <tr>
                    <td><label for="repeatpassword">Repeat New Password:</label></td>
                    <td><input type="password" id="repeatpassword" name="repeatpassword" required></td>
                </tr>
                    <td><input type="submit" id="changepasswordButton" name="changepassword" value="Change Password"></td>
            </table><br>            
        </form>

<!-- Delete my account button -->
        <?php if($_SESSION['username'] != "admin"){?>
        <form method="POST">
            <input type="hidden" name="deleteAcc" value="1">
            <button id="deleteButton" type="submit" onclick="return confirm('Are you sure you want to delete this account?')">Delete my account</button>
        </form>
        <?php } ?>
    </div>
<!-- Create a termin -->
    <div id="terminForm">
            <h2>Create a Termin</h2>

            <form id="createTerminForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
                <label for="termin_date">Date:</label>
                <input type="date" id="termin_date" name="termin_date" required>
                <label for="termin_time">Time:</label>
                <input type="time" id="termin_time" name="termin_time" required>
                <label for="description">Description:</label>
                <input type="text" id="description" name="description">                
                <br><br>
                <label for="zugriffUser">Enter a username of a user who can see your termins:</label>
                <input type="text" id="zugriffUser" name="zugriffUser">
                <button type="submit" id="createTermin" name="create_termin">Create Termin</button>
            </form>
<!-- Your termins -->
            <h2>Your Termins:</h2>
            <table class="termin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Description</th>
                        <th>Zugriff hat</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($termins as $termin): ?>
                        <tr>
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                                <input type="hidden" name="id" value="<?php echo $termin['id'] ?>">
                                <td><input type="date" name="termin_date" value="<?php echo $termin['termin_date'] ?>" required></td>
                                <td><input type="time" name="termin_time" value="<?php echo $termin['termin_time'] ?>" required></td>
                                <td><input type="text" id="myInput" name="description" value="<?php echo $termin['description'] ?>" onMouseOver="showFullText(event)"></td>                                
                                <td><input type="text" name="zugriffUser" value="<?php echo $termin['zugriffUser'] ?>" readonly></td>
                                <td>
                                    <button type="submit" name="update_termin">Update</button>
                                    <button type="submit" name="delete_termin" onclick="return confirm('Are you sure you want to delete this termin?')">Delete</button>
                                </td>
                            </form>
                            
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
<!-- Termins from other users -->
            <h2>Termins from other users:</h2>
            <table class="termin-table">
                <thead>
                    <tr>
                        <th>Termin from</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Description</th>                                           
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($zugriffTermins as $termin): ?>
                        <tr>
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                                <input type="hidden" name="id" value="<?php echo $termin['id'] ?>">
                                <td><input type="text" name="username" value="<?php echo $termin['username'] ?>" readonly></td>
                                <td><input type="date" name="termin_date" value="<?php echo $termin['termin_date'] ?>" readonly></td>
                                <td><input type="time" name="termin_time" value="<?php echo $termin['termin_time'] ?>" readonly></td>
                                <td><input type="text" id="myInput" name="description" onMouseOver="showFullText(event)" value="<?php echo $termin['description'] ?>"></td>                                
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div id="hoverText"></div>
    </body>
    </html>


