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
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?php echo $routes[str_replace("wpspy-", "", $page)]; ?>
        <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="?page=wpspy-dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
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