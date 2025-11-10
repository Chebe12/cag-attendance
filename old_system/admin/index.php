<!DOCTYPE html>
<?php
	session_start();
	if(ISSET($_SESSION['login_id'])){
		header('location: home.php');
	}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAG</title>
    <link rel="icon" href="..img/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
		
		<link rel = "stylesheet" type = "text/css" href = "../assets/css/bootstrap.min.css" />
		<link rel = "stylesheet" type = "text/css" href = "https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
		<link rel = "stylesheet" type = "text/css" href = "https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" />
		<link rel = "stylesheet" type = "text/css" href = "../assets/css/style.css" />

	<script src = "../assets/js/jquery-3.5.1.min.js"></script>
	<script src = "../assets/js/bootstrap.min.js"></script>
	<script src = "https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <style>
        body {
            background-color: green; /* Green background color */
        }
        .card {
            margin-top: 50px;
        }
        .btn-primary {
            background-color: #FFA500; /* Orange button color */
            border-color: #FFA500;
        }
        .btn-primary:hover {
            background-color: #FF8C00; /* Darker orange on hover */
            border-color: #FF8C00;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h1 class="text-center"><img src="../img/logo.png" alt="CAG" width="150px"></h1>

                        <h4 class="text-center">Login</h4>
                    </div>
                    <div class="card-body">
                        <form id="login-frm">
                            <div class="form-group">
                                <label class="control-label">Username:</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Password:</label>
                                <input type="password" maxlength="20" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Login <i class="fa fa-arrow-right"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="../assets/js/jquery.js"></script>
<script src="../assets/js/bootstrap.js"></script>
<script>
    $(document).ready(function(){
        $('#login-frm').submit(function(e){
            e.preventDefault();
            $.ajax({
                url:'login.php',
                method:'POST',
                data:$(this).serialize(),
                error:err=>{
                    console.log(err)
                },
                success:function(resp){
                    if(resp == true){
                        location.replace('home.php');
                    }
                }
            });
        });
    });
</script>
</html>
