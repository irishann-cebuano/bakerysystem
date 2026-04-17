<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}
header('Content-Type: application/json');

try {
    $conn = new PDO("mysql:host=localhost;dbname=db_bakeryrecord_system", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "DB Connection failed: " . $e->getMessage()]);
    exit;
}

// ------------------ DELETE RECORD ------------------
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $record_id = $_POST['record_id'] ?? '';

    if (empty($record_id)) {
        echo json_encode(["success" => false, "message" => "No record ID sent"]);
        exit;
    }

   
    $conn->prepare("DELETE FROM tb_labor WHERE record_id = ?")->execute([$record_id]);

    // delete main record
    $stmt = $conn->prepare("DELETE FROM tb_daily_rec WHERE record_id = ?");
    $stmt->execute([$record_id]);

    echo json_encode(["success" => true]);
    exit;
}

// ------------------ ADD RECORD ------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_GET['action']) || $_GET['action'] == 'insert')) {
    $date = $_POST['date'] ?? date('Y-m-d');
    $expense_category = $_POST['expense_category'] ?? '';
    $expense_items = $_POST['expense_items'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);

    if (!$expense_category || !$expense_items) {
        echo json_encode(["success" => false, "message" => "Expense category and item are required"]);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            INSERT INTO tb_daily_rec (date, expense_category, expense_items, amount)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$date, $expense_category, $expense_items, $amount]);

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}

// ------------------ FETCH RECORDS ------------------
try {
    $stmt = $conn->prepare("
        SELECT
            dr.record_id,
            dr.date,
            dr.expense_category,
            c.category_name,
            dr.expense_items,
            i.item_desc,
            dr.amount
        FROM tb_daily_rec dr
        LEFT JOIN tb_category c ON dr.expense_category = c.category_name
        LEFT JOIN tb_items i ON dr.expense_items = i.item_desc
        ORDER BY dr.date ASC, dr.record_id ASC
    ");
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Total expenses (all-time)
    $stmtTotal = $conn->prepare("SELECT SUM(amount) AS total_expenses FROM tb_daily_rec");
    $stmtTotal->execute();
    $total_expenses = floatval($stmtTotal->fetchColumn() ?? 0);


    echo json_encode([
        "success" => true,
        "records" => $records,
        "total_expenses" => $total_expenses
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}


?>
