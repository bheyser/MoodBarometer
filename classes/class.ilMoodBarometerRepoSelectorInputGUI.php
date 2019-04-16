<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodBarometerRepoSelectorInputGUI
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 *
 * @ilCtrl_IsCalledBy ilMoodBarometerRepoSelectorInputGUI: ilFormPropertyDispatchGUI
 */
class ilMoodBarometerRepoSelectorInputGUI extends ilRepositorySelectorInputGUI
{
	public function setContainerTypes($containerTypes)
	{
		$this->container_types = $containerTypes;
	}
	
	/**
	 * Render item
	 */
	function render($a_mode = "property_form")
	{
		$lng = $this->lng;
		$ilCtrl = $this->ctrl;
		$ilObjDataCache = $this->obj_data_cache;
		$tree = $this->tree;
		
		$tpl = new ilTemplate("tpl.prop_rep_select.html", true, true, "Services/Form");
		
		$tpl->setVariable("POST_VAR", $this->getPostVar());
		$tpl->setVariable("ID", $this->getFieldId());
		$tpl->setVariable("PROPERTY_VALUE", ilUtil::prepareFormOutput($this->getValue()));
		$tpl->setVariable("TXT_SELECT", $this->getSelectText());
		$tpl->setVariable("TXT_RESET", $lng->txt("reset"));
		switch ($a_mode)
		{
			case "property_form":
				$parent_gui = "ilpropertyformgui";
				break;
			
			case "table_filter":
				$parent_gui = get_class($this->getParent());
				break;
		}
		
		$ilCtrl->setParameterByClass("ilmoodbarometerreposelectorinputgui",
			"postvar", $this->getPostVar());
		$tpl->setVariable("HREF_SELECT",
			$ilCtrl->getLinkTargetByClass(array($parent_gui, "ilformpropertydispatchgui", "ilmoodbarometerreposelectorinputgui"),
				"showRepositorySelection"));
		$tpl->setVariable("HREF_RESET",
			$ilCtrl->getLinkTargetByClass(array($parent_gui, "ilformpropertydispatchgui", "ilmoodbarometerreposelectorinputgui"),
				"reset"));
		
		if ($this->getValue() > 0 && $this->getValue() != ROOT_FOLDER_ID)
		{
			$tpl->setVariable("TXT_ITEM",
				$ilObjDataCache->lookupTitle($ilObjDataCache->lookupObjId($this->getValue())));
		}
		else
		{
			$nd = $tree->getNodeData(ROOT_FOLDER_ID);
			$title = $nd["title"];
			if ($title == "ILIAS")
			{
				$title = $lng->txt("repository");
			}
			if (in_array($nd["type"], $this->getClickableTypes()))
			{
				$tpl->setVariable("TXT_ITEM", $title);
			}
		}
		return $tpl->get();
	}
}
