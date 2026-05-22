<?php

header("Content-Type: text/plain");

$auth_file = "auth.json";
$auth_data = json_decode(file_get_contents($auth_file), true);
$access_token = $auth_data['token'];
$locationid = $auth_data['locationid'];

require_once("./lib-pcloud/autoload.php");

if (isset($_GET["folderid"]))
	$folderid = (int)$_GET["folderid"];
else
	$folderid = 0;

if (isset($_GET["type"]))
	$type = $_GET["type"];
else
	$type = "sha1";

try {
	$pCloudApp = new pCloud\Sdk\App();
	$pCloudApp->setAccessToken($access_token);
	$pCloudApp->setLocationId($locationid);
	//$pCloudApp->setCurlExecutionTimeout(10);

	$pCloudFolder = new pCloud\Sdk\Folder($pCloudApp);

	$meta = $pCloudFolder->getMetadata($folderid)->metadata;
	
	$content = $pCloudFolder->getContent($folderid);
	
	foreach ($content as $item) {
		if (!$item->isfolder) {
			$pCloudFile = new pCloud\Sdk\File($pCloudApp);
			$info = $pCloudFile->getInfo($item->fileid);
			$mf = $info->metadata;

			if ($type == "md5")
				$hash = $info->md5;
			else
				$hash = $info->sha1;
			
			echo "{$hash}  {$item->name}\n";
		}
	}
} catch (Exception $e) {
	echo $e->getMessage();
}

?>
