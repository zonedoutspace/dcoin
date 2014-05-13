<?php
session_start();

if ( empty($_SESSION['user_id']) )
	die('!user_id');

define( 'DC', TRUE);

define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );

//require_once( ABSPATH . 'includes/errors.php' );
require_once( ABSPATH . 'includes/fns-main.php' );
require_once( ABSPATH . 'db_config.php' );
require_once( ABSPATH . 'includes/class-mysql.php' );

$db = new MySQLidb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

if (empty($_SESSION['restricted']) && !get_community_users($db)) {

	if ( $_REQUEST['type'] == 'mp4' )
		@unlink( ABSPATH . 'public/'.$_SESSION['user_id'].'_user_video.mp4' );

	if ( $_REQUEST['type'] == 'webm_ogg' ) {
		@unlink( ABSPATH . 'public/'.$_SESSION['user_id'].'_user_video.ogv' );
		@unlink( ABSPATH . 'public/'.$_SESSION['user_id'].'_user_video.webm' );
	}
}

?>