<?php 
    $page = 'wpspy-graphs';
    include plugin_dir_path( __FILE__ )."classes/config.php";
    include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
    include plugin_dir_path( __FILE__ )."classes/data.php";
?>

<div class="wrapper">
    <!-- Content Wrapper. Contains page content -->
    <div>
        <?php
        	include "_nav.php";
        ?>
        <section class="content">
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-12">
                    <?php include  plugin_dir_path( __FILE__ )."_form.php"; ?>
                </div>
            </div>

            <div class="row">
                <section class="col-lg-10">
                    <!-- GRAPHS -->
                    <div class="box box-solid bg-green-gradient graphs">
                        <div class="box-header">
                            <i class="ion ion-stats-bars"></i>
                            <h3 class="box-title">Graphs</h3>
                            <div class="pull-right box-tools">
                                <button type="button" class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body no-padding">
                        </div>
                        <div class="box-footer text-black">
                            <div class="row">
								<div class="col-md-3">
									<select id="compare_sites" class="select2 form-control">
									<?php 
										$sites = get_sites();
										$x = 0;
										foreach ($sites as $site => $value) {
											$y = ($x<1)?'selected':'';
											echo "<option ".$y.">".$value."</opiton>";
											$x++;
										}
									?>
									</select>
								</div>
								<div class="col-md-3">
									<select id="compare_sites2" class="select2 form-control">
									<?php 
										$sites = get_sites();
										$x = 0;
										foreach ($sites as $site => $value) {
											$y = ($x<1)?'selected':'';
											echo "<option ".$y.">".$value."</opiton>";
											$x++;
										}
									?>
									</select>	
								</div>

								<div class="col-md-2">
									<select id="chart_options" class="select2 form-control">
										<optgroup label="SEO Metrics">
											<option value="alexa_rank" selected>Alexa Rank</option>
											<option value="backlinks_alexa">Alexa Backlinks</option>
											<option value="backlinks_google">Google Backlinks</option>
											<option value="bounce_rate">Bounce Rate</option>
											<option value="dailytime_onsite">Time on Site</option>
										</optgroup>
										<optgroup label="Social Shares">
											<option value="facebook_count">Facebook</option>
											<option value="twitter_count">Twitter</option>
											<option value="stumbleupon_count">StumbleUpon</option>
											<option value="pinterest_count">Pinterest</option>
										</optgroup>
									</select>
								</div>

								<div class="col-md-2">
									<a href="javascript:void(0);" class="btn bg-maroon" id="update_chart">
										<i class="fa fa-line-chart"></i> Compare Sites
									</a>
								</div>
							</div>
							<div id="canvas-holder">
								<div id="chart-area">
									<p class="clearfix"></p>
									<div class="progress progress-xxs">
										<div class="progress-bar progress-bar-success progress-bar-striped"
											role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
											<span class="sr-only">60% Complete (warning)</span>
										</div>
									</div>
								</div>
							</div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="row">
                <section class="col-md-12">
                    <?php include "_export.php"; ?>
                </section>
            </div>
        </section>
    </div>
</div>