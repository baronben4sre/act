<?php
// Ask mysqli to handle the error (throwing exceptions and let the end user handle them... not for end user distribution)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

error_log(date("Y-m-d H:i:s") . " INFO do_classify.php called\n", 3, "/var/www/html/logs/cpt.log");

$mysqli = new mysqli(getenv('MYSQL_HOST'),getenv('MYSQL_USER'),getenv('MYSQL_PASSWORD'),getenv('MYSQL_NAME'));

// Retrieve POST parameters
$category = $_POST["category"] ?? null;
$id = $_POST["id"] ?? null;
$account = $_POST["account"] ?? null;

if (!$category || !$id === null) {
    error_log(date("Y-m-d H:i:s") . " ERROR do_classify.php: Missing required parameters (category = $category & id = $id)\n", 3, "/var/www/html/logs/cpt.log");
    die(json_encode(["status" => "error", "message" => "Missing required parameters."]));
}

// Prepare SQL statement
$stmt = $mysqli->prepare("update transactions set category_id = ? where id = ? and account_id = ?");
$stmt->bind_param("iii", $category, $id, $account);

error_log(date("Y-m-d H:i:s") . " INFO do_classify.php: Running the SQL: update transactions set categoryid = $category where id = $id and account_id = $account\n", 3, "/var/www/html/logs/cpt.log");

if ($stmt->execute()) {
    error_log(date("Y-m-d H:i:s") . " INFO do_classify.php: SQL ran successfully\n", 3, "/var/www/html/logs/cpt.log");
    echo json_encode(["status" => "success", "message" => "Classification updated"]);
} else {
    error_log(date("Y-m-d H:i:s") . " INFO do_classify.php: SQL failed. Error: " . $stmt->error . "\n", 3, "/var/www/html/logs/cpt.log");
    echo json_encode(["status" => "error", "message" => "Update failed: " . $stmt->error]);
}

$stmt->close();
$mysqli->close();
error_log(date("Y-m-d H:i:s") . " INFO do_classify.php exit\n", 3, "/var/www/html/logs/cpt.log");
?>

