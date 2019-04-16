<#1>
<?php

if($ilDB->tableExists('moodbar_mood_records'))
    $ilDB->dropTable('moodbar_mood_records');

if( !$ilDB->tableExists('moodbar_mood_records') )
{
	$fields = array(
		'year' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => true
		),
		'week' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => true
		),
		'usr_id' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => true
		),
		'mood' => array(
			'type' => 'integer',
			'length' => 1,
			'notnull' => true
		),
		'superior' => array(
			'type' => 'integer',
			'length' => 1,
			'notnull' => true
		),
		'department_role' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => true
		)
	);
	
	$ilDB->createTable('moodbar_mood_records', $fields);
	
	$ilDB->addPrimaryKey('moodbar_mood_records', array(
		'year', 'week', 'usr_id'
	));
}

?>