<?php
session_start();

if(!$_SESSION['id'])
{
    header('location : ../register/login.php');
}

$user_id = $_SESSION['id'];

require('../Model/database.php');

$query ="SELECT * FROM users WHERE id= '$user_id'";

$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// insert profile picture

if(isset($_FILES["profile_photo"]["name"])){
    $target_dir = "../photos/";
    $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
        $sql = "UPDATE users SET photo = '$target_file' WHERE id = '$user_id' ";
        $rs = mysqli_query($conn,$sql);
        header("Location: userpage.php");
    } else {
        echo "Sorry, there was an error uploading your file.";
        exit;
    }
}

// Store session id for user chat

$user_id_list = $_SESSION['id'];

$query_list = "SELECT * FROM users WHERE id != '".$user_id_list."'";
$result_list = mysqli_query($conn, $query_list);

if (!$result_list) {
    echo "Internal server error";
    exit;
}

// Store all users except for the one logged in inside an array

$user_list = array();
while($row = mysqli_fetch_assoc($result_list)) {
    $tmp = array();

    $tmp["id"] = $row["id"];
    $tmp["name"] = $row["name"];

    $user_list[$row['id']] = $tmp;

}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    User Page
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
  <!-- CSS Files -->
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="../assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
</head>

<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="white" data-active-color="danger">
      <div class="logo">
        <a class="simple-text">
          Dashboard
        </a>
      </div>
      <div class="sidebar-wrapper">
        <ul class="nav">
          <li class="active ">
            <a href="./user.php">
              <i class="nc-icon nc-single-02"></i>
              <p>User Profile</p>
            </a>
          </li>
          <li>
            <a href="./tables.php">
              <i class="nc-icon nc-tile-56"></i>
              <p>Table List</p>
            </a>
          </li>
          </li>
          <li class="active-pro">
            <a href="../Model/logout.php">
              <i class="nc-icon nc-diamond"></i>
              <p>Log Out</p>
            </a>
          </li>
        </ul>
      </div>
    </div>
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <a class="navbar-brand">Profile</a>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      <div class="content">
        <div class="row">
          <div class="col-md-4">
            <div class="card card-user">
              <div class="image">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Dark-brown-solid-color-background.jpg/2560px-Dark-brown-solid-color-background.jpg" alt="...">
              </div>
              <div class="card-body">
                <div class="author">
                  <a>
                      <form id="fileinfo" action="userpage.php" enctype="multipart/form-data" method="post" name="fileinfo">
                          <div id="profile-container-user">
                              <img class="avatar border-gray" id="profileImage-user" src="<?= $user['photo']?>">
                          </div>
                          <input id="imageUpload-user" type="file" name="profile_photo" placeholder="Photo" required="">
                      </form>
                    <h5 class="title"><?= $user['name']?></h5>
                  </a>
                  <p class="description">
                      <?= $user['email']?>
                  </p>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Users to chat</h4>
              </div>
              <div class="card-body">
                <ul class="list-unstyled team-members">
                    <?php
                    foreach ($chat_list as $key => $value){
                    ?>
                  <li>
                    <div class="row">
                      <div class="col-md-2 col-2">
                        <div class="avatar">
                          <img src="../assets/img/faces/ayo-ogunseinde-2.jpg" alt="Circle Image" class="img-circle img-no-padding img-responsive">
                        </div>
                      </div>
                      <div class="col-md-7 col-7">
                        DJ Khaled
                        <br />
                        <span class="text-muted"><small>Offline</small></span>
                      </div>
                      <div class="col-md-3 col-3 text-right">
                        <btn class="btn btn-sm btn-outline-success btn-round btn-icon"><i class="fa fa-envelope"></i></btn>
                      </div>
                    </div>
                  </li>
                    <?php } ?>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <div class="card card-user">
              <div class="card-header">
                <h5 class="card-title">Edit Profile</h5>
              </div>
              <div class="card-body">
                <form>
                  <div class="row">
                    <div class="col-md-5 pr-1">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input type="email" class="form-control" placeholder="Email" value="<?= $user['email']?>">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 pr-1">
                      <div class="form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" placeholder="Name" value="<?= $user['name']?>">
                      </div>
                    </div>
                    <div class="col-md-6 pl-1">
                      <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control" placeholder="Last Name" value="<?= $user['surname']?>">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4 pr-1">
                      <div class="form-group">
                        <label>Role</label>
                        <input type="text" class="form-control" value="<?= $user['role']?>" disabled>
                      </div>
                    </div>
                    <div class="col-md-4 px-1">
                      <div class="form-group">
                        <label>Gender</label>
                        <input type="text" class="form-control" value="<?= $user['gender']?>" disabled>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="update ml-auto mr-auto">
                      <button type="submit" class="btn btn-primary btn-round">Update Profile</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/jquery.min.js"></script>
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
</body>

</html>

<script>

    $.noConflict();

    // Image click function

    $("#profileImage-user").click(function() {
        $("#imageUpload-user").click();
    });

    // Image click function

    // Image upload or change function

    $("#imageUpload-user").change(function(){
        if (this){
            $('#fileinfo').submit();
        }
    });

    // Image upload or change function

    // User profile info update from the user himself

    function userupdate(id){
        var user_id = id;
        var name = $("#name").val();
        var surname = $("#surname").val();
        var email = $("#email").val();

        var data = {
            "action": "userupdate",
            "id": user_id,
            "name": name,
            "surname": surname,
            "email": email

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
                    window.location.href = "userpage.php";
                }

                if (response.code == 404) {
                    Swal.fire(response.message);
                }
            }
        });
    }

    // User profile info update from the user himself

    // User chat configuration

    $(document).ready(function(){

        // we call these 2 functions every 5 seconds in order to update user activity and chat history

        setInterval(function(){
            update_last_activity();
            update_chat_history_data();
        }, 5000);

        // we call these 2 functions every 5 seconds in order to update user activity and chat history

        // Create update activity function

        function update_last_activity(){

            $.ajax({
                url:"../Controller/actions.php",
                data: {"action": "update_last_activity"},
                success:function()
                {

                }
            })
        }

        // Create update activity function

        // Create modal pop up box for chat interface function

        function make_chat_dialog_box(to_user_id, to_user_name)
        {
            var modal_content = '<div id="user_dialog_'+to_user_id+'" class="user_dialog" title="You have chat with '+to_user_name+'">';
            modal_content += '<div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;" class="chat_history" data-touserid="'+to_user_id+'" id="chat_history_'+to_user_id+'">';
            modal_content += '</div>';
            modal_content += '<div class="form-group">';
            modal_content += '<textarea name="chat_message_'+to_user_id+'" id="chat_message_'+to_user_id+'" class="form-control" placeholder="Type..."></textarea>';
            modal_content += '</div><div class="form-group" align="right">';
            modal_content+= '<button type="button" name="send_chat" id="'+to_user_id+'" class="btn btn-info send_chat">Send</button></div></div>';
            $('#user_model_details').html(modal_content);
        }

        // Create modal pop up box for chat interface function

        // initiate the pop up with click action and load chat history

        $(document).on('click', '.start_chat', function(){
            var to_user_id = $(this).data('touserid');
            var to_user_name = $(this).data('tousername');
            make_chat_dialog_box(to_user_id, to_user_name);
            $("#user_dialog_"+to_user_id).dialog({
                autoOpen:false,
                width:400,
            });
            $('#user_dialog_'+to_user_id).dialog('open');

            var data = {
                "action":'get_chat_history',
                "to_user_id": to_user_id
            }

            $.ajax({
                url:"../Controller/actions.php",
                method: 'POST',
                type: 'POST',
                data: data,
                cache: false,
                success: function(result) {
                    var res = JSON.parse(result);
                    var chating = res.chat;
                    $('#chat_history_'+to_user_id).html(chating);
                }
            })
        });

        // initiate the pop up with click action and load chat history

        // Send chat with click action button

        $(document).on('click', '.send_chat', function(){
            var to_user_id = $(this).attr('id');
            var chat_message = $('#chat_message_'+to_user_id).val();
            $.ajax({
                url:"../Controller/actions.php",
                method:"POST",
                data:{to_user_id:to_user_id, chat_message:chat_message, "action": "insert_chat"},
                success:function(data)
                {
                    $('#chat_message_'+to_user_id).val('');
                    $('#chat_history_'+to_user_id).html(data);
                }
            })
        });

        // Send chat with click action button

        // Create update chat history function

        function update_chat_history_data()
        {
            $('.chat_history').each(function(){
                var to_user_id = $(this).data('touserid');
                var data = {
                    "action":'get_chat_history',
                    "to_user_id": to_user_id
                }
                $.ajax({
                    url:"../Controller/actions.php",
                    method: 'POST',
                    type: 'POST',
                    data: data,
                    cache: false,
                    success: function(result) {
                        var res = JSON.parse(result);
                        var chating = res.chat;
                        $('#chat_history_'+to_user_id).html(chating);
                    }
                })

            });
        }

        // Create update chat history function

        // Exit chat modal with x button in the top corner

        $(document).on('click', '.ui-button-icon', function(){
            $('.user_dialog').dialog('destroy').remove();
        });

        // Exit chat modal with x button in the top corner
    });

    // User chat configuration

</script>