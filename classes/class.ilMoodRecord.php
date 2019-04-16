<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodRecord
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
class ilMoodRecord
{
	const MOOD_ID_GOOD = '1';
	const MOOD_ID_NEUTRAL = '0';
	const MOOD_ID_BAD = '-1';
	
	const MOOD_IMG_GOOD = 'smiley_good.png';
	const MOOD_IMG_NEUTRAL = 'smiley_neutral.png';
	const MOOD_IMG_BAD = 'smiley_bad.png';
	
	/**
	 * @var int
	 */
	protected $year;
	
	/**
	 * @var int
	 */
	protected $week;
	
	/**
	 * @var int
	 */
	protected $userId;
	
	/**
	 * @var bool
	 */
	protected $moodId;
	
	/**
	 * @var bool
	 */
	protected $isSuperior;
	
	/**
	 * @var integer
	 */
	protected $departmentRoleId;
	
	/**
	 * ilMoodRecord constructor.
	 * @param int $year
	 * @param int $week
	 * @param int $userId
	 * @param bool $isSupirior
	 */
	public function __construct($year, $week, $userId)
	{
		$this->year = $year;
		$this->week = $week;
		$this->userId = $userId;
		$this->moodId = null;
		$this->isSuperior = false;
		$this->departmentRoleId = null;
	}
	
	/**
	 * @return int
	 */
	public function getYear()
	{
		return $this->year;
	}
	
	/**
	 * @param int $year
	 */
	public function setYear($year)
	{
		$this->year = $year;
	}
	
	/**
	 * @return int
	 */
	public function getWeek()
	{
		return $this->week;
	}
	
	/**
	 * @param int $week
	 */
	public function setWeek($week)
	{
		$this->week = $week;
	}
	
	/**
	 * @return int
	 */
	public function getUserId()
	{
		return $this->userId;
	}
	
	/**
	 * @param int $userId
	 */
	public function setUserId($userId)
	{
		$this->userId = $userId;
	}
	
	/**
	 * @return int|null
	 */
	public function getMoodId()
	{
		return $this->moodId;
	}
	
	/**
	 * @param int|null $moodId
	 */
	public function setMoodId($moodId)
	{
		$this->moodId = $moodId;
	}
	
	/**
	 * @return bool
	 */
	public function hasMood()
	{
		return in_array($this->getMoodId(), self::getAvailableMoods());
	}
	
	/**
	 * @return bool
	 */
	public function isSuperior()
	{
		return $this->isSuperior;
	}
	
	/**
	 * @param bool $isSupirior
	 */
	public function setSuperior($isSuperior)
	{
		$this->isSuperior = $isSuperior;
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
	
	public function load()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$query = "
			SELECT * FROM moodbar_mood_records
			WHERE year = %s AND week = %s AND usr_id = %s
		";
		
		$res = $DIC->database()->queryF($query, array('integer', 'integer', 'integer'), array(
			$this->getYear(), $this->getWeek(), $this->getUserId()
		));
		
		while($row = $DIC->database()->fetchAssoc($res))
		{
			$this->setMoodId((int)$row['mood']);
			$this->setSuperior((bool)$row['superior']);
			$this->setDepartmentRoleId((int)$row['department_role']);
		}
	}
	
	public function save()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$DIC->database()->replace('moodbar_mood_records', array(
			'year' => array('integer', $this->getYear()),
			'week' => array('integer', $this->getWeek()),
			'usr_id' => array('integer', $this->getUserId())
			
		), array(
			'mood' => array('integer', $this->getMoodId()),
			'superior' => array('integer', $this->isSuperior()),
			'department_role' => array('integer', $this->getDepartmentRoleId())
		));
	}
	
	/**
	 * @param $moodId
	 * @return string
	 */
	public static function getImageFilename($moodId)
	{
		switch($moodId)
		{
			case self::MOOD_ID_GOOD: return self::MOOD_IMG_GOOD;
			case self::MOOD_ID_NEUTRAL: return self::MOOD_IMG_NEUTRAL;
			case self::MOOD_ID_BAD: return self::MOOD_IMG_BAD;
		}
		
		return '';
	}
	
	/**
	 * @return array
	 */
	public static function getAvailableMoods()
	{
		return array(self::MOOD_ID_BAD, self::MOOD_ID_NEUTRAL, self::MOOD_ID_GOOD);
	}
}
