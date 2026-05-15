<?php
include '../config/db.php';

$products = $conn->query("SELECT * FROM equipments");
?>
<!DOCTYPE html>
<html>
<head>
<title>Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
<h2>Sports Equipment Shop</h2>

<div class="row">
<?php while($row = $products->fetch()) { ?>
<div class="col-md-4">
<div class="card p-3 mb-3">
<h4><?= $row['equipment_name'] ?></h4>
<p>Category: <?= $row['category'] ?></p>
<p>Price: ₱<?= $row['price'] ?></p>
<p>Stock: <?= $row['stock'] ?></p>

<form action="checkout.php" method="POST">
<input type="hidden" name="equipment_id" value="<?= $row['id'] ?>">
<input type="number" name="quantity" class="form-control mb-2" min="1" required>

<button class="btn btn-success" name="buy">Buy Now</button>
</form>
</div>
</div>
<?php } ?>
</div>
</body>
</html>
