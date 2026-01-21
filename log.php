<?php
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["ok" => false, "error" => "Method not allowed"]);
    exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Invalid JSON", "raw" => $raw]);
    exit;
}

$machine_name = trim((string) ($data["machine_name"]));
$prev_state = trim((string) ($data["prev_state"]));
$input_symbol = trim((string) ($data["input_symbol"]));
$next_state = trim((string) ($data["next_state"]));
$sim_time = $data["sim_time"];
$car_position = $data["car_position"];

if ($machine_name === "" || $prev_state === "" || $input_symbol === "" || $next_state === "" || $sim_time === null) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Missing fields", "received" => $data]);
    exit;
}

if (!is_numeric($sim_time)) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "sim_time must be numeric"]);
    exit;
}

if ($car_position !== null && $car_position !== "" && !is_numeric($car_position)) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "car_position must be numeric or null"]);
    exit;
}

require_once __DIR__ . "/db.php";

if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => "db.php must create a mysqli instance named $mysqli"]);
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $sim_time_f = (float) $sim_time;

    if ($car_position === null || $car_position === "") {
        $stmt = $mysqli->prepare(
            "INSERT INTO machine_logs (machine_name, prev_state, input_symbol, next_state, sim_time)
       VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssd", $machine_name, $prev_state, $input_symbol, $next_state, $sim_time_f);
    } else {
        $car_pos_f = (float) $car_position;
        $stmt = $mysqli->prepare(
            "INSERT INTO machine_logs (machine_name, prev_state, input_symbol, next_state, sim_time, car_position)
       VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssdd", $machine_name, $prev_state, $input_symbol, $next_state, $sim_time_f, $car_pos_f);
    }

    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();

    echo json_encode(["ok" => true, "id" => $id]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "ok" => false,
        "error" => "Insert failed",
        "detail" => $e->getMessage()
    ]);
}
