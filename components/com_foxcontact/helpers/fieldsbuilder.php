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

class FieldsBuilder
	{
	public $Fields = array();
	private $cparams;
	private $mparams;
	private $isvalid;

	public function __construct(&$component_params, &$module_params, $ismodule)
		{
		$this->cparams = $component_params;
		$this->mparams = $module_params;

		$this->LoadFields();
		if ($ismodule) $this->OverrideFields();		
		$this->ValidateEmail();  // email can have text without being valid		
		$this->isvalid = intval($this->ValidateForm());  // Is all fields valid?
		}
		

	public function IsValid()
		{
		return $this->isvalid;
		}


	function LoadFields()
		{
		for ($n = 0; $n < 2; ++$n) $this->LoadField("sender", $n);
		$this->LoadField("top_text", NULL);
		$this->LoadField("bottom_text", NULL);
		$this->LoadField("missing_fields_text", NULL);
		$this->LoadField("email_sent_text", NULL);
		$this->LoadField("spam_detected_text", NULL);
		for ($n = 0; $n < 10; ++$n) $this->LoadField("text", $n);
		for ($n = 0; $n < 3; ++$n) $this->LoadField("dropdown", $n);
		for ($n = 0; $n < 3; ++$n) $this->LoadField("textarea", $n);
		for ($n = 0; $n < 5; ++$n) $this->LoadField("checkbox", $n);
		}


	function LoadField($type, $number)  // Example: 'text', '0'
		{
		$index = $type . (string)$number;  // Example: 'text0'
		$this->Fields[$index]['Display'] = intval($this->cparams->get($index . "display", ""));
		// If not to be displayed, it's useless to continue reading other values
		if (!$this->Fields[$index]['Display']) return;

		$this->Fields[$index]['Type'] = $type;
		$this->Fields[$index]['Name'] = $this->cparams->get($index, "");
		$this->Fields[$index]['PostName'] = $this->SafeName($this->Fields[$index]['Name']);
		$this->Fields[$index]['Values'] = $this->cparams->get($index . "values", "");

//		$this->Fields[$index]['Width'] = intval($this->cparams->get($index . "width", ""));
//		$this->Fields[$index]['Height'] = intval($this->cparams->get($index . "height", ""));
		$this->Fields[$index]['Width'] = intval($this->cparams->get($type . "width", ""));
		$this->Fields[$index]['Height'] = intval($this->cparams->get($type . "height", ""));
		$this->Fields[$index]['Unit'] = $this->cparams->get($type . "unit", "");

		$this->Fields[$index]['Value'] = JRequest::getVar($this->Fields[$index]['PostName'], NULL, 'POST');
		if ($this->Fields[$index]['Value'] == $this->Fields[$index]['Name'])  // Example: Field='Your name' Value='Your name'
			{
			// Seems like a submission from the module without filling the field, so let's invalidate the value!
			$this->Fields[$index]['Value'] = "";
			}
		$this->Fields[$index]['IsValid'] = intval($this->ValidateField($this->Fields[$index]['Value'], $this->Fields[$index]['Display']));
		}


	function OverrideFields()
		{
		// Override some values with the module specific parameters
		for ($n = 0; $n < 2; ++$n) $this->OverrideField("sender", $n);
		for ($n = 0; $n < 3; ++$n) $this->OverrideField("textarea", $n);
		for ($n = 0; $n < 10; ++$n) $this->OverrideField("text", $n);
		}


	function OverrideField($type, $number)
		{
		$index = $type . (string)$number;
		// If not to be displayed, it's useless to continue overriding other values
		if (!isset($this->Fields[$index]) || !$this->Fields[$index]['Display']) return;

		// Properties named Type, Name, PostName, Display, Values don't need to be overridden
		$width = intval($this->mparams->get($type . "width", "0"));  // Read the value from the *module* parameters
		if ($width) $this->Fields[$index]['Width'] = $width;        // Only if set for the module, this value overrides the component one
		$height = intval($this->mparams->get($type . "height", "0"));  
		if ($height) $this->Fields[$index]['Height'] = $height;
		$this->Fields[$index]['Unit'] = $this->mparams->get($type . "unit", "px");  // Unit is always set for the module
		}


	function SafeName($name)
		{
		// In $_POST[names], spaces are replaced with underscores. The reason is that PHP used to create a local variable
		// for each form value (now it's optional an deprecated) and you can't have a variable with spaces on its name.
		// Other characters than spaces, are not invalid. So, it's better replace all of them

		// In addition, a valid variable name starts with a letter or underscore, followed by any number of letters, numbers, or underscores.
		// As a regular expression, it would be expressed thus: '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*
		// In case field name starts with a number, it's better to put an underscore before it
		// "/[^a-zA-Z0-9\s]/" this allows spaces
		// "/[^a-zA-Z0-9]/" this doesn't allow spaces
		// This code doesn't work for non latin charsets, because builds a name with only underscores
        //$name = "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $name);
		// Truncate to 64 char
		//$name = substr($name, 0, 64);        
		//return $name;
        return "_" . md5($name);
		}


	function ValidateForm()
		{
		$result = true;

		// Validate default fields
		$result &= $this->ValidateGroup("sender");
		// Validate Text fields
		$result &= $this->ValidateGroup("text");
		// Validate Dropdown fields
		$result &= $this->ValidateGroup("dropdown");
		// Validate Check Boxes
		$result &= $this->ValidateGroup("checkbox");
		// Validate text areas
		$result &= $this->ValidateGroup("textarea");

		return $result;
		}


	// $family can be 'text', 'dropdown', 'textarea' or 'checkbox'
	function ValidateGroup($family)
		{
		$result = true;

		for ($l = 0; $l < 10; ++$l)
			{
			// isset($this->Fields[$family . $l]) is needed to fix following error displayed when running on wamp server
			// Notice: Undefined index: sender[...] in C:\wamp\[...]\helpers\fieldsbuilder.php
			if (isset($this->Fields[$family . $l]) && $this->Fields[$family . $l]['Display'])
				{
				$result &= $this->Fields[$family . $l]['IsValid'];
				}
			}

		return $result;
		}


	// Check a single field and return a boolean value
	function ValidateField($fieldvalue, $fieldtype)
		{
		// Params:
		// $fieldvalue is a string with the text filled by user
		// $fieldtype can be 0 = unused, 1 = optional, 2 = required
		// P | R | F | V   (Post | Required | Filled | Valid)
		// 0 | 0 | 0 | 1
		// 0 | 0 | 1 | 1
		// 0 | 1 | 0 | 1
		// 0 | 1 | 1 | 1
		// 1 | 0 | 0 | 1
		// 1 | 0 | 1 | 1
		// 1 | 1 | 0 | 0
		// 1 | 1 | 1 | 1
		return !(count($_POST) && ($fieldtype == 2) && empty($fieldvalue));
		}


	function ValidateEmail()
		{
		if (!count($_POST)) return true;
		if (!isset($this->Fields['sender1']['Value'])) return false;

		// Todo: I should use for every field the property 'IsEmail' yes defined in xml
		$this->Fields['sender1']['IsValid'] &= (bool)strlen(filter_var($this->Fields['sender1']['Value'], FILTER_VALIDATE_EMAIL));
		//jimport('joomla.mail.helper');
		//(JMailHelper::isEmailAddress($email) == false)
		}


	function Dump()
		{
		$dump = "<h3>Fields class dump:</h3>" . print_r($this, true); 
		$dump = str_replace("\n", "<br>\n", $dump);
		$dump = str_replace(" ", "&nbsp;", $dump);
		return $dump;
		}

	}
