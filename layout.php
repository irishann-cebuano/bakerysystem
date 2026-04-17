<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bakery Expenses Record System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/bakery_records_system/style.css">
</head>

<body>
<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /bakery_records_system/index.php');
    exit;
}
?>

<header>
<h1>🍞 Bakery Daily Expenses Record System</h1>
</header>

<div class="sidebar">
 <ul>
    <li><a href="/bakery_records_system/sidebars/dashboard.php">🏠Dashboard</a></li>
    <li><a href="/bakery_records_system/sidebars/daily_records.php">📅Daily Records</a></li>
    <li><a href="/bakery_records_system/sidebars/employee.php">👨‍🍳Employee Management</a></li>
    <li><a href="/bakery_records_system/sidebars/labor_records.php">💼Labor Records</a></li>
    <li><a href="/bakery_records_system/sidebars/expense_categories.php">🏷️Expense Categories</a></li>
    <li><a href="/bakery_records_system/sidebars/expense_items.php">📦Expense Items</a></li>
    <li><a href="/bakery_records_system/sidebars/logout.php">🚪Logout</a></li>
</ul>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>