<?php

function cmp_flist ($a, $b)
{
    return strcmp($a->name, $b->name);
}

$auth_file = "auth.json";
$auth_data = json_decode(file_get_contents($auth_file), true);
$access_token = $auth_data['token'];
$locationid = $auth_data['locationid'];

require_once("./lib-pcloud/autoload.php");

if (isset($_GET["folderid"]))
	$folderid = (int)$_GET["folderid"];
else
	$folderid = 0;

try {
	$pCloudApp = new pCloud\Sdk\App();
	$pCloudApp->setAccessToken($access_token);
	$pCloudApp->setLocationId($locationid);
	$pCloudApp->setCurlExecutionTimeout(10);

	$pCloudFolder = new pCloud\Sdk\Folder($pCloudApp);

	echo "<ul style=\"list-style-type: none;\">";
	
	$meta = $pCloudFolder->getMetadata($folderid)->metadata;
	
	$content = $pCloudFolder->getContent($folderid);
	
	echo "Current folder: {$meta->name} (<a href=\"download.php?folderid=$folderid&type=md5\">md5</a> <a href=\"download.php?folderid=$folderid&type=sha1\">sha1</a>)<br>\n<br>\n";
	
	if ($folderid != 0)
		echo "<li><a href=\"index.php?folderid={$meta->parentfolderid}\">..</a></li>\n";

	usort($content, "cmp_flist");

	foreach ($content as $item) {
		echo "<li>";
		
		if ($item->isfolder) {
			echo "<a href=\"index.php?folderid={$item->folderid}\">".$item->name."</a>";
		}
		else {
//			$pCloudFile = new pCloud\File();
//			$info = $pCloudFile->getInfo($item->fileid);
//			$mf = $info->metadata;
			
			echo "{$item->name} ({$item->size} bytes)";

			if ($item->name == "checksum.sha1")
				echo " <a href=\"check.php?checksum_file_id={$item->fileid}&folderid={$folderid}\">Hash check</a>";
		}
		
		echo "</li>\n";
	}
	
//	var_dump($meta);
	
	if ($folderid != 0)
		echo "<li><a href=\"index.php?folderid={$meta->parentfolderid}\">..</a></li>\n";
	
	echo "</ul>\n";
} catch (Exception $e) {
	echo $e->getMessage();
}

?>
