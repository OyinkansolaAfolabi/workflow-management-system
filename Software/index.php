<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            width: 300px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-box .btn-google {
            background-color: #4285F4;
            color: white;
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container login-container">
    <div class="login-box">
        <h2>Login</h2>
        <form action="backend/login.php" method="POST">
            <button type="submit" class="btn btn-google">Login with Google</button>
        </form>
    </div>
</div>
</body>
</html>
