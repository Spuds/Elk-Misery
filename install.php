<?php

// If we have found SSI.php and we are outside of ElkArte, then we are running standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('ELK'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('ELK')) // If we are outside ElkArte and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as ElkArte\'s SSI.php.');

$db = database();
$dbtbl = db_table();

global $modSettings, $smcFunc, $sourcedir;

// Settings for the addon
$mod_settings = array();

// Settings to create the new tables...
$tables = array();

// Add a row to an existing table
$rows = array();

// Add new columns to a table
$columns = array();
$columns[] = array(
	'table_name' => '{db_prefix}members',
	'if_exists' => 'ignore',
	'error' => 'fatal',
	'parameters' => array(),
	'column_info' => array(
		'name' => 'misery',
		'type' => 'tinyint',
		'size' => 3,
		'null' => false,
		'default' => 0,
	)
);

foreach ($tables as $table)
	$dbtbl->db_create_table($table['table_name'], $table['columns'], $table['indexes'], $table['parameters'], $table['if_exists'], $table['error']);

foreach ($rows as $row)
	$db->insert($row['method'], $row['table_name'], $row['columns'], $row['data'], $row['keys']);

foreach ($columns as $column)
	$dbtbl->db_add_column($column['table_name'], $column['column_info'], $column['parameters'], $column['if_exists'], $column['error']);

// Update the settings if required
foreach ($mod_settings as $new_setting => $new_value)
{
	if (!isset($modSettings[$new_setting]))
		updateSettings(array($new_setting => $new_value));
}

if (ELK == 'SSI')
   echo 'Congratulations! You have successfully installed the Miserable User Addon';