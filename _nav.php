<script type="text/javascript">    
    /* Let's initialize the variable which will hold all the data for CSV and PDF exports */
	var exportableData = {};
</script>
<?php 
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	ini_set('max_execution_time', 300); //300 seconds = 5 minutes

	$_GIVEN_URL = "";

	if (isset($_GET['url'])) {
		$_GIVEN_URL = preg_replace(
		  '#((https?|ftp)://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)#i',
		  "$1",
		  $_GET['url']
		);
	}


	if (!function_exists('idlize')) {
		function idlize($str) {
			return str_replace("-", "_", $str);
		}
	}

	if (!function_exists('wpspyGetHost')) {
		function wpspyGetHost($address) { 
		   $parseUrl = parse_url(trim($address)); 
		   return trim(!empty($parseUrl['host']) ? $parseUrl['host'] : @array_shift(explode('/', $parseUrl['path'], 2))); 
		} 
	}


	$_GIVEN_URL_DOMAIN = !empty($_GIVEN_URL) ? wpspyGetHost($_GIVEN_URL) : "";
	$_SOURCE = isset($_GET['source']) ? trim($_GET['source']) : "";

	$routes = array(
		"site-info" => "Site Info",
		"page-info" => "Page Info",
		"seo-stats" => "Seo Stats",
		"rapid-indexer" => "Indexer",
		"keyword-research" => "Keyword Research",
		"social-stats" => "Social Stats",
		"traffic" => "Traffic",
		"links" => "Links",
		"graphs" => "Graphs",
		"tutorials" => "Tutorials",
		"support" => "Support",
		"previous-searches" => "History"
	);
	$plugin_data = get_option("plugin_info");
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?php echo $routes[str_replace("wpspy-", "", $page)]; ?>
        <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li>
        	<a href="?page=wpspy-dashboard">
        		<i class="fa fa-dashboard"></i>
        		<?php echo $plugin_data['name'].' v'.$plugin_data['version']; ?>
        	</a>
        </li>
        <li class="active">
        	<?php echo $routes[str_replace("wpspy-", "", $page)]; ?>
        </li>
    </ol>
</section>

<div class="hidden">
	<table id="export_table">
		<thead>
			<tr>
				<th>WP Spy - </th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>
<div class="loading"><div class="center">Grabbing data all over the web...</div></div>