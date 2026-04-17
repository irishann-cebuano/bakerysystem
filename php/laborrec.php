<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}
$conn = new PDO("mysql:host=localhost;dbname=db_bakeryrecord_system", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'fetch') {

    $stmt = $conn->prepare("
        SELECT
            l.labor_id,
            l.employee_id,
            l.record_id,
            l.hours_worked,
            l.rate_per_hour,
            l.total_pay,
            dr.date
        FROM tb_labor l
        LEFT JOIN tb_daily_rec dr ON l.record_id = dr.record_id
        ORDER BY l.labor_id asc
    ");

    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "records" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== 'update') {

    $employee_id = $_POST['employee_id'] ?? '';
    $hours_worked = (float)($_POST['hours_worked'] ?? 0);
    $rate_per_hour = (float)($_POST['rate_per_hour'] ?? 0);

    if (!$employee_id || $hours_worked <= 0 || $rate_per_hour <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid input"]);
        exit;
    }

    $total_pay = $hours_worked * $rate_per_hour;

    // latest record auto-link
    $stmt = $conn->prepare("SELECT record_id FROM tb_daily_rec ORDER BY record_id DESC LIMIT 1");
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        echo json_encode(["status" => "error", "message" => "No daily record found"]);
        exit;
    }

    $record_id = $record['record_id'];

    // validate employee exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tb_employee WHERE employee_id = ?");
    $stmt->execute([$employee_id]);

    if ($stmt->fetchColumn() == 0) {
        echo json_encode(["status" => "error", "message" => "Invalid employee"]);
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO tb_labor
        (employee_id, record_id, hours_worked, rate_per_hour, total_pay)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $employee_id,
        $record_id,
        $hours_worked,
        $rate_per_hour,
        $total_pay
    ]);

    echo json_encode(["status" => "success", "message" => "Labor added"]);
    exit;
}
if ($action === 'update') {

    $labor_id = $_POST['labor_id'] ?? '';
    $employee_id = $_POST['employee_id'] ?? '';
    $record_id = $_POST['record_id'] ?? '';
    $hours_worked = (float)($_POST['hours_worked'] ?? 0);
    $rate_per_hour = (float)($_POST['rate_per_hour'] ?? 0);

    if (!$labor_id) {
        echo json_encode(["status" => "error", "message" => "Missing labor ID"]);
        exit;
    }

    $total_pay = $hours_worked * $rate_per_hour;

    $stmt = $conn->prepare("
        UPDATE tb_labor
        SET employee_id = ?,
            record_id = ?,
            hours_worked = ?,
            rate_per_hour = ?,
            total_pay = ?
        WHERE labor_id = ?
    ");

    $stmt->execute([
        $employee_id,
        $record_id,
        $hours_worked,
        $rate_per_hour,
        $total_pay,
        $labor_id
    ]);

    echo json_encode(["status" => "success", "message" => "Labor updated"]);
    exit;
}
if ($action === 'delete') {

    $labor_id = $_POST['labor_id'] ?? null;

    if (empty($labor_id)) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing or invalid labor ID"
        ]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM tb_labor WHERE labor_id = ?");
    $stmt->execute([$labor_id]);

    echo json_encode([
        "status" => "success",
        "message" => "Labor deleted"
    ]);
    exit;
}
// fallback
echo json_encode([
    "message" => "No valid action"
]);


?>