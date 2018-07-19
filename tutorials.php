<div class="wpspy-wrapper">
	<?php 
		$page = 'wpspy-tutorials';
		include plugin_dir_path( __FILE__ )."classes/config.php";
		include plugin_dir_path( __FILE__ )."_nav.php"; 
		include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
		include plugin_dir_path( __FILE__ )."classes/data.php";
	
		// $data = @json_decode(@file_get_contents("http://topdogimsoftware.com/spyvideos/tuts.php?type=pro&plugin=wpspy"));
	?>
	<style type="text/css">
	.loading{ display: none; }
	</style>
	<div class="wpspy-content">		
		<div class="wpspy-results row">
			<?php 
				// foreach ($data as $key):
					?>
					<!-- first item -->
					<!-- <div class="col-12 tutorial">
						<div class="col-7">
							<?php 
								// @preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $key->link, $matches);
								
							?>
							<iframe class="video" width="380" height="315" src="http://youtube.com/embed/<?php // echo $matches[0]; ?>">
							</iframe>
						</div>
						<div class="col-4">
							<div class="title right"><?php // echo $key->title;?></div>
							<div class="content right">
								<?php // echo $key->content; ?>
							</div>
						</div>		
					</div> -->
					<?php
				// endforeach;
			?>

			<div class="col-12 tutorial">
				<div class="col-7">
					<iframe class="video" width="380" height="315" src="https://www.youtube.com/embed/zSUrTPmK39o?ecver=2">
					</iframe>
				</div>
				<div class="col-4">
					<div class="title right">
						Full Demo
					</div>
					<div class="content right">
						See it in action.
					</div>
				</div>		
			</div>
		</div>
	</div>
</div>