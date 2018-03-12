<?php
/*******************************************************************************
router.class.php

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

class router {
    
    private $registry;
    
    private $path;
    
    private $args = array();
    
    public $file;
    
    public $controller;
    
    public $action;
    
    function __construct($registry) 
    {
        $this->registry = $registry;
    }
    
    public function setPath($path)
    {
        
        if (is_dir($path) == false){
            throw new Exception ('Invalid controller path: `' . $path . '`');
        }
        
        $this->path = $path;
    }
    
    public function loader()
    {
        
        $this->getController();
        
        if (!is_readable($this->file)){
            $this->getController('error');
            $this->action = 'notfound';
        }
        
        if(!$this->registry->auth->authenticate()){
        	$this->getController('error');
            $this->action = 'notallowed';
        }
        $this->registry->user = $this->registry->auth->getUser();

        include $this->file;
        
        $class = $this->controller . 'Controller';
        $controller = new $class($this->registry);
        
        if (is_callable(array($controller, $this->action)) == false){
            $action = 'index';
        } else{
            $action = $this->action;
        }
        
        try {
        	$controller->$action();
        } catch (Exception $e){
        	include($this->path . '/errorController.php');
        	$this->registry->exception = $e;
            $controller = new errorController($this->registry);
            $controller->internalerror();
        }
    }
    
    private function getController($default = '')
    {
        
        $route = (
        	empty($default)
        	? (
        		empty($_GET['rt'])
        		? ''
        		: $_GET['rt'] 
        	) : $default
        );

        if (empty($route)){
            $route = 'index';
        }
        else{
            $parts = explode('/', $route);
            $this->controller = $parts[0];
            if(isset( $parts[1])){
                $this->action = $parts[1];
            }
        }
        
        if (empty($this->controller)){
            $this->controller = 'index';    
        }
        
        if (empty($this->action)){
            $this->action = 'index';
        }
        
        $this->registry->template->currentRt = $this->controller;
        $this->registry->template->currentAct = $this->action;
        $this->file = $this->path . '/' . $this->controller . 'Controller.php';
    }
}