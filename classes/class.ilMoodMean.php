<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodMean
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
class ilMoodMean
{
	/**
	 * @var integer
	 */
	protected $departmentRoleId;
	
	/**
	 * @var float
	 */
	protected $moodMean;
	
	/**
	 * @var int
	 */
	protected $moodCount;
	
	/**
	 * ilMoodMean constructor.
	 * @param int $departmentRoleId
	 * @param float $moodMean
	 * @param int $moodCount
	 */
	public function __construct($departmentRoleId, $moodMean, $moodCount)
	{
		$this->departmentRoleId = $departmentRoleId;
		$this->moodMean = $moodMean;
		$this->moodCount = $moodCount;
	}
	
	/**
	 * @return int
	 */
	public function getDepartmentRoleId()
	{
		return $this->departmentRoleId;
	}
	
	/**
	 * @param int $departmentRoleId
	 */
	public function setDepartmentRoleId($departmentRoleId)
	{
		$this->departmentRoleId = $departmentRoleId;
	}
	
	/**
	 * @return float
	 */
	public function getMoodMean()
	{
		return $this->moodMean;
	}
	
	/**
	 * @param float $moodMean
	 */
	public function setMoodMean($moodMean)
	{
		$this->moodMean = $moodMean;
	}
	
	/**
	 * @return int
	 */
	public function getMoodCount()
	{
		return $this->moodCount;
	}
	
	/**
	 * @param int $moodCount
	 */
	public function setMoodCount($moodCount)
	{
		$this->moodCount = $moodCount;
	}
}
