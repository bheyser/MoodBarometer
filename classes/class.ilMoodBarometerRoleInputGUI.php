<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodBarometerSuperiorsRoleInputGUI
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
class ilMoodBarometerRoleInputGUI extends ilTextInputGUI
{
	/**
	 * @var ilMoodBarometerPlugin
	 */
	protected $plugin;
	
	protected $selectedRoleId = 0;
	
	/**
	 * Constructor
	 *
	 * @param	string	$a_title	Title
	 * @param	string	$a_postvar	Post Variable
	 */
	function __construct($a_title, $a_postvar, $a_class, $a_autocomplete_cmd, ilMoodBarometerPlugin $plugin)
	{
		global $DIC;
		
		$this->ctrl = $DIC->ctrl();
		$ilCtrl = $DIC->ctrl();
		
		if (is_object($a_class))
		{
			$a_class = get_class($a_class);
		}
		$a_class = strtolower($a_class);
		
		parent::__construct($a_title, $a_postvar);
		$this->setInputType("raci");
		$this->setMaxLength(70);
		$this->setSize(30);
		$this->setDataSource($ilCtrl->getLinkTargetByClass($a_class, $a_autocomplete_cmd, "", true));
		
		$this->plugin = $plugin;
	}
	
	/**
	 * @return mixed
	 */
	public function getSelectedRoleId()
	{
		return $this->selectedRoleId;
	}
	
	/**
	 * @param mixed $selectedRoleId
	 */
	public function setSelectedRoleId($selectedRoleId)
	{
		$this->selectedRoleId = $selectedRoleId;
	}
	
	public function checkInput()
	{
		if( !parent::checkInput() )
		{
			return false;
		}
		
		if( strlen($_POST[$this->getPostVar()]) )
		{
			$list = self::getRoleList($_POST[$this->getPostVar()], false);
			
			if( !count($list) )
			{
				$this->setValue($_POST[$this->getPostVar()]);
				$this->setAlert($this->plugin->txt('superiors_role_invalid_alert'));
				return false;
			}
			
			$this->setSelectedRoleId($list[0]->id);
		}
		
		return true;
	}
	
	public static function getRoleList($roleSearchString, $autoComplete)
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		$ilDB = $DIC->database();
		
		if( $autoComplete )
		{
			$roleSearchString = '%'.$roleSearchString.'%';
		}
		
		$query = "SELECT o1.obj_id role_id, o1.title role, o2.title container FROM object_data o1 ".
			"JOIN rbac_fa fa ON o1.obj_id = rol_id ".
			"JOIN tree t1 ON fa.parent =  t1.child ".
			"JOIN object_reference obr ON ref_id = t1.parent ".
			"JOIN object_data o2 ON obr.obj_id = o2.obj_id ".
			"WHERE o1.type = 'role' ".
			"AND assign = 'y' ".
			"AND ".$ilDB->like('o1.title','text', $roleSearchString)." ".
			"AND fa.parent = 8 ".
			"ORDER BY role,container";
		
		$res = $ilDB->query($query);
		$counter = 0;
		$result = array();
		while($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT))
		{
			$result[$counter] = new stdClass();
			$result[$counter]->id = $row->role_id;
			$result[$counter]->value = $row->role;
			$result[$counter]->label = $row->role;
			++$counter;
		}
		
		return $result;
	}
}
