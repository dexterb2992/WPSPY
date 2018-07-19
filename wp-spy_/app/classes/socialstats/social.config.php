<?php

class SocialConfig{
	
	function FB(){
		$cfg = new stdClass();
		$cfg->AppId = '596073470493831';
		$cfg->AppSecret = 'e8d02ed0a87a7ca4290c20fe11f9b673';
		return $cfg;
	}

	function Twitter(){
		$cfg = new stdClass();
		$cfg->ApiKey = 'eIWVabsiPRW8pS44NXFpe182v'; // Consumer Key
		$cfg->ApiSecret = 'EoSq57TPhQZ5edKaLHDtQxlEae87pYWig613hlHVMNI90Wnr2X'; // Consumer Secret
		$cfg->AccessToken = '1910391642-vgr7EaFtVVKu8MXVkJTueY8XfhFIOn6x1BDF8hi';
		$cfg->AccessTokenSecret = 'rir3zGOvP1513YWkaLOAohPshSpyNAncVZsN0PCSrEV4d';
		return $cfg;
	}
}