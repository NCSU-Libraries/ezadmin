<?php
/*******************************************************************************
errorController.php

Description: Controller for assigning appropriate error message and type.

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

class errorController extends baseController {
	
	public function index()
	{
		$this->registry->template->title = 'EZ Admin - 500 Application Error';
		$this->registry->template->status = "<p>Unexpected Error.</p>";
		$this->registry->template->show('message');
	}

	public function notfound()
	{
		$this->registry->template->title = 'EZ Admin - 404 Not Found';
		$this->registry->template->status = "<p>The Requested Document Does Not Exist.</p>";
		$this->registry->template->show('message');
	}
	
	public function notallowed()
	{
		$this->registry->template->title = 'EZ Admin - 403 Not Allowed';
		$this->registry->template->status = "<p>The Requested Document Requires Additional Permissions.</p>";
		$this->registry->template->show('message');
	}

	public function internalerror()
	{
		$e = $this->registry->exception;
		$this->registry->template->title = 'EZ Admin - 500 Internal Error';
		$trace = array();
		foreach($e->getTrace() as $stack) {
			$trace[] = "<div class=\"stackSection\"><span class=\"stackLineLabel\">LINE:</span><span class=\"stackLine\">{$stack['line']}</span>" .
				" in <span class=\"stackFile\">{$stack['file']}</span></div> " .
			( '' != $stack['class']
				? "<div class=\"stackSection\"><span class=\"stackClassLabel\">CLASS:</span><span class=\"stackClass\">{$stack['class']}</span>" .
					"<span class=\"stackType\">{$stack['type']}</span>"
				: ''
			) .
			( '' != $stack['function']
				? (
					'' == $stack['class']
					? "<div class=\"stackSection\"><span class=\"stackFunctionLabel\">FUNCTION:</span>"
					: ''
				) . "<span class=\"stackFunction\">{$stack['function']}</span>" .
					(
						count($stack['args']) > 0
						? "(<span class=\"stackArgs\">" . implode( ', ', $stack['args']) . ")</span>"
						: '()'
					)
				: ''
			) . '</div>';
		}
		$this->registry->template->status = "<p>" . $e->getMessage() . "</p>" . "<ul><li>" . implode('</li><li>', $trace) . "</li></ul>";
		$this->registry->template->wideFormFactor = 1;
		$this->registry->template->show('message');
	}
}