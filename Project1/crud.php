<?php
    include("database.php");
    session_start();

    $sql = 'SELECT id, firstname, lastname FROM registeredusers';
    $result = mysqli_query($conn,$sql);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
// Delete
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete-user-id"])){
        $userIdToDelete = intval($_POST["delete-user-id"]);            
        $sql_delete = 'DELETE FROM registeredusers WHERE id = ?';
        $stmt = mysqli_prepare($conn, $sql_delete);                

        if($userIdToDelete != 1){

            if($stmt){
                mysqli_stmt_bind_param($stmt, 'i', $userIdToDelete);
                $result = mysqli_stmt_execute($stmt);

                
                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        echo "<div id='successful'>User with ID $userIdToDelete deleted successfully.</div>";
                    } else {
                        echo '<script>alert("User with that ID doesnt exist");</script>';
                    }
        
                    mysqli_stmt_close($stmt); 
                    
                    //$sql_reset = 'ALTER TABLE registeredusers AUTO_INCREMENT = 1';
                    //mysqli_query($conn, $sql_reset);
            }                              
        }   else{
                echo "<div id='successful'>You cannot perform actions on Admin account.</div>";
            }          
    }
// Read
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["read-user-id"])){
        $userIdToRead = intval($_POST["read-user-id"]);
            
            $sql_read = 'SELECT * FROM registeredusers WHERE id = ?';
            $stmt = mysqli_prepare($conn, $sql_read);

            if($stmt){
                mysqli_stmt_bind_param($stmt, 'i', $userIdToRead);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    
                    $readedUser = mysqli_fetch_assoc($result);      
                    echo "<table class='container' border='1' cellpadding='10'>";
                    echo "<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Username</th><th>Email</th><th>Place</th></tr>";
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($readedUser['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($readedUser['firstname']) . "</td>";
                    echo "<td>" . htmlspecialchars($readedUser['lastname']) . "</td>";
                    echo "<td>" . htmlspecialchars($readedUser['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($readedUser['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($readedUser['place']) . "</td>";
                    echo "</tr>";
                    echo "</table>";                      
                } else{
                    echo '<script>alert("User with that ID doesnt exist");</script>';
                }
            }
        mysqli_stmt_close($stmt);
    }
// Create
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create"])){
        echo '<form class="create" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">
        <h1><b>Create a user</b></h1>
        <table>
                <tr>
                    <td id="td"><label for="firstname">Firstname:</label></td>
                    <td id="td"><input type="text" id="firstname" name="firstname" required pattern="[a-zA-Z]+" placeholder="Jon"></td>
                </tr>
                <tr>
                    <td id="td"><label for="lastname">Lastname:</label></td>
                    <td id="td"><input type="text" id="lastname" name="lastname" required pattern="[a-zA-Z]+" placeholder="Kennedy"></td>
                </tr>
                <tr>
                    <td id="td"><label for="username">Username:</label></td>
                    <td id="td"><input type="text" id="username" name="username" required placeholder="Jon123"></td>
                </tr>
                <tr>
                    <td id="td"><label for="email">Email:</label></td>
                    <td id="td"><input type="text" id="email" name="email" required placeholder="marc99@gmail.com"></td>
                </tr>
                <tr>
                    <td id="td"><label for="password">Password:</label></td>
                    <td id="td"><input type="password" id="password" name="password" required minlength="6"></td>
                </tr>
                <tr>
                    <td id="td"><label for="repeatpassword">Repeat Password:</label></td>
                    <td id="td"><input type="password" id="repeatpassword" name="repeatpassword" required></td>
                </tr>
                <tr>
                    <td id="td"><label for="place">Place of living:</label></td>
                    <td id="td"><input type="text" id="place" name="place" required placeholder="City" pattern="[a-zA-Z]+"></td>
                </tr>
                <tr>
                    <td id="td" colspan="2"><input type="submit" id="createNew" name="createNew" value="Create new user"></td>  
                </tr>
        </table>       
    </form>';
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["createNew"])) {

        $firstname = $_POST["firstname"];
        $lastname = $_POST["lastname"];  
        $username = $_POST["username"]; 
        $email = $_POST["email"];
        $place = $_POST["place"];
        $password = $_POST["password"]; 
        $repatpassword = $_POST["repeatpassword"]; 
        $salt = random_bytes(16);
        $salt = bin2hex($salt);

        $message1 = "This is invalid Email";
        $message2 = "Username or Email already taken";
        $message3 = "Your repeated password is wrong";
    
        $sql_check = "SELECT * FROM registeredusers WHERE username = ? OR email = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);

        if($stmt_check) {
            mysqli_stmt_bind_param($stmt_check, 'ss', $username, $email);
            mysqli_stmt_execute($stmt_check);
            $result_check = mysqli_stmt_get_result($stmt_check);

            if(mysqli_num_rows($result_check) > 0) {
                echo "<script type='text/javascript'>alert('$message2');</script>";
                mysqli_stmt_close($stmt_check);
            } else { 
                mysqli_stmt_close($stmt_check);

                $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
                
                if (filter_var($emailB, FILTER_VALIDATE_EMAIL) === false || $emailB != $email) {
                    echo "<script type='text/javascript'>alert('Invalid email');</script>";
                } else if ($repatpassword != $password) {
                    echo "<script type='text/javascript'>alert('$message3');</script>";
                } else {
                    $hash1 = password_hash($password, PASSWORD_DEFAULT); 
                    $sql = "INSERT INTO registeredusers (firstname, lastname, username, email, password, place, salt)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $sql);
    
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, 'sssssss', $firstname, $lastname, $username, $email, $hash1, $place, $salt);

                    if (mysqli_stmt_execute($stmt)) {
                        echo "<div id='successful'>New user created successfully.</div>";
                    } else {
                        echo "<script type='text/javascript'>alert('$message2');</script>";
                    }
                    }
                }
            }
        }
    }   
    
    

// Update 
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update-user-id"])) {
        $userIdToUpdate = intval($_POST["update-user-id"]);

        $sql_read = 'SELECT * FROM registeredusers WHERE id = ?';
        $stmt = mysqli_prepare($conn, $sql_read);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $userIdToUpdate);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                $readedUser = mysqli_fetch_assoc($result);

                // Display the form with hidden user ID
                echo '<form class="create" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">
                    <h1><b>Update a user</b></h1>
                    <table>
                        <tr>
                            <td id="td"><label for="firstname">Firstname:</label></td>
                            <td id="td"><input type="text" id="firstname" name="firstname" required pattern="[a-zA-Z]+" value="' . htmlspecialchars($readedUser['firstname']) . '"></td>
                        </tr>
                        <tr>
                            <td id="td"><label for="lastname">Lastname:</label></td>
                            <td id="td"><input type="text" id="lastname" name="lastname" required pattern="[a-zA-Z]+" value="' . htmlspecialchars($readedUser['lastname']) . '"></td>
                        </tr>
                        <tr>
                            <td id="td"><label for="username">Username:</label></td>
                            <td id="td"><input type="text" id="username" name="username" required value="' . htmlspecialchars($readedUser['username']) . '"></td>
                        </tr>
                        <tr>
                            <td id="td"><label for="email">Email:</label></td>
                            <td id="td"><input type="text" id="email" name="email" required value="' . htmlspecialchars($readedUser['email']) . '"></td>
                        </tr>
                        <tr>
                            <td id="td"><label for="password">Password:</label></td>
                            <td id="td"><input type="password" id="password" name="password" minlength="6"></td>
                        </tr>
                        <tr>
                            <td id="td"><label for="repeatpassword">Repeat Password:</label></td>
                            <td id="td"><input type="password" id="repeatpassword" name="repeatpassword"></td>
                        </tr>
                        <tr>
                            <td id="td"><label for="place">Place of living:</label></td>
                            <td id="td"><input type="text" id="place" name="place" required pattern="[a-zA-Z]+" value="' . htmlspecialchars($readedUser['place']) . '"></td>
                        </tr>
                        <tr>
                            <td id="td" colspan="2">
                                <input type="hidden" name="update-user-id" value="' . htmlspecialchars($userIdToUpdate) . '">
                                <input type="submit" id="createNew" name="updateUser" value="Update user">
                            </td>
                        </tr>
                    </table>
                </form>';
            } else {
                echo '<div id="successful">User with that ID doesn\'t exist.</div>';
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["updateUser"])) {
        if (isset($_POST["update-user-id"])) {
            $id = intval($_POST["update-user-id"]);
            $firstname = $_POST["firstname"];
            $lastname = $_POST["lastname"];
            $username = $_POST["username"];
            $email = $_POST["email"];
            $place = $_POST["place"];
            $password = $_POST["password"];
            $repeatPassword = $_POST["repeatpassword"];

            $message1 = "Invalid email format.";
            $message2 = "Username or email already taken.";
            $message3 = "Passwords do not match.";
            $message4 = "You cannot perform actions on the Admin account.";

            if ($id != 1) {
                $sql_check = "SELECT * FROM registeredusers WHERE (username = ? OR email = ?) AND id != ?";
                $stmt_check = mysqli_prepare($conn, $sql_check);

                if ($stmt_check) {
                    mysqli_stmt_bind_param($stmt_check, 'ssi', $username, $email, $id);
                    mysqli_stmt_execute($stmt_check);
                    $result_check = mysqli_stmt_get_result($stmt_check);

                    if (mysqli_num_rows($result_check) > 0) {
                        echo '<div id="successful">' . $message2 . '</div>';
                        mysqli_stmt_close($stmt_check);
                    } else {
                        mysqli_stmt_close($stmt_check);

                        $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);

                        if (filter_var($emailB, FILTER_VALIDATE_EMAIL) === false || $emailB != $email) {
                            echo '<div id="successful">' . htmlspecialchars($message1, ENT_QUOTES, 'UTF-8') . '</div>';
                            exit();
                        }

                        if ($repeatPassword === $password) {
                            $hash = password_hash($password, PASSWORD_DEFAULT);
                            $sql_update = 'UPDATE registeredusers SET firstname=?, lastname=?, username=?, email=?, password=?, place=? WHERE id=?';
                            $stmt = mysqli_prepare($conn, $sql_update);

                            if ($stmt) {
                                mysqli_stmt_bind_param($stmt, 'ssssssi', $firstname, $lastname, $username, $email, $hash, $place, $id);
                                $result = mysqli_stmt_execute($stmt);

                                if ($result) {
                                    echo '<div id="successful">User with ID ' . htmlspecialchars($id) . ' updated successfully.</div>';
                                } else {
                                    echo '<div id="successful">' . $message2 . '</div>';
                                }
                                mysqli_stmt_close($stmt);
                            }
                        } else {
                            echo '<div id="successful">' . $message3 . '</div>';
                        }
                    }
                }
            } else {
                echo '<div id="successful">' . $message4 . '</div>';
            }
        } else {
            echo '<div id="successful">Update user ID is missing.</div>';
        }
    }
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Table</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body{
            margin: 0;
            height: 100vh;
            position:relative;
            background-color: #F1DCB6;
        }
        #allUsersTable{
            background-color: white;
            position:absolute;
            top:150px;
            left:200px;
            border: 5px solid black;
            border-radius: 4px;
            padding: 10px;
        }
        .container{
            position:absolute;
            top:500px;
            left:800px;
            width:800px;
        }
        table{
            width:100%;
            font-size: 25px;          
            background-color: lightgray;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 10px 30px;
            text-align: left;
        }
        th{
            font-size: 32px;        
        }
        button{            
            background-color: #C17969;
            color:white;
            border: 3px solid black;
            border-radius: 4px;
            height:40px;
            cursor:pointer;
        }
        
        #back-button{
            position:absolute;
            top:90px;
            left:200px;
            background-color: #d4b644;
            color:black;
            font-weight: bold;
            font-size: 20px;
        }
        #ajax-button{
            position:absolute;
            top:90px;
            left:460px;
            background-color: #d4b644;
            color:black;
            font-weight: bold;
            font-size: 20px;
        }
        #reload-button{
            position:absolute;
            top:90px;
            left:607px;
            background-color: #d4b644;
            color:black;
            font-weight: bold;
            font-size: 20px;
        }
        #form{
            position:absolute;
            top:200px;
            left:800px;            
        }   
        #createButton{
            height:35px;
            border: 3px solid black;
            border-radius: 4px;
            background-color: #7aed21;
            color:black;
            font-size:20px;
            font-weight: bold;
            cursor:pointer;
        }
        #readButton{
            height:35px;
            border: 3px solid black;
            border-radius: 4px;
            background-color: #e9f035;
            color:black;
            font-size:20px;
            font-weight: bold;
            cursor:pointer;
        }
        #updateButton{
            height:35px;
            border: 3px solid black;
            border-radius: 4px;
            background-color: #2ff5e8;
            color:black;
            font-size:20px;
            font-weight: bold;
            cursor:pointer;
        } 
        #deleteButton{
            height:35px;
            border: 3px solid black;
            border-radius: 4px;
            background-color: #ff5454;
            color:black;
            font-size:20px;
            font-weight: bold;
            cursor:pointer;
        }        
        .create{
            width:25%;
            border: 5px solid #ff7f7f;
            padding: 10px;
            text-align: center;
            background-color: white;
            border-radius: 8px;
            position:absolute;
            top:150px;
            left:1000px;
        }
        input[type="text"], input[type="password"]{
            padding: 4px;
            margin: 5px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        #td{
            background-color:#d0f5da;
            font-size:22px;
            padding: 5px;
        }
        #createNew{
            height:30px;
            border: 1px solid black;
            border-radius: 4px;
            background-color: #2a5585;
            color:white;
            font-size:15px;  
            font-weight: bold;          
            cursor:pointer;
        }
        #successful{
            position:absolute;
            top:-5px;
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
    
    </style>
    <script>  
        function deleteUser(){
            var userid = prompt("Enter the ID of the user you want to delete:");

            if (userid === null) {
                return;
            } else if (!isNaN(userid) && userid.trim() !== "") {
        
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>';

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete-user-id';
                input.value = userid;
                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            } else {
        
                alert("Invalid ID entered.");
            }       
        }  
        function readUser(){
            var userid = prompt("Enter the ID of the user you want to read:");

            if (userid === null) {
                return;
            } else if (userid !== null && !isNaN(userid)) {
                
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>';

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'read-user-id';
                input.value = userid;
                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert("Invalid ID entered.");
            }
        }     
        function updateUser(){
            var userid = prompt("Enter the ID of the user you want to update:");

            if (userid === null) {
                return;
            } else if (userid !== null && !isNaN(userid)) {
                
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>';

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'update-user-id';
                input.value = userid;
                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert("Invalid ID entered.");
            }
        }  
        function reloadPage(){
            window.location.href = 'crud.php';
        }
        
    </script>
</head>
<body>
    <div id="allUsersTable">
        <h1>Users:</h1>
        <table>
            <tr>
                <th>#id</th>
                <th>First Name</th>
                <th>Last Name</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                    <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>  

    <a href="userpage.php"><button id="back-button">Back to your Userpage</button></a>
    <a href="ajaxcrud.php"><button id="ajax-button">Ajax CRUD</button></a>
    <button onclick="reloadPage()" id="reload-button">Refresh Page</button>

    <form id="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
        <input type="submit" id="createButton" name="create" value="Create a user">
            <br><br>
        <input type="button" id="readButton" value="Read a user" onclick="readUser()">
            <br><br>
        <input type="button" id="updateButton" value="Update a user" onclick="updateUser()">        
            <br><br>
        <input type="button" id="deleteButton" value="Delete a user" onclick="deleteUser()">
    </form>  
</body>
</html>