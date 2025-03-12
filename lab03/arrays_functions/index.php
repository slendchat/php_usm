<?php
declare(strict_types=1);
include 'transactions.php';


/**
 * Calculates the total amount of all transactions.
 *
 * @param array $transactions An array of transactions. Each transaction is an
 *                            associative array with the keys 'id', 'date',
 *                            'amount', 'description', and 'merchant'.
 * @return float The total amount of all transactions.
 */
function calculateTotalAmount(array $transactions): float {
  // Initialize the total amount to zero.
  $amount = 0.0;

  // Iterate over all transactions and add their amounts to the total amount.
  foreach($transactions as $transaction){
    $amount += $transaction['amount'];
  }

  // Return the total amount.
  return $amount;
}

/**
 * Finds transactions that contain a specific part in their description.
 *
 * This function searches through all transactions and returns those
 * that contain the specified part in their description, case-insensitively.
 *
 * @param string $descriptionPart A part of the description to search for.
 * @return array An array of transactions that match the search criteria.
 */
function findTransactionByDescription(string $descriptionPart) {
    global $transactions;
    // Filter the transactions to find matches with the description part.
    return array_filter($transactions, fn($transaction) => str_contains(strtolower($transaction['description']), strtolower($descriptionPart)));
}

/**
 * Finds a transaction by its id.
 *
 * @param int $id The id of the transaction to search for.
 * @return array A list of transactions with the specified id.
 */
function findTransactionById(int $id){
  global $transactions;

  // Filter the transactions to find matches with the given id.
  return array_filter($transactions, fn($transaction) => $transaction['id']===$id);
}

//with foreach
// function findTransactionById(int $id){ 
  // $transaction = [];
  // foreach($transactions as $transaction){
  //   if(isset($transaction['id']) && ($transaction['id'] == $id)){
  //     $transaction = $transaction;
  //   }
  // }
// }

/**
 * Calculates the number of days since the transaction date.
 *
 * @param string $date The date of the transaction in the format "m/d/Y".
 * @return int The number of days since the transaction date.
 */
function daysSinceTransaction(string $date): int {
  // Create a DateTime object from the transaction date.
  $transdate = DateTime::createFromFormat("m/d/Y", $date);

  // Create a DateTime object for the current date.
  $now = new DateTime();

  // Calculate the difference between the two dates.
  $interval = $now->diff($transdate);

  // Return the number of days in the difference.
  return $interval->days;
}

/**
 * Adds a new transaction to the global transactions array.
 *
 * @param int $id The id of the new transaction.
 * @param string $date The date of the new transaction in the format "m/d/Y".
 * @param float $amount The amount of the new transaction.
 * @param string $description The description of the new transaction.
 * @param string $merchant The merchant of the new transaction.
 * @throws Exception If any of the required parameters are missing.
 */
function addTransaction(int $id, string $date, float $amount, string $description, string $merchant): void  {
  global $transactions;
  $temp = [];

  // Validate the parameters and throw an exception if any are missing.
  if (!isset($id)) {
    throw new Exception('NO ID');
  }
  if (!isset($date)) {
    throw new Exception('NO DATE');
  }
  if (!isset($amount)) {
    throw new Exception('NO AMOUNT');
  }
  if (!isset($merchant)) {
    throw new Exception('NO MERCHANT');
  }

  // Populate the temporary array with the transaction data.
  $temp['id'] = $id;
  $temp['date'] = $date;
  $temp['amount'] = $amount;
  $temp['description'] = $description;
  $temp['merchant'] = $merchant;

  // Add the new transaction to the global transactions array.
  array_push($transactions, $temp);
}
// ===============================================================
// =====================ADDED NEW TRANSACTION=====================
addTransaction(3,"2/18/2025",199.9,"","ASUS");

// sorting
// Sort the transactions array by date in descending order.
// The comparison function used with usort takes two transactions and compares
// their dates. The dates are converted to DateTime objects for comparison.
// The result of the comparison is negated to sort in descending order.
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
  foreach ($transactions as $t): ?>
    <tr>
      <td><?= $t['id'] ?></td>
      <td><?= $t['date'] ?></td>
      <td><?= $t['amount'] ?></td>
      <td><?= $t['description'] ?></td>
      <td><?= $t['merchant'] ?></td>
      <td><?= daysSinceTransaction($t['date']) ?></td>
    </tr>
  <?php endforeach; ?>
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
$transactionList = findTransactionById(1);
foreach($transactionList as $transaction){
  foreach($transaction as $key => $value){
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
$transactionList = findTransactionByDescription("dinner");
foreach($transactionList as $transaction){
  foreach($transaction as $key => $value){
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

  foreach ($transactions as $t): ?>
      <tr>
          <td><?= $t['id'] ?></td>
          <td><?= $t['date'] ?></td>
          <td><?= $t['amount'] ?></td>
          <td><?= $t['description'] ?></td>
          <td><?= $t['merchant'] ?></td>
          <td><?= daysSinceTransaction($t['date']) ?></td>
      </tr>
  <?php endforeach; ?>

  </tbody>
</table>

</body>
</html>