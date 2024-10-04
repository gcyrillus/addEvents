<h3>Rendez-vous</h3>
<ul class="event-list unstyled-list">
<?php
	echo '<?php 
		$plxPlug = $plxShow->plxMotor->plxPlugins->getInstance(\'addEvents\'); 
		$pattern = \'/^\d{4}$/\';
		$keys = array_keys($plxPlug->getParams());
		$result = preg_grep($pattern, $keys);
		$result = array_flip($result);
		$result = array_slice($result, 0, 5);
		$orderedByDate = array();
		foreach($result as $artnum => $v) {
			if($plxPlug->getParam($artnum) !=\'\') $orderedByDate[$artnum]= $plxPlug->getParam($artnum);		
		}
		arsort($orderedByDate);
		$linkarticles=PHP_EOL;;
		foreach($orderedByDate as $k => $v){
			ob_start();
			$art = $plxShow->plxMotor->parseArticle(\'data/articles/\'.$plxShow->plxMotor->plxGlob_arts->aFiles[$k]);		
			$uri = $plxShow->plxMotor->urlRewrite(\'index.php?article\'.intval($art[\'numero\']).\'/\'.$art[\'url\']);/////\'index.php?article\'.intval($k).\'/\'.$art[\'url\'];
			$format=\'<li><a class="#art_status" href="#art_url" title="#art_title">#art_title</a></li>\';
			$articles[$k]=$plxShow->plxMotor->plxGlob_arts->aFiles[$k];
			 $format = str_replace(\'#art_url\', $uri, $format  );
			 $date = plxDate::formatDate(str_replace(\'-\',\'\',$plxPlug->getParam(str_pad($k,4, \'0\', STR_PAD_LEFT))).\'8000\', $plxPlug->dateFormat );
			 $format = str_replace(\'#art_title\', $date , $format );
			 if(date(\'Y-m-d\') > $plxPlug->getParam($k)) $format = str_replace(\'#art_status\', \'#art_status past\' , $format );
			 if($plxShow->mode() ==\'article\' && $plxShow->artId() == $k )  $format = str_replace(\'#art_status\', \'active\' , $format );
			 else   $format = str_replace(\'#art_status\', \'inactive\' , $format );
			echo ob_get_clean().$format;
		}		
		?>';
?></ul>