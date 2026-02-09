<?php
// Ask mysqli to handle the error (throwing exceptions and let the end user handle them... not for end user distribution)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

error_log(date("Y-m-d H:i:s") . " INFO do_delete.php called\n", 3, "/var/www/html/logs/act.log");

$mysqli = new mysqli(getenv('MYSQL_HOST'),getenv('MYSQL_USER'),getenv('MYSQL_PASSWORD'),getenv('MYSQL_NAME'));

// Retrieve POST parameters
$id = $_POST["id"] ?? null;
$account = $_POST["account"] ?? null;

if (!$account || !$id === null) {
    error_log(date("Y-m-d H:i:s") . " ERROR do_delete.php: Missing required parameters (account = $account & id = $id)\n", 3, "/var/www/html/logs/act.log");
    die(json_encode(["status" => "error", "message" => "Missing required parameters."]));
}

// Prepare SQL statement
$stmt = $mysqli->prepare("delete from transactions where id = ? and account_id = ?");
$stmt->bind_param("ii", $id, $account);

error_log(date("Y-m-d H:i:s") . " INFO do_delete.php: Running the SQL:  $stmt->query \n", 3, "/var/www/html/logs/act.log");

if ($stmt->execute()) {
    error_log(date("Y-m-d H:i:s") . " INFO do_delete.php: SQL ran successfully\n", 3, "/var/www/html/logs/act.log");
    echo json_encode(["status" => "success", "message" => "Classification updated"]);
} else {
    error_log(date("Y-m-d H:i:s") . " INFO do_delete.php: SQL failed. Error: " . $stmt->error . "\n", 3, "/var/www/html/logs/act.log");
    echo json_encode(["status" => "error", "message" => "Update failed: " . $stmt->error]);
}

$stmt->close();
$mysqli->close();
error_log(date("Y-m-d H:i:s") . " INFO do_delete.php exit\n", 3, "/var/www/html/logs/act.log");
?>

