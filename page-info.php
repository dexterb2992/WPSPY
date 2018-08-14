<?php 
    $page = 'wpspy-page-info';
    include plugin_dir_path( __FILE__ )."classes/config.php";
    include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
    include plugin_dir_path( __FILE__ )."classes/data.php";

    if( isset( $_GIVEN_URL ) && trim($_GIVEN_URL) != ""  ){
		$cached = checkDataStatus('page_info', $_GIVEN_URL);
		$cached_links = checkDataStatus('link', $_GIVEN_URL);
		
		if( ($cached != 'false') && ( ($cached["body"] != "-") && (!is_null($cached["body"])) && !empty($cached["body"]) ) ){
			$pageinfo = new stdClass();
			
			foreach ($cached as $key => $value) {
				$pageinfo->$key = str_replace("\\", "", $value);
			}

			for($x=0; $x<3; $x++){
				$pageinfo->meta[$x] = new stdClass();
			}

			$pageinfo->meta[0]->description = $cached['meta_description'];
			$pageinfo->meta[1]->keywords = $cached['meta_keywords'];
			$pageinfo->meta[2]->robots = $cached['meta_robots'];
			$pageinfo->body = new stdClass();
			$pageinfo->body = json_decode( str_replace("\\", "", $cached["body"]) );
			echo '<script>exportableData = '.json_encode($cached).';</script>';
		}else{
			$html = getPageInfo("http://".$_GIVEN_URL, 'json');

			$pageinfo = json_decode($html);

			$data_array = array();
		}

		$pageinfo->internal_links = new stdClass();
		$pageinfo->external_links = new stdClass();

		if( $cached_links != 'false' && ( $cached_links["external_links"] != '' || $cached_links["internal_links"] != '' ) ){
			$pageinfo->internal_links = json_decode( str_replace('/\\/', '', $cached_links['internal_links']) );
			$pageinfo->external_links = json_decode( str_replace('/\\/', '', $cached_links['external_links']) );

		}else{
			$links = getLinks("http://".$_GIVEN_URL);
			$pageinfo->internal_links->links = @count($links["internal_links"]["links"]);
			$pageinfo->internal_links->nofollow = @count($links["internal_links"]["nofollow"]);

			$pageinfo->external_links->links = @count($links["external_links"]["links"]);
			$pageinfo->external_links->nofollow = @count($links["external_links"]["nofollow"]);

			$data_array["internal_links"] = json_encode( array("links" => @count($pageinfo->internal_links->links), "nofollow" => $pageinfo->internal_links->nofollow) );
			$data_array["external_links"] = json_encode( array("links" => @count($pageinfo->external_links->links), "nofollow" => $pageinfo->external_links->nofollow) );
		}
	}
?>

<div class="wrapper">
    <!-- Content Wrapper. Contains page content -->
    <div>
        <?php include "_nav.php"; ?>
        <section class="content">
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-12">
                    <?php include  plugin_dir_path( __FILE__ )."_form.php"; ?>
                </div>
            </div>

            <div class="row">
                <section class="col-lg-8 connectedSortable">
                    <!-- PAGE INFO -->
                    <div class="box box-solid bg-green-gradient page-info">
                        <div class="box-header">
                            <i class="fa fa-binoculars"></i>
                            <h3 class="box-title">Page Info</h3>
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
                            <div class="row">
                                <div class="col-md-12">
                                	<table class="table tbl-page-info">
										<thead>
											<tr>
												<th>Tag</th>
												<th>Content</th>
												<th>Length</th>
											</tr>
										</thead>
										<tbody>
											<tr class="url">
												<td>URL:</td>
												<td>
													<?php 
														echo isset($_GIVEN_URL) ? $_GIVEN_URL : '';
														if(isset($pageinfo)){
															$data_array["url"] = $_GIVEN_URL;
														}
													?>
												</td>
												<td>
													<?php 
														echo isset($_GIVEN_URL) ? strlen($_GIVEN_URL) : '';

													?>
												</td>
											</tr>
											<tr class="canonical-url">
												<td>Canonical URL:</td>
												<td>
													<?php 
														echo isset($pageinfo->canonical_url) ? $pageinfo->canonical_url : '';
														if(isset($pageinfo->canonical_url)){
															$data_array["canonical_url"] = $pageinfo->canonical_url;
														}
													?>
												</td>
												<td>
													<?php 
														echo ( isset($pageinfo->canonical_url) && ($pageinfo->canonical_url != "N/A") ) ? strlen($pageinfo->canonical_url) : '';
														
													?>
												</td>
											</tr>
											<tr class="pageinfo-title">
												<td>Title:</td>
												<td>
													<?php 
														echo isset($pageinfo->title) ? $pageinfo->title : '-';
														
														if( isset($pageinfo) && isset($pageinfo->title) ){
															$data_array["title"] = $pageinfo->title;
														}
													?>
												</td>
												<td>
													<?php echo isset($pageinfo->title) ? strlen($pageinfo->title) : '';?>
												</td>
											</tr>
											<tr class="meta-keywords">
												<td>Meta keywords:</td>
												<td>
													<?php 
														echo isset($pageinfo->meta[1]->keywords) ? $pageinfo->meta[1]->keywords : '';
														if( isset($pageinfo) ){
															$data_array["meta_keywords"] = isset($pageinfo->meta[1]->keywords) ? $pageinfo->meta[1]->keywords : 'N/A';
														}
													?>
												</td>
												<td>
													<?php echo isset($pageinfo->meta[1]->keywords) ? strlen($pageinfo->meta[1]->keywords) : '';?>
												</td>
											</tr>
											<tr class="meta-description">
												<td>Meta description:</td>
												<td>
													<?php 
														echo isset($pageinfo->meta[0]->description) ? $pageinfo->meta[0]->description : '';
														if(isset($pageinfo)){
															$data_array["meta_description"] = isset($pageinfo->meta[0]->description) ? $pageinfo->meta[0]->description : 'N/A';
														}
													?>
												</td>
												<td>
													<?php echo isset($pageinfo->meta[0]->description) ? strlen($pageinfo->meta[0]->description) : '';?>
												</td>
											</tr>
											<tr class="meta-robots">
												<td>Meta robots:</td>
												<td>
													<?php 
														echo isset($pageinfo->meta[2]->robots) ? $pageinfo->meta[2]->robots : '';
														if(isset( $pageinfo )){
															$data_array["meta_robots"] = isset($pageinfo->meta[2]->robots) ? $pageinfo->meta[2]->robots : 'N/A';
														}
													?>
												</td>
												<td>
													<?php echo isset($pageinfo->meta[2]->robots) ? strlen($pageinfo->meta[2]->robots) : '';?>
												</td>
											</tr>
											<tr class="external-links">
												<td>External links:</td>
												<td>
													<?php 
														echo isset($pageinfo->external_links->links) ? $pageinfo->external_links->links." ( ".$pageinfo->external_links->nofollow." nofollow)" : '';
													?>
												</td>
												<td></td>
											</tr>
											<tr class="internal-links">
												<td>Internal links:</td>
												<td>
													<?php 
														echo isset($pageinfo->internal_links->links) ? $pageinfo->internal_links->links." ( ".$pageinfo->internal_links->nofollow." nofollow)" : '';
													?>
												</td>
												<td></td>
											</tr>
											<tr class="h1">
												<td>H1:</td>
												<td>
													<?php
														echo isset($pageinfo->h1) ? $pageinfo->h1 : '';
														if(isset($pageinfo)){
															$data_array["h1"] = isset($pageinfo->h1) ? $pageinfo->h1 : 'N/A';
														}
													?>
												</td>
												<td>
													<?php echo isset($pageinfo->h1) && ($pageinfo->h1 != "N/A") ? strlen($pageinfo->h1) : '';?>
												</td>
											</tr>
											<tr class="h2">
												<td>H2:</td>
												<td>
													<?php 
														echo isset($pageinfo->h2) ? $pageinfo->h2 : '';
														if(isset($pageinfo)){
															$data_array["h2"] = isset($pageinfo->h2) ? $pageinfo->h2 : 'N/A';
														}
													?>
												</td>
												<td>
													<?php echo isset($pageinfo->h2) && ($pageinfo->h2 != "N/A") ? strlen($pageinfo->h2) : '';?>
												</td>
											</tr>
											<tr class="bold-strong">
												<td>Bold/Strong:</td>
												<td>
													<?php 
														echo isset($pageinfo->bold_strong) ? $pageinfo->bold_strong : '';
														if(isset($pageinfo)){
															$data_array["bold_strong"] = isset($pageinfo->bold_strong) ? $pageinfo->bold_strong : 'N/A';
														}
													?>
												</td>
												<td>
													<?php echo isset($pageinfo->bold_strong) && ($pageinfo->bold_strong != "N/A") ? strlen($pageinfo->bold_strong) : '';?>
												</td>
											</tr>
											<tr class="italic-em">
												<td>Italic/em:</td>
												<td>
													<?php 
														echo isset($pageinfo->italic_em) ? $pageinfo->italic_em : '';
														if(isset($pageinfo)){
															$data_array["italic_em"] = isset($pageinfo->italic_em) ? $pageinfo->italic_em : 'N/A';
														}
													?>
												</td>
												<td>
													<?php echo isset($pageinfo->italic_em) && ($pageinfo->italic_em != "N/A") ? strlen($pageinfo->italic_em) : '';?>
												</td>
											</tr>
											<tr class="body-text">
												<td>Body text:</td>
												<td>
													<?php 
														echo isset($pageinfo->body->content) ? $pageinfo->body->content : '';

														if( isset($pageinfo->body->content) ){
															echo $pageinfo->body->content;
															if($cached == 'false' || @$cached["body"] == "-" || @$cached['body'] == "" ){
																$data_array["body"] = json_encode( array("content" => $pageinfo->body->content, "length" => $pageinfo->body->length ) );
																echo '<script>exportableData = '.json_encode($data_array).';</script>';
																echo "saving...";
																$status = save_this_activity("http://".$_GIVEN_URL, $data_array);
															}
														}
													?>
												</td>
												<td>
													<?php echo isset($pageinfo->body->length) ? $pageinfo->body->length : '';?>

												</td>
											</tr>
										</tbody>
									</table>
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