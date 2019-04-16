<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Trait ilMoodBarometerItemListTrait
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
trait ilMoodBarometerMoodFilterTrait
{
	/**
	 * @var int
	 */
	protected $yearFilter;
	
	/**
	 * @var int
	 */
	protected $weekFilter;
	
	/**
	 * @var int
	 */
	protected $departmentRoleFilter;
	
	/**
	 * @var bool
	 */
	protected $superiorsOnly;
	
	/**
	 * @return int
	 */
	public function getYearFilter()
	{
		return $this->yearFilter;
	}
	
	/**
	 * @param int $yearFilter
	 */
	public function setYearFilter($yearFilter)
	{
		$this->yearFilter = $yearFilter;
	}
	
	/**
	 * @return int
	 */
	public function getWeekFilter()
	{
		return $this->weekFilter;
	}
	
	/**
	 * @param int $weekFilter
	 */
	public function setWeekFilter($weekFilter)
	{
		$this->weekFilter = $weekFilter;
	}
	
	/**
	 * @return int
	 */
	public function getDepartmentRoleFilter()
	{
		return $this->departmentRoleFilter;
	}
	
	/**
	 * @param int $departmentRoleFilter
	 */
	public function setDepartmentRoleFilter($departmentRoleFilter)
	{
		$this->departmentRoleFilter = $departmentRoleFilter;
	}
	
	/**
	 * @return bool
	 */
	public function isSuperiorsOnly()
	{
		return $this->superiorsOnly;
	}
	
	/**
	 * @param bool $superiorsOnly
	 */
	public function setSuperiorsOnly($superiorsOnly)
	{
		$this->superiorsOnly = $superiorsOnly;
	}
	
	public function getWhereExpression()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$conditions = array();
		
		if( $this->getYearFilter() )
		{
			$conditions[] = "year = ".$DIC->database()->quote($this->getYearFilter(), 'integer');
		}
		
		if( $this->getWeekFilter() )
		{
			$conditions[] = "week = ".$DIC->database()->quote($this->getWeekFilter(), 'integer');
		}
		
		if( $this->getDepartmentRoleFilter() )
		{
			$filter = $this->getDepartmentRoleFilter() < 0 ? 0 : $this->getDepartmentRoleFilter();
			
			$conditions[] = "department_role = ".$DIC->database()->quote(
				$filter, 'integer'
			);
		}
		
		if( $this->isSuperiorsOnly() )
		{
			$conditions[] = "superior = ".$DIC->database()->quote(1, 'integer');
		}
		
		if( count($conditions) )
		{
			return implode(' AND ', $conditions);
		}
		
		return "1 = 1";
	}
}