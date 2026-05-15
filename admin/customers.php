<?php
include '../auth/auth_check.php';
include '../config/db.php';

/* FETCH CUSTOMERS ONLY */

$customers = $conn->query("
SELECT *
FROM customers
ORDER BY customers.customer_id DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Customers List</title>

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

/* CARD */

.customer-card{
    background:#111827;
    border-radius:28px;
    padding:30px;
    box-shadow:0 10px 40px rgba(0,0,0,0.25);
}

/* HEADER */

.customer-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
    gap:15px;
}

/* SEARCH */

.search-box{
    position:relative;
    width:350px;
}

.search-box input{
    width:100%;
    height:52px;
    border:none;
    border-radius:16px;
    background:#1e293b;
    color:white;
    padding-left:48px;
    font-size:14px;
}

.search-box input:focus{
    outline:none;
    border:1px solid #3b82f6;
}

.search-box i{
    position:absolute;
    left:17px;
    top:15px;
    color:#94a3b8;
}

/* TOTAL */

.total-box{
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    padding:12px 20px;
    border-radius:16px;
    font-weight:600;
}

/* TABLE */

.table-wrapper{
    overflow-x:auto;
}

.customer-table{
    width:100%;
    border-collapse:separate;
    border-spacing:0 16px;
}

.customer-table thead th{
    padding:0 18px 14px;
    color:#94a3b8;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:1px;
    border:none;
}

.customer-table tbody tr{
    background:#1e293b;
    transition:0.3s;
    box-shadow:0 8px 25px rgba(0,0,0,0.18);
}

.customer-table tbody tr:hover{
    transform:translateY(-4px);
    background:#273449;
}

.customer-table tbody td{
    padding:18px;
    border:none;
    vertical-align:middle;
}

.customer-table tbody td:first-child{
    border-top-left-radius:18px;
    border-bottom-left-radius:18px;
}

.customer-table tbody td:last-child{
    border-top-right-radius:18px;
    border-bottom-right-radius:18px;
}

/* CUSTOMER INFO */

.customer-info{
    display:flex;
    align-items:center;
    gap:15px;
}

.customer-avatar{
    width:60px;
    height:60px;
    border-radius:18px;
    background:linear-gradient(135deg,#2563eb,#60a5fa);
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:24px;
    font-weight:700;
    color:white;
}

/* TEXT */

.customer-name{
    font-size:15px;
    font-weight:600;
}

.customer-email{
    font-size:13px;
    color:#94a3b8;
    margin-top:4px;
}

/* ROLE */

.role-badge{
    display:inline-block;
    padding:10px 16px;
    border-radius:50px;
    background:rgba(16,185,129,0.15);
    color:#34d399;
    font-size:13px;
    font-weight:600;
}

/* DATE */

.date-text{
    color:#cbd5e1;
    font-size:14px;
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

    .customer-header{
        flex-direction:column;
        align-items:flex-start;
    }

    .search-box{
        width:100%;
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

                Purchases

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

        <h1>Customers List</h1>

        <div class="admin-box">

            <i class="bi bi-person-circle"></i>

            <span>

                <?= $_SESSION['fullname']; ?>

            </span>

        </div>

    </div>

    <!-- CUSTOMER CARD -->

    <div class="customer-card">

        <!-- HEADER -->

        <div class="customer-header">

            <!-- SEARCH -->

            <div class="search-box">

                <i class="bi bi-search"></i>

                <input type="text"
                       id="searchInput"
                       placeholder="Search customer...">

            </div>

            <!-- TOTAL -->

            <div class="total-box">

                Total Customers:
                <?= $customers->rowCount(); ?>

            </div>

        </div>

        <!-- TABLE -->

        <div class="table-wrapper">

            <table class="customer-table">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Customer</th>
                        <th>Email</th>

                    </tr>

                </thead>

                <tbody id="customerTable">

                    <?php while($row = $customers->fetch()) { ?>

                    <tr>

                        <!-- ID -->

                        <td>

                            <strong>

                                #<?= $row['customer_id']; ?>

                            </strong>

                        </td>

                        <!-- CUSTOMER -->

                        <td>

                            <div class="customer-info">

                                <div class="customer-avatar">

                                    <?= strtoupper(substr($row['fullname'],0,1)); ?>

                                </div>

                                <div>

                                    <div class="customer-name">

                                        <?= $row['fullname']; ?>

                                    </div>

                                    <div class="customer-email">

                                        Customer Account

                                    </div>

                                </div>

                            </div>

                        </td>

                        <!-- EMAIL -->

                        <td>

                            <?= $row['email']; ?>

                        </td>

                    </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- SEARCH SCRIPT -->

<script>

const searchInput = document.getElementById("searchInput");

searchInput.addEventListener("keyup", function(){

    const value = this.value.toLowerCase();

    const rows = document.querySelectorAll("#customerTable tr");

    rows.forEach(row => {

        const text = row.innerText.toLowerCase();

        row.style.display = text.includes(value)
            ? ""
            : "none";
    });
});

</script>

</body>
</html>