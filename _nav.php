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
<?PHP if($page != "wpspy-keycheck") { ?>
	<div class="nav">
		<div class="nav-menu <?php echo ( $page == 'wpspy-site-info') ? 'selected' : '';?>">
			<a href="?page=wpspy-site-info<?php echo !empty($_GIVEN_URL) ? '&url='.$_GIVEN_URL : ''; ?>" data-href="?page=wpspy-site-info">Site Info</a>
		</div>
		<div class="nav-menu <?php echo ( $page == 'wpspy-page-info') ? 'selected' : '';?>">
			<a href="?page=wpspy-page-info<?php echo !empty($_GIVEN_URL) ? '&url='.$_GIVEN_URL : ''; ?>" data-href="?page=wpspy-page-info">Page Info</a>
		</div>
		<div class="nav-menu <?php echo ( $page == 'wpspy-seo-stats') ? 'selected' : '';?>">
			<a href="?page=wpspy-seo-stats<?php echo !empty($_GIVEN_URL) ? '&url='.$_GIVEN_URL : ''; ?>" data-href="?page=wpspy-seo-stats" id="nav_seo_stats">SEO Stats</a>
		</div>
		<div class="nav-menu <?php echo ( $page == 'wpspy-social-stats') ? 'selected' : '';?>">
			<a href="?page=wpspy-social-stats<?php echo !empty($_GIVEN_URL) ? '&url='.$_GIVEN_URL : ''; ?>" data-href="?page=wpspy-social-stats">Social Stats</a>
		</div>
		<div class="nav-menu <?php echo ( $page == 'wpspy-traffic') ? 'selected' : '';?>">
			<a href="?page=wpspy-traffic<?php echo !empty($_GIVEN_URL) ? '&url='.$_GIVEN_URL : ''; ?>" data-href="?page=wpspy-traffic">Traffic</a>
		</div>
		<div class="nav-menu <?php echo ( $page == 'wpspy-links') ? 'selected' : '';?>">
			<a href="?page=wpspy-links<?php echo !empty($_GIVEN_URL) ? '&url='.$_GIVEN_URL : ''; ?>" data-href="?page=wpspy-links" id="nav_links">Links</a>
		</div>
		<div class="nav-menu <?php echo ( $page == 'wpspy-graphs') ? 'selected' : '';?>">
			<a href="?page=wpspy-graphs<?php echo !empty($_GIVEN_URL) ? '&url='.$_GIVEN_URL : ''; ?>" data-href="?page=wpspy-graphs">Graphs</a>
		</div>
		<div class="nav-menu <?php echo ( $page == 'wpspy-tutorials') ? 'selected' : '';?>">
			<a href="?page=wpspy-tutorials<?php echo !empty($_GIVEN_URL) ? '&url='.$_GIVEN_URL : ''; ?>" data-href="?page=wpspy-tutorials">Tutorials</a>
		</div>
		<div class="nav-menu <?php echo ( $page == 'wpspy-support') ? 'selected' : '';?>">
			<a href="?page=wpspy-support<?php echo !empty($_GIVEN_URL) ? '&url='.$_GIVEN_URL : ''; ?>" data-href="?page=wpspy-support">Support</a>
		</div>
		<div class="nav-menu <?php echo ( $page == 'previous-searches') ? 'selected' : '';?> pull-right">
			<a href="?page=wpspy-previous-searches<?php echo !empty($_GIVEN_URL) ? '&url='.$_GIVEN_URL : ''; ?>" data-href="?page=wpspy-previous-searches">History</a>
		</div>
	</div>
<?PHP  } ?>
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