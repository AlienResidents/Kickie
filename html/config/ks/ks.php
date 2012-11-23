<?

$host = "localhost";
$port = "3306";
$dbas = "kickstart";
$user = "kickstart";
$pass = "the bucket";

$mac_address = exec("grep $_SERVER[REMOTE_ADDR] /proc/net/arp | awk '{print $4}'");
if (empty($mac_address)) {
  $mac_address = "UNKNOWN";
}
$pxe_mac_address = "01-" . preg_replace('/:/', '-', $mac_address);
$pxe_boot_area = "/app/kickstart/pxe_boot/pxelinux.cfg";
$pxe_build = "$pxe_boot_area/build";
$pxe_default = "$pxe_boot_area/default";

$mysqli = new mysqli($host, $user, $pass, $dbas, $port);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}
$query = "select hostname,mac_address,scheduled_build from servers where mac_address='$mac_address'";
$result = $mysqli->query($query) or die("Can't query stuff: " . $mysqli->error);
$row = $result->fetch_assoc();
if ($result->num_rows > 0) {
  $hostname = $row['hostname'];
  $scheduled_build = $row['scheduled_build'];
} else {
  echo "# No DB queries returned!\n";
}
$result->free();

if ($_GET['a']) {
  $when = $_GET['a'];
  $query = "update servers set last_build_$when=now() where mac_address='$mac_address'";
  $result = $mysqli->query($query) or die($mysqli->error);
  if ($when == "finish") {
    if (file_exists("$pxe_boot_area/$hostname")) {
      unlink("$pxe_boot_area/$hostname");
    }
    if (file_exists("$pxe_boot_area/$pxe_mac_address")) {
      unlink("$pxe_boot_area/$pxe_mac_address");
    }
    $query = "update servers set scheduled_build=0 where mac_address='$mac_address'";
    $result = $mysqli->query($query) or die($mysqli->error);
  }
}

$mysqli->close();

# Uncomment if you use rollout (http://www.github.com/dparrish/rollout)
#$rollout_port = "80";
#$rollout_url = "http://10.227.192.226:$rollout_port/rollout";
$kickstart_url = "http://10.227.192.226/kickstart";
$kickstart_finish_url = "http://10.227.192.226/kickstart/config/ks/ks.php?a=finish";
$kickstart_repo_url = "http://10.227.192.226/ubuntu";

$default_partition = "part /boot --fstype ext4 --size 256\n";
$default_partition .= "part swap --size 1024\n";
$default_partition .= "part pv.01 --size 1 --grow\n";
$default_partition .= "volgroup rootvg pv.01\n";
$default_partition .= "logvol / --fstype ext4 --vgname rootvg --size 5000 --name rootvol\n";

$handle = fopen("/tmp/ks.php.txt", "a+");

fwrite($handle, date("r") . "\n");
foreach (getallheaders() as $name => $value) {
    fwrite($handle, "$name: $value\n");
}
fwrite($handle, "\n");

$ks_contents = "# scheduled_build: $scheduled_build\n";
$ks_contents .= "# mac_address: $mac_address\n\n";
$ks_contents .= file_get_contents('./ks.cfg');
$ks_contents = preg_replace('/KICKSTART_REPO_URL/', $kickstart_repo_url, $ks_contents);
$ks_contents = preg_replace('/KICKSTART_FINISH_URL/', $kickstart_finish_url, $ks_contents);
$ks_contents = preg_replace('/DISK_PARTITION_INFORMATION/', $default_partition, $ks_contents);
$ks_contents = preg_replace('/HOSTNAME/', $row['hostname'], $ks_contents);
# Uncomment if you use rollout (http://www.github.com/dparrish/rollout)
#$ks_contents = preg_replace('/ROLLOUT_URL/', $rollout_url, $ks_contents);

if ($mac_address = "UNKNOWN") {
  echo "<pre>\n";
}
echo $ks_contents;
if ($mac_address = "UNKNOWN") {
  echo "</pre>\n";
}


fwrite($handle, $ks_contents);
fwrite($handle, "==================================================\n\n");
fclose($handle);
?>
