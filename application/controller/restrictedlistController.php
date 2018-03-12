<?php
/*******************************************************************************
restrictedlistController.php

Created by Jason Raitz,
NCSU Libraries, NC State University (libraries.opensource@ncsu.edu).

Copyright (c) 2015 North Carolina State University, Raleigh, NC.

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
class restrictedlistController extends baseController {

	public function index()
	{
        $db = $this->registry->db;
        $this->registry->template->restrictedData = $db->getRestrictedResources();
		$this->registry->template->makeCsv('restrictedlist');
	}

}