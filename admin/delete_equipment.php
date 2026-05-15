<?php
include '../config/db.php';
$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM equipments WHERE id=?");
$stmt->execute([$id]);
header("Location: inventory.php");
?>
