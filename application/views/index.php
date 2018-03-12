<div class="resourceList">
<?php
$currentLetter = '0';
$letters = array(
		'a','b','c','d','e','f','g','h','i','j',
		'l','m','n','o','p','q','r','s','t','u',
		'v','w','x','y','z'
	);
echo "<div class='hotlinks'>";
foreach($letters as $letter){
	echo "<a href='#letter_{$letter}'>" . strtoupper($letter) . "</a>";
}
echo "<span class='floatClear'>&nbsp;</span></div>";
foreach($resourceList as $resource){
$titleLetter = strtolower(substr($resource['title'],0,1));
if ($titleLetter != $currentLetter 
	&& in_array($titleLetter, $letters)
){
	echo "<div id='letter_{$titleLetter}' class='resourceHeader'>" . strtoupper($titleLetter) ."<span><a href='#top'>^</a></span></div>\n";
	$currentLetter = $titleLetter;
}
	$restricted = ($resource['restricted'] == 'T') ? " <span class='restricted'><span class='label'>(Restricted)</span></span>" : "";
	$editUrl = "index.php?rt=editresource&resource_id={$resource['id']}";
	//$deleteUrl = "index.php?rt=resourcelist/save&resource_id={$resource['id']}&delete_resource=true";
	$deleteUrl = "index.php?rt=index/deleteconfirm&resource_id={$resource['id']}";
	echo "<div id='rid_{$resource['id']}' class='resource'>\n";
	echo "<span class='title'><a href='{$editUrl}'><span class='label'>Edit </span>{$resource['title']}</a></span>\n";
	echo "<span class='deleteLink'><a href='{$deleteUrl}'><span class='label'>Delete {$resource['title']}</span></a></span>\n";
	echo "<span class='resourceType'><span class='padding'>{$resource['type']}{$restricted}</span></span>\n";
	$config_array = $resource['config'];
	
	if($resource['use_custom'] == 'T'){
		echo "<div class='configType'>Custom:</div><div class='configBlock'>\n";
		echo "<div class='customConfig'><div class='customConfigOuter'><div class='customConfigInner'>{$resource['custom_config']}</div></div></div>";
        echo "</div>\n";
	} else {
		echo "<div class='configType'>URLs:</div><ul class='configBlock'>\n";
		$urlCount = 0;
		$config_array = (array)$config_array;
		foreach($config_array as $config){
			$evenOrOdd = (
			    $urlCount++ % 2 == 0
			    ? 'even'
			    : 'odd'
			);
			echo "<li class='{$evenOrOdd}'><span class='configType'>{$config['config_type']}</span> - ";
			echo "<span class='configUrl'>{$config['config_value']}</span></li>\n";
		}
		echo "</ul>\n";
	}
	
	echo "</div>\n";
}
?>
<span class="floatClear">&nbsp;</span></div>