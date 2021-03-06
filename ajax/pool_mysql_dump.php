<?php
session_start();

if ( empty($_SESSION['user_id']) )
	die('!user_id');

define( 'DC', TRUE);

define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );

set_time_limit(0);

//require_once( ABSPATH . 'includes/errors.php' );
require_once( ABSPATH . 'db_config.php' );
require_once( ABSPATH . 'includes/autoload.php' );

$db = new MySQLidb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

$gzip = false;

if (!node_admin_access($db))
	die ('Permission denied');

$tables_array = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
		SHOW TABLES
		", 'array');

$tables_cmd = '';
$dump_user_id = intval($_REQUEST['dump_user_id']);
if ($dump_user_id) {
	foreach ($my_tables as $table)
		$tables_cmd .= "{$dump_user_id}_{$table} ";
}
else {
	$community_users = get_community_users($db);
	for ($i=0; $i<sizeof($community_users); $i++) {
		foreach ($my_tables as $table) {
			if (in_array("{$community_users[$i]}_{$table}", $tables_array))
				$tables_cmd .= "{$community_users[$i]}_{$table} ";
		}
	}
}

if ($gzip) {
	$filename = "backup-" . date("d-m-Y-H-i-s") . ".sql.gz";
	header( "Content-Type: application/x-gzip" );
	$add_cmd = ' | gzip --best"';
}
else{
	$filename = "backup-" . date("d-m-Y-H-i-s") . ".sql";
	header( "Content-Type: text/plain" );
	$add_cmd='';
}
header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

$cmd = "mysqldump  -u ".DB_USER." --password=".DB_PASSWORD."  -h".DB_HOST." --default-character-set=binary  --databases ".DB_NAME." --tables {$tables_cmd} --lock-tables=false --skip-add-locks {$add_cmd}";
passthru( $cmd, $data );

exit(0);

?>