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

	
?>
<div class="wpspy-head">
	<div class="logo">
		<img src="<?php echo plugins_url('/images/spy.png', __FILE__); ?>" draggable="false">
	</div>
<?PHP if($page != "wpspy-keycheck"): ?>
<?php 
	function idlize($str) {
		return str_replace("-", "_", $str);
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
	<!-- <div class="nav">
		<?php foreach($routes as $key => $name): ?>
			<div class="nav-menu <?php echo ($page == "wpspy-$key") ? 'selected' : '';?> <?php echo $key == 'previous-searches' ? 'pull-right' : ''; ?>">
				<a href="?page=wpspy-<?php echo $key; echo !empty($_GIVEN_URL) ? '&url='.$_GIVEN_URL : ''; ?>" data-href="?page=wpspy-<?php echo $key; ?>"
					id="<?php echo idlize($key); ?>">
					<?php echo $name; ?>
				</a>
			</div>
		<?php endforeach; ?>
	</div> -->
<?PHP  endif; ?>
</div>
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