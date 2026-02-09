<!DOCTYPE html>
<html>
    <head>
        <!-- head definitions go here -->
    </head>
    <body>
<table>
  <tr><td>


<?php 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

error_log(date("Y-m-d H:i:s") . " INFO duplicate.php called\n", 3, "/var/www/html/logs/act.log");

$mysqli = new mysqli(getenv('MYSQL_HOST'),getenv('MYSQL_USER'),getenv('MYSQL_PASSWORD'),getenv('MYSQL_DATABASE'));

$filter = isset($_GET['filter']) && is_numeric($_GET['filter']) ? (int) $_GET['filter'] : 0;
$filter_str = "";
if ($filter != 0)
	$filter_str = " and p1.account_id = $filter";
$sql = 'select a.name, p1.id, p1.operation, p1.libel, p1.amount, p1.category_id, p2.id, a.id from transactions p1, transactions p2, accounts a where p1.account_id=p2.account_id and p1.amount=p2.amount and p1.operation=p2.operation and p1.id != p2.id and p1.account_id=a.id' . $filter_str .' order by operation';

error_log(date("Y-m-d H:i:s") . " INFO duplicate.php query: $sql\n", 3, "/var/www/html/logs/act.log");

$result = $mysqli -> query($sql);

error_log(date("Y-m-d H:i:s") . " INFO duplicate.php query done\n", 3, "/var/www/html/logs/act.log");

$presetVal = "$filter";
$transactionId = '';
$accountId = '';

echo "<tr><td>Account</td><td>Transaction<br>id</td><td>Date</td><td>Libel</td><td>amount</td><td>category</td><td></td></tr>";

echo '<form id=deleteForm>';
  while($row = mysqli_fetch_array($result))
  {
    echo "<tr>";
    echo '<td class="select-container"></td>';
    //<td> " . htmlspecialchars($row[0]) . " </td>";
    echo "<td> " . htmlspecialchars($row[1]) . " </td>";
    echo "<td> " . htmlspecialchars($row[2]) . " </td>";
    echo "<td> " . htmlspecialchars($row[3]) . " </td>";
    echo "<td> " . htmlspecialchars($row[4]) . " </td>";
    echo "<td> " . htmlspecialchars($row[5]) . " </td>";
    echo '<td><button class="submit-btn">Delete' . $row[6] . ' </button></td>';
    $transactionId = $transactionId . ", " . $row[6];
    $accountId = $accountId . ", " . $row[7];
    echo "</tr>\n";
  }
echo '</form>';

?>
</table>
<script>
function filter() {
    let firstRowSelect = document.querySelector("tr .select-container select"); // Find first row's select
    if (firstRowSelect) {
         let url = new URL(window.location.href);
         url.searchParams.set("filter", firstRowSelect.value); 

	 window.location.href = url.toString();
    } 
}
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".submit-btn").forEach((btn) => {
        btn.addEventListener("click", function (event) {
            let row = btn.closest("tr"); // Get the row of the clicked button
            let formData = new FormData();
            formData.append("id", row.querySelector("select").getAttribute("transactionId"));
            formData.append("account", row.querySelector("select").getAttribute("accountId"));

            fetch("do_delete.php", {
                    method: "POST",
                    body: formData,
            })
                .then((response) => response.json())
                .then((data) => console.log("Response:", data));
        });
    });
});

document.addEventListener("DOMContentLoaded", function() {
        const ids = [<?php echo substr($transactionId, 2); ?>];  // Ids from the DB
	const account = [<?php echo substr($accountId, 2); ?>];  // account_id from the DB	
    document.querySelectorAll(".select-container").forEach((container, index) => {
        let select = document.createElement("select");
        select.innerHTML = `
<?php
$catStr = '';
$accounts = $mysqli -> query('select id,name from accounts');
$options = [];
while ($row = $accounts->fetch_assoc()) {
        $options[$row['name']] = $row['id'];
}
$mysqli -> close();
ksort($options);

foreach ($options as $name => $id) {
	$catStr = $catStr . '<option value="' . $id . '">' . htmlspecialchars($name) . "</option>\n";
      }
echo $catStr;
error_log(date("Y-m-d H:i:s") . " INFO duplicate.php exit\n", 3, "/var/www/html/logs/act.log");

?>
	`;

        // Set predefined value for this row
        select.value = account[index] || "999"; 
        select.setAttribute("transactionId", ids[index]); 
        select.setAttribute("accountId", account[index]); 

        container.appendChild(select);
    });
});
</script>


</body>
</html>

