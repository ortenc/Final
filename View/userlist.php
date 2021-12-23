<?php
include('../View/header.php');
include('../Controller/functions.php');
?>
<?php
session_start();

  if(!$_SESSION['id'])
  {
      header('location : login.php');
}

require('../Model/database.php');
$query = "SELECT * FROM users WHERE 1=1";

if(!empty($_POST['filterByGender'])) {
    $query .= " AND gender = '".mysqli_real_escape_string($conn, $_POST['filterByGender'])."'";
}

if(!empty($_POST['filterByName'])) {
    $data_name = explode(' ', $_POST['filterByName']);
    $query .= " AND name = '".mysqli_real_escape_string($conn, $data_name[0])."' AND surname ='".mysqli_real_escape_string($conn, $data_name[1])."'";
}


$result = mysqli_query($conn,$query);
$users = [];
while($row = mysqli_fetch_assoc($result)){
  $users[$row['id']]['id']= $row['id'];
  $users[$row['id']]['name']= $row['name'];
  $users[$row['id']]['surname'] = $row['surname'];
  $users[$row['id']]['email'] = $row['email'];
  $users[$row['id']]['gender'] = $row['gender'];
  $users[$row['id']]['role'] = $row['role'];
}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../Styles/main.css">
<style>
</style>
</head>
<body>
<div style="text-align: center">

    <a href="../Model/logout.php" title="logout">Log Out</a>
    <br>
    <br>
    <a href="Admin_User_Page.php" title="Profile">Profile</a>

</div>
<nav class="navbar navbar-expand-lg navbar-light bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand ">User List</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">

        </div>
        <div class="col-sm-4">
            <label>Name</label>
            <select class="form-control" name="filterByName" id="filterByName" >
                <?php if(!empty($_POST['filterByName'])) { ?>
                    <option value="<?=$_POST['filterByName']?>"><?=$_POST['filterByName']?></option>
                <?php } ?>
                <option></option>
                <?php
                $query_users = "SELECT id, name, surname FROM users";
                $result_users = mysqli_query($conn, $query_users);
                while($row = mysqli_fetch_assoc($result_users)) { ?>
                    <option value="<?= $row['name'] . " " . $row['surname'] ?>"><?= $row['name'] . " " . $row['surname'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-sm-4">
            <label>Gender</label>
            <select id='searchByGender' name="filterByGender" class="form-control">
                <?php if(!empty($_POST['filterByGender'])) { ?>
                    <option value="<?= $_POST['filterByGender'] ?>"><?= $_POST['filterByGender'] ?></option>
                <?php } ?>
                <option value=''>-- Select Gender--</option>
                <option value='male'>Male</option>
                <option value='female'>Female</option>
            </select>
        </div>
    </div>
</nav>

<form method="post"
    <div class="row">
        <div class="col-sm-4">
            <label>Name</label>
            <select class="form-control" name="filterByName" id="filterByName" >
                <?php if(!empty($_POST['filterByName'])) { ?>
                <option value="<?=$_POST['filterByName']?>"><?=$_POST['filterByName']?></option>
                <?php } ?>
                <option></option>
                <?php
                $query_users = "SELECT id, name, surname FROM users";
                $result_users = mysqli_query($conn, $query_users);
                while($row = mysqli_fetch_assoc($result_users)) { ?>
                    <option value="<?= $row['name'] . " " . $row['surname'] ?>"><?= $row['name'] . " " . $row['surname'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-sm-4">
            <label>Gender</label>
            <select id='searchByGender' name="filterByGender" class="form-control">
                <?php if(!empty($_POST['filterByGender'])) { ?>
                    <option value="<?= $_POST['filterByGender'] ?>"><?= $_POST['filterByGender'] ?></option>
                    <?php } ?>
                <option value=''>-- Select Gender--</option>
                <option value='male'>Male</option>
                <option value='female'>Female</option>
            </select>
        </div>
    </div>
    <div class="row" style="margin-top: 15px;">
        <div class="col-sm-12">
            <input name="submit" type="submit" class="btn btn-primary" value="Filter">
        </div>
    </div>
</form>
</body>
<table id="users" class="display dataTable">
  <thead>
    <tr>
    <th>Name</th>
    <th>Surname</th>
    <th>Email</th>
    <th>Gender</th>
    <th>Role</th>
    <th>Action</th>
  </tr>
  </thead>
  <tbody>
   <?php foreach($users as $user){ ?>
   <tr>
         <td>
             <input type="text" id="fname_<?= $user['id']?>" name="fname" value="<?= $user['name']?>">
         </td>
         <td>
             <input type="text" id="lname_<?= $user['id']?>" name="lname" value="<?= $user['surname']?>">
         </td>
         <td>
             <input type="text" id="email_<?= $user['id']?>" name="email" value="<?= $user['email']?>">
         </td>
         <td>
             <?= $user['gender']?>
         </td>
     <td>
         <select class="form-control" name="role" id="role_<?= $user['id']?>">
             <option value="<?= $user['role']?>"><?= $user['role']?></option>
             <option value="Admin">Admin</option>
             <option value="User">User</option>
         </select>
     </td>
       <td style="white-space: nowrap">
           <button class="btn btn-primary w-50" name="update" onclick="update('<?= $user['id']?>')">Update</button>
           <button class="btn btn-primary w-50" name="erase" onclick="erase('<?= $user['id']?>')">Delete</button>
       </td>
   </tr>
   <?php } ?>


  </tbody>

  

</table>
</html>
<script>

    $.noConflict();
    function isEmpty(value) {
        return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
    }

    function update(id) {
        var user_id = id;
        var fname = $("#fname_"+user_id).val();
        var lname = $("#lname_"+user_id).val();
        var email = $("#email_"+user_id).val();
        var role = $("#role_"+user_id).val();

        var data = {
            "action": "update",
            "id": user_id,
            "name": fname,
            "surname": lname,
            "email": email,
            "role": role

        };

        $.ajax({
            url: "actions.php",
            method: 'POST',
            type: 'POST',
            data: data,
            cache: false,
            success: function(result){
                var response = JSON.parse(result);

                if (response.code == 200) {
                    window.location.href = "userlist.php";
                }

                if (response.code == 404) {
                    Swal.fire(response.message);
                }



            }
        });
    }
    function erase(id) {
        var user_id = id;

        var data = {
            "action": "erase",
            "id": user_id

        };

        $.ajax({
            url: "actions.php",
            method: 'POST',
            type: 'POST',
            data: data,
            cache: false,
            success: function (result) {
                var response = JSON.parse(result);

                if (response.code == 200) {
                    window.location.href = "userlist.php";
                }

                if (response.code == 404) {
                    Swal.fire(response.message);
                }


            }
        });
    }

</script>
<?php
include('footer.php');
?>


