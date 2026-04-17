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

// ------------------ DELETE ITEM ------------------
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $item_id = $_POST['item_id'] ?? '';

    if (empty($item_id)) {
        echo json_encode(["status" => "error", "message" => "No item ID provided"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM tb_items WHERE item_id = ?");
    $stmt->execute([$item_id]);

    echo json_encode([
        "status" => $stmt->rowCount() > 0 ? "success" : "error",
        "message" => $stmt->rowCount() > 0 ? "Item deleted successfully" : "Item not found"
    ]);
    exit;
}

// ------------------ FETCH ITEMS (JOIN CATEGORY) ------------------
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $stmt = $conn->prepare("SELECT i.item_id, i.record_id, i.category_id, i.item_desc, i.quantity, i.unit_cost, i.line_total FROM tb_items i LEFT JOIN tb_category c ON i.category_id = c.category_id ORDER BY i.item_id ASC");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "records" => $items
    ]);
    exit;
}


function lookupCategoryId($conn, $category_name) {
    $stmt = $conn->prepare("SELECT category_id FROM tb_category WHERE category_name = ? LIMIT 1");
    $stmt->execute([$category_name]);
    return $stmt->fetchColumn();
}

// ------------------ ADD OR UPDATE ITEM ------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_GET['action'] ?? '';

    $item_id = $_POST['item_id'] ?? '';
    $record_id = !empty($_POST['record_id']) ? intval($_POST['record_id']) : null;
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $category_name = $_POST['category_name'] ?? '';
    $item_desc = trim($_POST['item_desc'] ?? '');
    $quantity = floatval($_POST['quantity'] ?? 0);
    $unit_cost = floatval($_POST['unit_cost'] ?? 0);

    if (!$item_desc) {
        echo json_encode(["status" => "error", "message" => "Item description is required"]);
        exit;
    }

    if ($quantity <= 0) {
        echo json_encode(["status" => "error", "message" => "Quantity must be greater than zero"]);
        exit;
    }

    if (!$category_id && $category_name) {
        $category_id = lookupCategoryId($conn, $category_name);
    }

    if (!$category_id) {
        echo json_encode(["status" => "error", "message" => "Category is required"]);
        exit;
    }

    $line_total = $quantity * $unit_cost;

    // ------------------ UPDATE ------------------
    if ($action === 'update') {

        if (empty($item_id)) {
            echo json_encode(["status" => "error", "message" => "Missing item ID"]);
            exit;
        }

        $stmt = $conn->prepare("
            UPDATE tb_items 
            SET record_id=?, category_id=?, item_desc=?, quantity=?, unit_cost=?, line_total=? 
            WHERE item_id=?
        ");

        $stmt->execute([
            $record_id,
            $category_id,
            $item_desc,
            $quantity,
            $unit_cost,
            $line_total,
            $item_id
        ]);

        echo json_encode([
            "status" => "success",
            "message" => "Item updated successfully"
        ]);
        exit;
    }

    // ------------------ INSERT ------------------
    $stmt = $conn->prepare("
        INSERT INTO tb_items 
        (record_id, category_id, item_desc, quantity, unit_cost, line_total)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $record_id,
        $category_id,
        $item_desc,
        $quantity,
        $unit_cost,
        $line_total
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Item added successfully"
    ]);
    exit;
}
// Default fallback
echo json_encode(["status" => "error", "message" => "Unsupported request"]);
exit;
?>

