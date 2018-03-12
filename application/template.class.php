<?php
/*******************************************************************************
template.class.php

Description: Template for interface.

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

Class Template {
    
    private $registry;
    
    private $vars = array();
    
    private $_header = 'layout/header.php';
    private $_footer = 'layout/footer.php';
    
    
    function __construct($registry)
    {
        $this->registry = $registry;
    }
    
    public function __set($index, $value)
    {
        $this->vars[$index] = $value;
    }
    
    function show($name,$layout = true) 
    {
        $path = APPLICATION_PATH . '/views' . '/' . $name . '.php';
        
        if (file_exists($path) == false){
            throw new Exception('Template not found in ' . $path);
            return false;
        }
        
        foreach ($this->vars as $key => $value){
            $$key = $value;
        }
        if ($layout) {
        	include(APPLICATION_PATH . $this->_header);
        }
        include ($path);
        if ($layout) {
            include(APPLICATION_PATH . $this->_footer);
        }
    }
    function makeCsv($name){
        $path = APPLICATION_PATH . '/views' . '/' . $name . '.php';
        if (file_exists($path) == false){
            throw new Exception('Template not found in ' . $path);
            return false;
        }
        foreach ($this->vars as $key => $value){
            $$key = $value;
        }
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=restrictedList.csv');

        include ($path);

    }
}
