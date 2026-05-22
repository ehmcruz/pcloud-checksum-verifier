<html>
	<head><title>Callback URI</title></head>
	<body>
		<p>Callback URI</p>
<?php

$code = $_GET["code"];
$locationid = $_GET["locationid"];
$hostname = $_GET["hostname"];

echo "Code: " . $code . "</br>";
echo "Locationid: " . $locationid . "</br>";
echo "Hostname: " . $hostname . "</br>";

?>

</body>
</html>