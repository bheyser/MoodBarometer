<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodBarometerInputGUI
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
class ilMoodBarometerInputGUI
{
	const MOOD_BAROMETER_ID = 'mood_barometer';
	
	const MOOD_BAROMETER_AJAX_PARAM = 'mood';
	
	/**
	 * @var ilMoodBarometerPlugin
	 */
	protected $plugin;
	
	/**
	 * @var ilMoodRecord
	 */
	protected $moodRecord;
	
	/**
	 * @var string
	 */
	protected $privacyLink;
	
	/**
	 * @var string
	 */
	protected $surveyLink;
	
	public function __construct(ilMoodBarometerPlugin $plugin, ilMoodRecord $moodRecord)
	{
		$this->plugin = $plugin;
		$this->moodRecord = $moodRecord;
		$this->privacyLink = '';
		$this->surveyLink = '';
	}
	
	/**
	 * @return string
	 */
	public function getPrivacyLink()
	{
		return $this->privacyLink;
	}
	
	/**
	 * @param string $privacyLink
	 */
	public function setPrivacyLink($privacyLink)
	{
		$this->privacyLink = $privacyLink;
	}
	
	/**
	 * @return string
	 */
	public function getSurveyLink()
	{
		return $this->surveyLink;
	}
	
	/**
	 * @param string $surveyLink
	 */
	public function setSurveyLink($surveyLink)
	{
		$this->surveyLink = $surveyLink;
	}
	
	public function getHTML()
	{
		$this->addRessourcesToMainTemplate();
		
		$tpl = $this->plugin->getTemplate('tpl.mood_barometer_input.html');
		
		if( $this->getPrivacyLink() )
		{
			$tpl->setCurrentBlock('link');
			$tpl->setVariable('LINK_HREF', $this->getPrivacyLink());
			$tpl->setVariable('LINK_TXT', $this->plugin->txt('privacy_link'));
			$tpl->parseCurrentBlock();
		}
		
		if( $this->getSurveyLink() )
		{
			$tpl->setCurrentBlock('link');
			$tpl->setVariable('LINK_HREF', $this->getSurveyLink());
			$tpl->setVariable('LINK_TXT', $this->plugin->txt('survey_link'));
			$tpl->parseCurrentBlock();
		}
		
		foreach(ilMoodRecord::getAvailableMoods() as $mood)
		{
			$tpl->setCurrentBlock('mood');
			
			$tpl->setVariable('MOOD_ID', $mood);
			
			if( $this->moodRecord->hasMood() && $this->moodRecord->getMoodId() != $mood )
			{
				$tpl->setVariable('MOOD_DISABLED', 'disabled');
			}
			
			$tpl->setVariable('MOOD_IMG_SRC', $this->plugin->getImagePath(
				ilMoodRecord::getImageFilename($mood)
			));
			
			$tpl->parseCurrentBlock();
		}
		
		$tpl->setVariable('MOOD_BAROMETER_ID', self::MOOD_BAROMETER_ID);
		
		$tpl->setVariable('SELECTED_MOOD_ID', $this->moodRecord->getMoodId());
		$tpl->setVariable('CURRENT_YEAR', $this->moodRecord->getYear());
		$tpl->setVariable('CURRENT_WEEK', $this->moodRecord->getWeek());
		
		$tpl->setVariable('MOOD_BAROMETER_AJAX_PARAM', self::MOOD_BAROMETER_AJAX_PARAM);
		$tpl->setVariable('MOOD_BAROMETER_AJAX_URL', $this->buildAjaxUrl());
		
		$tpl->setVariable('MOOD_BAROMETER_SELECTION_FREEZE',
			$this->plugin->getConfig()->isSelectionFreezeEnabled() ? 'true' : 'false'
		);
		
		return $tpl->get();
	}
	
	protected function buildAjaxUrl()
	{
		return $this->plugin->getDirectory().'/ajax.php';
	}
	
	protected function addRessourcesToMainTemplate()
	{
		// the following is not working for learning modules
		//global $DIC; /* @var ILIAS\DI\Container $DIC */
		//$DIC->ui()->mainTemplate()->addCss($this->plugin->getStyleSheetLocation('mood_barometer.css'));
		//$DIC->ui()->mainTemplate()->addJavaScript($this->plugin->getStyleSheetLocation('mood_barometer.js'));
		
		// learning module does re-init main template and replace the global only
		// (tpl within ui-mainTemplate is not replaced)
		
		$GLOBALS['tpl']->addCss($this->plugin->getStyleSheetLocation('mood_barometer.css'));
		$GLOBALS['tpl']->addJavaScript($this->plugin->getStyleSheetLocation('mood_barometer.js'));
	}
}
