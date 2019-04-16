<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Interface ilMoodBarometerTableDataProvider
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
interface ilMoodBarometerTableDataProvider
{
	/**
	 * @return array
	 */
	public function getTableDataArray();
}