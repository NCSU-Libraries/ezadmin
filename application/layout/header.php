<?php

$currentSectionClass = ' class="current"';
$userGuideUrl = $this->registry->config->get('links:userGuideUrl');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
  <title><?php print($title); ?></title>
  <link type="text/css" rel="stylesheet" href="css/ezproxy.css"/>
  
  <link rel="icon" href="icon.gif" type="image/gif"/> 
  <link rel="apple-touch-icon" href="touch.gif"/>
  
  <script type="text/javascript" language="javascript" src="scripts/jquery-1.4.1.js"></script> 
</head>
<body>
  <div id="top" class="outer">
    <div class="application">
      <div class="header">
        <p class="logo"><span class="label">EZ Admin</span></p>
        <ul>
          <li<?php print('index' == $currentRt && 'export' != $currentAct ? $currentSectionClass : ''); ?>><a href="index.php">resource list</a></li>
          <li<?php print('editresource' == $currentRt ? $currentSectionClass : ''); ?>><a href="index.php?rt=editresource">add resource</a></li>
          <li<?php print('index' == $currentRt && 'export' == $currentAct ? $currentSectionClass : ''); ?>><a href="index.php?rt=index/export">export to ezproxy</a></li>
          <li><a href="<?php print($userGuideUrl); ?>">user's guide</a> </li>
          <li><a href="index.php?rt=restrictedlist">download restricted list</a></li>
        </ul>
      </div>
      <div class="main">
        <?php if (isset($status)) { ?>
        <div class="message">
          <?php print($status); ?>
        </div>
      <?php }