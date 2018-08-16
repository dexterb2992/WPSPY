<?php 
	$page = 'wpspy-keyword-research';
	include plugin_dir_path( __FILE__ )."classes/config.php";
	include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
	include plugin_dir_path(__FILE__)."classes/data.php";
?>

<div class="wrapper">
    <!-- Content Wrapper. Contains page content -->
    <div>
        <?php include "_nav.php"; ?>
        <section class="content">
        	<div class="row">
				<div class="col-md-6">
					<div class="wpspy-form">
						<iframe id="remember" name="remember" class="hidden" src="about:blank"></iframe>
						<form target="remember" method="post" action="about:blank" id="form_wpkm">
							
							<div class="form-group form-inline">
								<input class="form-control wpspy-input" type="text" name="wpkm_keyword" id="wpkm_keyword" placeholder="Enter keyword" value="<?php echo !empty($_SOURCE) ? $_SOURCE : ''; ?>" />
								<button type="submit" class="btn btn-info" name="wpkm_submit" id="wpkm_submit">Go</button>
							</div>
							<div id="wpkm_results_count"></div>
						</form>
					</div>
				</div>

				<div class="col-md-6">
					<div class="wpspy-form">
						<iframe id="remember2" name="remember2" class="hidden" src="about:blank"></iframe>
						<form target="remember2" method="post" action="about:blank" id="form_wpkm_check_domain">

							<div class="form-group form-inline">
								<input type="text" class="form-control wpspy-input" name="domain_name" id="domain_name" placeholder="Enter your keyword" required />
								<select name="tdl" id="tdl_extension" class="form-control">
									<option value=".com">.com</option>
									<option value=".org">.org</option>
									<option value=".net">.net</option>
									<option value=".info">.info</option>
								</select>

								<input type="submit" class="btn btn-warning" name="wpkm_create_domains" id="wpkm_create_domains" value="Create Domains" />
								<button type="button" class="btn btn-success" name="wpkm_check_availability" id="wpkm_check_availability">
									Check Availability
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<div class="box box-solid bg-aqua-gradient page-info">
                        <div class="box-header">
                            <i class="fa fa-search"></i>
                            <h3 class="box-title">Results</h3>
                            <!-- tools box -->
                            <div class="pull-right box-tools">
                                <button type="button" class="btn btn-info btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /. tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body no-padding">
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer text-black">
                            <div id="wpkm_results_div"></div>
							<div class="export">
								<div id="exportable_table" style="display:none;"></div>
							</div>
                        </div>
                    </div>
				</div>

				<div class="col-md-6" id="div_generate_domains">
					<div class="box box-solid bg-purple-gradient page-info">
                        <div class="box-header">
                            <i class="fa fa-recycle"></i>
                            <h3 class="box-title">Results</h3>
                            <!-- tools box -->
                            <div class="pull-right box-tools">
                                <button type="button" class="btn bg-purple btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /. tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body no-padding">
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer text-black">
                            <div id="table_domains">
								<table id="generate_domains" class="table table-bordered"></table>
							</div>
							<div class="export">
								<a href="javascript:void(0)" id="export_domains" class="hidden btn bg-purple btn-flat">
									<i class="fa fa-paste"></i> Export
								</a>
							</div>
                        </div>
                    </div>

					<div id="exportable_table" style="display:none;"></div>
				</div>
			</div>
        </section>
    </div>
</div>