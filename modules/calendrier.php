<?php
	/**
		* Plugin 			addEvents module calendrier
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
	echo '<?php
	$plxPlug = $plxShow->plxMotor->plxPlugins->getInstance(\'addEvents\'); ?>';?>
	<h3><?= '<?php $plxPlug->lang(\'L_CALENDAR_EVENT\')?>'; ?></h3>
	<script src="plugins/addEvents/modules/js/vanilla-calendar.js"></script>
	<link rel="stylesheet" href="plugins/addEvents/modules/css/vanilla-calendar.css">
	<div id="calendrier" class="vanilla-calendar"></div>
	<p id="event_infos"></p>
	<script>
		const show = document.querySelector('#event_infos');
		let myCalendar = new VanillaCalendar({
			selector: "#calendrier",
			pastDates: true,		
			availableDates: [<?php		
		echo '<?php 
			if(class_exists(\'plxMyMultiLingue\')) {
				$mtlng =\'(_\'.$plxShow->plxMotor->aConf[\'default_lang\'].\')\';
			}
			else {
				$mtlng =\'\';
			}
			$plxPlug = $plxShow->plxMotor->plxPlugins->getInstance(\'addEvents\'); 
			$pattern = \'/^\d{4}\'.$mtlng.\'$/\';
			$keys = array_keys($plxPlug->getParams());
			$result = preg_grep($pattern, $keys);
			$result = array_flip($result);
			$orderedByDate = array();
			$eventsByDate = array();
			
			foreach($result as $artnum => $v) {
				if($plxPlug->getParam($artnum) !=\'\') {
					$orderedByDate[substr($artnum,0,4)]= $plxPlug->getParam($artnum);
					if(isset($plxShow->plxMotor->plxGlob_arts->aFiles[substr($artnum,0,4)])) $eventsByDate[$plxPlug->getParam($artnum)][]= substr($artnum,0,4);
				}
			}
			if(class_exists(\'plxMyMultiLingue\')) $mlng = $plxShow->defaultLang(false).\'/\';
			else $mlng=\'\';
			foreach($eventsByDate as $dates =>$evs) {
				$d= json_decode($dates)[0];
				$events_link =\'\';	
				$format="{ date: \'#event_date\', event: \'#event_links\', hover: \'<span class=\"tooltip\">#art_date <br> #art_title</span>\'},".PHP_EOL;
				$dateEvent = plxDate::formatDate(str_replace(\'-\',\'\',str_pad($d,4, \'0\', STR_PAD_LEFT)).\'8000\', $plxPlug->dateFormat );
				$format = str_replace(\'#event_date\', $d , $format );
				$format = str_replace(\'#art_date\', $dateEvent , $format );
				$nbEvents= count($evs);
				if($nbEvents > 1) {				
					$format= str_replace(\'#art_title\', $nbEvents.\' événements\' , $format );
						foreach($evs as $k => $artId){					
							$linksFormat = \'<a href=\"#art_url\" title=\"#art_date\" class=\"#art_status\">#art_title</a>\';
							$art = $plxShow->plxMotor->parseArticle(\'data/articles/\'.$mlng.$plxShow->plxMotor->plxGlob_arts->aFiles[$artId]);
							$uri = $plxShow->plxMotor->urlRewrite(\'index.php?\'.$mlng.\'article\'.intval($art[\'numero\']).\'/\'.$art[\'url\']);
							$linksFormat = str_replace(\'#art_url\', $uri, $linksFormat  );
							$linksFormat = str_replace(\'#art_date\', $dateEvent, $linksFormat  );
							if($plxShow->mode() ==\'article\' && $plxShow->artId() == $artId ) $linksFormat = str_replace(\'#art_status\', \'active\' , $linksFormat  ); 
							else  $linksFormat = str_replace(\'#art_status\', \'inactive\' , $linksFormat  ); 
							$linksFormat = str_replace(\'#art_title\', str_replace("\'","\\\'",$art[\'title\']), $linksFormat  );
							
							$events_link .= $linksFormat;
						}
						
					}else {
						$linksFormat = \'<a href=\"#art_url\" title=\"#art_date\" class=\"#art_status\">#art_title</a>\';
						$art = $plxShow->plxMotor->parseArticle(\'data/articles/\'.$mlng.$plxShow->plxMotor->plxGlob_arts->aFiles[$evs[0]]);
						$format= str_replace(\'#art_title\', str_replace("\'","\\\'",$art[\'title\']) , $format );
						$uri = $plxShow->plxMotor->urlRewrite(\'index.php?\'.$mlng.\'article\'.intval($art[\'numero\']).\'/\'.$art[\'url\']);
						$linksFormat = str_replace(\'#art_url\', $uri, $linksFormat  );
						$linksFormat = str_replace(\'#art_date\', $dateEvent, $linksFormat  );
						if($plxShow->mode() ==\'article\' && $plxShow->artId() == $evs[0] ) $linksFormat = str_replace(\'#art_status\', \'active\' , $linksFormat  ); 
						else  $linksFormat = str_replace(\'#art_status\', \'inactive\' , $linksFormat  ); 
						$linksFormat = str_replace(\'#art_title\', str_replace("\'","\\\'",$art[\'title\']), $linksFormat  );
						
						$events_link .= $linksFormat;
						
						}
				$format= str_replace(\'#event_links\', $events_link , $format );
				echo PHP_EOL.$format.PHP_EOL;
			}
			?>';
	?>		],
			datesFilter: true,
			onSelect: (data) => {   
			show.innerHTML=data.data.event;
		  },
			onHover: (data) => { 
			this.insertAdjacentHTML('beforeend',data.hover.hover);
		  },
			button_prev: show.innerHTML='',
			button_next: show.innerHTML='',
	});
	</script>