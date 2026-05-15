<?php
include '../auth/auth_check.php';
include '../config/db.php';

/* GET ID */

$id = $_GET['id'];

/* FETCH EQUIPMENT */

$stmt = $conn->prepare("SELECT * FROM equipments WHERE id=?");
$stmt->execute([$id]);
$data = $stmt->fetch();

/* UPDATE EQUIPMENT */

if(isset($_POST['update'])){

    $equipment_name = $_POST['equipment_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    /* CHECK IMAGE */

    if($_FILES['image']['name'] != ''){

        $imageName = $_FILES['image']['name'];
        $tmpName = $_FILES['image']['tmp_name'];

        $uploadDir = "../uploads/";

        // CREATE FOLDER

        if(!is_dir($uploadDir)){
            mkdir($uploadDir, 0777, true);
        }

        // UNIQUE NAME

        $newImageName = time() . "_" . $imageName;

        move_uploaded_file($tmpName, $uploadDir . $newImageName);

    } else {

        // KEEP OLD IMAGE

        $newImageName = $data['image'];
    }

    /* UPDATE */

    $stmt = $conn->prepare("
        UPDATE equipments
        SET equipment_name=?,
            category=?,
            price=?,
            stock=?,
            image=?
        WHERE id=?
    ");

    $stmt->execute([
        $equipment_name,
        $category,
        $price,
        $stock,
        $newImageName,
        $id
    ]);

    header("Location: inventory.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Equipment</title>

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
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

/* CARD */

.edit-card{
    width:100%;
    max-width:950px;
    background:#111827;
    border-radius:28px;
    overflow:hidden;
    display:flex;
    box-shadow:0 15px 40px rgba(0,0,0,0.35);
}

/* LEFT SIDE */

.left-side{
    width:40%;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    display:flex;
    justify-content:center;
    align-items:center;
    position:relative;
    overflow:hidden;
}

/* BACKGROUND */

.left-side::before{
    content:'';
    position:absolute;
    width:250px;
    height:250px;
    background:rgba(255,255,255,0.08);
    border-radius:50%;
    top:-100px;
    right:-100px;
}

.left-side::after{
    content:'';
    position:absolute;
    width:200px;
    height:200px;
    background:rgba(255,255,255,0.05);
    border-radius:50%;
    bottom:-90px;
    left:-90px;
}

/* IMAGE */

.current-image{
    width:220px;
    height:220px;
    border-radius:24px;
    object-fit:cover;
    border:5px solid rgba(255,255,255,0.25);
    z-index:2;
}

/* RIGHT SIDE */

.right-side{
    width:60%;
    padding:40px;
    position:relative;
    color:white;
}

/* BACK BUTTON */

.back-btn{
    position:absolute;
    top:20px;
    left:20px;
    width:42px;
    height:42px;
    border-radius:12px;
    background:#1e293b;
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    text-decoration:none;
    transition:0.3s;
}

.back-btn:hover{
    background:#2563eb;
    color:white;
    transform:translateX(-3px);
}

/* HEADER */

.form-header{
    margin-top:35px;
    margin-bottom:30px;
}

.form-header h2{
    font-size:30px;
    font-weight:700;
}

.form-header p{
    color:#94a3b8;
    margin-top:6px;
    font-size:14px;
}

/* INPUT GROUP */

.input-group-custom{
    margin-bottom:18px;
}

.input-group-custom label{
    display:block;
    margin-bottom:8px;
    font-size:13px;
    color:#cbd5e1;
}

/* INPUT */

.form-control{
    height:54px;
    border:none;
    border-radius:16px;
    background:#1e293b;
    color:white;
    padding-left:16px;
    font-size:14px;
}

.form-control:focus{
    background:#273449;
    color:white;
    box-shadow:none;
    border:1px solid #3b82f6;
}

.form-control::placeholder{
    color:#94a3b8;
}

/* FILE */

.file-upload{
    background:#1e293b;
    border:2px dashed #3b82f6;
    border-radius:18px;
    padding:20px;
    text-align:center;
    cursor:pointer;
    transition:0.3s;
}

.file-upload:hover{
    background:#273449;
}

.file-upload i{
    font-size:40px;
    color:#60a5fa;
    margin-bottom:8px;
}

.file-upload p{
    margin:0;
    color:#cbd5e1;
    font-size:14px;
}

/* PREVIEW */

.preview-container{
    margin-top:15px;
    text-align:center;
}

.preview-container img{
    width:130px;
    height:130px;
    border-radius:18px;
    object-fit:cover;
    border:3px solid #2563eb;
    display:none;
}

/* BUTTONS */

.button-group{
    display:flex;
    gap:12px;
    margin-top:25px;
}

.update-btn{
    flex:1;
    height:54px;
    border:none;
    border-radius:16px;
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    color:white;
    font-weight:600;
    transition:0.3s;
}

.update-btn:hover{
    transform:translateY(-2px);
}

.clear-btn{
    flex:1;
    height:54px;
    border:none;
    border-radius:16px;
    background:#334155;
    color:white;
    font-weight:600;
    transition:0.3s;
}

.clear-btn:hover{
    background:#475569;
}

/* RESPONSIVE */

@media(max-width:900px){

    .edit-card{
        flex-direction:column;
    }

    .left-side,
    .right-side{
        width:100%;
    }

    .left-side{
        height:280px;
    }
}

@media(max-width:768px){

    .button-group{
        flex-direction:column;
    }

    .right-side{
        padding:30px;
    }

    .current-image{
        width:180px;
        height:180px;
    }
}

</style>

</head>

<body>

<div class="edit-card">

    <!-- LEFT -->

    <div class="left-side">

        <img src="../uploads/<?= $data['image'] ?>"
             class="current-image"
             id="currentImage">

    </div>

    <!-- RIGHT -->

    <div class="right-side">

        <!-- BACK -->

        <a href="inventory.php"
           class="back-btn">

            <i class="bi bi-arrow-left"></i>

        </a>

        <!-- HEADER -->

        <div class="form-header">

            <h2>Edit Equipment</h2>

            <p>
                Update equipment information below.
            </p>

        </div>

        <!-- FORM -->

        <form method="POST"
              enctype="multipart/form-data"
              id="editForm">

            <!-- NAME -->

            <div class="input-group-custom">

                <label>Equipment Name</label>

                <input type="text"
                       name="equipment_name"
                       class="form-control"
                       value="<?= $data['equipment_name'] ?>"
                       required>

            </div>

            <!-- CATEGORY -->

            <div class="input-group-custom">

                <label>Category</label>

                <input type="text"
                       name="category"
                       class="form-control"
                       value="<?= $data['category'] ?>"
                       required>

            </div>

            <!-- PRICE -->

            <div class="input-group-custom">

                <label>Price</label>

                <input type="number"
                       step="0.01"
                       name="price"
                       class="form-control"
                       value="<?= $data['price'] ?>"
                       required>

            </div>

            <!-- STOCK -->

            <div class="input-group-custom">

                <label>Stock Quantity</label>

                <input type="number"
                       name="stock"
                       class="form-control"
                       value="<?= $data['stock'] ?>"
                       required>

            </div>

            <!-- IMAGE -->

            <div class="input-group-custom">

                <label>Update Equipment Image</label>

                <label class="file-upload">

                    <i class="bi bi-cloud-arrow-up-fill"></i>

                    <p>Upload New Image</p>

                    <input type="file"
                           name="image"
                           id="imageInput"
                           accept="image/*"
                           hidden>

                </label>

                <!-- PREVIEW -->

                <div class="preview-container">

                    <img id="previewImage">

                </div>

            </div>

            <!-- BUTTONS -->

            <div class="button-group">

                <!-- UPDATE -->

                <button type="submit"
                        name="update"
                        class="update-btn">

                    <i class="bi bi-check-circle-fill"></i>

                    Update Equipment

                </button>

                <!-- CLEAR -->

                <button type="reset"
                        class="clear-btn"
                        id="clearButton">

                    <i class="bi bi-eraser-fill"></i>

                    Clear

                </button>

            </div>

        </form>

    </div>

</div>

<!-- IMAGE PREVIEW -->

<script>

const imageInput = document.getElementById("imageInput");
const previewImage = document.getElementById("previewImage");
const currentImage = document.getElementById("currentImage");
const clearButton = document.getElementById("clearButton");

imageInput.addEventListener("change", function(){

    const file = this.files[0];

    if(file){

        const reader = new FileReader();

        reader.onload = function(e){

            previewImage.src = e.target.result;
            previewImage.style.display = "block";

            currentImage.src = e.target.result;
        }

        reader.readAsDataURL(file);
    }
});

/* CLEAR */

clearButton.addEventListener("click", function(){

    previewImage.style.display = "none";
    previewImage.src = "";

    currentImage.src = "../uploads/<?= $data['image'] ?>";
});

</script>

</body>
</html>