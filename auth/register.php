<?php
include '../config/db.php';

if(isset($_POST['register'])){

    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // PASSWORD CHECK
    if($password != $confirm_password){

        $error = "Passwords do not match!";

    } else {

        // CHECK EMAIL
        $check = $conn->prepare("SELECT * FROM users WHERE email=?");
        $check->execute([$email]);

        if($check->rowCount() > 0){

            $error = "Email already exists!";

        } else {

            // HASH PASSWORD
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO users(fullname,email,password)
                VALUES(?,?,?)
            ");

            $stmt->execute([
                $fullname,
                $email,
                $hashed_password
            ]);

            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SportHub Register</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#0f172a,#1e40af,#2563eb);
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
    top:-120px;
    left:-120px;
    filter:blur(90px);
}

body::after{
    content:'';
    position:absolute;
    width:350px;
    height:350px;
    background:#60a5fa;
    border-radius:50%;
    bottom:-120px;
    right:-120px;
    filter:blur(90px);
}

/* CARD */

.register-card{
    position:relative;
    width:450px;
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
}

.logo h2{
    margin-top:10px;
    font-weight:700;
}

.logo p{
    opacity:0.8;
    font-size:14px;
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
    color:rgba(255,255,255,0.75);
}

.form-control:focus{
    background:rgba(255,255,255,0.25);
    color:white;
    box-shadow:none;
    border:1px solid #93c5fd;
}

/* LEFT ICON */

.input-group .left-icon{
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
    cursor:pointer;
    color:white;
    z-index:100;
}

/* BUTTON */

.register-btn{
    width:100%;
    height:52px;
    border:none;
    border-radius:12px;
    background:white;
    color:#1e3a8a;
    font-weight:600;
    transition:0.3s;
}

.register-btn:hover{
    transform:translateY(-2px);
    background:#dbeafe;
}

/* FOOTER */

.login-link{
    margin-top:20px;
    text-align:center;
}

.login-link a{
    color:white;
    text-decoration:none;
    font-weight:600;
}

.login-link a:hover{
    text-decoration:underline;
}

/* ALERT */

.alert{
    border-radius:12px;
}

</style>

</head>

<body>

<div class="register-card">

    <!-- LOGO -->

    <div class="logo">

        <i class="bi bi-dribbble"></i>

        <h2>SportHub</h2>

        <p>Create your account</p>

    </div>

    <!-- ERROR -->

    <?php if(isset($error)) { ?>

        <div class="alert alert-danger">

            <?= $error ?>

        </div>

    <?php } ?>

    <!-- FORM -->

    <form method="POST">

        <!-- FULL NAME -->

        <div class="input-group">

            <i class="bi bi-person-fill left-icon"></i>

            <input type="text"
                   name="fullname"
                   class="form-control"
                   placeholder="Full Name"
                   required>

        </div>

        <!-- EMAIL -->

        <div class="input-group">

            <i class="bi bi-envelope-fill left-icon"></i>

            <input type="email"
                   name="email"
                   class="form-control"
                   placeholder="Email Address"
                   required>

        </div>

        <!-- PASSWORD -->

        <div class="input-group">

            <i class="bi bi-lock-fill left-icon"></i>

            <input type="password"
                   id="password"
                   name="password"
                   class="form-control"
                   placeholder="Password"
                   required>

            <i class="bi bi-eye-slash-fill toggle-password"
               onclick="togglePassword('password', this)"></i>

        </div>

        <!-- CONFIRM PASSWORD -->

        <div class="input-group">

            <i class="bi bi-shield-lock-fill left-icon"></i>

            <input type="password"
                   id="confirm_password"
                   name="confirm_password"
                   class="form-control"
                   placeholder="Confirm Password"
                   required>

            <i class="bi bi-eye-slash-fill toggle-password"
               onclick="togglePassword('confirm_password', this)"></i>

        </div>

        <!-- BUTTON -->

        <button type="submit"
                name="register"
                class="register-btn">

            Create Account

        </button>

    </form>

    <!-- FOOTER -->

    <div class="login-link">

        Already have an account?

        <a href="login.php">

            Login Here

        </a>

    </div>

</div>

<!-- PASSWORD TOGGLE SCRIPT -->

<script>

function togglePassword(fieldId, icon){

    const passwordField = document.getElementById(fieldId);

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