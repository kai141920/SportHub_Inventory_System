<?php
include '../auth/auth_check.php';
?>
<!DOCTYPE html>
<html>
<head>
<title>User Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
<h2>Welcome <?= $_SESSION['fullname'] ?></h2>

<a href="shop.php" class="btn btn-primary">Go to Shop</a>
<a href="../auth/logout.php" class="btn btn-danger">Logout</a>
</body>
</html>
