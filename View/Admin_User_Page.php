<?php
include('header.php');
include('../Controller/functions.php');
?>
<?php
session_start();

if(!$_SESSION['id'])
{
    header('location : login.php');
}

$user_id = $_SESSION['id'];

require('../Model/database.php');

$query ="SELECT * FROM users WHERE id= '$user_id'";

$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if(isset($_FILES["profile_photo"]["name"])){
    $target_dir = "../photos/";
    $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
        $sql = "UPDATE users SET photo = '$target_file' WHERE id = '$user_id' ";
        $rs = mysqli_query($conn,$sql);
        header("Location: Admin_User_Page.php");
    } else {
        echo "Sorry, there was an error uploading your file.";
        exit;
    }
}


$user_id_list = $_SESSION['id'];

$query_list = "SELECT * FROM users WHERE id != '".$user_id_list."'";
$result_list = mysqli_query($conn, $query_list);

if (!$result_list) {
    echo "Internal server error";
    exit;
}

$user_list = array();
while($row = mysqli_fetch_assoc($result_list)) {
    $tmp = array();

    $tmp["id"] = $row["id"];
    $tmp["name"] = $row["name"];

    $user_list[$row['id']] = $tmp;
}

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../Styles/main.css">
</head>
<style>
</style>
<body class="user-body">
<div class="container rounded bg-white mt-5 mb-5">
    <div class="row">
        <div class="col-md-3 border-right">
            <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                <form id="fileinfo" action="Admin_User_Page.php" enctype="multipart/form-data" method="post" name="fileinfo">
                    <div id="profile-container-user">
                        <img id="profileImage-user" src="<?= $user['photo']?>">
                    </div>
                    <input id="imageUpload-user" type="file" name="profile_photo" placeholder="Photo" required="" capture>
                </form>
                <span class="font-weight-bold"><?= $user['name']?></span>
                <span class="text-black-50"><?= $user['email']?></span><span> </span></div>
        </div>
        <div class="col-md-5 border-right">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-right">Profile Settings</h4>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12"><label class="labels-user">Name</label>
                        <input type="text" name="name" id="name" class="form-control user-field" placeholder="enter name" value="<?= $user['name']?>"></div>
                    <div class="col-md-12"><label class="labels-user">Surname</label>
                        <input type="text" name="surname" id="surname" class="form-control user-field" placeholder="enter surname" value="<?= $user['surname']?>"></div>
                    <div class="col-md-12"><label class="labels-user" >Role</label>
                        <input type="text" name="role" id="role" class="form-control user-field" value="<?= $user['role']?>" readonly></div>
                    <div class="col-md-12"><label class="labels-user">Gender</label>
                        <input type="text" name="gender" id="gender" class="form-control user-field" value="<?= $user['gender']?>" readonly></div>
                    <div class="col-md-12"><label class="labels-user">Email</label>
                        <input type="text" name="email" id="email" class="form-control user-field" placeholder="email" value="<?= $user['email']?>"></div>
                </div>
                <div class="mt-5 text-center"><button class="btn btn-primary profile-button-user" type="button" name="userupdate" onclick="userupdate(<?= $user['id']?>)">Update</button></div>
                <div class="mt-5 text-center"><a href="../Model/logout.php" title="logout">Log Out</a></div>
                <div class="mt-5 text-center"><a href="userlist.php" title="logout">User List</a></div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<div class="container rounded bg-white mt-5 mb-5">
    <div class="container">
        <div class="table-responsive">
            <h4 align="center">Online Users</h4>
            <p align="right">Hi - <?php echo $user['name'];  ?> </p>
</body>
<table id="users" class="display dataTable" >
    <thead>
    <tr>
        <th>Username</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($user_list as $key => $user) {
        $status = '';
        $current_timestamp = strtotime(date("Y-m-d H:i:s") . '- 10 second');
        $current_timestamp = date('Y-m-d H:i:s', $current_timestamp);
        $user_last_activity = fetch_user_last_activity($user['id'], $conn);
        if(strtotime($user_last_activity) > strtotime($current_timestamp)) {
            $status = '<span class="label label-success">Online</span>';
        } else {
            $status = '<span class="label label-danger">Offline</span>';
        }
        ?>
        <tr>
            <td>
                <label id="username<?= $user['id']?>" name="username"><?= $user['name'], count_unseen_message($user['id'], $_SESSION['id'], $conn)?></label>
            </td>
            <td>
                <label  id="status<?= $user['id']?>" name="status" ><?= $status?></label>
            </td>
            <td>
                <button type="button" class="btn btn-info btn-xs start_chat" data-touserid="<?= $user['id']?>" data-tousername="<?= $user['name']?>">Start Chat</button>
            </td>
        </tr>
    <?php } ?>


    </tbody>



</table>

<div id="user_model_details"</div>
</div>
</div>
</div>
</body>
<?php
include('footer.php');
?>
</html>
<script>

    $.noConflict();

    $("#profileImage-user").click(function(e) {
        $("#imageUpload-user").click();
    });

    $("#imageUpload-user").change(function(){
        if (this){
            $('#fileinfo').submit();
        }
    });

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
                    window.location.href = "Admin_User_Page.php";
                }

                if (response.code == 404) {
                    Swal.fire(response.message);
                }
            }
        });
    }

    $(document).ready(function(){

        setInterval(function(){
            update_last_activity();
            update_chat_history_data();
        }, 5000);

        function update_last_activity(){

            $.ajax({
                url:"../Controller/actions.php",
                data: {"action": "update_last_activity"},
                success:function()
                {

                }
            })
        }

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

        $(document).on('click', '.start_chat', function(){
            var to_user_id = $(this).data('touserid');
            var to_user_name = $(this).data('tousername');
            make_chat_dialog_box(to_user_id, to_user_name);
            $("#user_dialog_"+to_user_id).dialog({
                autoOpen:false,
                width:400
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

        $(document).on('click', '.ui-button-icon', function(){
            $('.user_dialog').dialog('destroy').remove();
        });
    });
</script>


