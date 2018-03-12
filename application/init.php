<?php
/*******************************************************************************
init.php

Description: Initializes and loads the necessary objects.

Created by Troy Hurteau, 
Modified by Eric McEachern,
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
switch (APPLICATION_ENV) {
	case 'development':
		
		ini_set('display_errors', 'On');
		error_reporting(E_ALL);
	break;

	case 'production':
	default:
		
		
}

include APPLICATION_PATH . 'Registry.php';
include APPLICATION_PATH . 'Config.php';
include APPLICATION_PATH . 'controller_base.class.php';
include APPLICATION_PATH . 'router.class.php';
include APPLICATION_PATH . 'template.class.php';

function __autoload($class_name) 
{
    $filename = strtolower($class_name) . '.class.php';
    $file = APPLICATION_PATH . '/model/' . $filename;
    if (file_exists($file) == false){
        return false;
    }
    include ($file);
}

$registry = Registry::getInstance();
$registry->config = Config::getInstance();
$registry->db = ezproxydb::getInstance();
$registry->auth = ezproxyauth::getInstance();