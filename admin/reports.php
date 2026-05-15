<?php
include '../auth/auth_check.php';
include '../config/db.php';

/* FETCH REPORTS */
/* FIXED DATABASE TABLES + COLUMN NAMES */

$reports = $conn->query("
SELECT 
    orders.order_id,
    customers.fullname,
    orders.total_amount,
    orders.payment_status,
    orders.order_date
FROM orders
JOIN customers 
ON orders.customer_id = customers.customer_id
ORDER BY orders.order_date DESC
");

/* MONTHLY INCOME */

$monthlyIncome = $conn->query("
SELECT 
    MONTH(order_date) AS month_num,
    DATE_FORMAT(order_date, '%b') AS month_name,
    SUM(total_amount) AS total_income
FROM orders
WHERE payment_status = 'Paid'
GROUP BY MONTH(order_date)
ORDER BY MONTH(order_date)
");

$months = [];
$income = [];

while($row = $monthlyIncome->fetch()){

    $months[] = $row['month_name'];
    $income[] = $row['total_income'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Sales Reports</title>

<!-- Bootstrap -->

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Chart JS -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    border-right:1px solid rgba(255,255,255,0.06);
}

.logo{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:45px;
}

.logo i{
    font-size:34px;
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
    margin-bottom:14px;
}

.sidebar-menu a{
    text-decoration:none;
    color:#cbd5e1;
    display:flex;
    align-items:center;
    gap:14px;
    padding:15px 18px;
    border-radius:16px;
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
    margin-bottom:30px;
}

.topbar h1{
    font-size:32px;
    font-weight:700;
}

.admin-box{
    background:#111827;
    padding:12px 18px;
    border-radius:16px;
    display:flex;
    align-items:center;
    gap:12px;
    box-shadow:0 5px 20px rgba(0,0,0,0.2);
}

.admin-box i{
    font-size:22px;
    color:#60a5fa;
}

/* CARDS */

.report-card{
    background:#111827;
    border-radius:28px;
    padding:30px;
    margin-bottom:30px;
    box-shadow:0 10px 40px rgba(0,0,0,0.25);
}

/* CARD TITLE */

.card-title-custom{
    font-size:22px;
    font-weight:700;
    margin-bottom:25px;
}

/* CHART */

.chart-container{
    width:100%;
    height:380px;
}

/* TABLE */

.table-wrapper{
    overflow-x:auto;
}

.report-table{
    width:100%;
    border-collapse:separate;
    border-spacing:0 16px;
}

.report-table thead th{
    padding:0 18px 14px;
    color:#94a3b8;
    font-size:12px;
    font-weight:600;
    text-transform:uppercase;
    border:none;
    letter-spacing:1px;
}

.report-table tbody tr{
    background:#1e293b;
    transition:0.3s;
    box-shadow:0 8px 25px rgba(0,0,0,0.18);
}

.report-table tbody tr:hover{
    transform:translateY(-4px);
    background:#273449;
}

.report-table tbody td{
    padding:18px;
    border:none;
    vertical-align:middle;
}

.report-table tbody td:first-child{
    border-top-left-radius:18px;
    border-bottom-left-radius:18px;
}

.report-table tbody td:last-child{
    border-top-right-radius:18px;
    border-bottom-right-radius:18px;
}

/* STATUS */

.status-badge{
    padding:10px 16px;
    border-radius:50px;
    font-size:13px;
    font-weight:600;
}

.paid{
    background:rgba(16,185,129,0.15);
    color:#34d399;
}

.pending{
    background:rgba(245,158,11,0.15);
    color:#fbbf24;
}

.failed{
    background:rgba(239,68,68,0.15);
    color:#f87171;
}

/* PRICE */

.amount{
    color:#60a5fa;
    font-weight:700;
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
        gap:15px;
        align-items:flex-start;
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
            <a href="dashboard.php">
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
            <a href="reports.php" class="active">
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

        <h1>Sales Reports</h1>

        <div class="admin-box">

            <i class="bi bi-person-circle"></i>

            <span><?= $_SESSION['fullname']; ?></span>

        </div>

    </div>

    <!-- GRAPH CARD -->

    <div class="report-card">

        <h3 class="card-title-custom">

            Monthly Income Overview

        </h3>

        <div class="chart-container">

            <canvas id="incomeChart"></canvas>

        </div>

    </div>

    <!-- TABLE CARD -->

    <div class="report-card">

        <h3 class="card-title-custom">

            Sales Transactions

        </h3>

        <div class="table-wrapper">

            <table class="report-table">

                <thead>

                    <tr>

                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Payment Status</th>
                        <th>Date & Time</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while($row = $reports->fetch()) { ?>

                    <?php

                    $statusClass = "pending";

                    if(strtolower($row['payment_status']) == "paid"){
                        $statusClass = "paid";
                    }

                    if(strtolower($row['payment_status']) == "failed"){
                        $statusClass = "failed";
                    }

                    ?>

                    <tr>

                        <td>

                            <strong>

                                #<?= $row['order_id'] ?>

                            </strong>

                        </td>

                        <td>

                            <?= $row['fullname'] ?>

                        </td>

                        <td>

                            <span class="amount">

                                ₱<?= number_format($row['total_amount'],2) ?>

                            </span>

                        </td>

                        <td>

                            <span class="status-badge <?= $statusClass ?>">

                                <?= $row['payment_status'] ?>

                            </span>

                        </td>

                        <td>

                            <?= date("F d, Y - h:i A", strtotime($row['order_date'])) ?>

                        </td>

                    </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- CHART -->

<script>

const ctx = document.getElementById('incomeChart');

new Chart(ctx, {

    type: 'bar',

    data: {

        labels: <?= json_encode($months) ?>,

        datasets: [{

            label: 'Monthly Income',

            data: <?= json_encode($income) ?>,

            borderWidth: 2,
            borderRadius: 12,
            backgroundColor: '#3b82f6',
            borderColor: '#60a5fa'

        }]
    },

    options: {

        responsive:true,

        maintainAspectRatio:false,

        plugins:{

            legend:{
                labels:{
                    color:'white'
                }
            }
        },

        scales:{

            x:{
                ticks:{
                    color:'white'
                },
                grid:{
                    color:'rgba(255,255,255,0.05)'
                }
            },

            y:{
                ticks:{
                    color:'white'
                },
                grid:{
                    color:'rgba(255,255,255,0.05)'
                }
            }
        }
    }
});

</script>

</body>
</html>