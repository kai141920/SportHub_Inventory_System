<?php
include '../auth/auth_check.php';
include '../config/db.php';

/* TOTAL UNIQUE EQUIPMENTS */

$totalEquipments = $conn->query("
SELECT COUNT(DISTINCT equipment_name)
FROM equipments
")->fetchColumn();

/* TOTAL CATEGORIES */

$totalCategories = $conn->query("
SELECT COUNT(DISTINCT category)
FROM equipments
")->fetchColumn();

/* TOTAL ORDERS */

$totalOrders = $conn->query("
SELECT COUNT(*) FROM orders
")->fetchColumn();

/* TOTAL CUSTOMERS */
/* FETCH FROM customers TABLE */

$totalCustomers = $conn->query("
SELECT COUNT(*) FROM customers
")->fetchColumn();

/* TOTAL INCOME */

$totalIncome = $conn->query("
SELECT IFNULL(SUM(total_amount),0)
FROM orders
WHERE payment_status = 'Paid'
")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard</title>

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
    color:white;
    overflow-x:hidden;
}

/* SIDEBAR */

.sidebar{
    position:fixed;
    width:260px;
    height:100vh;
    background:#111827;
    padding:25px;
    border-right:1px solid rgba(255,255,255,0.08);
}

.logo{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:45px;
}

.logo i{
    font-size:35px;
    color:#3b82f6;
}

.logo h2{
    font-size:24px;
    font-weight:700;
}

.sidebar-menu{
    list-style:none;
    padding:0;
}

.sidebar-menu li{
    margin-bottom:15px;
}

.sidebar-menu a{
    text-decoration:none;
    color:#cbd5e1;
    display:flex;
    align-items:center;
    gap:15px;
    padding:14px 18px;
    border-radius:14px;
    transition:0.3s;
    font-size:15px;
    font-weight:500;
}

.sidebar-menu a:hover,
.sidebar-menu .active{
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    color:white;
    transform:translateX(4px);
}

.sidebar-menu i{
    font-size:18px;
}

/* MAIN */

.main-content{
    margin-left:260px;
    padding:30px;
}

/* TOPBAR */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:35px;
}

.topbar h1{
    font-size:32px;
    font-weight:700;
}

.admin-box{
    display:flex;
    align-items:center;
    gap:12px;
    background:#111827;
    padding:12px 18px;
    border-radius:16px;
    box-shadow:0 5px 20px rgba(0,0,0,0.25);
}

.admin-box i{
    font-size:22px;
    color:#60a5fa;
}

/* WELCOME */

.welcome-box{
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    border-radius:28px;
    padding:35px;
    margin-bottom:35px;
    position:relative;
    overflow:hidden;
}

.welcome-box::before{
    content:'';
    position:absolute;
    width:260px;
    height:260px;
    background:rgba(255,255,255,0.08);
    border-radius:50%;
    top:-120px;
    right:-120px;
}

.welcome-box h2{
    font-size:34px;
    font-weight:700;
    position:relative;
    z-index:2;
}

.welcome-box p{
    margin-top:10px;
    opacity:0.9;
    position:relative;
    z-index:2;
}

/* DASHBOARD CARDS */

.dashboard-link{
    text-decoration:none;
}

.dashboard-card{
    background:linear-gradient(145deg,#111827,#1e293b);
    border:none;
    border-radius:24px;
    padding:28px;
    color:white;
    position:relative;
    overflow:hidden;
    transition:0.4s;
    height:190px;
    box-shadow:0 10px 30px rgba(0,0,0,0.25);
    cursor:pointer;
}

.dashboard-card:hover{
    transform:translateY(-8px) scale(1.02);
    box-shadow:0 20px 40px rgba(37,99,235,0.25);
}

.dashboard-card::before{
    content:'';
    position:absolute;
    width:220px;
    height:220px;
    background:rgba(255,255,255,0.04);
    border-radius:50%;
    top:-100px;
    right:-100px;
}

/* CARD ICON */

.card-icon{
    width:65px;
    height:65px;
    border-radius:18px;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:28px;
    margin-bottom:18px;
    position:relative;
    z-index:2;
}

.blue{
    background:linear-gradient(135deg,#2563eb,#60a5fa);
}

.green{
    background:linear-gradient(135deg,#10b981,#34d399);
}

.orange{
    background:linear-gradient(135deg,#f59e0b,#fbbf24);
}

.purple{
    background:linear-gradient(135deg,#8b5cf6,#a78bfa);
}

.red{
    background:linear-gradient(135deg,#ef4444,#f87171);
}

/* TEXT */

.dashboard-card h5{
    font-size:15px;
    color:#cbd5e1;
    margin-bottom:12px;
    position:relative;
    z-index:2;
}

.dashboard-card h2{
    font-size:38px;
    font-weight:700;
    position:relative;
    z-index:2;
    color:white;
}

.card-view{
    margin-top:12px;
    font-size:13px;
    color:#94a3b8;
    position:relative;
    z-index:2;
}

/* RESPONSIVE */

@media(max-width:991px){

    .sidebar{
        width:100%;
        height:auto;
        position:relative;
    }

    .main-content{
        margin-left:0;
    }

    .topbar{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">

        <i class="bi bi-dribbble"></i>

        <h2>SportHub</h2>

    </div>

    <ul class="sidebar-menu">

        <li>
            <a href="dashboard.php" class="active">
                <i class="bi bi-grid-fill"></i>
                Dashboard
            </a>
        </li>

        <li>
            <a href="inventory.php">
                <i class="bi bi-box-seam-fill"></i>
                Inventory
            </a>
        </li>
        
        <li>
            <a href="purchase.php">
                <i class="bi bi-cart-fill"></i>
                Purchase
            </a>
        </li>
        
        <li>
            <a href="reports.php">
                <i class="bi bi-bar-chart-fill"></i>
                Reports
            </a>
        </li>
        
        <li>
            <a href="../auth/logout.php">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </a>
        </li>

    </ul>

</div>

<!-- MAIN -->

<div class="main-content">

    <!-- TOPBAR -->

    <div class="topbar">

        <h1>Admin Dashboard</h1>

        <div class="admin-box">

            <i class="bi bi-person-circle"></i>

            <span>

                <?= $_SESSION['fullname']; ?>

            </span>

        </div>

    </div>

    <!-- WELCOME -->

    <div class="welcome-box">

        <h2>Welcome Back, Admin 👋</h2>

        <p>

            Monitor your inventory, income, customers, categories, and sales performance in one place.

        </p>

    </div>

    <!-- DASHBOARD CARDS -->

    <div class="row g-4">

        <!-- TOTAL EQUIPMENTS -->

        <div class="col-xl-3 col-md-6">

            <a href="inventory.php" class="dashboard-link">

                <div class="dashboard-card">

                    <div class="card-icon blue">

                        <i class="bi bi-box-seam-fill"></i>

                    </div>

                    <h5>Total Equipments</h5>

                    <h2><?= $totalEquipments ?></h2>

                    <div class="card-view">

                        View All Equipments →

                    </div>

                </div>

            </a>

        </div>

        <!-- TOTAL CATEGORIES -->

        <div class="col-xl-3 col-md-6">

            <a href="inventory.php" class="dashboard-link">

                <div class="dashboard-card">

                    <div class="card-icon purple">

                        <i class="bi bi-tags-fill"></i>

                    </div>

                    <h5>Total Categories</h5>

                    <h2><?= $totalCategories ?></h2>

                    <div class="card-view">

                        View Categories →

                    </div>

                </div>

            </a>

        </div>

        <!-- TOTAL ORDERS -->

        <div class="col-xl-3 col-md-6">

            <a href="purchase.php" class="dashboard-link">

                <div class="dashboard-card">

                    <div class="card-icon green">

                        <i class="bi bi-cart-check-fill"></i>

                    </div>

                    <h5>Total Orders</h5>

                    <h2><?= $totalOrders ?></h2>

                    <div class="card-view">

                        View Orders →

                    </div>

                </div>

            </a>

        </div>

        <!-- TOTAL CUSTOMERS -->

        <div class="col-xl-3 col-md-6">

            <a href="customers.php" class="dashboard-link">

                <div class="dashboard-card">

                    <div class="card-icon orange">

                        <i class="bi bi-people-fill"></i>

                    </div>

                    <h5>Total Customers</h5>

                    <h2><?= $totalCustomers ?></h2>

                    <div class="card-view">

                        View Customers →

                    </div>

                </div>

            </a>

        </div>

    </div>

    <!-- TOTAL INCOME -->

    <div class="row mt-4">

        <div class="col-12">

            <a href="reports.php" class="dashboard-link">

                <div class="dashboard-card">

                    <div class="card-icon red">

                        <i class="bi bi-cash-stack"></i>

                    </div>

                    <h5>Total Income</h5>

                    <h2>

                        ₱<?= number_format($totalIncome,2) ?>

                    </h2>

                    <div class="card-view">

                        View Income Reports →

                    </div>

                </div>

            </a>

        </div>

    </div>

</div>

</body>
</html>