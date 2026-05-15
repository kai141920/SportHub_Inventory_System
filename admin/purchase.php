<?php
include '../auth/auth_check.php';
include '../config/db.php';

/* =========================================
   UPDATE PAYMENT STATUS
========================================= */

if(isset($_POST['update_status'])){

    $order_id = $_POST['order_id'];
    $payment_status = $_POST['payment_status'];

    /* GET ORDER */

    $getOrder = $conn->prepare("
    SELECT
        orders.payment_status,
        order_items.quantity,
        order_items.equipment_id

    FROM orders

    JOIN order_items
    ON orders.order_id = order_items.order_id

    WHERE orders.order_id=?
    ");

    $getOrder->execute([$order_id]);

    $order = $getOrder->fetch(PDO::FETCH_ASSOC);

    if($order){

        $old_status = $order['payment_status'];
        $quantity = $order['quantity'];
        $equipment_id = $order['equipment_id'];

        /* GET EQUIPMENT */

        $equipment = $conn->prepare("
        SELECT *
        FROM equipments
        WHERE id=?
        ");

        $equipment->execute([$equipment_id]);

        $item = $equipment->fetch(PDO::FETCH_ASSOC);

        if($item){

            $stock = $item['stock'];

            /* PENDING TO PAID */

            if($old_status == "Pending"
            && $payment_status == "Paid"){

                if($quantity <= $stock){

                    $newStock = $stock - $quantity;

                    $updateStock = $conn->prepare("
                    UPDATE equipments
                    SET stock=?
                    WHERE id=?
                    ");

                    $updateStock->execute([
                        $newStock,
                        $equipment_id
                    ]);
                }
            }

            /* PAID TO PENDING */

            if($old_status == "Paid"
            && $payment_status == "Pending"){

                $newStock = $stock + $quantity;

                $updateStock = $conn->prepare("
                UPDATE equipments
                SET stock=?
                WHERE id=?
                ");

                $updateStock->execute([
                    $newStock,
                    $equipment_id
                ]);
            }

            /* UPDATE STATUS */

            $update = $conn->prepare("
            UPDATE orders
            SET payment_status=?
            WHERE order_id=?
            ");

            $update->execute([
                $payment_status,
                $order_id
            ]);
        }
    }

    header("Location: purchase.php");
    exit();
}

/* =========================================
   ADD PURCHASE
========================================= */

if(isset($_POST['add_purchase'])){

    $customer_id = $_POST['customer_id'];
    $equipment_id = $_POST['equipment_id'];
    $quantity = $_POST['quantity'];
    $payment_status = $_POST['payment_status'];

    /* GET EQUIPMENT */

    $equipment = $conn->prepare("
    SELECT *
    FROM equipments
    WHERE id=?
    ");

    $equipment->execute([$equipment_id]);

    $item = $equipment->fetch(PDO::FETCH_ASSOC);

    if($item){

        $price = $item['price'];
        $stock = $item['stock'];

        /* CHECK STOCK */

        if($payment_status == "Paid"){

            if($quantity > $stock){

                $error = "Not enough stock available!";

            }else{

                /* DEDUCT STOCK */

                $newStock = $stock - $quantity;

                $updateStock = $conn->prepare("
                UPDATE equipments
                SET stock=?
                WHERE id=?
                ");

                $updateStock->execute([
                    $newStock,
                    $equipment_id
                ]);
            }
        }

        if(!isset($error)){

            $total = $price * $quantity;

            /* INSERT ORDER */

            $stmt = $conn->prepare("
            INSERT INTO orders
            (
                customer_id,
                total_amount,
                payment_status,
                order_date
            )
            VALUES (?,?,?,NOW())
            ");

            $stmt->execute([
                $customer_id,
                $total,
                $payment_status
            ]);

            $order_id = $conn->lastInsertId();

            /* INSERT ORDER ITEM */

            $orderItem = $conn->prepare("
            INSERT INTO order_items
            (
                order_id,
                equipment_id,
                quantity
            )
            VALUES (?,?,?)
            ");

            $orderItem->execute([
                $order_id,
                $equipment_id,
                $quantity
            ]);

            header("Location: purchase.php");
            exit();
        }
    }
}

/* =========================================
   DELETE PURCHASE
========================================= */

if(isset($_GET['delete'])){

    $delete_id = $_GET['delete'];

    /* GET ORDER */

    $getOrder = $conn->prepare("
    SELECT
        orders.payment_status,
        order_items.quantity,
        order_items.equipment_id

    FROM orders

    JOIN order_items
    ON orders.order_id = order_items.order_id

    WHERE orders.order_id=?
    ");

    $getOrder->execute([$delete_id]);

    $order = $getOrder->fetch(PDO::FETCH_ASSOC);

    if($order){

        /* RETURN STOCK */

        if($order['payment_status'] == "Paid"){

            $equipment = $conn->prepare("
            SELECT *
            FROM equipments
            WHERE id=?
            ");

            $equipment->execute([
                $order['equipment_id']
            ]);

            $item = $equipment->fetch(PDO::FETCH_ASSOC);

            $newStock =
            $item['stock'] + $order['quantity'];

            $restore = $conn->prepare("
            UPDATE equipments
            SET stock=?
            WHERE id=?
            ");

            $restore->execute([
                $newStock,
                $order['equipment_id']
            ]);
        }

        /* DELETE ORDER ITEMS */

        $deleteItems = $conn->prepare("
        DELETE FROM order_items
        WHERE order_id=?
        ");

        $deleteItems->execute([$delete_id]);

        /* DELETE ORDER */

        $deleteOrder = $conn->prepare("
        DELETE FROM orders
        WHERE order_id=?
        ");

        $deleteOrder->execute([$delete_id]);
    }

    header("Location: purchase.php");
    exit();
}

/* =========================================
   FETCH PURCHASES
========================================= */

$purchases = $conn->query("
SELECT
    orders.order_id,
    customers.fullname,
    equipments.equipment_name,
    equipments.category,
    equipments.price,
    equipments.image,
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

/* FETCH CUSTOMERS */

$customers = $conn->query("
SELECT *
FROM customers
ORDER BY fullname ASC
");

/* FETCH EQUIPMENTS */

$equipments = $conn->query("
SELECT *
FROM equipments
ORDER BY category ASC
");

/* TOTALS */

$totalPurchases = $conn->query("
SELECT COUNT(*)
FROM orders
")->fetchColumn();

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

<title>Purchase Management</title>

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

/* =========================================
   PROFESSIONAL SEARCH BAR
========================================= */

.search-wrapper{
    width:100%;
    display:flex;
    justify-content:flex-end;
    align-items:center;
}

.search-box-pro{

    width:460px;
    height:65px;

    position:relative;

    display:flex;
    align-items:center;

    padding:0 18px;

    border-radius:24px;

    overflow:hidden;

    background:
    linear-gradient(
        145deg,
        rgba(30,41,59,0.98),
        rgba(15,23,42,0.98)
    );

    border:1px solid rgba(255,255,255,0.06);

    box-shadow:
    0 15px 35px rgba(0,0,0,0.30),
    inset 0 1px 1px rgba(255,255,255,0.03);

    transition:all .35s ease;
}

.search-box-pro:hover{

    transform:translateY(-2px);

    box-shadow:
    0 18px 45px rgba(37,99,235,0.18),
    0 15px 35px rgba(0,0,0,0.35);
}

.search-box-pro:focus-within{

    border:1px solid rgba(59,130,246,0.70);

    box-shadow:
    0 0 0 5px rgba(59,130,246,0.14),
    0 20px 50px rgba(37,99,235,0.22);
}

.search-glow{

    position:absolute;

    width:180px;
    height:180px;

    background:
    rgba(59,130,246,0.14);

    border-radius:50%;

    top:-60px;
    right:-60px;

    filter:blur(30px);
}

.search-icon{

    font-size:20px;

    color:#94a3b8;

    margin-right:15px;

    z-index:2;
}

.search-box-pro input{

    flex:1;

    height:100%;

    border:none;
    outline:none;

    background:transparent;

    color:white;

    font-size:15px;

    font-weight:500;

    z-index:2;
}

.search-box-pro input::placeholder{

    color:#64748b;
}

.search-filter-btn{

    width:46px;
    height:46px;

    border:none;

    border-radius:15px;

    background:
    linear-gradient(
        135deg,
        #2563eb,
        #3b82f6
    );

    color:white;

    display:flex;
    justify-content:center;
    align-items:center;

    transition:.3s;

    z-index:2;
}

.search-filter-btn:hover{

    transform:scale(1.07);

    box-shadow:
    0 10px 25px rgba(59,130,246,0.35);
}

.logo{
    margin-bottom:45px;
}

.logo h2{
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
    gap:14px;
    padding:14px 18px;
    border-radius:14px;
    transition:.3s;
}

.sidebar-menu a:hover,
.sidebar-menu .active{
    background:#2563eb;
    color:white;
}

/* MAIN */

.main-content{
    margin-left:260px;
    padding:30px;
}

/* CARD */

.card-box{
    background:#111827;
    border-radius:24px;
    padding:25px;
    margin-bottom:25px;
}

/* EQUIPMENT CARD */

.equipment-card{
    background:#111827;
    border-radius:20px;
    overflow:hidden;
    transition:.3s;
    height:100%;
}

.equipment-card:hover{
    transform:translateY(-5px);
}

.equipment-image{
    width:100%;
    height:220px;
    object-fit:cover;
}

.equipment-body{
    padding:20px;
}

/* BUTTON */

.btn-order{
    width:100%;
    border:none;
    background:#2563eb;
    color:white;
    padding:12px;
    border-radius:12px;
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
            <a href="purchase.php" class="active">

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

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1>Purchase Management</h1>

        <a href="view_orders.php"
           class="btn btn-success">

            <i class="bi bi-eye-fill"></i>

            View Orders

        </a>

    </div>

    <!-- SEARCH BAR -->

    <div class="search-wrapper mb-4">

        <div class="search-box-pro">

            <div class="search-glow"></div>

            <i class="bi bi-search search-icon"></i>

            <input type="text"
                   id="searchInput"
                   placeholder="Search orders, customers, equipments...">

            <button type="button"
                    class="search-filter-btn">

                <i class="bi bi-sliders"></i>

            </button>

        </div>

    </div>

    <!-- STATS -->

    <div class="row mb-4">

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

    <!-- EQUIPMENT CARDS -->

    <div class="row"
         id="equipmentContainer">

        <?php while($equipment = $equipments->fetch()) { ?>

        <div class="col-md-4 mb-4 equipment-item">

            <div class="equipment-card">

                <img src="../uploads/<?= $equipment['image'] ?>"
                     class="equipment-image">

                <div class="equipment-body">

                    <h4>

                        <?= $equipment['equipment_name'] ?>

                    </h4>

                    <p>

                        Category:
                        <?= $equipment['category'] ?>

                    </p>

                    <p>

                        Price:
                        ₱<?= number_format($equipment['price'],2) ?>

                    </p>

                    <p>

                        Stock:
                        <?= $equipment['stock'] ?>

                    </p>

                    <button class="btn-order"
                            data-bs-toggle="modal"
                            data-bs-target="#orderModal<?= $equipment['id'] ?>">

                        Order Now

                    </button>

                </div>

            </div>

        </div>

        <!-- ORDER MODAL -->

        <div class="modal fade"
             id="orderModal<?= $equipment['id'] ?>"
             tabindex="-1">

            <div class="modal-dialog">

                <div class="modal-content">

                    <form method="POST">

                        <div class="modal-header">

                            <h5>

                                Add Purchase

                            </h5>

                            <button type="button"
                                    class="btn-close btn-close-white"
                                    data-bs-dismiss="modal">
                            </button>

                        </div>

                        <div class="modal-body">

                            <input type="hidden"
                                   name="equipment_id"
                                   value="<?= $equipment['id'] ?>">

                            <!-- CUSTOMER -->

                            <div class="mb-3">

                                <label>

                                    Customer

                                </label>

                                <select name="customer_id"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        Select Customer
                                    </option>

                                    <?php
                                    $customerList = $conn->query("
                                    SELECT *
                                    FROM customers
                                    ORDER BY fullname ASC
                                    ");

                                    while($customer = $customerList->fetch()) {
                                    ?>

                                    <option value="<?= $customer['customer_id'] ?>">

                                        <?= $customer['fullname'] ?>

                                    </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <!-- QUANTITY -->

                            <div class="mb-3">

                                <label>

                                    Quantity

                                </label>

                                <input type="number"
                                       name="quantity"
                                       class="form-control quantityInput"
                                       data-price="<?= $equipment['price'] ?>"
                                       required>

                            </div>

                            <!-- TOTAL -->

                            <div class="mb-3">

                                <label>

                                    Total Amount

                                </label>

                                <input type="text"
                                       class="form-control totalAmount"
                                       readonly>

                            </div>

                            <!-- STATUS -->

                            <div class="mb-3">

                                <label>

                                    Payment Status

                                </label>

                                <select name="payment_status"
                                        class="form-select">

                                    <option value="Pending">
                                        Pending
                                    </option>

                                    <option value="Paid">
                                        Paid
                                    </option>

                                </select>

                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="submit"
                                    name="add_purchase"
                                    class="btn btn-primary">

                                Save Purchase

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        <?php } ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

/* SEARCH */

document.getElementById("searchInput")
.addEventListener("keyup", function(){

    let value =
    this.value.toLowerCase();

    let items =
    document.querySelectorAll(".equipment-item");

    items.forEach(item => {

        let text =
        item.innerText.toLowerCase();

        item.style.display =
        text.includes(value)
        ? ""
        : "none";
    });
});

/* TOTAL COMPUTATION */

document.querySelectorAll(".quantityInput")
.forEach(input => {

    input.addEventListener("input", function(){

        let quantity =
        this.value;

        let price =
        this.dataset.price;

        let total =
        quantity * price;

        let modal =
        this.closest(".modal");

        modal.querySelector(".totalAmount")
        .value = "₱" + total.toFixed(2);
    });
});

</script>

</body>
</html>