<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodBarometerConfig
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
class ilMoodBarometerConfig
{
	const SELECTION_FREEZE_DEFAULT = true;
	
	/**
	 * @var ilSetting
	 */
	protected $settings;
	
	/**
	 * ilMoodBarometerConfig constructor.
	 * @param string $settingsId
	 */
	public function __construct($settingsId)
	{
		$this->settings = new ilSetting($settingsId);
	}
	
	public function isSelectionFreezeEnabled()
	{
		return (bool)$this->settings->get('selection_freeze', (int)self::SELECTION_FREEZE_DEFAULT);
	}
	
	public function setSelectionFreezeEnabled($selectionFreezeEnabled)
	{
		$this->settings->set('selection_freeze', (int)$selectionFreezeEnabled);
	}
	
	public function getPrivacyCategoryRefId()
	{
		return $this->settings->get('privacy_category', 0);
	}
	
	public function setPrivacyCategoryRefId($privacyCategoryRefId)
	{
		$this->settings->set('privacy_category', $privacyCategoryRefId);
	}
	
	public function getSuperiorsSurveyRefId()
	{
		return $this->settings->get('superiors_survey', 0);
	}
	
	public function setSuperiorsSurveyRefId($superiorsSurveyRefId)
	{
		$this->settings->set('superiors_survey', $superiorsSurveyRefId);
	}
	
	public function getSuperiorsRoleId()
	{
		return $this->settings->get('superiors_role', 0);
	}
	
	public function setSuperiorsRoleId($superiorsRoleId)
	{
		$this->settings->set('superiors_role', $superiorsRoleId);
	}
	
	public function getDepartmentRoleIds()
	{
		return explode(',', $this->settings->get('department_roles', ''));
	}
	
	public function setDepartmentRoleIds($departmentRoleIds)
	{
		$this->settings->set('department_roles', implode(',', $departmentRoleIds));
	}
	
	public function setMinimumRecordAggregation($minimumRecordAggregation)
	{
		$this->settings->set('min_aggregation', $minimumRecordAggregation);
	}
	
	public function getMinimumRecordAggregation()
	{
		return $this->settings->get('min_aggregation', 0);
	}
	
	/**
	 * @return string
	 */
	public function getSuperiorsRoleName()
	{
		$roleId = $this->getSuperiorsRoleId();
		
		if( $roleId == 0 )
		{
			return '';
		}
		
		$role = ilObjectFactory::getInstanceByObjId($roleId, false);
		
		if( $role instanceof ilObjRole )
		{
			return $role->getTitle();
		}
		
		return '';
	}
}
