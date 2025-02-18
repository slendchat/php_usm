<?php
declare(strict_types=1);
include 'transactions.php';


function calculateTotalAmount(array $transactions): float {
  $amount = 0.0;
  foreach($transactions as $transaction){
    $amount+=$transaction['amount'];
  }
  return $amount;
}

function findTransactionByDescription(string $descriptionPart) {
  global $transactions;
  $matchtrans = [];
  foreach($transactions as $trtn){
    if(str_contains(strtolower($trtn['description']),strtolower($descriptionPart))){
      array_push($matchtrans, $trtn);
    }
  }
  return $matchtrans;
}

function findTransactionById(int $id){
  // $transaction = [];
  global $transactions;
  // foreach($transactions as $trtn){
  //   if(isset($trtn['id']) && ($trtn['id'] == $id)){
  //     $transaction = $trtn;
  //   }
  // }
  $res = array_filter($transactions, fn($trtn) => $trtn['id']===$id);
  return $res;
}

function daysSinceTransaction(string $date): int {
  $transdate = DateTime::createFromFormat("m/d/Y",$date);
  $now = new DateTime();
  $interval = $now->diff($transdate);
  return $interval->days;
}

function addTransaction(int $id, string $date, float $amount, string $description, string $merchant): void  {
  global $transactions;
  $temp = [];
  isset($id) ? $temp['id']=$id : throw new Exception('NO ID');
  isset($id) ? $temp['date']=$date : throw new Exception('NO DATE');
  isset($id) ? $temp['amount']=$amount : throw new Exception('NO AMOUNT');
  isset($id) ? $temp['description']=$description : $temp['description']="" ;
  isset($id) ? $temp['merchant']=$merchant : throw new Exception('NO MERCHANT');
  
  array_push($transactions,$temp);
}
// ===============================================================
// =====================ADDED NEW TRANSACTION=====================
addTransaction(3,"2/18/2025",199.9,"","ASUS");

// sorting
usort($transactions, function($a,$b){
  $datea = DateTime::createFromFormat("m/d/Y",$a['date']);
  $dateb = DateTime::createFromFormat("m/d/Y",$b['date']);
  return ($datea <=> $dateb)*-1;
});

?>


<!-- =================HTML================= -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Arrays&Functions</title>
</head>
<body>

<!-- TABLE -->
<table border='1' class="tlist_table">
<!-- Table header -->
  <thead>
      <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Amount</th>
        <th>Description</th>
        <th>Merchant</th>
        <th>Days since transaction</th>
      </tr>
  </thead>

<!-- Table data -->
  <tbody>
  <?php
    foreach ($transactions as $t) {
      echo "<tr>";
      echo "<td>{$t['id']}</td>";
      echo "<td>{$t['date']}</td>";
      echo "<td>{$t['amount']}</td>";
      echo "<td>{$t['description']}</td>";
      echo "<td>{$t['merchant']}</td>";
      echo "<td>",daysSinceTransaction($t['date']),"</td>";
      echo "</tr>";
    }
  ?>
  </tbody>
</table>

<br><br>
<!-- TOTAL SUM OF TRANSACTIONS -->
<h3>Total sum of transactions:</h3>
<p>
<?php
echo calculateTotalAmount($transactions);
?>
</p>

<!-- TRANSACTIONS BY ID -->
<h3>Transaction by id 1:</h3>
<p>
<?php
$trtnList = findTransactionById(1);
foreach($trtnList as $trtn){
  foreach($trtn as $key => $value){
    echo "$key: $value<br>";
  }
}
?>
</p>

<!-- TRANSACTIONS BY PART OF DESCRIPTION -->
<!-- shows all matches -->
<h3>Transaction by description part "dinner":</h3>
<p>
<?php
$trtnList = findTransactionByDescription("dinner");
foreach($trtnList as $trtn){
  foreach($trtn as $key => $value){
    echo "$key: $value<br>";
  }
}
?>
</p>

<!-- TABLE -->
<h3> Table sorted by sum(Amount) descending order </h3>
<table border='1' class="tlist_table">
<!-- Table header -->
  <thead>
      <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Amount</th>
        <th>Description</th>
        <th>Merchant</th>
        <th>Days since transaction</th>
      </tr>
  </thead>

<!-- Table data -->
  <tbody>
  <?php
    usort($transactions, fn($a, $b) => $b['amount'] <=> $a['amount']);
    foreach ($transactions as $t) {
      echo "<tr>";
      echo "<td>{$t['id']}</td>";
      echo "<td>{$t['date']}</td>";
      echo "<td>{$t['amount']}</td>";
      echo "<td>{$t['description']}</td>";
      echo "<td>{$t['merchant']}</td>";
      echo "<td>",daysSinceTransaction($t['date']),"</td>";
      echo "</tr>";
    }
  ?>
  </tbody>
</table>

</body>
</html>