<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Kickie</title>
<link rel="stylesheet" href="css/kickstart.css" />
<!-- <script src=".js"></script> --!>
</head>
<body>

<center>
  <h1>Kickie</h1>
</center>
<hr />

<div id='body-wrapper'>

<?

$rows[0] = "even";
$rows[1] = "odd";

$host = "localhost";
$user = "kickstart";
$pass = "the bucket";
$dbas = "kickstart";
$port = "3306";

$mysqli = new mysqli($host, $user, $pass, $dbas, $port);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}

$query = "select * from servers";
$result = $mysqli->query($query) or die("Can't query stuff: " . $mysqli->error);
echo "<table>\n";
$fields = $result->fetch_fields();
echo "  <tr>\n";
echo "    <th>Status</th>\n";
foreach ($fields as $field) {
  echo "    <th>" . ucwords(preg_replace('/_/', ' ', $field->name)) . "</th>\n";
}
echo "  </tr>\n";
$count = 0;
while ($row = $result->fetch_assoc()) {
  echo "  <tr class='body row-" . $rows[$count%2] . "'>\n";
  echo "    <td>\n";
  echo "      <!--\n";
  echo "     <div class='circleBase blue'> </div>\n";
  echo "     <div class='circleBase green'> </div>\n";
  echo "     <div class='circleBase yellow'> </div>\n";
  echo "     <div class='circleBase red'> </div>\n";
  echo "      --!>\n";
  echo "    </td>\n";
  foreach ($row as $key => $val) {
    echo "    <td>$val</td>\n";
  }
  echo "  </tr>\n";
  $count++;
}
echo "</table>\n";
$result->free();
$mysqli->close();


?>

</div>

</body>
</html>
