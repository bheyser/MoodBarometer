<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodRecordList
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
class ilMoodRepository
{
	/**
	 * @param array $userIds
	 * @param int $year
	 * @param int $week
	 * @return array
	 */
	public static function fetchUserIdsMissingMoodRecord($userIds, $year, $week)
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$IN_userIds = $DIC->database()->in('users.usr_id', $userIds, false, 'integer');
		
		$query = "
			
			SELECT users.usr_id FROM usr_data users
			
			LEFT JOIN moodbar_mood_records moods
			ON moods.usr_id = users.usr_id
			AND moods.year = %s
			AND moods.week = %s
			
			WHERE $IN_userIds
			AND moods.usr_id IS NULL
			
		";
		
		$res = $DIC->database()->queryF($query, array('integer', 'integer'), array($year, $week));
		
		$userIds = array();
		
		while($row = $DIC->database()->fetchAssoc($res))
		{
			$userIds[] = $row['usr_id'];
		}
		
		return $userIds;
	}

	/**
	 * @param int $userId
	 * @return ilMoodRecord
	 */
	public static function getCurrentMoodByUserId($userId)
	{
		$ts = time();
		
		$year = date('Y', $ts);
		$week = date('W', $ts);
		
		$moodRec = new ilMoodRecord($year, $week, $userId);
		$moodRec->load();
		
		return $moodRec;
	}
	
	/**
	 * @param int $userId
	 * @return ilMoodRecord
	 */
	public static function saveNewMoodByUserId($userId, $moodId, $isSuperior, $departmentRoleId)
	{
		$ts = time();
		
		$year = date('Y', $ts);
		$week = date('W', $ts);
		
		$moodRec = new ilMoodRecord($year, $week, $userId);
		$moodRec->setMoodId($moodId);
		$moodRec->setSuperior($isSuperior);
		$moodRec->setDepartmentRoleId($departmentRoleId);
		$moodRec->save();
		
		return $moodRec;
	}
	
	/**
	 * @param $yearFilter
	 * @param $weekFilter
	 * @param $superiorsFilter
	 * @param $departmentRoleFilter
	 * @return ilMoodRecordList
	 */
	public static function getFilteredMoodList($yearFilter, $weekFilter, $superiorsFilter, $departmentRoleFilter)
	{
		$list = new ilMoodRecordList();
		
		$list->setYearFilter($yearFilter);
		$list->setWeekFilter($weekFilter);
		$list->setSuperiorsOnly($superiorsFilter);
		$list->setDepartmentRoleFilter($departmentRoleFilter);
		
		$list->load();
		
		return $list;
	}
	
	/**
	 * @param $yearFilter
	 * @param $weekFilter
	 * @param $superiorsFilter
	 * @param $departmentRoleFilter
	 * @return ilMoodMeanList
	 */
	public static function getFilteredMoodMeansByDepartmentList($minRecords, $yearFilter, $weekFilter, $superiorsFilter, $departmentRoleFilter)
	{
		$list = new ilMoodMeanList();
		
		$list->setMinimumGroupRecords($minRecords);
		
		$list->setYearFilter($yearFilter);
		$list->setWeekFilter($weekFilter);
		$list->setSuperiorsOnly($superiorsFilter);
		$list->setDepartmentRoleFilter($departmentRoleFilter);
		
		$list->load();
		
		return $list;
	}
}
