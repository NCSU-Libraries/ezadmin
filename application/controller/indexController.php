<?php
/*******************************************************************************
indexController.php

Description: Primary controller for main interface page.

Created by Karl Doerr, 
Modified by Troy Hurteau, Eric McEachern, Emily Lynema, 
NCSU Libraries, NC State University (libraries.opensource@ncsu.edu).

Copyright (c) 2011 North Carolina State University, Raleigh, NC.

EZadmin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

EZadmin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

EZadmin as distributed by NCSU Libraries is located at:
http://code.google.com/p/ezadmin/

*******************************************************************************/


class indexController extends baseController
{

	public $resourceArray = array();
	private $configArray = array();
	
	private function populateResourceArray()
	{
		$resourceQuery =
			"SELECT r.id, title, custom_config, t.name AS resource_type, use_custom, restricted, note "
			. "FROM resource AS r "
			. "JOIN resource_type AS t ON t.`id` = r.`type` "
			. "ORDER BY title";
		$db = $this->registry->db;
		$resourceQuery = $db->real_escape_string($resourceQuery);
		$resourceResult = $db->query($resourceQuery);
		$x = 0;
		if (!$resourceResult) {
			throw new Exception('No Resource Types Found. Error: ' . $db->error);
		}
		while($row = $resourceResult->fetch_array()){
			$this->resourceArray[$x] = array();
			$this->resourceArray[$x]['id'] = $row[0];
			$this->resourceArray[$x]['title'] = $row[1];
			$this->resourceArray[$x]['type'] = $row[3];
			$this->resourceArray[$x]['custom_config'] = $row[2];
			$this->resourceArray[$x]['use_custom'] = $row[4];
			$this->resourceArray[$x]['restricted'] = $row[5];
			$this->resourceArray[$x]['note'] = $row[6];
			if(!empty($this->configArray[$row[0]])){
				$this->resourceArray[$x]['config'] = $this->configArray[$row[0]];
			}
			else{
				$this->resourceArray[$x]['config'] = NULL;
			}
			$x++;
		}
	}
	
	private function populateConfigList()
	{
		$db = $this->registry->db;
		$configQuery = "SELECT c.id, resource, t.name AS config_type, config_value "
			. "FROM config AS c "
			. "JOIN config_type AS t ON c.type = t.id "
			. "ORDER BY resource, config_type";
		$configQuery = $db->real_escape_string($configQuery);
		$configResult = $db->query($configQuery);
		$resource = 0;
		if (!$configResult) {
			throw new Exception('No Resource Types Found. Error: ' . $db->error);
		}
		while($row = $configResult->fetch_array()){
			if($resource != $row[1]){
				$resource = $row[1];
				$this->configArray[$resource] = array();
			}
			$id = $row[0];
			$this->configArray[$resource][$id] = array();
			$this->configArray[$resource][$id]['config_type'] = $row[2];
			$this->configArray[$resource][$id]['config_value'] = $row[3];
		}
		
	}
	
	private function getConfigListFor($id)
	{
		$db = $this->registry->db;
		$configQuery = "SELECT id, resource, type, config_value FROM config WHERE resource = {$id} ORDER BY resource";
		$configQuery = $db->real_escape_string($configQuery);
		$configResult = $db->query($configQuery);
		if ($configResult) {
			$resource = 0;
			$returnArray = array();
			while($row = $configResult->fetch_array()){
				$returnArray[] = array(
					'type' => $row[2],
					'config_value' => $row[3]
				);
			}
		}
	}
	
	private function getResource($id)
	{
		$resourceQuery = "SELECT id, title, custom_config, type, use_custom, restricted, note FROM resource WHERE id = {$id}";
		$db = $this->registry->db;
		$resourceQuery = $db->real_escape_string($resourceQuery);
		$resourceResult = $db->query($resourceQuery);
		if ($resourceResult) {
			$row = $resourceResult->fetch_array();
			return array(
				'title' => $row[1],
				'custom' => $row[2],
				'type' => $row[3],
				'use_custom' => $row[4],
				'note' => $row[6]
			);
		}
	}
	
	public function index()
	{
		$this->populateConfigList();
		$this->populateResourceArray();
		$this->registry->template->resourceList = $this->resourceArray;
		$this->registry->template->title = 'EZ Admin - Resource Controller';
		$this->registry->template->show('index');
	}

	public function deleteConfirm()
	{
		$resourceid = $_REQUEST['resource_id'];
		$config = $this->getConfigListFor($resourceid);
		$resource = $this->getResource($resourceid);
		$this->registry->template->resource = '';
		$this->registry->template->title = 'EZ Admin - Confirm Delete';
		$this->registry->template->status = "<p>Are you sure you want to delete {$resource['title']} ?</p>"
		  . "<p>
		  <a class='noButton' href='index.php?rt=index#rid_{$resourceid}'>Cancel</a>
		  <a class='yesButton' href='index.php?rt=index/save&resource_id={$resourceid}&delete_resource=true'>Yes</a>
		  <span class='floatClear'>&nbsp;</span>
		  </p>";
		$this->registry->template->show('message');
		
	}
	
	public function save()
	{
		    $this->correctMagicQuotes();
		    $db = $this->registry->db;
		    $user = $this->registry->user;
			$title = array_key_exists('resource_name', $_REQUEST) ? $_REQUEST['resource_name'] : '';
			$custom_config = array_key_exists('custom_config', $_REQUEST) ? $_REQUEST['custom_config'] : '';
			$resource_type = array_key_exists('resource_type', $_REQUEST) ? $_REQUEST['resource_type'] : '';
			$use_custom = isset($_REQUEST['use_custom']) && 'true' == $_REQUEST['use_custom'] ? "T" : "F";
			$restricted = isset($_REQUEST['is_restricted']) ? "T" : "F";
			$note = array_key_exists('note', $_REQUEST)? $_REQUEST['note'] : '';
			$resourceid = "";
			$updateStatement = "";
			if(isset($_REQUEST['delete_resource'])){
				$this->registry->template->title = 'EZ Admin - Deleted Resource';
				//echo "Deleteing!";
				//print_r($_REQUEST);
				//die;
				$oldData = $this->getResource($_REQUEST['resource_id']);
				$old_title = $oldData['title'];
				
				$resourceid = $_REQUEST['resource_id'];
				$deleteConfigQuery = "DELETE FROM config WHERE resource = ?";
				$deleteResourceQuery = "DELETE FROM resource WHERE id = ?";
				$deleteConfigStmt = $db->prepare($deleteConfigQuery);
				$deleteConfigStmt->bind_param('i', $resourceid);
				$deleteConfigStmt->execute();
				$deleteConfigStmt->close();
				
				$deleteResourceStmt = $db->prepare($deleteResourceQuery);
				$deleteResourceStmt->bind_param('i', $resourceid);
				$deleteResourceStmt->execute();
				if($deleteResourceStmt->affected_rows > 0){
					$updateStatement = "Successfully deleted the {$old_title} resource.<br />";
				}
				$deleteResourceStmt->close();
			} else if(isset($_REQUEST['save_resource']) && $_REQUEST['resource_id'] != ""){
				$this->registry->template->title = 'EZ Admin - Edited Resource';
				//echo "Updating!";
				//print_r($_REQUEST);
				//die;
				$resourceid = $_REQUEST['resource_id'];
				$current_title = "";
				$current_custom_config = "";
				$current_resource_type = "";
				$selectResource = "SELECT title, custom_config, type FROM resource WHERE id = ?";
				$selectStmt = $db->prepare($selectResource);
				$selectStmt->bind_param('i', $resourceid);
				$selectStmt->execute();
				$selectStmt->bind_result($current_title, $current_custom_config, $current_resource_type);
				$selectStmt->fetch();
				$selectStmt->close();
				//if($current_title != "" && $current_resource_type != ""){
				if($current_title != trim('')){
					$resourceUpdateQuery = "UPDATE resource SET title=?, custom_config=?, type=?, use_custom=?, restricted=?, note=? , last_edited_by_user=? WHERE id = ?";
					$updateStmt = $db->prepare($resourceUpdateQuery);
					$updateStmt->bind_param('ssissssi', $title, $custom_config, $resource_type, $use_custom, $restricted, $note, $user, $resourceid);
					$updateStmt->execute();
					if($updateStmt->affected_rows > 0){
						$updateStatement = "Successfully updated the <a href='#rid_" . $resourceid . "'>" . $title . "</a> resource.<br />";
					}
					$updateStmt->close();
					$configs = array();
					foreach ($_REQUEST as $key=>$value){
						$temp = explode("_", $key);
						if($temp[0] == "url" && trim($value) != ''){
							$configs[$temp[2]][$temp[1]] = $value;
						}
					}
					$urlArray = array();
					foreach($configs as $config){
						$urlid = (
							array_key_exists('id', $config)
							? $config['id']
							: ''
						);
						if (!array_key_exists('name', $config)) {
							if ('' != $urlid) {
								$deleteUrl = "DELETE FROM config WHERE id = ?";
								$deleteUrlStmt = $db->prepare($deleteUrl);
								$deleteUrlStmt->bind_param('i', $urlid);
								$deleteUrlStmt->execute();
								if($deleteUrlStmt->affected_rows > 0){
									$updateStatement .= "Successfully deleted the <b>blank</b> configuration value for <a href='#rid_" . $resourceid . "'>" . $title . "</a>. <br />";
								}
								$deleteUrlStmt->close();
							}
						} else {
							$urlname = $config['name'];
							$urltype = $config['select'];
							array_push($urlArray, $urlid);
							if('' != $urlid && ($urlname != "" && !is_null($urlname))){
								$configUpdate= "UPDATE config SET resource=?, type=?, config_value=? WHERE id=?";
								$configUpdateStmt = $db->prepare($configUpdate);
								$configUpdateStmt->bind_param('iisi', $resourceid, $urltype, $urlname, $urlid);
								$configUpdateStmt->execute();
								if($configUpdateStmt->affected_rows > 0){
									$updateStatement .= "Successfully updated the $urlname configuration value for <a href='#rid_" . $resourceid . "'>" . $title . "</a>.<br />";
								}
								$configUpdateStmt->close();
							}
							else{
								$configInsert = "INSERT INTO config (resource, type, config_value) VALUES (?, ?, ?)";
								$configInsertStmt = $db->prepare($configInsert);
								$configInsertStmt->bind_param('iss', $resourceid, $urltype, $urlname);
								$configInsertStmt->execute();
								array_push($urlArray, $db->insert_id);
								if($configInsertStmt->affected_rows > 0){
									$updateStatement .= "Successfully added the $urlname configuration value for <a href='#rid_" . $resourceid . "'>" . $title . "</a>.<br /> ";
								}
								$configInsertStmt->close();
							}
						}
					}
					$current_configId = "";
					$current_configType = "";
					$current_configValue = "";
					$currentIdArray = array();
					$existingTitlesArray = array();
					$configSelectQuery = "SELECT id, type, config_value FROM config WHERE resource = ?";
					$configStmt = $db->prepare($configSelectQuery);
					$configStmt->bind_param('i', $resourceid);
					$configStmt->execute();
					$configStmt->bind_result($current_configId, $current_configType, $current_configValue);
					while($configStmt->fetch()){
						array_push($currentIdArray, $current_configId);
						$existingTitlesArray[$current_configId] = $current_configValue;
					}
					$configStmt->close();
					
					$idDiff = array_diff($currentIdArray, $urlArray);
					foreach ($idDiff as $id){
						$deleteUrl = "DELETE FROM config WHERE id = ?";
						$deleteUrlStmt = $db->prepare($deleteUrl);
						$deleteUrlStmt->bind_param('i', $id);
						$deleteUrlStmt->execute();
						if($deleteUrlStmt->affected_rows > 0){
							$updateStatement .= "Successfully deleted the " .  $existingTitlesArray[$id] . " configuration value for <a href='#rid_" . $resourceid . "'>" . $title . "</a>. <br />";
						}
						$deleteUrlStmt->close();
					}
				} else {
					$updateStatement = "Failed to update a resource. Not found in the database.<br />";
				}
			} else {
				//echo "Adding!";
				//print_r($_REQUEST);
				//die;
				$insert = "INSERT INTO resource (title, custom_config, type, use_custom, restricted, note) VALUES (?, ?, ?, ?, ?, ?)";
				$stmt = $db->prepare($insert);
				if ($stmt) {
					$stmt->bind_param('ssisss', $title, $custom_config, $resource_type, $use_custom, $restricted, $note);
					$stmt->execute();
					$resourceid = $db->insert_id;
					if($stmt->affected_rows > 0){
						$updateStatement ="Successfully added <a href='#rid_" . $resourceid . "'>" . $title . "</a> resource.<br />";
					}
					$stmt->close();
					$configs = array();
					foreach ($_REQUEST as $key=>$value){
						$temp = explode("_", $key);
						if($temp[0] == "url"){
							if(!array_key_exists($temp[2], $configs)) {
								$configs[$temp[2]] = array();
							}
							$configs[$temp[2]][$temp[1]] = $value;
						}
					}
					foreach($configs as $config){
						$urlname = $config['name'];
						$urltype = $config['select'];
						if($urlname != '' && !is_null($urlname)){
							$configInsert = "INSERT INTO config (resource, type, config_value) VALUES (?, ?, ?)";
							$configStmt = $db->prepare($configInsert);
							if ($configStmt) {
								$configStmt->bind_param('iis', $resourceid, $urltype, $urlname);
								$configStmt->execute();
								if($configStmt->affected_rows > 0){
									$updateStatement .="<br /> Successfully added the $urlname configuration value.";
								}
								$configStmt->close();
							} else {
								$updateStatement .= "Failed to add {$urlname} configuration value to {$title} resource. DB save failed.<br />";
							}
						}

					}
				} else {
					$updateStatement = "Failed to add {$title} resource. DB save failed.<br />";
				}
			}
			//When deleteing resources, we need a confirmation box.
			
			if ('' != trim($updateStatement)) {
				$this->registry->template->status = $updateStatement;
			}
			$this->index();
			
		}
		
	public function export()
	{
		if(!array_key_exists('export_confirm',$_REQUEST) || 'Export' != $_REQUEST['export_confirm']){
			$this->registry->template->title = 'Please confirm export';
			$this->registry->template->show('exportConfirm');
		} else {
				//3) Does someone have to sign in?  Will only certain people see it
			
			$this->populateConfigList();
			$this->populateResourceArray();
	        $path = $this->registry->outputPath;
	        $pushNow = $this->registry->pushUpdates;
			$error = '';
				
			$restrictedTypes = array('T', 'F');
			$restrictedStart = "####### This is the list of restricted resrources  #########\nGroup restricted\n\n";
//			$unrestrictedStart = "####### This is the list of unrestricted resrources  #########\nGroup unrestricted\n\nIncludeFile sage.txt\nIncludeFile oxford.txt\n\n"; //saving as an example of how to manually include a txt file on the proxy server
			$unrestrictedStart = "####### This is the list of unrestricted resrources  #########\nGroup unrestricted\n\n";
			
			$writeSuccess = 1;
			
			foreach ($restrictedTypes as $restricted){
				$file = ('T' == $restricted ? $path .'restrictedoutput.txt' : $path. 'unrestrictedoutput.txt');
				$writeStart = 'T' == $restricted ? $restrictedStart : $unrestrictedStart;
				$fh = fopen($file, 'w');
				if($fh){						
					$writeSuccess = $writeSuccess && fwrite($fh, $writeStart);
						foreach($this->resourceArray as $resource){
							if($resource['restricted'] == $restricted){
								$resourceString = "T " . $resource['title'] . "\n";
								$writeSuccess = $writeSuccess && fwrite($fh, $resourceString);
								if ($resource['use_custom'] == 'T'){
									$configString = $resource['custom_config'] . "\n";
									$writeSuccess = $writeSuccess && fwrite($fh, $configString);
								}
								else{
									$resourceConfig = (array)$resource['config'];
									foreach ($resourceConfig as $config){
										$configString = $config['config_type'] . " " . $config['config_value'] . "\n";
										$writeSuccess = $writeSuccess && fwrite($fh, $configString);
									}
								}
								$writeSuccess = $writeSuccess && fwrite($fh, "\n");
							}
						}
					fclose($fh);
				} else {
					$error .= "<p>An error occured while writing to the " . ($restricted == 'T' ? '' : 'un') . "restricted configuration file.<p>";
				}
				if(!$writeSuccess) {
					$error .= "<p>An error occured while writing to the " . ($restricted == 'T' ? '' : 'un') . "restricted configuration file.<p>";
				}
			}
			
			if('' == trim($error) && $pushNow){
				foreach ($this->registry->uploadTargets->uploadTarget as $uploadTarget){
					if (!rsyncAdapter::run($this->registry, $path . 'restrictedoutput.txt', $uploadTarget)){
						$error .= "<p>An error occured while uploading the unrestricted configuration file.<p>";
						$error .= '<p>' . rsyncAdapter::getErrorMessage() . '</p>';
					}
					if (!rsyncAdapter::run($this->registry, $path . 'unrestrictedoutput.txt', $uploadTarget)){
						$error .= "<p>An error occured while uploading the restricted configuration file.<p>";
						$error .= '<p>' . rsyncAdapter::getErrorMessage() . '</p>';
					}
				}
			}
			$this->registry->template->status = (
				'' == trim($error) 
				? 'Configuration Files Written.' 
				: $error
			);
			$this->index();
		}
	}
	

    private function correctMagicQuotes()
    {
        if(ini_get('magic_quotes_gpc')){
            foreach($_REQUEST as $requestIndex=>$requestValue){
                $_REQUEST[$requestIndex] = $this->stripSlashesHandleArrays($requestValue);
            }
            foreach($_GET as $getIndex=>$getValue){
                $_GETT[$getIndex] = $this->stripSlashesHandleArrays($getValue);
            }
            foreach($_POST as $postIndex=>$postValue){
                $_POST[$postIndex] = $this->stripSlashesHandleArrays($postValue);
            }
            foreach($_COOKIE as $cookieIndex=>$cookieValue){
                $_COOKIE[$cookieIndex] = $this->stripSlashesHandleArrays($cookieValue);
            }
        }
    }

    private function stripSlashesHandleArrays($value)
    {
        if (is_array($value)){
            $newValue = array();
            foreach($value as $valueIndex=>$valueValue){
                $newValue[$valueIndex] = $this->stripSlashesHandleArrays($valueValue);
            }
            return $newValue;
        } else {
            return stripslashes($value);
        }
    }   	
	
}
