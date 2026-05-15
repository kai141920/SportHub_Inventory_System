<?php
include '../auth/auth_check.php';
include '../config/db.php';

/* =========================================
   UPDATE ORDER
========================================= */

if(isset($_POST['update_order'])){

    $order_id = $_POST['order_id'];
    $quantity = $_POST['quantity'];
    $payment_status = $_POST['payment_status'];

    /* GET ORDER */

    $getOrder = $conn->prepare("
    SELECT *
    FROM orders
    WHERE order_id=?
    ");

    $getOrder->execute([$order_id]);

    $order = $getOrder->fetch();

    /* GET ORDER ITEM */

    $getItem = $conn->prepare("
    SELECT *
    FROM order_items
    WHERE order_id=?
    ");

    $getItem->execute([$order_id]);

    $orderItem = $getItem->fetch();

    if($order && $orderItem){

        $equipment_id =
        $orderItem['equipment_id'];

        /* GET EQUIPMENT */

        $equipment = $conn->prepare("
        SELECT *
        FROM equipments
        WHERE id=?
        ");

        $equipment->execute([$equipment_id]);

        $item = $equipment->fetch();

        $stock = $item['stock'];

        $price = $item['price'];

        $old_quantity =
        $orderItem['quantity'];

        $old_status =
        $order['payment_status'];

        /* RETURN STOCK */

        if($old_status == "Paid"){

            $restoreStock =
            $stock + $old_quantity;

            $restore = $conn->prepare("
            UPDATE equipments
            SET stock=?
            WHERE id=?
            ");

            $restore->execute([
                $restoreStock,
                $equipment_id
            ]);

            $stock = $restoreStock;
        }

        /* DEDUCT STOCK */

        if($payment_status == "Paid"){

            if($quantity > $stock){

                $error =
                "Not enough stock available!";

            }else{

                $newStock =
                $stock - $quantity;

                $deduct = $conn->prepare("
                UPDATE equipments
                SET stock=?
                WHERE id=?
                ");

                $deduct->execute([
                    $newStock,
                    $equipment_id
                ]);
            }
        }

        if(!isset($error)){

            $total =
            $price * $quantity;

            /* UPDATE ORDER */

            $updateOrder = $conn->prepare("
            UPDATE orders
            SET
                total_amount=?,
                payment_status=?
            WHERE order_id=?
            ");

            $updateOrder->execute([
                $total,
                $payment_status,
                $order_id
            ]);

            /* UPDATE ORDER ITEM */

            $updateItem = $conn->prepare("
            UPDATE order_items
            SET quantity=?
            WHERE order_id=?
            ");

            $updateItem->execute([
                $quantity,
                $order_id
            ]);

            header("Location: view_orders.php");
            exit();
        }
    }
}

/* =========================================
   DELETE ORDER
========================================= */

if(isset($_GET['delete'])){

    $delete_id = $_GET['delete'];

    /* GET ORDER */

    $getOrder = $conn->prepare("
    SELECT *
    FROM orders
    WHERE order_id=?
    ");

    $getOrder->execute([$delete_id]);

    $order = $getOrder->fetch();

    /* GET ORDER ITEM */

    $getItem = $conn->prepare("
    SELECT *
    FROM order_items
    WHERE order_id=?
    ");

    $getItem->execute([$delete_id]);

    $orderItem = $getItem->fetch();

    if($order && $orderItem){

        /* RETURN STOCK */

        if($order['payment_status'] == "Paid"){

            $equipment = $conn->prepare("
            SELECT *
            FROM equipments
            WHERE id=?
            ");

            $equipment->execute([
                $orderItem['equipment_id']
            ]);

            $item = $equipment->fetch();

            $newStock =
            $item['stock'] + $orderItem['quantity'];

            $restore = $conn->prepare("
            UPDATE equipments
            SET stock=?
            WHERE id=?
            ");

            $restore->execute([
                $newStock,
                $orderItem['equipment_id']
            ]);
        }

        /* DELETE ORDER ITEM */

        $deleteItem = $conn->prepare("
        DELETE FROM order_items
        WHERE order_id=?
        ");

        $deleteItem->execute([$delete_id]);

        /* DELETE ORDER */

        $deleteOrder = $conn->prepare("
        DELETE FROM orders
        WHERE order_id=?
        ");

        $deleteOrder->execute([$delete_id]);
    }

    header("Location: view_orders.php");
    exit();
}

/* =========================================
   FETCH ORDERS
========================================= */

$purchases = $conn->query("
SELECT
    orders.order_id,
    customers.fullname,
    equipments.equipment_name,
    equipments.category,
    equipments.price,
    order_items.quantity,
    orders.total_amount,
    orders.payment_status,
    orders.order_date

FROM orders

JOIN customers
ON orders.customer_id = customers.customer_id

JOIN order_items
ON orders.order_id = order_items.order_id

JOIN equipments
ON order_items.equipment_id = equipments.id

ORDER BY orders.order_id DESC
");

/* TOTAL PURCHASES */

$totalPurchases = $conn->query("
SELECT COUNT(*)
FROM orders
")->fetchColumn();

/* TOTAL SALES */

$totalSales = $conn->query("
SELECT IFNULL(SUM(total_amount),0)
FROM orders
WHERE payment_status='Paid'
")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>View Orders</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet">

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
}

.sidebar-menu a:hover,
.sidebar-menu .active{
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    color:white;
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
    background:#111827;
    padding:12px 18px;
    border-radius:16px;
}

/* CARD */

.card-box{
    background:#111827;
    border-radius:24px;
    padding:25px;
    margin-bottom:25px;
}

/* SEARCH */

.search-box{
    position:relative;
    width:420px;
}

.search-box input{
    width:100%;
    height:58px;
    border:none;
    border-radius:18px;
    background:#1e293b;
    color:white;
    padding-left:50px;
    font-size:15px;
}

.search-box i{
    position:absolute;
    top:18px;
    left:18px;
    color:#94a3b8;
}

/* TABLE */

.table{
    color:white;
}

.table-dark{
    border-radius:18px;
    overflow:hidden;
}

/* BUTTONS */

.btn-edit{
    background:#2563eb;
    color:white;
    border:none;
}

.btn-delete{
    background:#dc2626;
    color:white;
    border:none;
}

/* MODAL */

.modal-content{
    background:#111827;
    color:white;
    border-radius:20px;
}

.form-control,
.form-select{
    background:#1e293b;
    border:none;
    color:white;
    height:50px;
}

.form-control:focus,
.form-select:focus{
    background:#273449;
    color:white;
    box-shadow:none;
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

    <div class="topbar">

        <h1>View Orders</h1>

        <div class="admin-box">

            <?= $_SESSION['fullname']; ?>

        </div>

    </div>

    <!-- STATS -->

    <div class="row">

        <div class="col-md-6">

            <div class="card-box">

                <h5>Total Purchases</h5>

                <h2><?= $totalPurchases ?></h2>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card-box">

                <h5>Total Sales</h5>

                <h2>

                    ₱<?= number_format($totalSales,2) ?>

                </h2>

            </div>

        </div>

    </div>

    <!-- SEARCH -->

    <div class="d-flex justify-content-end mb-4">

        <div class="search-box">

            <i class="bi bi-search"></i>

            <input type="text"
                   id="searchInput"
                   placeholder="Search orders...">

        </div>

    </div>

    <!-- TABLE -->

    <div class="card-box">

        <div class="table-responsive">

            <table class="table table-dark table-hover align-middle">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Customer</th>
                        <th>Equipment</th>
                        <th>Category</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>

                    </tr>

                </thead>

                <tbody id="purchaseTable">

                    <?php while($row = $purchases->fetch()) { ?>

                    <tr>

                        <td>#<?= $row['order_id'] ?></td>

                        <td><?= $row['fullname'] ?></td>

                        <td><?= $row['equipment_name'] ?></td>

                        <td><?= $row['category'] ?></td>

                        <td><?= $row['quantity'] ?></td>

                        <td>

                            ₱<?= number_format($row['total_amount'],2) ?>

                        </td>

                        <td><?= $row['payment_status'] ?></td>

                        <td>

                            <?= date("F d, Y", strtotime($row['order_date'])) ?>

                        </td>

                        <td>

                            <!-- EDIT -->

                            <button class="btn btn-edit btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal<?= $row['order_id'] ?>">

                                Edit

                            </button>

                            <!-- DELETE -->

                            <a href="?delete=<?= $row['order_id'] ?>"
                               class="btn btn-delete btn-sm"
                               onclick="return confirm('Delete order?')">

                                Delete

                            </a>

                        </td>

                    </tr>

                    <!-- EDIT MODAL -->

                    <div class="modal fade"
                         id="editModal<?= $row['order_id'] ?>"
                         tabindex="-1">

                        <div class="modal-dialog">

                            <div class="modal-content">

                                <form method="POST">

                                    <div class="modal-header">

                                        <h5>Edit Order</h5>

                                        <button type="button"
                                                class="btn-close btn-close-white"
                                                data-bs-dismiss="modal">
                                        </button>

                                    </div>

                                    <div class="modal-body">

                                        <input type="hidden"
                                               name="order_id"
                                               value="<?= $row['order_id'] ?>">

                                        <!-- QUANTITY -->

                                        <div class="mb-3">

                                            <label class="mb-2">

                                                Quantity

                                            </label>

                                            <input type="number"
                                                   name="quantity"
                                                   class="form-control"
                                                   value="<?= $row['quantity'] ?>"
                                                   required>

                                        </div>

                                        <!-- STATUS -->

                                        <div class="mb-3">

                                            <label class="mb-2">

                                                Payment Status

                                            </label>

                                            <select name="payment_status"
                                                    class="form-select">

                                                <option value="Paid"
                                                <?= $row['payment_status']=="Paid" ? "selected" : "" ?>>

                                                    Paid

                                                </option>

                                                <option value="Pending"
                                                <?= $row['payment_status']=="Pending" ? "selected" : "" ?>>

                                                    Pending

                                                </option>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="modal-footer">

                                        <button type="submit"
                                                name="update_order"
                                                class="btn btn-primary">

                                            Save Changes

                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- SEARCH SCRIPT -->

<script>

const searchInput =
document.getElementById("searchInput");

searchInput.addEventListener("keyup", function(){

    const value =
    this.value.toLowerCase();

    const rows =
    document.querySelectorAll("#purchaseTable tr");

    rows.forEach(row => {

        const text =
        row.innerText.toLowerCase();

        row.style.display =
        text.includes(value)
        ? ""
        : "none";
    });
});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>