<?php 
    $page = 'wpspy-seo-stats';
    include plugin_dir_path( __FILE__ )."classes/config.php";
    include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
    include plugin_dir_path( __FILE__ )."classes/data.php";

    if( isset( $_GIVEN_URL ) && trim($_GIVEN_URL) != "" ){

        $cached = checkDataStatus('seo_stats', $_GIVEN_URL);
        
        if( ($cached !== 'false') && ( isset($cached["alexa_rank"]) && 
        $cached["alexa_rank"] != 0) ){
            $seostats = array();
            $seostats["rank"] = new stdClass();
            $seostats["backlinks"] = new stdClass();
            $seostats["pages_indexed"] = new stdClass();
            $seostats["site_metrics"] = new stdClass();
            $seostats["cached"] = new stdClass();

            $seostats["rank"]->alexa_traffic_rank = $cached["alexa_rank"];
            $seostats["rank"]->quantcast_traffic_rank = $cached["quantcast_traffic_rank"];
            $seostats["rank"]->google_page_rank = $cached["google_page_rank"];
            $seostats["rank"]->alexa_rank_in_country = @json_decode($cached["alexa_rank_in_country"]);

            $seostats["backlinks"]->alexa = $cached["backlinks_alexa"];
            $seostats["backlinks"]->open_site_explorer = $cached["backlinks_open_site_explorer"];
            $seostats["backlinks"]->google = $cached["backlinks_google"];
            // $seostats["backlinks"]->ahrefs = $cached["backlinks_ahrefs"];
            $seostats["backlinks"]->sogou = $cached["backlinks_sogou"];

            $seostats["pages_indexed"]->ask = $cached["page_indexed_ask"];
            $seostats["pages_indexed"]->baidu = $cached["page_indexed_baidu"];
            $seostats["pages_indexed"]->bing = $cached["page_indexed_bing"];
            $seostats["pages_indexed"]->goo = $cached["page_indexed_goo"];
            $seostats["pages_indexed"]->google = $cached["page_indexed_google"];
            $seostats["pages_indexed"]->sogou = $cached["page_indexed_sogou"];
            $seostats["pages_indexed"]->yahoo = $cached["page_indexed_yahoo"];
            $seostats["pages_indexed"]->yandex = $cached["page_indexed_yandex"];
            $seostats["pages_indexed"]->_360 = $cached["page_indexed__360"];

            $seostats["site_metrics"]->bounce_rate = @$cached["bounce_rate"];
            $seostats["site_metrics"]->dailytime_onsite = @$cached["dailytime_onsite"];
            $seostats["site_metrics"]->daily_pageviews_per_visitor = @$cached["daily_pageviews_per_visitor"];

            $seostats["cached"]->archive = "https://web.archive.org/web/*/http://".$_GIVEN_URL;
            $seostats["cached"]->google = " https://webcache.googleusercontent.com/search?cd=1&hl=en&ct=clnk&gl=us&q=cache:http://".$_GIVEN_URL;
            echo '<script>exportableData = '.json_encode($seostats).';</script>';
        }else{
            $html = getSeoStats($_GIVEN_URL, 'json');
            $seostats = (array) json_decode($html);
            echo '<script>exportableData = '.$html.';</script>';

            $data_array = array();
            $data_array["url"] = $_GIVEN_URL;
            $data_array["alexa_rank"] = $seostats["rank"]->alexa_traffic_rank;
            $data_array["google_page_rank"] = $seostats["rank"]->google_page_rank;
            $data_array["quantcast_traffic_rank"] = $seostats["rank"]->quantcast_traffic_rank;

            $data_array["alexa_rank_in_country"] = (string) json_encode($seostats["rank"]->alexa_rank_in_country);

            $data_array["bounce_rate"] = $seostats["site_metrics"]->bounce_rate;
            $data_array["dailytime_onsite"] = $seostats["site_metrics"]->dailytime_onsite;
            $data_array["daily_pageviews_per_visitor"] = $seostats["site_metrics"]->daily_pageviews_per_visitor;


            $data_array["backlinks_alexa"] = $seostats["backlinks"]->alexa;
            $data_array["backlinks_google"] = $seostats["backlinks"]->google;
            $data_array["backlinks_open_site_explorer"] = $seostats["backlinks"]->open_site_explorer;
            $data_array["backlinks_sogou"] = $seostats["backlinks"]->sogou;
            // $data_array["backlinks_ahrefs"] = $seostats["backlinks"]->ahrefs;

            $data_array["page_indexed_ask"] = $seostats["pages_indexed"]->ask;
            $data_array["page_indexed_baidu"] = $seostats["pages_indexed"]->baidu;
            $data_array["page_indexed_bing"] = $seostats["pages_indexed"]->bing;
            $data_array["page_indexed_goo"] = $seostats["pages_indexed"]->goo;
            $data_array["page_indexed_google"] = $seostats["pages_indexed"]->google;
            $data_array["page_indexed_sogou"] = $seostats["pages_indexed"]->sogou;
            $data_array["page_indexed_yahoo"] = $seostats["pages_indexed"]->yahoo;
            $data_array["page_indexed_yandex"] = $seostats["pages_indexed"]->yandex;

            if( !empty($seostats["pages_indexed"]->_360) ){
                $data_array["page_indexed__360"] = $seostats["pages_indexed"]->_360;
            }

            $temp = $seostats["site_metrics"]->bounce_rate;

            $data_array["bounce_rate"] = $temp == "" ? 0 : $temp;
            $data_array["dailytime_onsite"] = $seostats["site_metrics"]->dailytime_onsite;
            $data_array["daily_pageviews_per_visitor"] = $seostats["site_metrics"]->daily_pageviews_per_visitor;
            $status = save_this_activity($_GIVEN_URL, $data_array);
            $data_array["cached"] = array(
                "archive" => "https://web.archive.org/web/*/".$_GIVEN_URL,
                "google" => "http://webcache.googleusercontent.com/search?cd=1&hl=en&ct=clnk&gl=us&q=cache:".$_GIVEN_URL
            );
            echo '<script>exportableData = '.json_encode($data_array).';</script>';
            
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
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3 id="alexa_rank">
                                <?php echo isset($seostats["rank"]->alexa_traffic_rank) ? $seostats["rank"]->alexa_traffic_rank : '-'; ?>
                            </h3>
                            <p>Alexa Traffic Rank</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-line-chart"></i>
                        </div>
                        <a href="#" class="small-box-footer"><i class="fa fa-line-chart"></i></a>
                    </div>
                </div>
            </div>

            <div class="row">
                <section class="col-lg-7 connectedSortable">
                    <!-- BACKLINKS -->
                    <div class="box box-solid bg-green-gradient backlinks">
                        <div class="box-header">
                            <i class="fa fa-pagelines"></i>
                            <h3 class="box-title">Backlinks</h3>
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
                                    <div class="entry alexa">
                                        <div class="left">
                                            <span class="spy-icon-alexa spy-icon"></span>
                                            Alexa
                                        </div>
                                        <div class="right">
                                            <?php 
                                                if( isset($seostats["backlinks"]->alexa) ){
                                                    if( strstr( $seostats["backlinks"]->alexa, 'http' ) != "" ){
                                                        echo '<a href="'.$seostats["backlinks"]->alexa.'" class="spy-icon spy-icon-eye"></a>';
                                                    }else{
                                                        echo '<span id="alexa_backlinks">'.$seostats["backlinks"]->alexa.'</span>';
                                                    }
                                                }else{
                                                    echo '<span id="alexa_backlinks">N/A</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="entry google">
                                        <div class="left">
                                            <span class="spy-icon-google spy-icon"></span>Google
                                        </div>
                                        <div class="right">
                                            <?php 
                                                if( isset($seostats["backlinks"]->google) ){
                                                    if( strstr( $seostats["backlinks"]->google, 'http' ) != "" ){
                                                        echo '<a href="'.$seostats["backlinks"]->google.'" class="spy-icon spy-icon-eye" id="google_backlinks"></a>';
                                                    }else{
                                                        echo '<span id="google_backlinks">'.$seostats["backlinks"]->google.'</span>';
                                                    }
                                                }else{
                                                    echo '<span id="google_backlinks">N/A</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="entry open_site_explorer">
                                        <div class="left">
                                            <span class="spy-icon-open-site-explorer spy-icon"></span>
                                            Open Site Explorer
                                        </div>
                                        <div class="right">
                                            <?php 
                                                if( isset($seostats["backlinks"]->open_site_explorer) ){
                                                    if( strstr( $seostats["backlinks"]->open_site_explorer, 'http' ) != "" ){
                                                        echo '<a href="'.$seostats["backlinks"]->open_site_explorer.'" class="spy-icon spy-icon-eye" id="open_site_explorer"></a>';
                                                    }else{
                                                        echo '<span id="open_site_explorer">'.$seostats["backlinks"]->open_site_explorer.'</span>';
                                                    }
                                                }else{
                                                    echo '<span id="open_site_explorer">N/A</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="entry sogou">
                                        <div class="left">
                                            <span class="spy-icon-sogou spy-icon"></span>Sogou
                                        </div>
                                        <div class="right">
                                            <?php 
                                                if( isset($seostats["backlinks"]->sogou) ){
                                                    if( strstr( $seostats["backlinks"]->sogou, 'http' ) != "" ){
                                                        echo '<a href="'.$seostats["backlinks"]->sogou.'" class="spy-icon spy-icon-eye" id="sogou"></a>';
                                                    }else{
                                                        echo '<span id="sogou">'.$seostats["backlinks"]->sogou.'</span>';
                                                    }
                                                }else{
                                                    echo '<span id="sogou">N/A</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>   
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>
                    </div>

                    <!-- CACHED -->
                    <div class="box box-solid bg-teal-gradient cached">
                        <div class="box-header">
                            <div class="pull-right box-tools">
                                <button type="button" class="btn bg-teal btn-sm pull-right" data-widget="collapse"
                                    data-toggle="tooltip" title="Collapse" style="margin-right: 5px;">
                                <i class="fa fa-minus"></i></button>
                            </div>
                            <i class="fa fa-book"></i>
                            <h3 class="box-title">
                                Cached
                            </h3>
                        </div>
                        <div class="box-body">
                            
                        </div>
                        <div class="box-footer no-border">
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="todo-list">
                                        <li class="entry">
                                            <div class="left">
                                                <i class="fa fa-archive"></i>
                                                Archive.org
                                            </div>
                                            <div class="right">
                                                <a href="#" id="archived" target="_blank" <?php echo isset($seostats["cached"]->archive) ? 'href="'.$seostats["cached"]->archive.'" class="fa fa-eye"' : 'href="#"'; ?>></a>
                                            </div>
                                        </li>
                                        <li class="entry">
                                            <div class="left">
                                                <i class="fa fa-google"></i> Google
                                            </div>
                                            <div class="right">
                                                <a id="google_cached" target="_blank" <?php echo isset($seostats["cached"]->google) ? 'href="'.$seostats["cached"]->google.'" class="fa fa-eye"' : 'href="#"'; ?>></a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <section class="col-lg-5 connectedSortable">
                    <!-- PAGES INDEXEWD -->
                    <div class="box box-solid box-warning pages-indexed">
                        <div class="box-header">
                            <div class="pull-right box-tools">
                                <button type="button" class="btn bg-orange btn-sm pull-right" data-widget="collapse"
                                    data-toggle="tooltip" title="Collapse" style="margin-right: 5px;">
                                <i class="fa fa-minus"></i></button>
                            </div>
                            <i class="fa fa-globe"></i>
                            <h3 class="box-title">
                                Pages Indexed
                            </h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="entry ask">
                                        <div class="left">
                                            <span class="spy-icon-ask spy-icon"></span>Ask
                                        </div>
                                        <div class="right">
                                            <?php 
                                                if( isset($seostats["pages_indexed"]->ask) ){
                                                    if( strstr( $seostats["pages_indexed"]->ask, 'http' ) != "" ){
                                                        echo '<a href="'.$seostats["pages_indexed"]->ask.'" class="spy-icon spy-icon-eye" id="ask"></a>';
                                                    }else{
                                                        echo '<span id="ask">'.$seostats["pages_indexed"]->ask.'</span>';
                                                    }
                                                }else{
                                                    echo '<span id="ask">N/A</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="entry baidu">
                                        <div class="left">
                                            <span class="spy-icon-baidu spy-icon"></span>Baidu
                                        </div>
                                        <div class="right">
                                            <?php 
                                                if( isset($seostats["pages_indexed"]->baidu) ){
                                                    if( strstr( $seostats["pages_indexed"]->baidu, 'http' ) != "" ){
                                                        echo '<a href="'.$seostats["pages_indexed"]->baidu.'" class="spy-icon spy-icon-eye" id="baidu"></a>';
                                                    }else{
                                                        echo '<span id="baidu">'.$seostats["pages_indexed"]->baidu.'</span>';
                                                    }
                                                }else{
                                                    echo '<span id="baidu">N/A</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="entry bing">
                                        <div class="left">
                                            <span class="spy-icon-bing spy-icon"></span>Bing
                                        </div>
                                        <div class="right">
                                            <?php 
                                                if( isset($seostats["pages_indexed"]->bing) ){
                                                    if( strstr( $seostats["pages_indexed"]->bing, 'http' ) != "" ){
                                                        echo '<a href="'.$seostats["pages_indexed"]->bing.'" class="spy-icon spy-icon-eye" id="bing"></a>';
                                                    }else{
                                                        echo '<span id="bing">'.$seostats["pages_indexed"]->bing.'</span>';
                                                    }
                                                }else{
                                                    echo '<span id="bing">N/A</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="entry goo">
                                        <div class="left">
                                            <span class="spy-icon-goo spy-icon"></span>Goo
                                        </div>
                                        <div class="right">
                                            <?php 
                                                if( isset($seostats["pages_indexed"]->goo) ){
                                                    if( strstr( $seostats["pages_indexed"]->goo, 'http' ) != "" ){
                                                        echo '<a href="'.$seostats["pages_indexed"]->goo.'" class="spy-icon spy-icon-eye" id="goo"></a>';
                                                    }else{
                                                        echo '<span id="goo">'.$seostats["pages_indexed"]->goo.'</span>';
                                                    }
                                                }else{
                                                    echo '<span id="goo">N/A</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="entry google">
                                        <div class="left">
                                            <span class="spy-icon-google spy-icon"></span>Google
                                        </div>
                                        <div class="right">
                                            <?php 
                                                if( isset($seostats["pages_indexed"]->google) ){
                                                    if( strstr( $seostats["pages_indexed"]->google, 'http' ) != "" ){
                                                        echo '<a href="'.$seostats["pages_indexed"]->google.'" class="spy-icon spy-icon-eye" id="google_page_indexed"></a>';
                                                    }else{
                                                        echo '<span id="google_page_indexed">'.$seostats["pages_indexed"]->google.'</span>';
                                                    }
                                                }else{
                                                    echo '<span id="google_page_indexed">N/A</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="entry sogou">
                                        <div class="left">
                                            <span class="spy-icon-sogou spy-icon"></span>Sogou
                                        </div>
                                        <div class="right">
                                            <?php 
                                                if( isset($seostats["pages_indexed"]->sogou) ){
                                                    if( strstr( $seostats["pages_indexed"]->sogou, 'http' ) != "" ){
                                                        echo '<a href="'.$seostats["pages_indexed"]->sogou.'" class="spy-icon spy-icon-eye" id="sogou"></a>';
                                                    }else{
                                                        echo '<span id="sogou">'.$seostats["pages_indexed"]->sogou.'</span>';
                                                    }
                                                }else{
                                                    echo '<span id="sogou">N/A</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="entry yahoo">
                                        <div class="left">
                                            <span class="spy-icon-yahoo spy-icon"></span>Yahoo
                                        </div>
                                        <div class="right">
                                            <?php 
                                                if( isset($seostats["pages_indexed"]->yahoo) ){
                                                    if( strstr( $seostats["pages_indexed"]->yahoo, 'http' ) != "" ){
                                                        echo '<a href="'.$seostats["pages_indexed"]->yahoo.'" class="spy-icon spy-icon-eye" id="yahoo"></a>';
                                                    }else{
                                                        echo '<span id="yahoo">'.$seostats["pages_indexed"]->yahoo.'</span>';
                                                    }
                                                }else{
                                                    echo '<span id="yahoo">N/A</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="entry yandex">
                                        <div class="left">
                                            <span class="spy-icon-yandex spy-icon"></span>Yandex
                                        </div>
                                        <div class="right">
                                            <?php 
                                                if( isset($seostats["pages_indexed"]->yandex) ){
                                                    if( strstr( $seostats["pages_indexed"]->yandex, 'http' ) != "" ){
                                                        echo '<a href="'.$seostats["pages_indexed"]->yandex.'" class="spy-icon spy-icon-eye" id="yandex"></a>';
                                                    }else{
                                                        echo '<span id="yandex">'.$seostats["pages_indexed"]->yandex.'</span>';
                                                    }
                                                }else{
                                                    echo '<span id="yandex">N/A</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="entry _360">
                                        <div class="left">
                                            <span class="spy-icon-360 spy-icon"></span>360
                                        </div>
                                        <div class="right">
                                            <?php 
                                                if( isset($seostats["pages_indexed"]->_360) ){
                                                    if( strstr( $seostats["pages_indexed"]->_360, 'http' ) != "" ){
                                                        echo '<a href="'.$seostats["pages_indexed"]->_360.'" class="spy-icon spy-icon-eye" id="360"></a>';
                                                    }else{
                                                        echo '<span id="360">'.$seostats["pages_indexed"]->_360.'</span>';
                                                    }
                                                }else{
                                                    echo '<span id="360">N/A</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer no-border"></div>
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