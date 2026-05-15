<?php
session_start();
include '../config/db.php';

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);

    $user = $stmt->fetch();

    if($user && password_verify($password, $user['password'])){

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];

        if($user['role'] == 'admin'){

            header("Location: ../admin/dashboard.php");

        } else {

            header("Location: ../user/dashboard.php");
        }

        exit();

    } else {

        $error = "Invalid Email or Password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SportHub Login</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#0f172a,#1e3a8a,#2563eb);
    overflow:hidden;
}

/* BACKGROUND EFFECT */

body::before{
    content:'';
    position:absolute;
    width:350px;
    height:350px;
    background:#3b82f6;
    border-radius:50%;
    top:-100px;
    left:-100px;
    filter:blur(90px);
}

body::after{
    content:'';
    position:absolute;
    width:350px;
    height:350px;
    background:#60a5fa;
    border-radius:50%;
    bottom:-100px;
    right:-100px;
    filter:blur(90px);
}

/* LOGIN CARD */

.login-card{
    position:relative;
    width:420px;
    padding:40px;
    border-radius:25px;
    background:rgba(255,255,255,0.12);
    backdrop-filter:blur(20px);
    border:1px solid rgba(255,255,255,0.2);
    box-shadow:0 8px 32px rgba(0,0,0,0.25);
    color:white;
    z-index:10;
    animation:fadeIn 0.8s ease;
}

@keyframes fadeIn{

    from{
        opacity:0;
        transform:translateY(30px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* LOGO */

.logo{
    text-align:center;
    margin-bottom:30px;
}

.logo i{
    font-size:60px;
    color:#fff;
}

.logo h2{
    margin-top:10px;
    font-weight:700;
}

.logo p{
    font-size:14px;
    opacity:0.8;
}

/* INPUTS */

.input-group{
    position:relative;
    margin-bottom:20px;
}

.form-control{
    height:52px;
    border:none;
    border-radius:12px;
    background:rgba(255,255,255,0.15);
    color:white;
    padding-left:45px;
    padding-right:45px;
}

.form-control::placeholder{
    color:rgba(255,255,255,0.7);
}

.form-control:focus{
    background:rgba(255,255,255,0.25);
    color:white;
    box-shadow:none;
    border:1px solid #93c5fd;
}

/* LEFT ICON */

.left-icon{
    position:absolute;
    left:15px;
    top:16px;
    color:white;
    z-index:100;
}

/* PASSWORD TOGGLE */

.toggle-password{
    position:absolute;
    right:15px;
    top:16px;
    color:white;
    cursor:pointer;
    z-index:100;
}

/* BUTTON */

.login-btn{
    width:100%;
    height:52px;
    border:none;
    border-radius:12px;
    background:white;
    color:#1e3a8a;
    font-weight:600;
    transition:0.3s;
}

.login-btn:hover{
    transform:translateY(-2px);
    background:#dbeafe;
}

/* REGISTER LINK */

.register-link{
    text-align:center;
    margin-top:20px;
}

.register-link a{
    color:#fff;
    text-decoration:none;
    font-weight:600;
}

.register-link a:hover{
    text-decoration:underline;
}

/* ALERT */

.alert{
    border-radius:12px;
}

</style>

</head>

<body>

<div class="login-card">

    <!-- LOGO -->

    <div class="logo">

        <i class="bi bi-dribbble"></i>

        <h2>SportHub</h2>

        <p>Inventory Management System</p>

    </div>

    <!-- ERROR -->

    <?php if(isset($error)) { ?>

        <div class="alert alert-danger">

            <?= $error ?>

        </div>

    <?php } ?>

    <!-- FORM -->

    <form method="POST">

        <!-- EMAIL -->

        <div class="input-group">

            <i class="bi bi-envelope-fill left-icon"></i>

            <input type="email"
                   name="email"
                   class="form-control"
                   placeholder="Enter your email"
                   required>

        </div>

        <!-- PASSWORD -->

        <div class="input-group">

            <i class="bi bi-lock-fill left-icon"></i>

            <input type="password"
                   id="password"
                   name="password"
                   class="form-control"
                   placeholder="Enter your password"
                   required>

            <i class="bi bi-eye-slash-fill toggle-password"
               onclick="togglePassword()"></i>

        </div>

        <!-- BUTTON -->

        <button type="submit"
                name="login"
                class="login-btn">

            Login

        </button>

    </form>

    <!-- FOOTER -->



</div>

<!-- PASSWORD TOGGLE SCRIPT -->

<script>

function togglePassword(){

    const passwordField = document.getElementById("password");

    const icon = document.querySelector(".toggle-password");

    if(passwordField.type === "password"){

        passwordField.type = "text";

        icon.classList.remove("bi-eye-slash-fill");
        icon.classList.add("bi-eye-fill");

    } else {

        passwordField.type = "password";

        icon.classList.remove("bi-eye-fill");
        icon.classList.add("bi-eye-slash-fill");
    }
}

</script>

</body>
</html>