<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAG</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include custom CSS -->
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">

    <style>
        body {
            background-color: green;
        }
        .btn-orange {
            background-color: #FFA500;
            border-color: #FFA500;
            color: #fff;
        }
        .btn-orange:hover {
            background-color: green;
            color: #fff;
            border: none;
        }
        h1 {
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            color: #FFA500;
            margin-top: -calc(21);
        }
        p, a {
            text-align: center;
            text-transform: capitalize;
            font-weight: italic;
            color: green;
            text-decoration: none;
        }
        img{
            width:150px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h1><img src="img/logo.png" alt="CAG"></h1>
                        <div class="text-center mb-4">
                            <h4><?php echo date('F d, Y') ?></h4>
                            <span id="now"></span>
                        </div>
                        <div class="col-md-12">
                            <form action="" id="att-log-frm">
							<div id="log_display"></div> <!-- Add this line -->
                                <div class="form-group">
                                    <label for="eno" class="control-label">Enter your Staff Number</label>
                                    <input type="text" id="eno" name="eno" class="form-control col-sm-12">
                                </div>
                                <div class="text-center mb-4">
                                    <button type="button" class="btn btn-orange btn-lg btn-block log_now" data-id="1">Sign-In </button>
                                    <button type="button" class="btn btn-orange btn-lg btn-block log_now" data-id="2">Sign-Out</button>
                                    <!--<button type="button" class="btn btn-orange btn-lg btn-block log_now" data-id="3">Afternoon SignIn</button>-->
                                    <!--<button type="button" class="btn btn-orange btn-lg btn-block log_now" data-id="4">Afternoon SignOut</button>-->
                                </div>
                                <p><a href="https://caglobals.com">Visit CA Global website</a></p>
                                <div class="loading" style="display: none">Please wait...</div>
                                <!-- <div id="log_display"></div> Add this line -->
                                <div class="alert alert-danger error-message" style="display: none"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        setInterval(function(){
            var time = new Date();
            var now = time.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true });
            var nowElement = document.getElementById('now');
            if (nowElement) {
                nowElement.textContent = now;
            }
        }, 500);

        document.querySelectorAll('.log_now').forEach(function(button) {
            button.addEventListener('click', function() {
                var eno = document.querySelector('[name="eno"]').value;
                if (eno === '') {
                    alert("Please enter your employee number");
                } else {
                    document.querySelectorAll('.log_now').forEach(function(btn) {
                        btn.style.display = 'none';
                    });
                    document.querySelector('.loading').style.display = 'block';
                    var dataId = this.getAttribute('data-id');
                    fetch('./admin/time_log.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'type=' + dataId + '&eno=' + encodeURIComponent(eno)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.querySelectorAll('.log_now').forEach(function(btn) {
                            btn.style.display = 'block';
                        });
                        document.querySelector('.loading').style.display = 'none';
                        var logDisplayElement = document.getElementById('log_display');
                        if (logDisplayElement) {
                            if (data.status === 1) {
                                document.querySelector('[name="eno"]').value = '';
                                logDisplayElement.innerHTML = '<div class="alert alert-success" role="alert">' + data.msg + '</div>';
                                setTimeout(function() {
                                    logDisplayElement.innerHTML = '';
                                }, 5000);
                            } else {
                                logDisplayElement.innerHTML = '<div class="alert alert-danger" role="alert">' + data.msg + '</div>';
                            }
                        }
                    })
                    .catch(error => {
                        document.querySelector('.loading').style.display = 'none';
                        document.querySelector('.error-message').textContent = 'Error: ' + error.message;
                        document.querySelector('.error-message').style.display = 'block';
                    });
                }
            });
        });
    });
</script>

</body>
</html>
