<?php 
	/**
		* Plugin 			addEvents module list
		*
		* @CMS required		PluXml 
		* @version			4.0
		* @date				2024-12-03
		* @author 			G.Cyrillus
		░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
		░       ░░  ░░░░░░░  ░░░░  ░  ░░░░  ░░      ░░       ░░░      ░░  ░░░░░░░        ░░      ░░░░░   ░░░  ░        ░        ░
		▒  ▒▒▒▒  ▒  ▒▒▒▒▒▒▒  ▒▒▒▒  ▒▒  ▒▒  ▒▒  ▒▒▒▒  ▒  ▒▒▒▒  ▒  ▒▒▒▒  ▒  ▒▒▒▒▒▒▒▒▒▒  ▒▒▒▒  ▒▒▒▒▒▒▒▒▒▒    ▒▒  ▒  ▒▒▒▒▒▒▒▒▒▒  ▒▒▒▒
		▓       ▓▓  ▓▓▓▓▓▓▓  ▓▓▓▓  ▓▓▓    ▓▓▓  ▓▓▓▓  ▓       ▓▓  ▓▓▓▓  ▓  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓      ▓▓▓▓▓  ▓  ▓  ▓      ▓▓▓▓▓▓  ▓▓▓▓
		█  ███████  ███████  ████  ██  ██  ██  ████  █  ███████  ████  █  ██████████  ██████████  ████  ██    █  ██████████  ████
		█  ███████        ██      ██  ████  ██      ██  ████████      ██        █        ██      ██  █  ███   █        ████  ████
		█████████████████████████████████████████████████████████████████████████████████████████████████████████████████████████
	**/	
	echo'<?php
	$plxPlug = $plxShow->plxMotor->plxPlugins->getInstance(\'addEvents\'); ?>';
?>
	<h3><?= '<?= $plxShow->plxMotor->aCats[$plxPlug->event_cat][\'name\']?>'; ?></h3>
<ul class="event-list unstyled-list">
<?php
	echo '<?php 
		if(class_exists(\'plxMyMultiLingue\')) {
			$mtlng =\'(_\'.$plxShow->plxMotor->aConf[\'default_lang\'].\')\';
		}
		else {
			$mtlng =\'\';
		}
		
		$pattern = \'/^\d{4}\'.$mtlng.\'$/\';
		$keys = array_keys($plxPlug->getParams());
		$result = preg_grep($pattern, $keys);
		$result = array_flip($result);
		
		$orderedByDate = array();
		foreach($result as $artnum => $v) {
			if($plxPlug->getParam($artnum) !=\'\') $orderedByDate[substr($artnum,0,4)]= json_decode($plxPlug->getParam($artnum))[0];		
		}
		arsort($orderedByDate);
		$orderedByDate = array_slice($orderedByDate, 0, 5);
		$linkarticles=PHP_EOL;
		if(class_exists(\'plxMyMultiLingue\')) $mlang = $plxShow->defaultLang(false).\'/\';
		else $mlang=\'\';
		foreach($orderedByDate as $k => $v){
			if(isset($plxShow->plxMotor->plxGlob_arts->aFiles[$k])) {
			ob_start();
			$art = $plxShow->plxMotor->parseArticle(\'data/articles/\'.$mlang.$plxShow->plxMotor->plxGlob_arts->aFiles[$k]);		
			$uri = $plxShow->plxMotor->urlRewrite(\'index.php?\'.$mlang.\'article\'.intval($art[\'numero\']).\'/\'.$art[\'url\']);/////\'index.php?article\'.intval($k).\'/\'.$art[\'url\'];
			$format=\'<li><a class="#art_status" href="#art_url" title="#art_title">#art_title</a></li>\';
			$articles[$k]=$plxShow->plxMotor->plxGlob_arts->aFiles[$k];
			 $format = str_replace(\'#art_url\', $uri, $format  );
			 $date = plxDate::formatDate(str_replace(\'-\',\'\',json_decode($plxPlug->getParam($k.substr($mtlng , 1 , -1 )))[0]).\'0000\', $plxPlug->dateFormat );
			 $format = str_replace(\'#art_title\', $date , $format );
			 if(date(\'Y-m-d\') > json_decode($plxPlug->getParam($k.substr($mtlng,1,-1)))[0]) $format = str_replace(\'#art_status\', \'#art_status past\' , $format );
			 if($plxShow->mode() ==\'article\' && $plxShow->artId() == $k )  $format = str_replace(\'#art_status\', \'active\' , $format );
			 else   $format = str_replace(\'#art_status\', \'inactive\' , $format );
			echo ob_get_clean().$format;
			}
		}		
		?>';
?></ul>