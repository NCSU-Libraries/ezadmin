<?php
/*******************************************************************************
index.php

Description: Bootstrap script for EZadmin. Sets up the environment and handles
some basic installation issues.

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

if (file_exists('localize.php')) {
	include('localize.php');
}

defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (
    	getenv('APPLICATION_ENV') 
    	? getenv('APPLICATION_ENV') 
    	: 'production'
    ));
    
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', (
    	getenv('APPLICATION_PATH') 
    	? getenv('APPLICATION_PATH') 
    	: (realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/application/')
    ));

defined('APPLICATION_CONF')
    || define('APPLICATION_CONF', (
    	getenv('APPLICATION_CONF') 
    	? getenv('APPLICATION_CONF') 
    	: (APPLICATION_PATH . 'config/config.xml')
    ));

if (!is_readable(APPLICATION_PATH . 'init.php')) {
	header('HTTP/1.0 500 Internal Server Error');
	die('This applicaiton has not been properly installed. Unable to access startup scripts.');
}

require_once(APPLICATION_PATH . 'init.php');

$rawOutputPath = trim($registry->config->get('outputPath'));
$translatedOutputPath = trim(str_replace('[APPLICATION_PATH]', APPLICATION_PATH, $rawOutputPath));
$realOutputPath = realpath($translatedOutputPath) . '/';

if ('' == $rawOutputPath || '' == $translatedOutputPath) {
	header('HTTP/1.0 500 Internal Server Error');
	die("This applicaiton has not been properly installed. The output folder is not properly specified ({$translatedOutputPath}).");
}

if ('' == $realOutputPath || ! is_writable($translatedOutputPath)) {
	header('HTTP/1.0 500 Internal Server Error');
	die("This applicaiton has not been properly installed. Insufficient permissions in the output folder ({$translatedOutputPath}).");
}

$registry->outputPath = $realOutputPath;
$registry->pushUpdates = 'true' == strtolower(trim($registry->config->get('pushUpdates')));
$registry->uploadCommand = trim($registry->config->get('uploadCommand'));
$registry->uploadTargets = $registry->config->get('uploads:+');
$registry->template = new template($registry);

$registry->router = new router($registry);
$registry->router->setPath (APPLICATION_PATH . '/controller');

$registry->router->loader();