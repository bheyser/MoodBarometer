<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodRecordList
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
class ilMoodRecordList implements Iterator, ilMoodBarometerTableDataProvider
{
	use ilMoodBarometerItemListTrait;
	use ilMoodBarometerMoodFilterTrait;
	
	public function load()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$res = $DIC->database()->query("
			SELECT * FROM moodbar_mood_records
			WHERE {$this->getWhereExpression()}
		");
		
		while($row = $DIC->database()->fetchAssoc($res))
		{
			$moodRec = new ilMoodRecord(
				$row['year'], $row['week'], $row['usr_id']
			);
			
			$moodRec->setMoodId($row['mood']);
			$moodRec->setSuperior($row['superior']);
			$moodRec->setDepartmentRoleId($row['department_role']);
			
			$this->addItem($moodRec);
		}
	}
	
	/**
	 * @return array
	 */
	public function getTableDataArray()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		$objCache = $DIC['ilObjDataCache']; /* @var ilObjectDataCache $objCache */
		
		$data = array();
		
		foreach($this as $moodRec)
		{
			/* @var ilMoodRecord $moodRec */
			
			$data[] = array(
				'year' => $moodRec->getYear(),
				'week' => $moodRec->getWeek(),
				'role_name' => $objCache->lookupTitle($moodRec->getDepartmentRoleId()),
				'user' => ilObjUser::_lookupFullname($moodRec->getUserId()),
				'mood' => $moodRec->getMoodId()
			);
		}
		
		return $data;
	}
	
	public static function getAllYears()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$res = $DIC->database()->query(
			"SELECT DISTINCT moodbar_mood_records.year FROM moodbar_mood_records"
		);
		
		$years = array();
		
		while($row = $DIC->database()->fetchAssoc($res))
		{
			$years[] = $row['year'];
		}
		
		return $years;
	}
	
	public static function getAllWeeks()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$res = $DIC->database()->query(
			"SELECT DISTINCT moodbar_mood_records.week FROM moodbar_mood_records"
		);
		
		$weeks = array();
		
		while($row = $DIC->database()->fetchAssoc($res))
		{
			$weeks[] = $row['week'];
		}
		
		return $weeks;
	}
}
