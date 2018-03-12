<?php
/*******************************************************************************
ezproxyauth.class.php

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

class ezproxyauth{
	protected static $_instance;
	
	private static $_userList = array();

	private $user = null;

	private function __construct()
	{
        $registry = registry::getInstance();
        $db = $registry->db;
        self::populateList($db);
	}
	
	public static function getInstance()
	{
		
		if(!self::$_instance)
		{
			$c = __CLASS__;
			self::$_instance = new $c;
		}
		return self::$_instance;
	}

	private function populateList($db)
	{
		$userQuery = "SELECT user FROM auth";
		$userQuery = $db->real_escape_string($userQuery);
		$userResult = $db->query($userQuery);
		self::$_userList = array();
		while($row = $userResult->fetch_array()){
			self::$_userList[] = $row[0];
		}
	}

    public function authenticate()
    {
    	if (!Config::falsey(Config::get('auth:require'))) {
	        $userServerSource = 
	        	Config::has('auth:userServerSource') 
	        	? Config::get('auth:userServerSource') 
	        	: false;
	        $this->user =
	        	$userServerSource && array_key_exists($userServerSource,$_SERVER)
	        	? $_SERVER[$userServerSource]
	        	: (
	        		array_key_exists('REMOTE_USER', $_SERVER)
	        		? $_SERVER['REMOTE_USER']
	        		: ''
	        	);
	        return self::isAllowed($this->user);
    	} else {
			$this->user = array_key_exists('REMOTE_USER', $_SERVER)
					? $_SERVER['REMOTE_USER']
					: '';
    		return true;
    	}
    }	

    public function getUser()
	{
		return $this->user;
	}

	public static function isAllowed($user='')
	{
		return(!('' == $user) && in_array($user,self::$_userList));
	}
}