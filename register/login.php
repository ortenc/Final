<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Colorlib Templates">
    <meta name="author" content="Colorlib">
    <meta name="keywords" content="Colorlib Templates">

    <!-- Title Page-->
    <title>Login</title>

    <!-- Icons font CSS-->
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Vendor CSS-->
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/main.css" rel="stylesheet" media="all">
</head>

<body>
    <div class="page-wrapper bg-gra-02 p-t-130 p-b-100 font-poppins">
        <div class="wrapper wrapper--w680">
            <div class="card card-4">
                <div class="card-body">
                    <h2 class="title">Login</h2>
                    <div class="row row-space">
                    </div>
                    <div class="input-group">
                        <label class="label">Email</label>
                            <input type="text" class="input--style-4" id="staticEmail" placeholder="email@example.com"
                                   name="email" required>
                    </div>
                    <div class="input-group">
                        <label class="label">Password</label>
                            <input type="password" class="input--style-4" id="password" name="password" required>
                    </div>
                    <div class="p-t-15">
                        <button class="btn btn--radius-2 btn--blue" type="submit" onclick="login()">Submit</button>
                        <button class="btn btn--radius-2 btn--blue" type="submit" onclick="register()">Register</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Vendor JS-->
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/datepicker/moment.min.js"></script>
    <script src="vendor/datepicker/daterangepicker.js"></script>

    <!-- Main JS-->
    <script src="js/global.js"></script>
</body>

</html>
<!-- end document-->
<script>
    function isEmpty(value) {
        return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
    }
    function login() {

        var email = $("#staticEmail").val();
        var password = $("#password").val();


        if (isEmpty(email)) {
            $("#staticEmail").addClass("error");
            Swal.fire("Email cannot be empty");
            return;
        } else {
            $("#staticEmail").removeClass("error");
        }
        if (isEmpty(password)) {
            $("#password").addClass("error");
            Swal.fire("Password cannot be empty");
            return;
        } else {
            $("#password").removeClass("error");
        }

        var data = {
            "action": "login",
            "email": email,
            "password": password
        };

        $.ajax({
            url: '../Controller/actions.php',
            method: 'POST',
            type: 'POST',
            data: data,
            cache: false,
            success: function(result){
                var response = JSON.parse(result);
                if (response.code == 200) {
                    window.location.href = "../userinterface/user.php";
                }
                if (response.code == 201) {
                    window.location.href = "../userinterface/user.php";
                }

                if (response.code == 404) {
                    Swal.fire("Sorry");
                }

            }
        });
    }
    function register(){
        window.location.href = "register.php";
    }

</script>