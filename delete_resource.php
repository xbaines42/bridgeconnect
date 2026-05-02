<?php
require_once "auth.php";
require_once "db.php";
requireRole(['admin']);

$id = intval($_GET['id']);
$stmt = $conn->prepare("DELETE FROM resources WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: admin_dashboard.php");
exit();
?>
