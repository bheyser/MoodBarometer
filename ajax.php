<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

chdir('../../../../../../../');
include_once 'include/inc.header.php';

/* @var ilMoodBarometerPlugin $plugin */
$plugin = ilPluginAdmin::getPluginObject(
	IL_COMP_SERVICE, 'UIComponent', 'uihk', 'MoodBarometer'
);

$plugin->handleAjaxRequest();
