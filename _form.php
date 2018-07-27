<div class="wpspy-form">
	<iframe src="about:blank" id="remember" name="remember" class="hidden"></iframe>
	<form method="post" action="" id="form_wpspy" target="remember">
		<input	type="text" name="wpspy_url" id="wpspy_url" placeholder="www.example.com"
			value="<?php echo isset($_GIVEN_URL) && trim($_GIVEN_URL) != "" ? $_GIVEN_URL : ''; ?>"/>
		<input type="submit" class="wpspy_btn" name="wpspy_submit" data-page="<?php echo str_replace("wpspy-", "", $page)?>" id="wpspy_submit" value="Go" />
	</form>
</div>