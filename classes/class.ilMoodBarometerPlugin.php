<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilUIMoodBarometerPlugin
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
class ilMoodBarometerPlugin extends ilUserInterfaceHookPlugin
{
	/**
	 * @var ilMoodBarometerConfig
	 */
	protected $config;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->includePluginClasses();
		
		$this->config = new ilMoodBarometerConfig($this->getSlotId().'_'.$this->getId());
	}
	
	public function getPluginName()
	{
		return "MoodBarometer";
	}
	
	public function includePluginClasses()
	{
		$this->includeClass('trait.ilMoodBarometerItemListTrait.php');
		$this->includeClass('trait.ilMoodBarometerMoodFilterTrait.php');
		$this->includeClass('interface.ilMoodBarometerTableDataProvider.php');

		$this->includeClass('class.ilMoodBarometerConfig.php');
		
		$this->includeClass('class.ilMoodRepository.php');
		$this->includeClass('class.ilMoodRecord.php');
		$this->includeClass('class.ilMoodRecordList.php');
		$this->includeClass('class.ilMoodMean.php');
		$this->includeClass('class.ilMoodMeanList.php');
		
		$this->includeClass('class.ilMoodBarometerInputGUI.php');
		$this->includeClass('class.ilMoodBarometerRoleInputGUI.php');
		$this->includeClass('class.ilMoodBarometerRepoSelectorInputGUI.php');
		
		$this->includeClass('class.ilMoodBarometerAbstractTableGUI.php');
		$this->includeClass('class.ilMoodBarometerMoodRecordsTableGUI.php');
		$this->includeClass('class.ilMoodBarometerMoodMeansTableGUI.php');
	}
	
	/**
	 * @return ilMoodBarometerConfig
	 */
	public function getConfig()
	{
		return $this->config;
	}
	
	public function handleAjaxRequest()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$moodRec = ilMoodRepository::saveNewMoodByUserId( $DIC->user()->getId(),
			$_POST[ilMoodBarometerInputGUI::MOOD_BAROMETER_AJAX_PARAM]['moodId'],
			$this->isSuperior($DIC->user()->getId()),
			$this->getDepartmentRole($DIC->user()->getId())
		);
		
		header('Content-Type: application/json');
		
		echo json_encode(array(
			'barometerId' => ilMoodBarometerInputGUI::MOOD_BAROMETER_ID,
			'selectedMoodId' => $moodRec->getMoodId()
		));
	}
	
	/**
	 * @param int $userId
	 * @return int
	 */
	public function getDepartmentRole($userId)
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		foreach($this->getConfig()->getDepartmentRoleIds() as $roleId)
		{
			if( !in_array($userId, $DIC->rbac()->review()->assignedUsers($roleId)) )
			{
				continue;
			}
			
			return $roleId;
		}
		
		return 0;
	}
	
	/**
	 * @param int $userId
	 * @return bool
	 */
	public function isSuperior($userId)
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$assignedRoles = $DIC->rbac()->review()->assignedGlobalRoles($userId);
		
		return in_array($this->getConfig()->getSuperiorsRoleId(), $assignedRoles);
	}
	
	/**
	 * @param int $refId
	 * @return string
	 */
	public function getRepositoryLink($refId)
	{
		return ilLink::_getLink($refId);
	}
	
	/**
	 * @param bool $withSystemRoles
	 * @return array
	 */
	public function getGlobalRolesSelectOptions($withSystemRoles = false)
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		$objCache = $DIC['ilObjDataCache']; /* @var ilObjectDataCache $objCache */
		
		$roles = array();
		
		foreach($DIC->rbac()->review()->getGlobalRoles() as $roleId)
		{
			if( $roleId == ANONYMOUS_ROLE_ID )
			{
				continue;
			}
			
			$roles[$roleId] = $this->getRoleName($roleId);
		}
		
		return $roles;
	}
	
	/**
	 * @return string
	 */
	public function getRoleName($roleId)
	{
		if( $roleId == 0 )
		{
			return '';
		}
		
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		$objCache = $DIC['ilObjDataCache']; /* @var ilObjectDataCache $objCache */
		
		return $objCache->lookupTitle($roleId);
	}
}
