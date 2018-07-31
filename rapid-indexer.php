<div class="wpspy-wrapper">
	<?php 
		$page = 'wpspy-rapid-indexer';
		include plugin_dir_path( __FILE__ )."classes/config.php";
		include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
		include plugin_dir_path( __FILE__ )."_nav.php"; 
		include plugin_dir_path(__FILE__)."classes/data.php";
	?>

	<div class="wpspy-content">
		<!-- START WP Indexer Dashboard -->
		<div class="wpindexer-wrapper container">

			<div class="wpindexer-form row">
				<div class="col-md-12">
					<iframe id="remember" name="remember" class="hidden" src="about:blank"></iframe>
					<form target="remember" method="post" target="remember" action="about:blank" id="form_wpindexer">
						<div class="form-group form-inline">
							<input	type="text" class="form-control" name="wpindexer_url" id="wpindexer_url" placeholder="www.example.com"/>
							<input type="submit" class="btn btn-flat btn-info wpindexer_btn wpspy_btn" name="wpindexer_submit" id="wpindexer_submit" value="Go" />
						</div>
					</form>
				</div>	
			</div>

			<div class="wpindexer-results row" style="margin-top: 20px; margin-bottom: 15px;">
				<div class="col-md-12">
					<div class="box" style="margin-left: 0;">
						<div class="title">Results</div>
						<div class="content">
							<div class="entry">
								<div id="wpindexer-results-div">
									<table class="table table-striped">
										<thead></thead>
										<tbody></tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- END WP Indexer Dashboard -->
	</div>
</div>