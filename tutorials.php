<?php 
	$page = 'wpspy-tutorials';
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
            <div class="post">
           
	            <div class="col-md-12 tutorial">
	            	<div class="col-md-4 text-green">
						<div class="title right">
							<i class="ion ion-android-film"></i>
							 Full Demo
						</div>
						<div class="content right">
							See it in action.
						</div>
					</div>
					<div class="col-md-7">
						<div class="embed-responsive embed-responsive-16by9">
						 	<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/zSUrTPmK39o?ecver=2"></iframe>
						</div>
					</div>		
				</div>
			 </div>
        </section>
    </div>
</div>