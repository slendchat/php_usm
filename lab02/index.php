<?php

$dayofweek = date('N');

if(in_array($dayofweek,[1,3,5]))
{
  $jhonschedule = "8:00 - 12:00";
}
else
{
  $jhonschedule = "Нерабочий день";
}

$janeschedule = (in_array($dayofweek, [2, 4, 6])) 
    ? "12:00 - 16:00" 
    : "Нерабочий день";

?>
<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Расписание</title>
    <style>
        table, td, th {
            border: 1px solid #000;
            border-collapse: collapse;
            padding: 5px;
        }
    </style>
</head>
<body>
<h2>Расписание на сегодня</h2>
<table>
    <tr>
        <th>№</th>
        <th>Фамилия Имя</th>
        <th>График работы</th>
    </tr>
    <tr>
        <td>1</td>
        <td>John Styles</td>
        <td><?php echo $jhonschedule; ?></td>
    </tr>
    <tr>
        <td>2</td>
        <td>Jane Doe</td>
        <td><?php echo $janeschedule; ?></td>
    </tr>
</table>
<br><br><br>
<?php
echo "Loop made with: For<br>";
$a = 0;
$b = 0;

for ($i = 0; $i <= 5; $i++) {
  echo "i: ",$i,"<br>";
  echo "a: ",$a,"<br>";
  echo "b: ",$b,"<br>";
   $a += 10;
   $b += 5;
   echo "<br>";
}

echo "End of the loop: a = $a, b = $b<br><br>";

?>

<?php
echo "Loop made with: While<br>";
$a = 0;
$b = 0;
$i = 0;

while($i<=5) {
  echo "i: ",$i,"<br>";
  echo "a: ",$a,"<br>";
  echo "b: ",$b,"<br>";
   $a += 10;
   $b += 5;
   echo "<br>";
   $i++;
}

echo "End of the loop: a = $a, b = $b<br><br>";

?>

<?php
echo "Loop made with: Do-While<br>";
$a = 0;
$b = 0;
$i = 0;

 do{
  echo "i: ",$i,"<br>";
  echo "a: ",$a,"<br>";
  echo "b: ",$b,"<br>";
   $a += 10;
   $b += 5;
   echo "<br>";
   $i++;
}while($i<=5);

echo "End of the loop: a = $a, b = $b<br><br>";

?>
</body>
</html>

