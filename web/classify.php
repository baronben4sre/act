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

error_log(date("Y-m-d H:i:s") . " INFO classify.php called\n", 3, "/var/www/html/logs/act.log");

$mysqli = new mysqli(getenv('MYSQL_HOST'),getenv('MYSQL_USER'),getenv('MYSQL_PASSWORD'),getenv('MYSQL_DATABASE'));

$filter = isset($_GET['filter']) && is_numeric($_GET['filter']) ? (int) $_GET['filter'] : 0;
$filter_str = "";
if ($filter != 0)
	$filter_str = " and c.id = $filter";
$sql = 'select a.name,t.operation,t.libel,t.amount,c.id,t.id,a.id from transactions t, categories c, accounts a where t.category_id = c.id and t.account_id=a.id' . $filter_str . ' order by operation desc limit 100 offset 0';
error_log(date("Y-m-d H:i:s") . " INFO classify.php query: $sql\n", 3, "/var/www/html/logs/act.log");

$result = $mysqli -> query($sql);

error_log(date("Y-m-d H:i:s") . " INFO classify.php query done\n", 3, "/var/www/html/logs/act.log");

$presetVal = "$filter";
$transactionId = '0';
$accountId = '0';

echo "<tr><td>Account</td><td>Date</td><td>Libel</td><td>amount</td><td class='select-container'></td><td><button onclick='filter()'>Filter</button></td></tr>";

echo '<form id=classifyForm>';
  while($row = mysqli_fetch_array($result))
  {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row[0]) . "</td>";
    echo "<td>" . htmlspecialchars($row[1]) . "</td>";
    echo "<td>" . htmlspecialchars($row[2]) . "</td>";
    echo "<td>" . htmlspecialchars($row[3]) . "</td>";
    echo '<td class="select-container"></td>';
    echo '<td><button class="submit-btn">Submit</button></td>';
    $presetVal = $presetVal . ", " . $row[4];
    $transactionId = $transactionId . ", " . $row[5];
    $accountId = $accountId . ", " . $row[6];
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
        event.preventDefault(); // Prevent normal form submission
            let row = btn.closest("tr"); // Get the row of the clicked button
            let formData = new FormData();
            formData.append("category", row.querySelector("select").value);
            formData.append("id", row.querySelector("select").getAttribute("data-row"));
            formData.append("account", row.querySelector("select").getAttribute("account"));

            fetch("do_classify.php", {
                    method: "POST",
                    body: formData,
            })
                .then((response) => response.json())
                .then((data) => console.log("Response:", data));
        });
    });
});

document.addEventListener("DOMContentLoaded", function() {
	const presetValues = [<?php echo $presetVal; ?>]; // Default values for each row
        const ids = [<?php echo $transactionId; ?>];  // Ids from the DB
	const account = [<?php echo $accountId; ?>];  // account_id from the DB	
    document.querySelectorAll(".select-container").forEach((container, index) => {
        let select = document.createElement("select");
        select.innerHTML = `
<?php
$catStr = '<option value=\"0\">Category</option>';
$categories = $mysqli -> query('select id,name from categories');
$options = [];
while ($row = $categories->fetch_assoc()) {
        $options[$row['name']] = $row['id'];
}
$mysqli -> close();
ksort($options);

foreach ($options as $name => $id) {
	$catStr = $catStr . '<option value="' . $id . '">' . htmlspecialchars($name) . "</option>\n";
      }
echo $catStr;
error_log(date("Y-m-d H:i:s") . " INFO classify.php exit\n", 3, "/var/www/html/logs/act.log");

?>
	`;

        // Set predefined value for this row
        select.value = presetValues[index] || "0"; 
        select.setAttribute("data-row", ids[index]); 
        select.setAttribute("account", account[index]); 

        container.appendChild(select);
    });
});
</script>


</body>
</html>

