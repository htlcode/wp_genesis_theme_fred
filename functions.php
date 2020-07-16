<?php
define('THEME_DIR', basename(dirname(__FILE__)));
define('THEME_NAME', 'wp-genesis-fred' );
$my_theme_info = wp_get_theme();
define('CHILD_THEME_NAME', $my_theme_info->get('Name'));
define('CHILD_THEME_URL', '' );
define('CHILD_THEME_VERSION', $my_theme_info->get('Version'));
define('THEME_SETTINGS_FIELD', 'my-theme-settings');
define('GENESIS_LANGUAGES_DIR', STYLESHEETPATH.'/languages');
define('GENESIS_LANGUAGES_URL', STYLESHEETPATH.'/languages');

require_once(get_template_directory().'/lib/init.php');
require_once(get_stylesheet_directory().'/lib/My_Theme.php');

//Initialize Child Theme
function initialize_wp_genesis_fred(){
	$core = WpGenesisFred\My_Theme::get_instance();
}
initialize_wp_genesis_fred();