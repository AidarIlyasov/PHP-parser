<!DOCTYPE>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Название</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css" type="text/css">
  <style>
  body,html{
  	height: 100%;
  }
  	td[align="left"] > a > b{
		color: red !important;
  	}
  	img.preloader{
	    position: absolute;
	    top: 50%;
	    left: 0;
	    right: 0;
	    margin: -200px auto 0px;
	    z-index: 99;
	}
	.scroll-down{
		width: 100%;
		text-align: center;
		display: none;
	}
	.scroll-down img{
		width: 80px;
	}
	#thepage{
		display: block!important;
	}
	.wrapper{
		display: -webkit-flex;
		display: -moz-flex;
		display: -ms-flex;
		display: -o-flex;
		display: flex;
		-ms-align-items: center;
		align-items: center;
		height: 100%;
		-webkit-flex-wrap: wrap;
		-moz-flex-wrap: wrap;
		-ms-flex-wrap: wrap;
		-o-flex-wrap: wrap;
		flex-wrap: wrap;
	}
	.person_info{
		display: -webkit-flex;
		display: -moz-flex;
		display: -ms-flex;
		display: -o-flex;
		display: flex;
		justify-content: space-around;
		margin: 0 auto;
	}
  </style>
</head>
<body>
	<!-- <img class="preloader" src="./img/Custom-loader-ABT.gif" alt=""> -->
	<div class="wrapper">
	  <form action="index.php" method="GET" class="person_info col-md-8 md-8">
	    <input type="text" class="form-control  md-8 col-md-8" name="personInfo" placeholder="Фамилия Имя Отчество">
	    <input type="submit" name="submit" style="font-size: 1.1rem" class="btn btn-primary btn-lg btn-bloc col-md-2" id="submit" value="Обработать">
	  </form>
	  <!-- <div class="scroll-down"><img class="scroll-down" src="./img/scroll-down-1.gif" alt=""></div> -->
	</div>  
	<?php 
		include_once("libs/curl_jquery.php");
		include_once("libs/simple_html_dom.php");		
		
		if(isset($_GET["submit"])){
			$nameAutor = "$_GET[personInfo]";
		$url_auth = "https://elibrary.ru/";
		$curentPage = 1;
		$aut_data = [
			"login"      => "Ilyasov_Aidar",
			"password"   => "microlab06",
			"knowme"     => "on",
			"surname"    => $nameAutor,
			"sortorder"  => 0,
			"order"      => 1,
			"codetype"   => "SPIN",
			"autorboxid" => 0
		];


		$result = curl_get($url_auth,$url_auth,$aut_data);
		$autorPages = str_get_html($result);
		// echo $autorPages;
		$link = $autorPages->find("a[href='/authors.asp']",0)->href;
		$result2 = curl_get($url_auth.$link,$url_auth,$aut_data);
		$autorPages2 = str_get_html($result2);
		$topInfo = $autorPages2->find("td[class='midtext']");

		// echo $autorPages2;
		$info = $topInfo[2].":  ".$topInfo[7].",  ".$topInfo[3].":  ".$topInfo[8]."   ".$topInfo[4].":  ".$topInfo[9]."\r\n";
		
		// $a1 = $autorPages2->find("a[title='Список публикаций данного автора в РИНЦ']",0)->href;
		$a3 = $autorPages2->find("a[title='Список публикаций данного автора в РИНЦ']",0)->href;
		// $a3 = $autorPages2->find("a[title='Анализ публикационной активности автора']",0)->href;
		$page = curl_get($url_auth.$a1,$link,$aut_data);
		$newPage = str_get_html($page);

		$pageAutinfoShort = curl_get($url_auth.$a3,$url_auth,$aut_data);
		$pageAutinfo = str_get_html($pageAutinfoShort);
		// echo $pageAutinfo;
		$a4 = $pageAutinfo->find("td[width:'580px'] > td[class='midtext']");


		$aVakLink =$pageAutinfo->find("a[title='Полный список публикаций автора на портале elibrary.ru']",0)->href;		

		$altInfo = $a4[2]."  ".$a4[3].", \r\n".$a4[4]."  ".$a4[5].", \r\n".$a4[6]."  ".$a4[7].", \r\n".$a4[8]."  ".$a4[9].", \r\n".$a4[10]."  ".$a4[11].", \r\n".$a4[42]."  ".$a4[43].", \r\n".$a4[54]."  ".$a4[55].",  \r\n".$a4[44]."  ".$a4[45]."  \r\n";

		$AName = explode(" ",$nameAutor);

		$AName = $AName[0]." ".mb_substr($AName[1],0,1,'UTF-8').".".mb_substr($AName[2],0,1,'UTF-8');

		$ANameFile = iconv("UTF-8", "windows-1251", $AName);
		$fp = fopen("result/".$ANameFile.".txt", "a+");
		$paginationCount = ceil(intval($autorPages2->find("a[title='Список публикаций данного автора в РИНЦ']",0)->plaintext)/20);
		echo $paginationCount."<br><hr>";
		fwrite($fp, $nameAutor."\r\n");

		for ($curentPage; $curentPage <= $paginationCount; $curentPage++) {
			$aut_data["pagenum"] = $curentPage;
			$vak = curl_get($url_auth.$a3,$url_auth,$aut_data);
			$vakPage = str_get_html($vak);
		 	
			foreach ($vakPage->find("tr[id^='arw']") as $element) {
				 $a = $element->find("a[href^='/item.asp']",0);
				 $postLink = $a->href;
				 $full_text = mb_strtolower($a->plaintext);	
					
				 $e = $element ->find("font");

				 // echo $e[2];
				 // preg_match('/^(2017)/',$e[2]->plaintext,$match);
				 $num = intval(mb_substr(strval(preg_replace('/\D/','', $e[2]->plaintext)),0,4,'UTF-8'));
				 // echo $num."<br>";
				 if($num < 2014){
				 	break 2;
				 }
					
				 $soAutors  = $element->find("font",1)->plaintext;
				 $full_text = $element->find("font",2)->plaintext;

				 $arar = explode(".",$full_text);
				 $dateText = array();
				 for($i = count($arar); $i > 0;$i--){
				 	$dateText[] = $arar[$i].".";
				 }
				 $dateText = implode(array_reverse($dateText));
				 $postPage = curl_get("https://elibrary.ru/".$postLink);
				 $postPage = str_get_html($postPage);
				 $typeDocument = $postPage->find("td[width:'574px'] > font[color='#00008f']");
				 $postOrg = $postPage->find("a[href^='publisher_']")->plaintext;

				 $CityBlock = $postPage->find("td[width:'504px'] > a[href^='publisher_']");


				 foreach ($CityBlock as $k => $publisher) {

				 }
				 preg_match('/\(([^()]*)\)/', $publisher->parent()->plaintext, $m);
				 

				 $result =  $AName." ".$a->plaintext." / ".$soAutors." // ".$typeDocument[24]->plaintext."-".$m[0].": Изд-во ".$publisher->plaintext.",".$dateText."\r\n";
				 echo $result."<br>"."\r\n";
				 fwrite($fp, $result);	
				 // break;	
			}
			
		} 
		// fwrite($fp, "Все публикации на elibrary.ru \r\n");
		// foreach($newPage->find("tr[id^='arw']") as $element) {
		//  $a = $element->find("a",0);

		//  $postLink = $a->href;
		//  $full_text = mb_strtolower($a->plaintext);
		
		//  $soAutors  = $element->find("font",1)->plaintext;
		//  $full_text = $element->find("font",2)->plaintext;

		//  $arar = explode(".",$full_text);
		//  $dateText = array();
		//  for($i = count($arar); $i > 0;$i--){
		//  	$dateText[] = $arar[$i].".";
		//  }
		//  $dateText = implode(array_reverse($dateText));
		//  $postPage = curl_get("https://elibrary.ru/".$postLink);
		//  $postPage = str_get_html($postPage);
		//  $typeDocument = $postPage->find("td[width:'574px'] > font[color='#00008f']");
		//  $postOrg = $postPage->find("a[href^='publisher_']")->plaintext;

		//  $CityBlock = $postPage->find("td[width:'504px'] > a[href^='publisher_']");


		//  foreach ($CityBlock as $k => $publisher) {

		//  }
		//  preg_match('/\(([^()]*)\)/', $publisher->parent()->plaintext, $m);
		 

		//  $result =  $AName." ".$a->plaintext." / ".$soAutors." // ".$typeDocument[24]->plaintext." — ".$m[0].": Изд-во ".$publisher->plaintext.",".$dateText."\r\n";
		//  echo $result."<br>"."\r\n";
		//  fwrite($fp, $result);
		//  break;

		// }
		fclose($fp);
	}


	?>
	<script>
	  window.onload = function(){
	  	document.getElementsByClassName("preloader")[0].style.display = 'none';
	  	document.getElementsByClassName("scroll-down")[0].style.display = "block";
	  }
	</script>
</body>
</html>
	