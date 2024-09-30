<?php
/*
include("database.php");
$conn = mysqli_connect("localhost", "root", "", "users");

if(isset($_POST['createTermin']))
{
    $termin_date = mysqli_real_escape_string($conn, $_POST['termin_date']);
    $termin_time = mysqli_real_escape_string($conn, $_POST['termin_time']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $zugriffUser = mysqli_real_escape_string($conn, $_POST['zugriffUser']);
    

    $query = "INSERT INTO termins (termin_date, termin_time, description, zugriffUser) VALUES ('$termin_date', '$termin_time', '$description', '$zugriffUser')";
    $query_run = mysqli_query($conn, $query);

    if($query_run)
    {
        $res = [
            'status' => 200, 
            'message' => 'Termin Created Successfully'
        ]; 
        echo json_encode($res);
        return false;
    }
    else
    {
        $res = [
            'status' => 500, 
            'message' => 'Termin not created'
        ]; 
        echo json_encode($res);
        return false;
    }
}

mysqli_close($conn);
*/
?>
