<?php
error_reporting(E_ALL);

include( dirname( dirname ( dirname( dirname( dirname(__FILE__) ) ) ) )."/wp-load.php" );


# Get the name of the current folder
define('FOLDER_NAME',  basename( dirname(__DIR__) )."/" );	


define( 'WPSPY_HOST', plugins_url(FOLDER_NAME.'wp-spy_/') );

define( 'BASE_URL',  plugins_url(FOLDER_NAME) );

#Do not forget to add trailing slash at the end.
// define('ABS_PATH', dirname( dirname ( dirname( dirname( dirname(__FILE__) ) ) ) ).'/wp-content/plugins/wp-spy/wp-spy_/');

define('ABS_PATH', dirname( plugin_dir_path( __FILE__ ) ) . '/wp-spy_/');

define('BASE_PATH', BASE_URL.FOLDER_NAME);

#This folder contains system files
define('APP_FOLDER', ABS_PATH.'app/');

#Class Folder, Do not change. Ask for guidance.
define('CLASS_FOLDER', APP_FOLDER.'classes/');

