<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodBarometerPluginConfigGUI
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 *
 * @ilCtrl_Calls ilMoodBarometerConfigGUI: ilPropertyFormGUI
 * @ilCtrl_Calls ilMoodBarometerConfigGUI: ilMoodBarometerMoodRecordsTableGUI
 * @ilCtrl_Calls ilMoodBarometerConfigGUI: ilMoodBarometerMoodMeansTableGUI
 */
class ilMoodBarometerConfigGUI extends ilPluginConfigGUI
{
	const TAB_ID_CONFIG = 'config_tab';
	const TAB_ID_REPORT = 'report_tab';
	const SUB_TAB_REPORT_DETAILED_ID = 'report_detailed_subtab';
	const SUB_TAB_REPORT_AGGREGATED_ID = 'report_aggregated_subtab';
	
	const IL_PLUGIN_DEFAULT_CMD = 'configure';
	const CMD_SHOW_CONFIG_FORM = 'showConfigForm';
	const CMD_RETURN_TO_CONFIG_FORM = 'returnToConfigForm';
	const CMD_SAVE_CONFIG_FORM = 'saveConfigForm';
	const CMD_ROLE_AUTOCOMPLETE = 'roleAutoComplete';
	
	/**
	 * @var ilMoodBarometerPlugin
	 */
	public $plugin_object;
	
	public function handleTabs($tabId)
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$DIC->tabs()->addTab(self::TAB_ID_CONFIG, $this->plugin_object->txt(self::TAB_ID_CONFIG),
			$DIC->ctrl()->getLinkTarget($this, self::CMD_SHOW_CONFIG_FORM)
		);
		
		$DIC->tabs()->addTab(self::TAB_ID_REPORT, $this->plugin_object->txt(self::TAB_ID_REPORT),
			$DIC->ctrl()->getLinkTargetByClass(
				'ilMoodBarometerMoodRecordsTableGUI',
				ilMoodBarometerAbstractTableGUI::CMD_SHOW_TABLE
			)
		);
		
		$DIC->tabs()->activateTab($tabId);
	}
	
	protected function handleReportSubTabs($subTabId)
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$DIC->tabs()->addSubTab(self::SUB_TAB_REPORT_DETAILED_ID,
			$this->plugin_object->txt(self::SUB_TAB_REPORT_DETAILED_ID),
			$DIC->ctrl()->getLinkTargetByClass(
				'ilMoodBarometerMoodRecordsTableGUI',
				ilMoodBarometerAbstractTableGUI::CMD_SHOW_TABLE
			)
		);
		
		$DIC->tabs()->addSubTab(self::SUB_TAB_REPORT_AGGREGATED_ID,
			$this->plugin_object->txt(self::SUB_TAB_REPORT_AGGREGATED_ID),
			$DIC->ctrl()->getLinkTargetByClass(
				'ilMoodBarometerMoodMeansTableGUI',
				ilMoodBarometerAbstractTableGUI::CMD_SHOW_TABLE
			)
		);
		
		$DIC->tabs()->activateSubTab($subTabId);
	}
	
	public function executeCommand()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		parent::executeCommand();
		
		switch( $DIC->ctrl()->getNextClass($this) )
		{
			case 'ilpropertyformgui':
				
				$DIC->tabs()->clearTargets();
				
				$DIC->tabs()->setBackTarget($DIC->language()->txt('back'),
					$DIC->ctrl()->getLinkTarget($this, self::CMD_RETURN_TO_CONFIG_FORM)
				);
				
				$DIC->ctrl()->setReturn($this, self::CMD_RETURN_TO_CONFIG_FORM);
				
				$form = $this->buildConfigForm();
				$DIC->ctrl()->forwardCommand($form);
				
				break;
				
			case 'ilmoodbarometermoodrecordstablegui':
				
				$this->handleTabs(self::TAB_ID_REPORT);
				$this->handleReportSubTabs(self::SUB_TAB_REPORT_DETAILED_ID);
				
				$gui = ilMoodBarometerMoodRecordsTableGUI::getInstance($this->plugin_object);
				
				$DIC->ctrl()->forwardCommand($gui);
				
				break;
				
			case 'ilmoodbarometermoodmeanstablegui':
				
				$this->handleTabs(self::TAB_ID_REPORT);
				$this->handleReportSubTabs(self::SUB_TAB_REPORT_AGGREGATED_ID);
				
				$gui = ilMoodBarometerMoodMeansTableGUI::getInstance($this->plugin_object);
				
				$DIC->ctrl()->forwardCommand($gui);
				
				break;
		}
	}
	
	public function performCommand($cmd)
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		if( $DIC->ctrl()->getNextClass($this) == 'ilpropertyformgui' )
		{
			return;
		}
		
		switch($cmd)
		{
			case self::CMD_ROLE_AUTOCOMPLETE:
				
				$this->roleAutoComplete();
				break;
				
			case self::CMD_SAVE_CONFIG_FORM:
				
				$this->handleTabs(self::TAB_ID_CONFIG);
				$this->saveConfigForm();
				break;
			
			case self::CMD_RETURN_TO_CONFIG_FORM:

				$this->handleTabs(self::TAB_ID_CONFIG);
				$this->returnToConfigForm();
				break;
			
			case self::CMD_SHOW_CONFIG_FORM:
			case self::IL_PLUGIN_DEFAULT_CMD:

				$this->handleTabs(self::TAB_ID_CONFIG);
				$this->showConfigForm();
				break;
		}
	}
	
	protected function roleAutoComplete()
	{
		$q = $_REQUEST["term"];
		$list = ilMoodBarometerRoleInputGUI::getRoleList($q, true);
		echo json_encode($list);
		exit;
	}
	
	/**
	 * @param ilPropertyFormGUI $form
	 */
	protected function initConfigForm(ilPropertyFormGUI $form)
	{
		$selFreezeInp = $form->getItemByPostVar('selection_freeze');
		$selFreezeInp->setChecked($this->plugin_object->getConfig()->isSelectionFreezeEnabled());
		$selFreezeInp->writeToSession();
		
		$privacyCatInp = $form->getItemByPostVar('privacy_category');
		$privacyCatInp->setValue($this->plugin_object->getConfig()->getPrivacyCategoryRefId());
		$privacyCatInp->writeToSession();
		
		$supSrvInp = $form->getItemByPostVar('superiors_survey');
		$supSrvInp->setValue($this->plugin_object->getConfig()->getSuperiorsSurveyRefId());
		$supSrvInp->writeToSession();
		
		$supRoleInp = $form->getItemByPostVar('superiors_role');
		$supRoleInp->setValue($this->plugin_object->getConfig()->getSuperiorsRoleName());
		$supRoleInp->writeToSession();

		$departRolesInp = $form->getItemByPostVar('department_roles');
		$departRolesInp->setValue($this->plugin_object->getConfig()->getDepartmentRoleIds());
		$departRolesInp->writeToSession();
		
		$value = $this->plugin_object->getConfig()->getMinimumRecordAggregation();
		if(!$value) $value = '';
		$supRoleInp = $form->getItemByPostVar('min_aggregation');
		$supRoleInp->setValue($value);
		$supRoleInp->writeToSession();
	}
	
	/**
	 * @return ilPropertyFormGUI
	 */
	protected function buildConfigForm()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$form = new ilPropertyFormGUI();
		
		$form->setFormAction($DIC->ctrl()->getFormAction($this, self::CMD_SHOW_CONFIG_FORM));
		$form->addCommandButton(self::CMD_SAVE_CONFIG_FORM, $DIC->language()->txt('save'));
		
		$form->setTitle($this->plugin_object->txt(self::TAB_ID_CONFIG));
		
		$selFreezeInp = new ilCheckboxInputGUI(
			$this->plugin_object->txt('selection_freeze_input'), 'selection_freeze'
		);
		$selFreezeInp->setInfo($this->plugin_object->txt('selection_freeze_input_info'));
		$selFreezeInp->setParent($form);
		$selFreezeInp->readFromSession();
		$form->addItem($selFreezeInp);
		
		$privacyCatInp = new ilMoodBarometerRepoSelectorInputGUI(
			$this->plugin_object->txt('privacy_category_input'), 'privacy_category'
		);
		$privacyCatInp->setInfo($this->plugin_object->txt('privacy_category_input_info'));
		$privacyCatInp->setHeaderMessage($this->plugin_object->txt('privacy_category_select_advice'));
		$privacyCatInp->setContainerTypes(array('cat'));
		$privacyCatInp->setClickableTypes(array('cat'));
		$privacyCatInp->setParent($form);
		$privacyCatInp->readFromSession();
		$form->addItem($privacyCatInp);
		
		$supSrvInp = new ilMoodBarometerRepoSelectorInputGUI(
			$this->plugin_object->txt('superiors_survey_input'), 'superiors_survey'
		);
		$supSrvInp->setInfo($this->plugin_object->txt('superiors_survey_input_info'));
		$supSrvInp->setHeaderMessage($this->plugin_object->txt('superiors_survey_select_advice'));
		$supSrvInp->setClickableTypes(array('svy'));
		$supSrvInp->setParent($form);
		$supSrvInp->readFromSession();
		$form->addItem($supSrvInp);
		
		$supRoleInp = new ilMoodBarometerRoleInputGUI(
			$this->plugin_object->txt('superiors_role_input'), 'superiors_role',
			$this, 'roleAutoComplete', $this->plugin_object
		);
		$supRoleInp->setInfo($this->plugin_object->txt('superiors_role_input_info'));
		$supRoleInp->setRequired(true);
		$supRoleInp->setParent($form);
		$supRoleInp->readFromSession();
		$form->addItem($supRoleInp);
		
		$departRolesInp = new ilMultiSelectInputGUI(
			$this->plugin_object->txt('department_roles_input'), 'department_roles'
		);
		$departRolesInp->setInfo($this->plugin_object->txt('department_roles_input_info'));
		$departRolesInp->setOptions($this->plugin_object->getGlobalRolesSelectOptions());
		$departRolesInp->setWidth(400);
		$departRolesInp->setHeight(200);
		$departRolesInp->setParent($form);
		$departRolesInp->readFromSession();
		$form->addItem($departRolesInp);

		$minAggrInp = new ilTextInputGUI(
			$this->plugin_object->txt('min_aggregation_input'), 'min_aggregation'
		);
		$minAggrInp->setInfo($this->plugin_object->txt('min_aggregation_input_info'));
		$minAggrInp->setParent($form);
		$minAggrInp->readFromSession();
		$form->addItem($minAggrInp);
		
		return $form;
	}
	
	protected function returnToConfigForm($form = null)
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		if($form === null)
		{
			$form = $this->buildConfigForm();
		}
		
		$DIC->ui()->mainTemplate()->setContent($form->getHTML());
	}
	
	protected function showConfigForm($form = null)
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		if($form === null)
		{
			$form = $this->buildConfigForm();
			$this->initConfigForm($form);
		}
		
		$DIC->ui()->mainTemplate()->setContent($form->getHTML());
	}
	
	protected function saveConfigForm()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$form = $this->buildConfigForm();
		
		$form->setValuesByPost();
		
		if( !$form->checkInput() )
		{
			return $this->showConfigForm($form);
		}
		
		$this->plugin_object->getConfig()->setSelectionFreezeEnabled(
			$form->getItemByPostVar('selection_freeze')->getChecked()
		);
		
		$this->plugin_object->getConfig()->setPrivacyCategoryRefId(
			$form->getItemByPostVar('privacy_category')->getValue()
		);
		
		$this->plugin_object->getConfig()->setSuperiorsSurveyRefId(
			$form->getItemByPostVar('superiors_survey')->getValue()
		);
		
		$this->plugin_object->getConfig()->setSuperiorsRoleId(
			$form->getItemByPostVar('superiors_role')->getSelectedRoleId()
		);
		
		$this->plugin_object->getConfig()->setDepartmentRoleIds(
			$form->getItemByPostVar('department_roles')->getValue()
		);
		
		$this->plugin_object->getConfig()->setMinimumRecordAggregation(
			$form->getItemByPostVar('min_aggregation')->getValue()
		);
		
		$form->getItemByPostVar('privacy_category')->clearFromSession();
		$form->getItemByPostVar('superiors_survey')->clearFromSession();
		$form->getItemByPostVar('superiors_role')->clearFromSession();
		$form->getItemByPostVar('department_roles')->clearFromSession();
		$form->getItemByPostVar('min_aggregation')->clearFromSession();
		
		
		ilUtil::sendSuccess($this->plugin_object->txt('config_modified'), true);
		$DIC->ctrl()->redirect($this, self::CMD_SHOW_CONFIG_FORM);
	}
}
