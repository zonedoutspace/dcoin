<?php
session_start();

define( 'DC', TRUE);

define( 'ABSPATH', dirname(__FILE__) . '/' );

set_time_limit(0);

require_once( ABSPATH . 'includes/fns-main.php' );

$install_progress = 0;
if (file_exists(ABSPATH . 'db_config.php')) {
	require_once( ABSPATH . 'db_config.php' );
	require_once( ABSPATH . 'includes/class-mysql.php' );
	require_once( ABSPATH . 'includes/class-parsedata.php' );
	$db = new MySQLidb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
	$db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__,'SET NAMES utf8');
	// узнаем, на каком шаге остановились
	$install_progress =  $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, '
			SELECT `progress`
			FROM `'.DB_PREFIX.'install`
			', 'fetch_one');
}



if ( isset($_REQUEST['tpl_name']) && check_input_data($_REQUEST['tpl_name'], 'tpl_name') && ( !empty($_SESSION['user_id']) || preg_match('/install/', $_REQUEST['tpl_name']) ) )
	$tpl_name = $_REQUEST['tpl_name'];
else if ( isset($install_progress) && $install_progress==='complete' )
	$tpl_name = 'login';
else
	$tpl_name = 'install_step_0';


if (@$_REQUEST['parameters']=='lang=ru') {
	$lang = 'ru';
	setlang($lang);
}
else if (@$_REQUEST['parameters']=='lang=en') {
	$lang = 'en';
	setlang($lang);
}

if (!isset($lang)) {
	if (@$_SESSION['lang'])
		$lang = $_SESSION['lang'];
	else if (@$_COOKIE['lang'])
		$lang = $_COOKIE['lang'];
}
if (!isset($lang))
	$lang = 'en';

if (!preg_match('/^[a-z]{2}$/iD', $lang))
	die('lang error');

require_once( ABSPATH . 'lang/'.$lang.'.php' );

$tpl['periods'] = array(86400=>'1 '.$lng['day'], 604800=>'1 '.$lng['week'], 31536000=>'1 '.$lng['year'], 2592000=>'1 '.$lng['month'], 1209600=>'2 '.$lng['weeks']);

if (!empty($_SESSION['user_id']) && $_SESSION['user_id']!='wait' && isset($db) && $install_progress=='complete') {
	$user_id = $_SESSION['user_id'];
	//$user_id =  get_my_user_id($db);
	$my_user_id = $user_id;
	$count_sign = 1;
	$pk =  $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
			SELECT `public_key_1`, `public_key_2`
			FROM `".DB_PREFIX."users`
			WHERE `user_id` = {$user_id}
			", 'fetch_array');
	if ($pk['public_key_1'])
		$count_sign = 2;
	if ($pk['public_key_2'])
		$count_sign = 3;
}
else{
	$user_id = 0;
	$my_user_id=0;
}

$countries =  array('Afghanistan','Albania','Algeria','American Samoa','Andorra','Angola','Anguilla','Antarctica','Antigua and Barbuda','Argentina','Armenia','Aruba','Australia','Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bermuda','Bhutan','Bolivia','Bosnia and Herzegovina','Botswana','Bouvet Island','Brazil','British Indian Ocean Territory','British Virgin Islands','Brunei','Bulgaria','Burkina Faso','Burundi','Cambodia','Cameroon','Canada','Cape Verde','Cayman Islands','Central African Republic','Chad','Chile','China','Christmas Island','Cocos [Keeling] Islands','Colombia','Comoros','Congo [DRC]','Congo [Republic]','Cook Islands','Costa Rica','Croatia','Cuba','Cyprus','Czech Republic','Côte d\'Ivoire','Denmark','Djibouti','Dominica','Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Ethiopia','Falkland Islands [Islas Malvinas]','Faroe Islands','Fiji','Finland','France','French Guiana','French Polynesia','French Southern Territories','Gabon','Gambia','Gaza Strip','Georgia','Germany','Ghana','Gibraltar','Greece','Greenland','Grenada','Guadeloupe','Guam','Guatemala','Guernsey','Guinea','Guinea-Bissau','Guyana','Haiti','Heard Island and McDonald Islands','Honduras','Hong Kong','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland','Isle of Man','Israel','Italy','Jamaica','Japan','Jersey','Jordan','Kazakhstan','Kenya','Kiribati','Kosovo','Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg','Macau','Macedonia [FYROM]','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Martinique','Mauritania','Mauritius','Mayotte','Mexico','Micronesia','Moldova','Monaco','Mongolia','Montenegro','Montserrat','Morocco','Mozambique','Myanmar [Burma]','Namibia','Nauru','Nepal','Netherlands','Netherlands Antilles','New Caledonia','New Zealand','Nicaragua','Niger','Nigeria','Niue','Norfolk Island','North Korea','Northern Mariana Islands','Norway','Oman','Pakistan','Palau','Palestinian Territories','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Pitcairn Islands','Poland','Portugal','Puerto Rico','Qatar','Romania','Russia','Rwanda','Réunion','Saint Helena','Saint Kitts and Nevis','Saint Lucia','Saint Pierre and Miquelon','Saint Vincent and the Grenadines','Samoa','San Marino','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','South Georgia and the South Sandwich Islands','South Korea','Spain','Sri Lanka','Sudan','Suriname','Svalbard and Jan Mayen','Swaziland','Sweden','Switzerland','Syria','São Tomé and Príncipe','Taiwan','Tajikistan','Tanzania','Thailand','Timor-Leste','Togo','Tokelau','Tonga','Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Turks and Caicos Islands','Tuvalu','U.S. Minor Outlying Islands','U.S. Virgin Islands','Uganda','Ukraine','United Arab Emirates','United Kingdom','United States','Uruguay','Uzbekistan','Vanuatu','Vatican City','Venezuela','Vietnam','Wallis and Futuna','Western Sahara','Yemen','Zambia','Zimbabwe');

//$races = array(1=>'Mongoloid', 2=>'Caucasian', 3=>'Negroid');
$races = array(1=>$lng['race_1'], 2=>$lng['race_2'], 3=>$lng['race_3']);

if ($tpl_name && !empty($_SESSION['user_id']) && $install_progress=='complete') {

	if ( isset($db) && get_community_users($db) ){
		define('COMMUNITY', true);
	}

	if (isset($db))
		define('MY_PREFIX', get_my_prefix($db));

	// уведомления
	$tpl['alert'] = @$_REQUEST['parameters']['alert'];
	if (isset($db))
		require_once( ABSPATH . 'content/alert_message.php' );

	$skip_restricted_users = array('cash_requests_in', 'cash_requests_out', 'upgrade', 'notifications');
	if ( empty($_SESSION['restricted']) || !in_array($tpl_name, $skip_restricted_users) )
		require_once( ABSPATH . 'content/'.$tpl_name.'.php' );
}
else if ($tpl_name) {
	require_once( ABSPATH . 'content/'.$tpl_name.'.php' );
}
else {
	require_once( ABSPATH . 'content/login.php' );
}
?>