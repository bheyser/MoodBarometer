<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodMeanList
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
class ilMoodMeanList implements  Iterator, ilMoodBarometerTableDataProvider
{
	use ilMoodBarometerItemListTrait;
	use ilMoodBarometerMoodFilterTrait;
	
	/**
	 * @var int
	 */
	protected $minimumGroupRecords;
	
	/**
	 * @return int
	 */
	public function getMinimumGroupRecords()
	{
		return $this->minimumGroupRecords;
	}
	
	/**
	 * @param int $minimumGroupRecords
	 */
	public function setMinimumGroupRecords($minimumGroupRecords)
	{
		$this->minimumGroupRecords = $minimumGroupRecords;
	}
	
	protected function getHavingExpression()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		if( $this->getMinimumGroupRecords() )
		{
			return 'HAVING COUNT(mood) >= '.$DIC->database()->quote($this->getMinimumGroupRecords(), 'integer');
		}
		
		return '';
	}
	
	public function load()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$res = $DIC->database()->query("
			SELECT department_role, AVG(mood) mood_mean, COUNT(mood) mood_count
			FROM moodbar_mood_records
			WHERE {$this->getWhereExpression()}
			GROUP BY department_role
			{$this->getHavingExpression()}
		");
		
		while($row = $DIC->database()->fetchAssoc($res))
		{
			$moodRec = new ilMoodMean(
				$row['department_role'], $row['mood_mean'], $row['mood_count']
			);
			
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
		
		foreach($this as $moodMean)
		{
			/* @var ilMoodMean $moodMean */
			
			$data[] = array(
				'role_name' => $objCache->lookupTitle($moodMean->getDepartmentRoleId()),
				'mood_mean' => $moodMean->getMoodMean(),
				'user_count' => $moodMean->getMoodCount()
			);
		}
		
		return $data;
	}
}
