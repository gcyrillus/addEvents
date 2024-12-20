<?php
	/**
		* Plugin 			addEvents module today
		*
		* @CMS required		PluXml 
		* @version			3.1
		* @date				2024-10-13
		* @author 			G.Cyrillus
		░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
		░       ░░  ░░░░░░░  ░░░░  ░  ░░░░  ░░      ░░       ░░░      ░░  ░░░░░░░        ░░      ░░░░░   ░░░  ░        ░        ░
		▒  ▒▒▒▒  ▒  ▒▒▒▒▒▒▒  ▒▒▒▒  ▒▒  ▒▒  ▒▒  ▒▒▒▒  ▒  ▒▒▒▒  ▒  ▒▒▒▒  ▒  ▒▒▒▒▒▒▒▒▒▒  ▒▒▒▒  ▒▒▒▒▒▒▒▒▒▒    ▒▒  ▒  ▒▒▒▒▒▒▒▒▒▒  ▒▒▒▒
		▓       ▓▓  ▓▓▓▓▓▓▓  ▓▓▓▓  ▓▓▓    ▓▓▓  ▓▓▓▓  ▓       ▓▓  ▓▓▓▓  ▓  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓      ▓▓▓▓▓  ▓  ▓  ▓      ▓▓▓▓▓▓  ▓▓▓▓
		█  ███████  ███████  ████  ██  ██  ██  ████  █  ███████  ████  █  ██████████  ██████████  ████  ██    █  ██████████  ████
		█  ███████        ██      ██  ████  ██      ██  ████████      ██        █        ██      ██  █  ███   █        ████  ████
		█████████████████████████████████████████████████████████████████████████████████████████████████████████████████████████
	**/	
	echo '<?php 
	$today = date(\'Y-m-d\');
	if(class_exists(\'plxMyMultiLingue\')) {
		$mtlng =\'(_\'.$plxShow->plxMotor->aConf[\'default_lang\'].\')\';
		$mlng = $plxShow->defaultLang(false).\'/\';
	}
	else {
		$mtlng =\'\';
		$mlng=\'\';
	}
	$plxPlug = $plxShow->plxMotor->plxPlugins->getInstance(\'addEvents\'); 
	$pattern = \'/^\d{4}\'.$mtlng.\'$/\';
	$keys = array_keys($plxPlug->getParams());
	$result = preg_grep($pattern, $keys);
	foreach($result as $artnum => $v) {
		if(class_exists(\'plxMyMultiLingue\')) $v = substr($v,0,4);		
		if(isset(json_decode($plxPlug->getParam($v.substr($mtlng,1,-1)))[0]) && json_decode($plxPlug->getParam($v.substr($mtlng,1,-1)))[0] == $today) {		
			echo \'<div class="today"><link rel="stylesheet" href="plugins/addEvents/modules/css/today.css">\';
			$art = $plxShow->plxMotor->parseArticle(\'data/articles/\'.$mlng.$plxShow->plxMotor->plxGlob_arts->aFiles[$v]);		
			$uri = $plxShow->plxMotor->urlRewrite(\'index.php?\'.$mlng.\'article\'.intval($art[\'numero\']).\'/\'.$art[\'url\']);
			echo \'<p><a href="\'.$uri.\'" title="\'.$art[\'title\'].\'">C\\\'est Aujourd\\\'Hui !</a></p>\';
			if($art[\'thumbnail\'] !=\'\') {
				echo \'<figure><img class="art_thumbnail" src="\'.$art[\'thumbnail\'].\'" alt="\'.$art[\'thumbnail_alt\'].\'"/><figcation>\'.$today.\'</figcaption></figure>\';
			}
			else {
				echo \'<p>\'.$art[\'title\'].\'</p>\';
			}
		echo \'</div>\';
		}	
	}?>';


