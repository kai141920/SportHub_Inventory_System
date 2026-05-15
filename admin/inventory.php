<?php
include '../auth/auth_check.php';
include '../config/db.php';

/* ADD EQUIPMENT */

if(isset($_POST['add'])){

    $equipment_name = $_POST['equipment_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // IMAGE

    $imageName = $_FILES['image']['name'];
    $tmpName = $_FILES['image']['tmp_name'];

    $uploadDir = "../uploads/";

    // CREATE FOLDER

    if(!is_dir($uploadDir)){
        mkdir($uploadDir, 0777, true);
    }

    // UNIQUE IMAGE NAME

    $newImageName = time() . "_" . $imageName;

    move_uploaded_file($tmpName, $uploadDir . $newImageName);

    // INSERT

    $stmt = $conn->prepare("
        INSERT INTO equipments
        (equipment_name,category,price,stock,image)
        VALUES (?,?,?,?,?)
    ");

    $stmt->execute([
        $equipment_name,
        $category,
        $price,
        $stock,
        $newImageName
    ]);

    header("Location: inventory.php");
    exit();
}

/* FETCH EQUIPMENTS */
$equipments = $conn->query("SELECT * FROM equipments");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Inventory Management</title>

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

/* CARD */

.inventory-card{
    background:#111827;
    border-radius:28px;
    padding:30px;
    box-shadow:0 10px 40px rgba(0,0,0,0.25);
}

/* HEADER */

.inventory-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
    gap:15px;
}

/* SEARCH */

.search-box{
    position:relative;
    width:100%;
    max-width:400px;
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

/* ADD BUTTON */

.add-btn{
    border:none;
    padding:13px 22px;
    border-radius:16px;
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    color:white;
    font-weight:600;
    transition:0.3s;
}

.add-btn:hover{
    transform:translateY(-3px);
}

/* TABLE */

.table-wrapper{
    overflow-x:auto;
}

.inventory-table{
    width:100%;
    border-collapse:separate;
    border-spacing:0 16px;
}

.inventory-table thead th{
    padding:0 18px 14px;
    color:#94a3b8;
    font-size:12px;
    font-weight:600;
    text-transform:uppercase;
    border:none;
    letter-spacing:1px;
}

.inventory-table tbody tr{
    background:#1e293b;
    transition:0.3s;
    box-shadow:0 8px 25px rgba(0,0,0,0.18);
}

.inventory-table tbody tr:hover{
    transform:translateY(-5px);
    background:#273449;
    box-shadow:0 12px 30px rgba(37,99,235,0.15);
}

.inventory-table tbody td{
    padding:18px;
    vertical-align:middle;
    border:none;
}

.inventory-table tbody td:first-child{
    border-top-left-radius:18px;
    border-bottom-left-radius:18px;
}

.inventory-table tbody td:last-child{
    border-top-right-radius:18px;
    border-bottom-right-radius:18px;
}

/* PRODUCT */

.product-info{
    display:flex;
    align-items:center;
    gap:15px;
}

.product-img{
    width:65px;
    height:65px;
    border-radius:18px;
    object-fit:cover;
    border:3px solid rgba(59,130,246,0.25);
}

.product-name{
    font-size:15px;
    font-weight:600;
}

.product-category{
    font-size:13px;
    color:#94a3b8;
    margin-top:4px;
}

/* PRICE */

.price{
    color:#60a5fa;
    font-weight:700;
    font-size:15px;
}

/* STOCK */

.stock-badge{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:10px 16px;
    border-radius:50px;
    font-size:13px;
    font-weight:600;
}

.in-stock{
    background:rgba(16,185,129,0.15);
    color:#34d399;
}

.low-stock{
    background:rgba(245,158,11,0.15);
    color:#fbbf24;
}

.out-stock{
    background:rgba(239,68,68,0.15);
    color:#f87171;
}

/* ACTIONS */

.action-group{
    display:flex;
    align-items:center;
    gap:10px;
}

.action-btn{
    width:42px;
    height:42px;
    border:none;
    border-radius:14px;
    color:white;
    transition:0.3s;
}

.edit-btn{
    background:linear-gradient(135deg,#f59e0b,#fbbf24);
}

.delete-btn{
    background:linear-gradient(135deg,#ef4444,#f87171);
}

.action-btn:hover{
    transform:translateY(-3px);
}

/* MODAL */

.modal-content{
    background:#111827;
    border:none;
    border-radius:24px;
    color:white;
}

.modal-header{
    border-bottom:1px solid rgba(255,255,255,0.08);
}

.modal-footer{
    border-top:1px solid rgba(255,255,255,0.08);
}

.form-control{
    background:#1e293b;
    border:none;
    color:white;
    height:50px;
    border-radius:14px;
}

.form-control:focus{
    background:#273449;
    color:white;
    box-shadow:none;
    border:1px solid #3b82f6;
}

.file-upload{
    background:#1e293b;
    border:2px dashed #3b82f6;
    border-radius:16px;
    padding:20px;
    text-align:center;
    cursor:pointer;
}

.file-upload i{
    font-size:40px;
    color:#60a5fa;
}

.preview-container{
    margin-top:15px;
    text-align:center;
}

.preview-container img{
    width:110px;
    height:110px;
    border-radius:16px;
    object-fit:cover;
    display:none;
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

    .inventory-header{
        flex-direction:column;
        align-items:flex-start;
    }

    .search-box{
        width:100%;
        max-width:100%;
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
            <a href="inventory.php" class="active">
                <i class="bi bi-box-seam-fill"></i>
                Inventory
            </a>
        </li>
        
        <li>
           <a href="purchase.php">
               <i class="bi bi-bar-chart-fill"></i>
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

        <h1>Inventory Management</h1>

        <div class="admin-box">

            <i class="bi bi-person-circle"></i>

            <span><?= $_SESSION['fullname']; ?></span>

        </div>

    </div>

    <!-- CARD -->

    <div class="inventory-card">

        <!-- HEADER -->

        <div class="inventory-header">

            <!-- SEARCH -->

            <div class="search-box">

                <i class="bi bi-search"></i>

                <input type="text"
                       id="searchInput"
                       placeholder="Search equipment, category...">

            </div>

            <!-- ADD BUTTON -->

            <button class="add-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#addModal">

                <i class="bi bi-plus-circle-fill"></i>

                Add Equipment

            </button>

        </div>

        <!-- TABLE -->

        <div class="table-wrapper">

            <table class="inventory-table">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Equipment</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>

                    </tr>

                </thead>

                <tbody id="inventoryTable">

                    <?php while($row = $equipments->fetch()) { ?>

                    <?php

                    $stockClass = "in-stock";

                    if($row['stock'] <= 10){
                        $stockClass = "low-stock";
                    }

                    if($row['stock'] <= 0){
                        $stockClass = "out-stock";
                    }

                    ?>

                    <tr>

                        <!-- ID -->

                        <td>

                            <strong>#<?= $row['id'] ?></strong>

                        </td>

                        <!-- PRODUCT -->

                        <td>

                            <div class="product-info">

                                <img src="../uploads/<?= $row['image'] ?>"
                                     class="product-img">

                                <div>

                                    <div class="product-name">

                                        <?= $row['equipment_name'] ?>

                                    </div>

                                    <div class="product-category">

                                        <?= $row['category'] ?>

                                    </div>

                                </div>

                            </div>

                        </td>

                        <!-- PRICE -->

                        <td>

                            <div class="price">

                                ₱<?= number_format($row['price'],2) ?>

                            </div>

                        </td>

                        <!-- STOCK -->

                        <td>

                            <span class="stock-badge <?= $stockClass ?>">

                                <i class="bi bi-box-seam-fill"></i>

                                <?= $row['stock'] ?> Stocks

                            </span>

                        </td>

                        <!-- ACTIONS -->

                        <td>

                            <div class="action-group">

                                <a href="edit_equipment.php?id=<?= $row['id'] ?>">

                                    <button class="action-btn edit-btn">

                                        <i class="bi bi-pencil-fill"></i>

                                    </button>

                                </a>

                                <a href="delete_equipment.php?id=<?= $row['id'] ?>"
                                   onclick="return confirm('Delete this equipment?')">

                                    <button class="action-btn delete-btn">

                                        <i class="bi bi-trash-fill"></i>

                                    </button>

                                </a>

                            </div>

                        </td>

                    </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- MODAL -->

<div class="modal fade"
     id="addModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    <i class="bi bi-plus-circle-fill"></i>

                    Add Equipment

                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <!-- FORM -->

            <form method="POST"
                  enctype="multipart/form-data">

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="mb-2">Equipment Name</label>

                        <input type="text"
                               name="equipment_name"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="mb-2">Category</label>

                        <input type="text"
                               name="category"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="mb-2">Price</label>

                        <input type="number"
                               step="0.01"
                               name="price"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="mb-2">Stock</label>

                        <input type="number"
                               name="stock"
                               class="form-control"
                               required>

                    </div>

                    <!-- IMAGE -->

                    <div class="mb-3">

                        <label class="mb-2">

                            Equipment Image

                        </label>

                        <label class="file-upload d-block">

                            <i class="bi bi-cloud-arrow-up-fill"></i>

                            <p class="mt-2 mb-0">

                                Upload Equipment Image

                            </p>

                            <input type="file"
                                   name="image"
                                   id="imageInput"
                                   accept="image/*"
                                   hidden
                                   required>

                        </label>

                        <div class="preview-container">

                            <img id="previewImage">

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->

                <div class="modal-footer">

                    <button type="reset"
                            class="btn btn-secondary"
                            id="clearButton">

                        Clear

                    </button>

                    <button type="submit"
                            name="add"
                            class="btn btn-primary">

                        Save Equipment

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- BOOTSTRAP -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- SEARCH SCRIPT -->

<script>

const searchInput = document.getElementById("searchInput");

searchInput.addEventListener("keyup", function(){

    const filter = this.value.toLowerCase();

    const rows = document.querySelectorAll("#inventoryTable tr");

    rows.forEach(row => {

        const text = row.innerText.toLowerCase();

        row.style.display = text.includes(filter)
            ? ""
            : "none";

    });

});

</script>

<!-- IMAGE PREVIEW -->

<script>

const imageInput = document.getElementById("imageInput");
const previewImage = document.getElementById("previewImage");
const clearButton = document.getElementById("clearButton");

imageInput.addEventListener("change", function(){

    const file = this.files[0];

    if(file){

        const reader = new FileReader();

        reader.onload = function(e){

            previewImage.src = e.target.result;
            previewImage.style.display = "block";
        }

        reader.readAsDataURL(file);
    }
});

/* CLEAR IMAGE */

clearButton.addEventListener("click", function(){

    previewImage.style.display = "none";
    previewImage.src = "";
});

</script>

</body>
</html>