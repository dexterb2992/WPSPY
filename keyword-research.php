<div class="wpspy-wrapper">
	<?php 
		$page = 'wpspy-keyword-research';
		include plugin_dir_path( __FILE__ )."classes/config.php";
		include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
		include plugin_dir_path( __FILE__ )."_nav.php"; 
		include plugin_dir_path(__FILE__)."classes/data.php";
	?>

	<div class="wpspy-content">
		<!-- START WP Keyword Master Dashboard -->
		<div class="container">

			<div class="wpindexer-form wpspy-results row">
				<div class="col-6">
					<div class="wpspy-form">
						<iframe id="remember" name="remember" class="hidden" src="about:blank"></iframe>
						<form target="remember" method="post" action="about:blank" id="form_wpkm">
							
							<div class="form-group form-inline">
								<input class="form-control wpspy-input" type="text" name="wpkm_keyword" id="wpkm_keyword" placeholder="Enter keyword"/>
								<input type="submit" class="wpspy_btn" name="wpkm_submit" id="wpkm_submit" value="Go" />
							</div>
							<div id="wpkm_results_count"></div>
						</form>
					</div>
				</div>

				<div class="col-6">
					<div class="wpspy-form">
						<iframe id="remember2" name="remember2" class="hidden" src="about:blank"></iframe>
						<form target="remember2" method="post" action="about:blank" id="form_wpkm_check_domain">

							<div class="form-group form-inline">
								<input type="text" class="form-control wpspy-input" name="domain_name" id="domain_name" placeholder="Enter your keyword" required/>
								<select name="tdl" id="tdl_extension" class="form-control">
									<option value=".com">.com</option>
									<option value=".org">.org</option>
									<option value=".net">.net</option>
									<option value=".info">.info</option>
								</select>

								<input type="submit" class="wpspy_btn" name="wpkm_create_domains" id="wpkm_create_domains" value="Create Domains" />
								<input type="button" class="wpspy_btn" name="wpkm_check_availability" id="wpkm_check_availability" value="Check Availability" />
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="wpindexer-form wpspy-results row">
				<div class="col-6">
					<div class="box" style="margin-left: 0;">
						<div class="title">Results</div>
						<div class="content">
							<div class="entry">
								<div id="wpkm_results_div"></div>
								<div class="export">
									<div id="exportable_table" style="display:none;"></div>
								</div>
							</div>
						</div>
					</div>
					
				</div>
				<div class="col-6" id="div_generate_domains">
					<div class="wpkm-form">

						<div id="wpkm_results_div">
							<div class="box" style="margin-left: 0;">
								<div class="title">Results</div>
								<div class="content">
									<div class="entry">
										<div id="table_domains">
											<table id="generate_domains"></table>
										</div>
										<div class="export">
											<a href="javascript:void(0)" id="export_domains" class="hidden btn bg-purple btn-flat">
												<i class="fa fa-paste"></i> Export
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div id="exportable_table" style="display:none;"></div>
				</div>
			</div>
		</div>
		<!-- END WP Keyword Master Dashboard -->
	</div>
</div>