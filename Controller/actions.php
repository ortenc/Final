<?php

require '../Model/database.php';
include 'functions.php';

session_start();



    // Register user in database for the first time  action code



if ($_POST['action'] == "register") {

    // Escaping the strings from the database fields we need

    $txtname = mysqli_real_escape_string($conn, $_POST['fname']);
    $txtsurname = mysqli_real_escape_string($conn, $_POST['lname']);
    $txtemail = mysqli_real_escape_string($conn, $_POST['email']);
    $txtgender = mysqli_real_escape_string($conn, $_POST['gender']);
    $txtpassword1 = mysqli_real_escape_string($conn, $_POST['password1']);

    $number = preg_match('@[0-9]@', $txtpassword1);
    $uppercase = preg_match('@[A-Z]@', $txtpassword1);
    $lowercase = preg_match('@[a-z]@', $txtpassword1);
    $specialChars = preg_match('@[^\w]@', $txtpassword1);

    if(strlen($txtpassword1) < 8 || !$number || !$uppercase || !$lowercase || !$specialChars) {
        echo json_encode(array("code" => "404", "message" => "Password must be at least 8 characters in length and must contain at least one number, one upper case letter, one lower case letter and one special character."));
        exit;
    }
    $txtpassword2 = mysqli_real_escape_string($conn, $_POST['password2']);

    if($txtpassword1!=$txtpassword2){
        echo json_encode(array("code" => "404", "message" => "Password fields are not the same"));
        exit;
    }
    $hash = password_hash($txtpassword1, PASSWORD_DEFAULT); // hashing the password with the default algorithm

    //Return error code if one of the fields is empty

    if (empty($txtname)) {
        echo json_encode(array("code" => "404", "message" => "Name cannot be empty!"));
        exit;
    }
    if (empty($txtsurname)) {
        echo json_encode(array("code" => "404", "message" => "Surname cannot be empty!"));
        exit;
    }
    if (empty($txtemail)) {
        echo json_encode(array("code" => "404", "message" => "Name cannot be empty!"));
        exit;
    }
    if (empty($txtgender)) {
        echo json_encode(array("code" => "404", "message" => "Gender cannot be empty!"));
        exit;
    }
    if (empty($txtpassword1)) {
        echo json_encode(array("code" => "404", "message" => "Password cannot be empty!"));
        exit;
    }
    // check if e-mail address is well-formed
    if (!filter_var($txtemail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(array("code" => "404", "message" => "Email is not correct"));
        exit;
    }

    $query_check = "SELECT id FROM users WHERE email='$txtemail'";
    $result_check = mysqli_query($conn, $query_check);
    $numRows = mysqli_num_rows($result_check);

    if ($numRows > 0) {
        echo json_encode(array("code" => "404", "message" => "This email already exist in our system!"));
        exit;
    }

    // database insert SQL code

    $query_insert = "INSERT INTO users
      SET name = '$txtname',
         surname = '$txtsurname',
         email = '$txtemail',
         gender = '$txtgender',
         password = '$hash',
         role = 'User'";

    $result_insert = mysqli_query($conn, $query_insert);

    if ($result_insert) {
        echo json_encode(array("code" => "200", "message" => "Success"));
        exit;
    }
    else{
        echo json_encode(array("code" => "404", "message" => "Error"));
    }



    // Register user in database for the first time  action code



    // Log in user into the webapage action code



} elseif ($_POST['action'] == "login") {

    $txtpassword = $_POST['password'];
    $txtemail = mysqli_real_escape_string($conn, $_POST['email']);

    if (empty($txtemail)) {
        echo json_encode(array("code" => "404", "message" => "Email cannot be empty!"));
        exit;
    }
    if (empty($txtpassword)) {
        echo json_encode(array("code" => "404", "message" => "Password cannot be empty!"));
        exit;
    }

    $query_select = "SELECT * FROM users WHERE email='$txtemail'";
    $result = mysqli_query($conn, $query_select);
    $check = mysqli_fetch_assoc($result);

    if (password_verify($txtpassword, $check['password'])) {
        $_SESSION['id'] = $check['id'];
        $_SESSION['name'] = $check['name'];
        $sub_query_insert = "INSERT INTO login_details 
                      SET user_id = '" . $check['id'] . "'
                      ";
        $sub_result = mysqli_query($conn, $sub_query_insert);
        $_SESSION['login_details_id'] = mysqli_insert_id($conn);

    if ($check['role'] == "User") {
            echo json_encode(array("code" => "201", "message" => "Success"));
            exit;
        }
    if ($check['role'] == "Admin") {
            echo json_encode(array("code" => "200", "message" => "Success"));
            exit;
        }
    }
    else{
        echo json_encode(array("code" => "404", "message" => "Password incorrect!"));
        exit;
    }


    // Log in user into the webapage action code


    // Update user details in the database from admin panel action code



} elseif ($_POST['action'] == "update") {

    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $fname = mysqli_real_escape_string($conn, $_POST['name']);
    $lname = mysqli_real_escape_string($conn, $_POST['surname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $query_update = "UPDATE users
      SET name = '$fname',
          surname = '$lname',
          email = '$email',
          role = '$role'
         WHERE id = '$id'";


    $result_check = mysqli_query($conn, $query_update);

    if ($result_check) {
        echo json_encode(array("code" => "200", "message" => "Success"));
        exit;
    }
    else{
        echo json_encode(array("code" => "404", "message" => "Error"));
        exit;
    }


    // Update user details in the database from admin panel action code


    // Delete user from database from admin panel action code


} elseif ($_POST['action'] == "erase") {

    $id = mysqli_real_escape_string($conn, $_POST['id']);

    $query_delete = "delete from users where id = '$id'";

    $result_check = mysqli_query($conn, $query_delete);

    if ($result_check) {
        echo json_encode(array("code" => "200", "message" => "Success"));
        exit;
    }
    else{
        echo json_encode(array("code" => "404", "message" => "Error"));
        exit;
    }


    // Delete user from database from admin panel action code


    // Personal user details update from userpage panel code



} elseif ($_POST['action'] == "userupdate") {

    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $fname = mysqli_real_escape_string($conn, $_POST['name']);
    $lname = mysqli_real_escape_string($conn, $_POST['surname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $query_update_users = "UPDATE users
      SET name = '$fname',
          surname = '$lname',
          email = '$email'
         WHERE id = '$id'";

    $result_check = mysqli_query($conn, $query_update_users);

    if ($result_check) {
        echo json_encode(array("code" => "200", "message" => "Success"));
        exit;
    } else {
        echo json_encode(array("code" => "404", "message" => "Error"));
        exit;
    }


    // Personal user details update from userpage panel code


    // Get chat history when the pop up is initiated from start chat button click



} elseif ($_POST['action'] == "get_chat_history") {

    $to_user_id = mysqli_real_escape_string($conn, $_POST['to_user_id']);
    $from_user_id = mysqli_real_escape_string($conn, $_SESSION['id']);
    $data = fetch_user_chat_history($from_user_id, $to_user_id, $conn);

    echo json_encode(array("code" => "200", "message" => "Success", 'chat' => $data));


}


    // Get chat history when the pop up is initiated from start chat button click



    // Update last activity time of the user



elseif ($_POST['action']== "update_last_activity") {

    $query = "UPDATE login_details 
              SET last_activity = now() 
              WHERE login_details_id = '" . $_SESSION["login_details_id"] . "'
              ";

    $statement = mysqli_query($conn, $query);
}



    // Update last activity time of the user



   // Insert chat from user to user with message body



elseif ($_POST['action']== "insert_chat") {

    $to_user_id = mysqli_real_escape_string($conn, $_POST['to_user_id']);
    $from_user_id = mysqli_real_escape_string($conn, $_SESSION['id']);
    $chat_message = mysqli_real_escape_string($conn, $_POST['chat_message']);
    $status = 1;

    $query = "
INSERT INTO chat_message 
SET to_user_id = '$to_user_id',
    from_user_id = '$from_user_id',
    chat_message = '$chat_message',
    status = '1'";

    $result = mysqli_query($conn, $query);

    if ($result) {
        echo fetch_user_chat_history($_SESSION['id'], $_POST['to_user_id'], $conn);
    }
}



// Insert chat from user to user with message body
?>