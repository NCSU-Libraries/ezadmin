<?php
/*******************************************************************************
Config.php
Implements a manager for configuration loading and access

Created by Troy Hurteau, 
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

/**
 * 
 * Class for loading and providing access to configuration information.
 * @author jthurtea
 *
 */

class Config{
	
	protected static $_configuration = null;
	
	protected static $_singleton = null;
	
	public static function getInstance()
	{
		if (!isset(self::$_singleton)) {
			$c = __CLASS__;
			self::$_singleton = new $c();
		}
		return self::$_singleton;
	}
	
	protected function __construct()
	{
		self::_init();
	}
	
	protected static function _init()
	{
		if (null === self::$_configuration) {
			if(!is_readable(APPLICATION_CONF)){
				throw new Exception('This applicaiton has not been properly installed. Unable to access configuration file.');
			}
			self::$_configuration = simplexml_load_file(APPLICATION_CONF);
			if (!self::$_configuration) {
				throw new Exception('This applicaiton has not been properly installed. The configuration file is malformed.');
			}
		}
	}
	
	public static function get($name)
	{
		self::_init();
		return self::_get(is_array($name) ? $name : explode(':', $name), self::$_configuration);
	}
	
	public static function has($name)
	{
		try{
			self::get($name);
		} catch (Exception $e) {
			return false;
		}
		return true;
	}
	
	protected static function _get($name, $source=null)
	{
		$currentPosition = (null == $source ? self::$_configuration : $source);
		foreach ($name as $index=>$subName) {
			if (isset($currentPosition->$subName)) {
				if ($index == count($name) - 1) {
					return (string)$currentPosition->$subName;
				} else if (array_key_exists($index + 1, $name) && '+' == $name[$index + 1]) {
					return $currentPosition->$subName;
				} else {
					$currentPosition = $currentPosition->$subName;
				}
			} else {
				$nameString = implode(':', $name);
				throw new Exception("Requested configuration option \"{$nameString}\" is not available.");
			}
		}
	}
	
	public static function falsey($value)
	{
		return (
			'false' == trim(strtolower($value))
			|| 'f' == trim(strtolower($value))
			|| 'no' == trim(strtolower($value))
			|| 'n' == trim(strtolower($value))
			|| (is_numeric($value) && 0 == intval($value))
			|| '' == trim($value)
		);
	}
	
	public static function truthy($value)
	{
		return !self::falsey($value);
	}
}
