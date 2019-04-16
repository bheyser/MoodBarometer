<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodBarometerMoodTableGUI
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
class ilMoodBarometerMoodRecordsTableGUI extends ilMoodBarometerAbstractTableGUI
{
	/**
	 * @return ilMoodRecordList
	 */
	protected function buildRecordList()
	{
		$list = ilMoodRepository::getFilteredMoodList(
			$this->getFilterItemByPostVar('year')->getValue(),
			$this->getFilterItemByPostVar('week')->getValue(),
			$this->getFilterItemByPostVar('superiors_only')->getChecked(),
			$this->getFilterItemByPostVar('department_role')->getValue()
		);
		
		return $list;
	}
	
	/**
	 * ilMoodBarometerMoodRecordsTableGUI constructor.
	 * @param ilMoodBarometerPlugin $plugin
	 */
	public function __construct(ilMoodBarometerPlugin $plugin)
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$this->plugin = $plugin;
		
		$this->setId('mood_records_tbl');
		$this->setPrefix('mood_records_tbl');
		
		$this->setTitle($plugin->txt('report_detailed_subtab'));
		
		$this->setFormAction($DIC->ctrl()->getFormAction($this, self::CMD_SHOW_TABLE));
		
		parent::__construct($this, self::CMD_SHOW_TABLE);
		$this->setFilterCommand(self::CMD_APPLY_FILTER);
		$this->setResetCommand(self::CMD_RESET_FILTER);
		
		$this->setRowTemplate(
			$this->plugin->getDirectory().'/templates/tpl.mood_recs_table_row.html'
		);
		
		$this->setExportFormats(array(self::EXPORT_CSV));
	}
	
	public function numericOrdering($field)
	{
		switch($field)
		{
			case 'year':
			case 'week':
			case 'mood':
				
				return true;
		}
		
		return false;
	}
	
	public function initColumns()
	{
		$this->addColumn($this->plugin->txt('mood_recs_head_year'), 'year');
		$this->addColumn($this->plugin->txt('mood_recs_head_week'), 'week');
		$this->addColumn($this->plugin->txt('mood_recs_head_department'), 'department');
		$this->addColumn($this->plugin->txt('mood_recs_head_user'), 'user');
		$this->addColumn($this->plugin->txt('mood_recs_head_mood'), 'mood');
	}
	
	public function fillRow($data)
	{
		$this->tpl->setVariable('YEAR', $data['year']);
		$this->tpl->setVariable('WEEK', $data['week']);
		$this->tpl->setVariable('DEPARTMENT', $this->buildDepartmentString($data['role_name']));
		$this->tpl->setVariable('USER', $data['user']);
		$this->tpl->setVariable('MOOD', $this->getMoodIconPath($data));
	}
	
	/**
	 * @param $data
	 * @return string
	 */
	protected function getMoodIconPath($data)
	{
		return $this->plugin->getImagePath(
			ilMoodRecord::getImageFilename($data['mood'])
		);
	}
}
