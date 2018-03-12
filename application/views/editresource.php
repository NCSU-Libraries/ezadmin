<script type="text/javascript">

function getConfigTypes(){
	return <?php print(json_encode($configTypes, false));?>;
}

function updateFormState()
{
<?php
	if(
		(isset($resourceInfo) && ($resourceInfo['use_custom'] == "T"))
	) {?>
    $("#toggleConfig").removeClass('urlOption');
    
<?php } ?>
    updateConfigDisplay();
}

<?php include('scripts/editResource.js');?>
</script>
<?php
$resourceid = isset($resourceInfo) && array_key_exists('resource_id',$resourceInfo) ? $resourceInfo['resource_id'] : "";
$resourceTitle = isset($resourceInfo['title']) ? $resourceInfo['title'] : "";
$default = (isset($resourceInfo) && $resourceInfo['custom_config'] == "") ? "checked = \"checked\"": "";
$custom = (isset($resourceInfo) && $resourceInfo['custom_config'] != "") ? "checked = \"checked\"": "";
$toggleText = (isset($resourceInfo) && $resourceInfo['custom_config'] != "" ? 'use standard URLs' : 'use custom configuration');
$customConfig = (isset($resourceInfo) && $resourceInfo['custom_config'] != "") ? $resourceInfo['custom_config']: "";
$restricted = (isset($resourceInfo) && $resourceInfo['restricted'] == "T") ? "checked = \"checked\"": "";
$note = isset($resourceInfo['note']) ? $resourceInfo['note'] : "";
$last_edit_date = isset($resourceInfo['last_edit_date']) ? $resourceInfo['last_edit_date'] : "";
$last_edited_by = isset($resourceInfo['last_edited_by']) ? $resourceInfo['last_edited_by'] : "";

//New problem.  How am I going to dynamically genereate the page to account for the multiple URLs?
?>
<div class="resourceForm">

<form id="resource_form" name="resource_form" method="post" action="index.php?rt=index/save" onsubmit="return testing();" >
<input type="hidden" name="resource_id" value="<?php print($resourceid); ?>" />

<label for="resource_name">Resource Name:</label><input type="text" value="<?php print(htmlentities($resourceTitle)); ?>" size="50" name="resource_name" id="resource_name" />
<label for="resource_type">Resource Type:</label><select name="resource_type" id="resource_type">
<?php
foreach($resourceTypes as $name => $id) {
	$selected = (
		isset($resourceInfo) && $resourceInfo['resource_type'] == $name
		? 'selected="selected"'
		: ''
	);
	?>
  <option <?php print($selected); ?> value="<?php print($id); ?>"><?php print($name);?></option>
<?php
}
?>
</select>

<label for="is_restricted">Restricted: </label><input type="checkbox" name="is_restricted" value="true" id="is_restricted" <?php print($restricted); ?>/>
	<label for="note">Notes: </label><textarea name="note" id="note" cols="50" rows="3" maxlength="500"><?php print(htmlentities($note)); ?></textarea><br>
<div id="configSection">
    <span class="formSectionHeader">Configuration:</span>
    <a href="#" id="addUrl" onClick="return false;" class="addUrlButton">add URL</a>
    <a href="#" id="toggleConfig" onClick="return false;" class="configTypeToggle urlOption"><?php print($toggleText); ?></a>
    <div id="configBody">
      <div id="configUrls">
<?php
    $x = 0;
    if(isset($configInfo)){
        foreach($configInfo as $config){
            $type = $config['config_type'];
            $value = $config['config_value'];
            $configId = $config['id'];
?> 
<div class="urlConfigBlock">
<input type="hidden" name="url_id_<?php print($x); ?>" value="<?php print($configId); ?>" />
<a class="deleteUrlButton" href="#" onClick="return false;" id="delete_<?php print($value); ?>" class="del">delete URL</a>
<label for="url_name_<?php print($x); ?>">URL:</label><input class="urlInput" type="text" value="<?php print(htmlentities($value)); ?>" size="50" name="url_name_<?php print($x); ?>" id="url_name_<?php print($x); ?>" />
<label for="url_select_<?php print($x); ?>">URL Type:</label><select name="url_select_<?php print($x); ?>">
<?php
foreach($configTypes as $name => $id) {
	$selected = (
		$type == $name
		? 'selected="selected"'
		: ''
	);
	?>
  <option <?php print($selected); ?> value="<?php print($id); ?>"><?php print($name);?></option>
<?php
}
?>
</select>
</div>

<?php
            $x++;
        }
    } else {
        $x = 1;
?>
<div class="urlConfigBlock">
<a class="deleteUrlButton" href="#" id="delete_0" class="del">delete URL</a>
<label for="url_name_0">URL:</label><input type="text" value="" size="50" name="url_name_0" id="url_name_0" />
<label for="url_select_0">URL Type:</label><select name="url_select_0" id="url_select_0">
<?php foreach($configTypes as $name => $id) { ?>
	<option value="<?php print($id); ?>"><?php print($name);?></option>
<?php } ?>
</select>

</div>
<?php 
    }
?>         
      <span class="floatClear">&nbsp;</span></div>
      <div id="configCustom">
          <label for="custom_config">Custom Configuration:</label>
          <textarea name="custom_config" rows="8" cols="36" id="custom_config"><?php print(htmlentities($customConfig)); ?></textarea>
      <span class="floatClear">&nbsp;</span></div>
    </div>
</div>
<span class="floatClear">&nbsp;</span>
	<?php
	if($last_edit_date != "" || $last_edit_date !="") {
		?>
        <div><span class="formSectionHeader">Last Edited </span><?php echo($last_edit_date.' by '.$last_edited_by."</div>"); ?>
        <span class="floatClear">&nbsp;</span>
<?php
	}
?>
    <input class="rightMargin" type="submit" value="Save Resource" name="save_resource"/>
<?php
    if($resourceid != ""){
?>
<input type="button" class="rightMargin" value="Delete Resource" name="delete_resource" id="delete_resource"/>
<?php
    }
?>
<input type="button" value="Cancel" name="cancel" id="cancel"/>
<input type="hidden" name="created_urls" value="<?php print($x); ?>" id="created_urls" />
<input type="hidden" name="use_custom" id="use_custom" value="false" />
<span class="floatClear">&nbsp;</span>
</form>
</div>