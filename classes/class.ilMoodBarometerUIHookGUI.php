<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodBarometerUIHookGUI
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
class ilMoodBarometerUIHookGUI extends ilUIHookPluginGUI
{
	/**
	 * @var ilMoodBarometerPlugin
	 */
	protected $plugin_object = null;
	
	public function getHTML($a_comp, $a_part, $a_par = array())
	{
		if( $this->isMainMenuTemplateHook($a_comp, $a_part, $a_par) )
		{
			return $this->modifyMainMenuTemplate($a_par['html']);
		}

		return array("mode" => ilUIHookPluginGUI::KEEP, "html" => "");
	}
	
	private function isMainMenuTemplateHook($a_comp, $a_part, $a_par)
	{
		if( $a_comp != '' )
		{
			return false;
		}
		
		if( $a_part != 'template_get' )
		{
			return false;
		}
		
		if( !isset($a_par['tpl_id']) )
		{
			return false;
		}
		
		if( $a_par['tpl_id'] != 'Services/MainMenu/tpl.main_menu.html' )
		{
			return false;
		}
		
		return true;
	}
	
	private function modifyMainMenuTemplate($html)
	{
		$matches = null;
		
		if( preg_match('/<nav id="ilTopNav".*?>/is', $html, $matches) )
		{
			$html = str_replace(
				$matches[0], $matches[0].$this->getMoodBarometerHtml(), $html
			);
		}
		
		return array('mode' => ilUIHookPluginGUI::REPLACE, 'html' => $html);
	}
	
	private function getMoodBarometerHtml()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$moodRec = ilMoodRepository::getCurrentMoodByUserId($DIC->user()->getId());
		
		$moodBarometerGUI = new ilMoodBarometerInputGUI($this->plugin_object, $moodRec);
		
		if( $this->plugin_object->getConfig()->getPrivacyCategoryRefId() )
		{
			$moodBarometerGUI->setPrivacyLink($this->plugin_object->getRepositoryLink(
				$this->plugin_object->getConfig()->getPrivacyCategoryRefId()
			));
		}
		
		if( $this->plugin_object->getConfig()->getSuperiorsSurveyRefId()
			&& $this->plugin_object->isSuperior($DIC->user()->getId()) )
		{
			$moodBarometerGUI->setSurveyLink($this->plugin_object->getRepositoryLink(
				$this->plugin_object->getConfig()->getSuperiorsSurveyRefId()
			));
		}
		
		return $moodBarometerGUI->getHTML();
	}
}
