<?php
if (!defined('DC')) die("!defined('DC')");

$tpl['data'] = get_node_config();

$script_name = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
		SELECT `script_name`
		FROM `".DB_PREFIX."main_lock`
		", 'fetch_one');
if ($script_name == 'my_lock')
	$tpl['my_status'] = 'OFF';
else
	$tpl['my_status'] = 'ON';

if (!get_community_users($db))
	$tpl['my_mode'] = 'Single';
else
	$tpl['my_mode'] = 'Pool';

$tpl['config_ini'] = file_get_contents( ABSPATH . 'config.ini' );

require_once( ABSPATH . 'templates/node_config.tpl' );

?>