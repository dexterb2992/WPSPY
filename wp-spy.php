<?php
/**
 * @package WP_SPY
 * @version 1.3.3
 */
/*
Plugin Name: WP Spy (Pro)
Description: Discover the plugins and theme used by any wordpress blog. Instantly download any free plugin and theme...with one click. Discover the Whois details about the site (great for offliners, service providers). Discover site vulnerabilities, see all the social shares, get the domains backlinks Archive all your searches for future analysis, View history of site, backlinks, shares etc.
Version: 1.3.3
Author: TopDogIMSolutions
Author URI: http://onlinesuccesswithchris.com
*/


//Version of currently activated plugin
$current_version = '1.3.3';

add_action( 'admin_menu', 'wpspy_admin_menu' );
add_action( 'wp_en queue_scripts', 'wpspy_plugin_styles' );

function wpspy_admin_menu() {

  /* Add our plugin menu and administration screen */
  if($_REQUEST['page']=="wpspy-keycheck") {
      $pageSpyUrl='wpspy-keycheck';
  }else{
      $pageSpyUrl='wpspy-dashboard';                       // The slug to use in the URL of the screen
  }
  /* Add our plugin menu and administration screen */
  $page_hook_suffix = add_menu_page(
      __( 'WP Spy Pro Dashboard', $pageSpyUrl ),          // The menu title
      __( 'WP Spy Pro', $pageSpyUrl ),                    // The screen title
      'manage_options',                                   // The capability required for access to this menu
      $pageSpyUrl,                                        // The slug to use in the URL of the screen
      'wpspy_manage_menu',                                // The function to call to display the screen
      '',
      4
  );

  /* Add our submenus */
  add_submenu_page(
      "wpspy-dashboard", "Site Info", "Site Info", 0, 
      "wpspy-site-info", "wpspy_site_info"
  );

  add_submenu_page(
      "wpspy-dashboard", "Page Info", "Page Info", 0, 
      "wpspy-page-info", "wpspy_page_info"
  );

  add_submenu_page(
      "wpspy-dashboard", "SEO Stats", "SEO Stats", 0, 
      "wpspy-seo-stats", "wpspy_seo_stats"
  );

  add_submenu_page(
      "wpspy-dashboard", "Rapid Indexer", "Rapid Indexer", 0,
      "wpspy-rapid-indexer", "wpspy_rapid_indexer"
  );

  add_submenu_page(
      "wpspy-dashboard", "Keyword Research", "Keyword Research", 0,
      "wpspy-keyword-research", "wpspy_keyword_research"
  );

  add_submenu_page(
      "wpspy-dashboard", "Social Stats", "Social Stats", 0, 
      "wpspy-social-stats", "wpspy_social_stats"
  );


  add_submenu_page(
      "wpspy-dashboard", "Traffic", "Traffic", 0, 
      "wpspy-traffic", "wpspy_traffic"
  );

  add_submenu_page(
      "wpspy-dashboard", "Links", "Links", 0, 
      "wpspy-links", "wpspy_links"
  );

  add_submenu_page(
      "wpspy-dashboard", "Graphs", "Graphs", 0, 
      "wpspy-graphs", "wpspy_graphs"
  );

  add_submenu_page(
      "wpspy-dashboard", "History", "History", 0, 
      "wpspy-previous-searches", "wpspy_previous_searches"
  );

  add_submenu_page(
      "wpspy-dashboard", "Tutorials", "Tutorials", 0, 
      "wpspy-tutorials", "wpspy_tutorials"
  );

  add_submenu_page(
      "wpspy-dashboard", "Support", "Support", 0, 
      "wpspy-support", "wpspy_support"
  );

}


function wpspy_admin_scripts() {

    /* Register our script. */
    wp_register_script( 'FileSaver', plugins_url( '/js/FileSaver.js', __FILE__ ) );
    wp_register_script( 'jspdf', plugins_url( '/js/jspdf.js', __FILE__ ) );
    wp_register_script( 'cell', plugins_url( '/js/cell.js', __FILE__ ) );
    wp_register_script( 'from_html', plugins_url( '/js/from_html.js', __FILE__ ) );
    wp_register_script( 'split_text_to_size', plugins_url( '/js/split_text_to_size.js', __FILE__ ) );
    wp_register_script( 'standard_fonts_metrics', plugins_url( '/js/standard_fonts_metrics.js', __FILE__ ) );
    
    wp_register_script( 'jquery-ui.min', plugins_url( '/js/jquery-ui.min.js', __FILE__ ) );
    wp_register_script( 'jquery-ui-cdn', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js');

    wp_register_script( 'jquery.dataTables.min', plugins_url( '/js/jquery.dataTables.min.js', __FILE__ ) );
    wp_register_script( 'fusioncharts', plugins_url( '/js/fusioncharts.js', __FILE__ ) );
    wp_register_script( 'fusioncharts.theme.zune', plugins_url( '/js/fusioncharts.theme.zune.js', __FILE__ ) );
    wp_register_script( 'fusioncharts.powercharts', plugins_url( '/js/fusioncharts.powercharts.js', __FILE__ ) );
    wp_register_script( 'jquery.timeago', plugins_url( '/js/jquery.timeago.js', __FILE__ ) );
    wp_register_script( 'export', plugins_url( '/js/export.js', __FILE__ ) );
    wp_register_script( 'script', plugins_url( '/js/script.js', __FILE__ ) );
    wp_register_script( 'bootstrapjs', plugins_url( '/assets/bootstrap/js/bootstrap.min.js', __FILE__ ) );
    wp_register_script( 'select2js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js' );
    wp_register_script( 'admin_lte', plugins_url( '/assets/dist/js/app.min.js', __FILE__ ) );

    /* Register style sheet. */
    wp_register_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css' );
    wp_register_style( 'jquery.dataTables.min', plugins_url( '/css/jquery.dataTables.min.css', __FILE__ ) );
    wp_register_style( 'jquery-ui', plugins_url( '/css/jquery-ui.css', __FILE__ ) );
    wp_register_style( 'jquery-ui-css-cdn', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css');
    wp_register_style( 'wpspy-style', plugins_url( '/css/style.css', __FILE__ ) );
    wp_register_style( 'bootstrap', plugins_url( '/assets/bootstrap/css/bootstrap.min.css', __FILE__ ) );
    wp_register_style( 'ionicons', plugins_url( '/assets/Ionicons/css/ionicons.min.css', __FILE__ ) );
    wp_register_style( 'select2css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css' );
    wp_register_style( 'admin-lte', plugins_url( '/assets/dist/css/AdminLTE.min.css', __FILE__ ) );

    /* Link our already registered script to a page */
    wp_enqueue_script( 'FileSaver' );
    wp_enqueue_script( 'jspdf' );
    wp_enqueue_script( 'cell' );
    wp_enqueue_script( 'split_text_to_size' );
    wp_enqueue_script( 'standard_fonts_metrics' );
    wp_enqueue_script( 'from_html' );
    wp_enqueue_script( 'jquery-ui.min' );
    wp_enqueue_script( 'jquery-ui-cdn' );
    wp_enqueue_script( 'fusioncharts' );
    wp_enqueue_script( 'fusioncharts.theme.zune' );
    wp_enqueue_script( 'fusioncharts.powercharts' );
    wp_enqueue_script( 'jquery.timeago' );
    wp_enqueue_script( 'jquery.dataTables.min' );
    wp_enqueue_script( 'script' );
    wp_enqueue_script( 'export' );
    wp_enqueue_script( 'bootstrapjs' );
    wp_enqueue_script( 'select2js' );
    wp_enqueue_script( 'admin_lte' );


    /* Link our already registered style to a page */
    wp_enqueue_style('fontawesome');
    wp_enqueue_style( 'jquery.dataTables.min' );
    wp_enqueue_style( 'jquery-ui' );
    wp_enqueue_style( 'jquery-ui-css-cdn' );
    wp_enqueue_style( 'wpspy-style' );
    wp_enqueue_style( 'bootstrap' );
    wp_enqueue_style( 'ionicons' );
    wp_enqueue_style( 'select2css' );
    wp_enqueue_style( 'admin-lte' );



}

add_action('admin_init', 'wpspy_admin_scripts');


function wpspy_manage_menu() {
    /* Display our administration screen */
    if($_REQUEST['page']=="wpspy-keycheck") {
      include('wpspy-keycheck.php');                                   // The slug to use in the URL of the screen
  }else{
      include('wpspy-dashboard.php');      
  }
    //include('wpspy-dashboard.php');
}

/* Add all the plugins submenu page */

function wpspy_site_info(){
    include 'site-info.php';
}

function wpspy_page_info(){
    include 'page-info.php';
}

function wpspy_seo_stats(){
    include 'seo-stats.php';
}

function wpspy_rapid_indexer() {
    wp_register_script( 'rapid_indexer', plugins_url( '/js/rapid-indexer.js', __FILE__ ) );
    wp_enqueue_script( 'rapid_indexer' );
    include 'rapid-indexer.php';
}

function wpspy_keyword_research() {
    wp_register_script( 'keyword_research', plugins_url( '/js/keyword-research.js', __FILE__ ) );
    wp_enqueue_script( 'keyword_research' );
    include 'keyword-research.php';
}

function wpspy_social_stats(){
    include 'social-stats.php';
}

function wpspy_traffic(){
    include 'traffic.php';
}

function wpspy_links(){
    include 'links.php';
}

function wpspy_graphs(){
    include 'graphs.php';
}

function wpspy_previous_searches(){
    include 'history.php';
}

function wpspy_tutorials(){
    include "tutorials.php";
}
function wpspy_support(){
    include "support.php";
}


/* Create our table where we can store the data from specific searches */
add_action( 'init', 'wpspy_register_activity_log_table', 1 );
add_action( 'switch_blog', 'wpspy_register_activity_log_table' );

function wpspy_register_activity_log_table() {
    global $wpdb;
    $wpdb->wpspy_activity_log = "{$wpdb->prefix}wpspy_activity_log";
}



function wpspy_create_tables() {
  // Code for creating a table goes here
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    global $charset_collate;


  // Call this manually as we may have missed the init hook
    wpspy_register_activity_log_table();

    if($wpdb->get_var("SHOW TABLES LIKE '$ '") != "{$wpdb->wpspy_activity_log}") {
    // if wpspy table is not created, we can create the table here.
        ob_start();
        $sql_create_table = "CREATE TABLE {$wpdb->wpspy_activity_log} (
        id bigint(20) unsigned NOT NULL auto_increment,
        url varchar(255) NOT NULL default '0',

        canonical_url varchar(255) NOT NULL default 'N/A',
        title varchar(255),
        meta_keywords longtext,
        meta_description longtext,
        meta_robots longtext,
        h1 longtext,
        h2 longtext,
        bold_strong longtext,
        italic_em longtext,
        body longtext,

        external_links longtext,
        internal_links longtext,

        alexa_rank varchar(255) NOT NULL default '0',
        google_page_rank varchar(255) NOT NULL default '0',
        quantcast_traffic_rank varchar(255) NOT NULL default '0',
        alexa_rank_in_country longtext,

        robot varchar(5) NOT NULL default '0',
        sitemap_index varchar(5) NOT NULL default '0',

        ip varchar(50) NOT NULL default 'N/A',
        city varchar(255) NOT NULL default 'N/A',
        country varchar(255) NOT NULL default 'N/A',
        country_code varchar(255) NOT NULL default 'N/A',
        dns longtext,

        backlinks_alexa varchar(255) NOT NULL default '0',
        backlinks_google varchar(255) NOT NULL default '0',
        backlinks_open_site_explorer varchar(255) NOT NULL default '0',
        backlinks_sogou varchar(255) NOT NULL default '0',
        backlinks_ahrefs varchar(255) NOT NULL default '0',

        bounce_rate varchar(255) NOT NULL default '0',
        referring_domains varchar(255) NOT NULL default '0',
        referring_ips varchar(255) NOT NULL default '0',
        dailytime_onsite varchar(255) NOT NULL default '-',
        daily_pageviews_per_visitor varchar(255) NOT NULL default '-',

        page_indexed_ask varchar(255) NOT NULL default '0',
        page_indexed_baidu varchar(255) NOT NULL default '0',
        page_indexed_bing varchar(255) NOT NULL default '0',
        page_indexed_goo varchar(255) NOT NULL default '0',
        page_indexed_google varchar(255) NOT NULL default '0',
        page_indexed_sogou varchar(255) NOT NULL default '0',
        page_indexed_yahoo varchar(255) NOT NULL default '0',
        page_indexed_yandex varchar(255) NOT NULL default '0',
        page_indexed__360 varchar(255) NOT NULL default '0',

        facebook_count varchar(11) NOT NULL default '0',
        twitter_count varchar(11) NOT NULL default '0',
        google_count varchar(11) NOT NULL default '0',
        linkedin_count varchar(11) NOT NULL default '0',
        pinterest_count varchar(11) NOT NULL default '0',
        stumbleupon_count varchar(11) NOT NULL default '0',

        score_strength varchar(10) NOT NULL default '0',
        score_sentiment varchar(10) NOT NULL default '-',
        score_passion varchar(10) NOT NULL default '0',
        score_reach varchar(10) NOT NULL default '0 ',
        sentiment varchar(255) NOT NULL default '-',
        top_keywords longtext,

        wordpress_data longtext,

        activity_date datetime NOT NULL default '0000-00-00 00:00:00',
        PRIMARY KEY (id)
    ) $charset_collate; ";
    dbDelta( $sql_create_table );

    ob_flush();
}
}


add_action('admin_init', 'wpspy_activity_log_upgradecheck');
function wpspy_activity_log_upgradecheck(){

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    global $charset_collate;

  //Database version - this may need upgrading.
    $installed_version = get_option('wpspy_activity_log_version');
    // die("installed_version: ".$installed_version." current_version: ".$current_version);

    $tbl_settings = "{$wpdb->prefix}wpspy_activity_settings";

    if($wpdb->get_var("SHOW TABLES LIKE '$ '") != $tbl_settings ){
      $sql_settings = "CREATE TABLE  $tbl_settings (id bigint(20) unsigned NOT NULL auto_increment,
      recommended_tools_limit  int(11) default '10', PRIMARY KEY (id)) $charset_collate;";
      dbDelta( $sql_settings );
  }

  if( !$installed_version ){
       //No installed version - we'll assume its just been freshly installed
     add_option('wpspy_activity_log_version', $current_version);

 }elseif( $installed_version != $current_version ){
        /* 
        * If this is an old version, perform some updates.
        */

        //Installed version is before 1.1 - upgrade to 1.1
        if( version_compare('1.2', $installed_version) ){
            global $wpdb;


            // ob_start();
            $row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                WHERE table_name = '{$wpdb->wpspy_activity_log}' AND column_name = 'country_code'"  );
            if(empty($row)){
              $sql = "ALTER TABLE {$wpdb->wpspy_activity_log} 
              ADD COLUMN `country_code` varchar(50) default 'N/A' after `country`";
              if( $wpdb->query($sql) ){

              }else{
                print_r($sql);
            }
        }


            // ob_flush();
    }

        //Database is now up to date: update installed version to latest version
    update_option('wpspy_activity_log_version', $current_version);
}
}


function wpspy_uninstall_plugin(){
    global $wpdb;
    //Remove our table (if it exists)
    $wpdb->query("DROP TABLE IF EXISTS $wpdb->wpspy_activity_log");

    //Remove the database version
    delete_option('wpspy_activity_log_version');

    /*Remove any other options your plug-in installed and clear any plug-in cron jobs */
}


// Create tables on plugin activation
register_activation_hook( __FILE__, 'wpspy_create_tables' );

register_uninstall_hook(__FILE__,'wpspy_uninstall_plugin');


// Process Ajax
function my_ajax_action_wpspy() {
  include plugin_dir_path( __FILE__ ) . 'classes/ajax.php';
  die;
}


add_action('wp_ajax_wpspy_ajax', 'my_ajax_action_wpspy');