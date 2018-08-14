<?php 
    $page = 'wpspy-rapid-indexer';
    include plugin_dir_path( __FILE__ )."classes/config.php";
    include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
    include plugin_dir_path( __FILE__ )."classes/data.php";

?>

<div class="wrapper">
    <!-- Content Wrapper. Contains page content -->
    <div>
        <?php include "_nav.php"; ?>
        <section class="content">
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-12">
                    <div class="wpspy-form">
						<iframe src="about:blank" id="remember" name="remember" class="hidden"></iframe>
						<form method="post" action="" id="form_wpindexer" target="remember" class="form form-inline">
							<div class="input-group">
					            <input	type="text" class="form-control" name="wpindexer_url" id="wpindexer_url" placeholder="www.example.com"/>
					            <span class="input-group-btn">
					            	<input type="submit" class="wpindexer_btn btn btn-primary" name="wpindexer_submit" id="wpindexer_submit" value="Go" />
					            </span>
					          </div>
						</form>
					</div>
                </div>
            </div>

            <div class="row">
                <section class="col-lg-12 connectedSortable">
                    <!-- PAGE INFO -->
                    <div class="box box-solid bg-green-gradient rapid-indexer">
                        <div class="box-header">
                            <i class="fa fa-search"></i>
                            <h3 class="box-title">Results</h3>
                            <!-- tools box -->
                            <div class="pull-right box-tools">
                                <button type="button" class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /. tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body no-padding">
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer text-black">
                            <div class="wpindexer-results row">
								<div class="col-md-12">
									<div id="wpindexer-results-div">
										<table class="table table-bordered table-striped">
											<thead></thead>
											<tbody></tbody>
										</table>
									</div>
								</div>
							</div>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>
</div>