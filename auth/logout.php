<?php
session_start();

/* LOGOUT CONFIRMATION */

if(isset($_POST['logout'])){

    session_destroy();

    header("Location: login.php");
    exit();
}

if(isset($_POST['cancel'])){

    header("Location: ../admin/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Logout Confirmation</title>

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
    font-family:'Poppins',sans-serif;
}

body{
    background:#0f172a;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    overflow:hidden;
}

/* CARD */

.logout-card{
    width:100%;
    max-width:420px;
    background:#111827;
    border-radius:28px;
    padding:40px 35px;
    text-align:center;
    color:white;
    box-shadow:0 20px 50px rgba(0,0,0,0.35);
    position:relative;
    overflow:hidden;
}

/* BACKGROUND EFFECT */

.logout-card::before{
    content:'';
    position:absolute;
    width:220px;
    height:220px;
    background:rgba(255,255,255,0.04);
    border-radius:50%;
    top:-100px;
    right:-100px;
}

/* ICON */

.logout-icon{
    width:90px;
    height:90px;
    margin:auto;
    border-radius:50%;
    background:linear-gradient(135deg,#ef4444,#f87171);
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:40px;
    margin-bottom:25px;
    position:relative;
    z-index:2;
}

/* TEXT */

.logout-card h2{
    font-size:28px;
    font-weight:700;
    margin-bottom:12px;
    position:relative;
    z-index:2;
}

.logout-card p{
    color:#cbd5e1;
    font-size:15px;
    line-height:1.7;
    margin-bottom:30px;
    position:relative;
    z-index:2;
}

/* BUTTONS */

.button-group{
    display:flex;
    gap:15px;
    position:relative;
    z-index:2;
}

.logout-btn,
.cancel-btn{
    flex:1;
    height:52px;
    border:none;
    border-radius:14px;
    font-size:15px;
    font-weight:600;
    transition:0.3s;
}

/* YES BUTTON */

.logout-btn{
    background:linear-gradient(135deg,#ef4444,#f87171);
    color:white;
}

.logout-btn:hover{
    transform:translateY(-3px);
}

/* NO BUTTON */

.cancel-btn{
    background:#1e293b;
    color:white;
}

.cancel-btn:hover{
    background:#334155;
}

/* RESPONSIVE */

@media(max-width:500px){

    .logout-card{
        margin:20px;
        padding:35px 25px;
    }

    .button-group{
        flex-direction:column;
    }
}

</style>

</head>

<body>

<!-- LOGOUT CARD -->

<div class="logout-card">

    <!-- ICON -->

    <div class="logout-icon">

        <i class="bi bi-box-arrow-right"></i>

    </div>

    <!-- TITLE -->

    <h2>Logout</h2>

    <p>

        Are you sure you want to logout from your account?

    </p>

    <!-- FORM -->

    <form method="POST">

        <div class="button-group">

            <!-- YES -->

            <button type="submit"
                    name="logout"
                    class="logout-btn">

                Yes, Logout

            </button>

            <!-- NO -->

            <button type="submit"
                    name="cancel"
                    class="cancel-btn">

                No, Stay

            </button>

        </div>

    </form>

</div>

</body>
</html>