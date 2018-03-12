<?php
	function curl_get($url, $referer = "http://www.google.com/",$personalData = [],$search_info = []){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_COOKIEJAR, "d://cookie.txt");
		curl_setopt($ch, CURLOPT_COOKIEFILE,"d://cookie.txt");
		// curl_setopt($ch, CURLOPT_PROXY, "154.119.50.246:53281")
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($personalData));
		// curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($search_info));
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Geco/20100101 Firefox/58.0.2");
		curl_setopt($ch,CURLOPT_REFERER, $referer);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, flase);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
?>