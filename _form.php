<div class="wpspy-form">
	<iframe src="about:blank" id="remember" name="remember" class="hidden"></iframe>
	<form method="post" action="" id="form_wpspy" target="remember" class="form form-inline">
		<div class="input-group">
            <input	type="text" name="wpspy_url" id="wpspy_url" placeholder="www.example.com" class="form-control"
				value="<?php echo isset($_GIVEN_URL) && trim($_GIVEN_URL) != "" ? $_GIVEN_URL : ''; ?>"/>
            <span class="input-group-btn">
            	<button type="submit" class="btn btn-primary" name="wpspy_submit" data-page="<?php echo str_replace("wpspy-", "", $page); ?>" id="wpspy_submit">
					Go
				</button>
            </span>
          </div>
	</form>
</div>