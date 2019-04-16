<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodBarometerAbstractTableGUI
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Modules/Test
 */
abstract class ilMoodBarometerAbstractTableGUI extends ilTable2GUI
{
	const CMD_SHOW_TABLE = 'showTable';
	const CMD_APPLY_FILTER = 'applyFilter';
	const CMD_RESET_FILTER = 'resetFilter';
	
	/**
	 * @var ilMoodBarometerPlugin
	 */
	protected $plugin;
	
	public function executeCommand()
	{
		switch( $this->ctrl->getNextClass($this) )
		{
			case strtolower(__CLASS__):
			case '':
				
				$cmd = $this->ctrl->getCmd().'Cmd';
				return $this->$cmd();
			
			default:
				
				$this->ctrl->setReturn($this, self::CMD_SHOW_TABLE);
				return parent::executeCommand();
		}
	}
	
	protected function showTableCmd()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$recordList = $this->buildRecordList();
		
		$this->setData($recordList->getTableDataArray());
		
		$DIC->ui()->mainTemplate()->setContent($this->getHTML());
	}
	
	protected function applyFilterCmd()
	{
		$this->writeFilterToSession();
		$this->showTableCmd();
	}
	
	protected function resetFilterCmd()
	{
		$this->resetFilter();
		$this->initFilter();
		$this->showTableCmd();
	}
	
	/**
	 * @param ilMoodBarometerPlugin $plugin
	 * @return ilMoodBarometerMoodRecordsTableGUI
	 */
	public static function getInstance(ilMoodBarometerPlugin $plugin)
	{
		$table = new static($plugin);
		
		$table->initColumns();
		$table->initFilter();
		
		return $table;
	}
	
	protected function getYearsSelectOptions()
	{
		$years = array(
			'0' => $this->plugin->txt('filter_all_option')
		);
		
		foreach(ilMoodRecordList::getAllYears() as $year)
		{
			$years[$year] = $year;
		};
		
		return $years;
	}
	
	protected function getWeeksSelectOptions()
	{
		$weeks = array(
			'0' => $this->plugin->txt('filter_all_option')
		);
		
		foreach(ilMoodRecordList::getAllWeeks() as $week)
		{
			$weeks[$week] = $week;
		}
		
		return $weeks;
	}
	
	protected function getDepartmentRoleSelectOptions()
	{
		$roles = array(
			'0' => $this->plugin->txt('filter_all_option'),
			'-1' => $this->plugin->txt('no_department_role')
		);
		
		foreach($this->plugin->getConfig()->getDepartmentRoleIds() as $roleId)
		{
			$roles[$roleId] = $this->plugin->getRoleName($roleId);
		}
		
		return $roles;
	}
	
	public function initFilter()
	{
		$this->filters = array();
		$this->filter = array();
		
		$year = new ilSelectInputGUI($this->plugin->txt('mood_recs_head_year'), 'year');
		$year->setOptions($this->getYearsSelectOptions());
		$this->addFilterItem($year);
		$year->readFromSession();
		$this->filter['year'] = $year->getValue();
		
		$week = new ilSelectInputGUI($this->plugin->txt('mood_recs_head_week'), 'week');
		$week->setOptions($this->getWeeksSelectOptions());
		$this->addFilterItem($week);
		$week->readFromSession();
		$this->filter['week'] = $week->getValue();
		
		$departmentRole = new ilSelectInputGUI(
			$this->plugin->txt('department_role_filter'), 'department_role'
		);
		$departmentRole->setOptions($this->getDepartmentRoleSelectOptions());
		$this->addFilterItem($departmentRole);
		$departmentRole->readFromSession();
		$this->filter['department_role'] = $departmentRole->getValue();
		
		$superiorsOnly = new ilCheckboxInputGUI(
			$this->plugin->txt('superiors_only_filter'), 'superiors_only'
		);
		$this->addFilterItem($superiorsOnly);
		$superiorsOnly->readFromSession();
		$this->filter['superiors_only'] = $superiorsOnly->getChecked();
	}
	
	protected function buildDepartmentString($roleName)
	{
		if( $roleName == '' )
		{
			return $this->plugin->txt('no_department_role');
		}
		
		return $roleName;
	}
	
	abstract protected function initColumns();
	
	/**
	 * @return ilMoodBarometerTableDataProvider
	 */
	abstract protected function buildRecordList();
}
