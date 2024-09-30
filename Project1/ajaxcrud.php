<?php
    include("database.php");

    if(isset($_POST['delete_user']))
{

    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $query = "DELETE FROM registeredusers WHERE id='$user_id'";
    $query_run = mysqli_query($conn, $query);

    if($query_run)
    {
        $res = [
            'status' => 200, 
            'message' => 'User Deleted Successfully'
        ]; 
        echo json_encode($res);
        return false;
    }
    else
    {
        $res = [
            'status' => 500, 
            'message' => 'User not deleted'
        ]; 
        echo json_encode($res);
        return false;
    }
}

if(isset($_POST['update_user']))
{
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);

    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $place = mysqli_real_escape_string($conn, $_POST['place']);

    if(empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($place))
    {
        $res = [
            'status' => 422, 
            'message' => 'All fields are mandatory'
        ]; 
        echo json_encode($res);
        return false;
    }

    $query = "UPDATE registeredusers SET firstname='$firstname', lastname='$lastname', username='$username', email='$email', place='$place' 
                WHERE id='$user_id'";
    $query_run = mysqli_query($conn, $query);

    if($query_run)
    {
        $res = [
            'status' => 200, 
            'message' => 'User Updated Successfully'
        ]; 
        echo json_encode($res);
        return false;
    }
    else
    {
        $res = [
            'status' => 500, 
            'message' => 'User not updated'
        ]; 
        echo json_encode($res);
        return false;
    }
}

if(isset($_GET['user_id']))
{  
    $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);

    $query = "SELECT * FROM registeredusers WHERE id='$user_id'";
    $query_run = mysqli_query($conn, $query);

    if(mysqli_num_rows($query_run) == 1)
    {
        $user = mysqli_fetch_array($query_run);
        
        $res = [
            'status' => 200, 
            'message' => 'User Fetch Successfully by id',
            'data' => $user
        ]; 
        echo json_encode($res);
        exit();
    }
    else
    {
        $res = [
            'status' => 404, 
            'message' => 'User id not found'
        ]; 
        echo json_encode($res);
        exit();
    }
}

if(isset($_POST['save_user']))
{
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $place = mysqli_real_escape_string($conn, $_POST['place']);

    if(empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($place))
    {
        $res = [
            'status' => 422, 
            'message' => 'All fields are mandatory'
        ]; 
        echo json_encode($res);
        return false;
    }

    $query = "INSERT INTO registeredusers (firstname, lastname, username, email, place) VALUES ('$firstname', '$lastname', '$username', '$email', '$place')";
    $query_run = mysqli_query($conn, $query);

    if($query_run)
    {
        $res = [
            'status' => 200, 
            'message' => 'User Created Successfully'
        ]; 
        echo json_encode($res);
        return false;
    }
    else
    {
        $res = [
            'status' => 500, 
            'message' => 'User not created'
        ]; 
        echo json_encode($res);
        return false;
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
  <body>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>


<!-- Add User Modal--> 
    <div class="modal fade" id="userAddModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="saveUser">
                    <div class="modal-body">

                        <div class="alert alert-warning d-none" id="errorMessage"></div>

                        <div class="mb-3">
                            <label for="">Firstname</label>
                            <input type="text" name="firstname" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="">Lastname</label>
                            <input type="text" name="lastname" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="">Username</label>
                            <input type="text" name="username" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="">Email</label>
                            <input type="text" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="">Place</label>
                            <input type="text" name="place" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Edit User Modal--> 
    <div class="modal fade" id="userEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="updateUser">
                    <div class="modal-body">

                        <div class="alert alert-warning d-none" id="errorMessageUpdate"></div>

                        <input type="hidden" name="user_id" id="user_id">
                        
                        <div class="mb-3">
                            <label for="">Firstname</label>
                            <input type="text" name="firstname" id="firstname"  class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="">Lastname</label>
                            <input type="text" name="lastname" id="lastname" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="">Username</label>
                            <input type="text" name="username" id="username" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="">Email</label>
                            <input type="text" name="email" id="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="">Place</label>
                            <input type="text" name="place" id="place" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- View User Modal--> 

    <div class="modal fade" id="userViewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">View User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="modal-body">

                        <input type="hidden" name="user_id" id="user_id">
                        
                        <div class="mb-3">
                            <label for="">Firstname</label>
                            <p id="view_firstname" class="form-control"></p> 
                        </div>
                        <div class="mb-3">
                            <label for="">Lastname</label>
                            <p id="view_lastname" class="form-control"></p>
                        </div>
                        <div class="mb-3">
                            <label for="">Username</label>
                            <p id="view_username" class="form-control"></p>                        
                        </div>
                        <div class="mb-3">
                            <label for="">Email</label>
                            <p id="view_email" class="form-control"></p> 
                        </div>
                        <div class="mb-3">
                            <label for="">Place</label>
                            <p id="view_place" class="form-control"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>PHP Ajax CRUD
                            <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#userAddModal">
                                Add User
                            </button>
                            <a href="crud.php"><button class="btn btn-primary float-end">Normal CRUD</button></a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <table id="myTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Firstname</th>
                                    <th>Lastname</th>
                                    <th>Username</th>
                                    <th>Email</th>                                    
                                    <th>Place</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM registeredusers";
                                $query_run = mysqli_query($conn, $query);

                                if(mysqli_num_rows($query_run) > 0)
                                {
                                    foreach($query_run as $user)
                                    {
                                        ?>
                                        <tr>
                                            <td><?= $user['id']?></td>
                                            <td><?= $user['firstname']?></td>
                                            <td><?= $user['lastname']?></td>
                                            <td><?= $user['username']?></td>
                                            <td><?= $user['email']?></td>                                            
                                            <td><?= $user['place']?></td>
                                            <td>
                                                <button type="button" value="<?=$user['id'];?>" class="viewUserBtn btn btn-info">View</button>
                                                <button type="button" value="<?=$user['id'];?>" class="editUserBtn btn btn-success">Edit</button>
                                                <button type="button" value="<?=$user['id'];?>" class="deleteUserBtn btn btn-danger">Delete</button>
                                            </td>
                                        </tr>
                                        <?php

                                        
                                    }
                                }
                                ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>

        $(document).on('submit', '#saveUser', function(e){
            e.preventDefault();

            var formData = new FormData(this);
            formData.append("save_user", true);

            $.ajax({
                type: "POST",
                url: "ajaxcrud.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                    var res = $.parseJSON(response);
                    if(res.status == 422){
                        $('#errorMessage').removeClass('d-none');
                        $('#errorMessage').text(res.message);
                    } else if(res.status == 200){
                        $('#errorMessage').addClass('d-none');
                        $('#userAddModal').modal('hide');
                        $('#saveUser')[0].reset();

                        $('#myTable').load(location.href +  " #myTable");
                    }
                }
            });
        });

        $(document).on('click', '.editUserBtn', function(){
        
                var user_id = $(this).val();
                // alert(user_id);
                $.ajax({
                type: "GET",
                url: "ajaxcrud.php?user_id=" + user_id,
                success: function(response){

                        var res = jQuery.parseJSON(response);
                        if(res.status == 422){

                            alert(res.message);

                        } else if(res.status == 200){

                            $('#user_id').val(res.data.id);
                            $('#firstname').val(res.data.firstname);
                            $('#lastname').val(res.data.lastname);
                            $('#username').val(res.data.username);                          
                            $('#email').val(res.data.email);
                            $('#place').val(res.data.place);

                            $('#userEditModal').modal('show');
                            
                        }

                    }
                });
        });

        $(document).on('submit', '#updateUser', function(e){
            e.preventDefault();

            var formData = new FormData(this);
            formData.append("update_user", true);

            $.ajax({
                type: "POST",
                url: "ajaxcrud.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                    
                    var res = $.parseJSON(response);
                    if(res.status == 422){
                        $('#errorMessageUpdate').removeClass('d-none');
                        $('#errorMessageUpdate').text(res.message);
                    } else if(res.status == 200){
                        $('#errorMessageUpdate').addClass('d-none');
                        $('#userEditModal').modal('hide');
                        $('#updateUser')[0].reset();

                        $('#myTable').load(location.href +  " #myTable");
                    } 
                }
            });
        });

        $(document).on('click', '.viewUserBtn', function(){
        
            var user_id = $(this).val();
            // alert(user_id);
            $.ajax({
            type: "GET",
            url: "ajaxcrud.php?user_id=" + user_id,
            success: function(response){

                    var res = jQuery.parseJSON(response);
                    if(res.status == 422){

                        alert(res.message);

                    } else if(res.status == 200){

                        $('#view_firstname').text(res.data.firstname);
                        $('#view_lastname').text(res.data.lastname);
                        $('#view_username').text(res.data.username);                          
                        $('#view_email').text(res.data.email);
                        $('#view_place').text(res.data.place);

                        $('#userViewModal').modal('show');
                        
                    }

                }
            });
        });

        $(document).on('click', '.deleteUserBtn', function(e){

            e.preventDefault();

            if(confirm('Are you sure you want to delete a user?'))
            {
                var user_id = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "ajaxcrud.php",
                    data: {
                        'delete_user': true,
                        'user_id': user_id
                    },
                    success: function(response){

                        var res = $.parseJSON(response);
                        if(res.status == 500){

                            alert(res.message);
                        }else{
                            alert(res.message);
                            $('#myTable').load(location.href + " #myTable");
                        }
                    }
                });
            }
        });

</script>
  </body>
</html>

<?php
mysqli_close($conn);
?>