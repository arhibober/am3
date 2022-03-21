<?php
/*
This file is part of "Fox Contact Form", a free Joomla! 1.6 Contact Form
You can redistribute it and/or modify it under the terms of the GNU General Public License
GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
Author: Demis Palma
Documentation at http://www.fox.ra.it/joomla-extensions/fox-contact-form.html
Copyright: 2011 Demis Palma
*/

// no direct access
defined('_JEXEC') or die ('Restricted access');

class VFDebugger
	{
	protected $FieldsBuilder;
	public $UserMsg;

	public function __construct(&$fields)
		{
		$this->FieldsBuilder = $fields;
		}

		
	public function Dump()
		{
		$dump = "<h3>Debug info:</h3>\n";
        $dump = "<h5>Is the whole form valid? {" . $this->FieldsBuilder->IsValid() . "}</h5>\n";
		$dump .= $this->DumpPost();
		$dump .= $this->DumpUserMsg();
		return $dump;
		}


	protected function DumpPost()
		{
		$dump = "<h5>php POST data counter: {" . count($_POST) . "}</h5>\n";
		$dump .= "<p><table width=\"100%\">";
		$dump .= "<tr>";
		$dump .= "<th>Name</th><th>Value</th>";
		$dump .= "</tr>";
		foreach ( $_POST as $key => $value )
			{
			$dump .= "<tr>";
			$dump .= "<td>POST['" . $key . "']</td>" . "<td>" . $value . "</td>";
			$dump .= "</tr>";
			}
		$dump .= "</table></p>\n";
		return $dump;
		}


	protected function DumpUserMsg()
		{
 		$dump = "<h5>Additional messages</h5>";
		$dump .= "<p>" . $this->UserMsg . "</p>";
		return $dump;
		}

	}
