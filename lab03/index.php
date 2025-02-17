<?php
declare(strict_types=1);
$transactions = [
  [
    "id" => 1,
    "date" => date("2/17/2025"),
    "amount" => 100.00,
    "description" => "groceries payment",
    "merchant" => "SuperMart",
  ],
  [
    "id" => 2,
    "date" => date("m/d/Y", strtotime("yesterday")),
    "amount" => 90.00,
    "description" => "dinner payment",
    "merchant" => "Local Restaurant",
  ],
  ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
<table border='1'>
<thead>
    <tr>
      <th>ID</th>
      <th>Date</th>
      <th>Amount</th>
      <th>Description</th>
      <th>Merchant</th>
    </tr>
</thead>

<tbody>
<!-- Вывод студентов -->
</tbody>
<?php
  foreach ($transactions as $t) {
    echo "<tr>";
    echo "<td>{$t['id']}</td>";
    echo "<td>{$t['date']}</td>";
    echo "<td>{$t['amount']}</td>";
    echo "<td>{$t['description']}</td>";
    echo "<td>{$t['merchant']}</td>";
    echo "</tr>";
  }
?>
</table>




  
</body>
</html>
<?php

?>