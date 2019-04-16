<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodBarometerMoodMeansTableGUI
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
class ilMoodBarometerMoodMeansTableGUI extends ilMoodBarometerAbstractTableGUI
{
	/**
	 * @return ilMoodRecordList
	 */
	protected function buildRecordList()
	{
		$list = ilMoodRepository::getFilteredMoodMeansByDepartmentList(
			$this->plugin->getConfig()->getMinimumRecordAggregation(),
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
		
		$this->setId('mood_means_tbl');
		$this->setPrefix('mood_means_tbl');
		
		$this->setTitle($plugin->txt('report_aggregated_subtab'));
		
		$this->setFormAction($DIC->ctrl()->getFormAction($this, self::CMD_SHOW_TABLE));
		
		parent::__construct($this, self::CMD_SHOW_TABLE);
		$this->setFilterCommand(self::CMD_APPLY_FILTER);
		$this->setResetCommand(self::CMD_RESET_FILTER);
		
		$this->setRowTemplate(
			$this->plugin->getDirectory().'/templates/tpl.mood_means_table_row.html'
		);
		
		$this->setExportFormats(array(self::EXPORT_CSV));
	}
	
	public function numericOrdering($field)
	{
		switch($field)
		{
			case 'mood':
			case 'count':
				
				return true;
		}
		
		return false;
	}
	
	public function initColumns()
	{
		$this->addColumn($this->plugin->txt('mood_recs_head_department'), 'department');
		$this->addColumn($this->plugin->txt('mood_recs_head_mood'), 'mood');
		$this->addColumn($this->plugin->txt('mood_recs_head_count'), 'count');
	}
	
	public function fillRow($data)
	{
		$department = $this->buildDepartmentString($data['role_name']);
		
		$this->tpl->setVariable('DEPARTMENT', $department);
		$this->tpl->setVariable('MOOD_MEAN', $this->formatMoodMean($data['mood_mean']));
		$this->tpl->setVariable('USER_COUNT', $data['user_count']);
	}
	
	protected function formatMoodMean($moodMean)
	{
		return sprintf('%.2f', $moodMean);
	}
}
