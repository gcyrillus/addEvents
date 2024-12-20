<?php if(!defined('PLX_ROOT')) exit; 

	/**
		* Plugin 			addEvents page statique
		*
		* @CMS required		PluXml 
		* @version			4.1.0
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
	
	$plug = $this->plxMotor->plxPlugins->getInstance(basename(__DIR__));
	if(class_exists('plxMyMultiLingue')) {
		$mtlng ='(_'.$this->plxMotor->aConf['default_lang'].')';
		$mlng = $this->defaultLang(false).'/';
	} 
	else {
		$mtlng ='';
		$mlng='';	
	}
	$pattern = '/^\d{4}'.$mtlng.'$/';
	$keys = array_keys($plug->getParams());
	$result = preg_grep($pattern, $keys);
	$result = array_flip($result);
	$orderedByDate = array();
	foreach($result as $artnum => $v) {
		if($plug->getParam('past') == 0 && isset(json_decode($plug->getParam($artnum))[0]) && json_decode($plug->getParam($artnum))[0] < date('Y-m-d') ) continue;
		if($plug->getParam($artnum) !='') $orderedByDate[$artnum]= json_decode($plug->getParam($artnum))[0];		
	}
	asort($orderedByDate);
	if($plug->getParam('tri') =='desc') arsort($orderedByDate);

	# Pagination
	# nombre de Commentaire à afficher par page
	$bypage  = $plug->getParam('bypage')=='' ? 5: $plug->getParam('bypage');
	
	#  1 au lieu de 0 pour afficher les liens de chaque page
	$intermediaire = $plug->getParam('intermediaire')=='' ? 0: $plug->getParam('intermediaire');
	
	#  1 pour afficher la derniere page des commentaires par défaut
	$showLast = $plug->getParam('showLast')=='' ? 0: $plug->getParam('showLast'); 
	
	#########################################
	# FIN configuration
	#########################################
	
	
	# comptage des événements
	$num = count($orderedByDate);	
   
	# Style barre pagination commentaires
	echo '</p><style>
	:where(.page-item.page-link.active) { text-decoration:underline;font-weight:bold; padding:0.3em 2em;}
	.pagination.text-center.center.bordered {border-radius: 5px;width:max-content;  margin:auto;  border:solid 1px}
	#static-events {color:#ff7f50;font-weight:bold;}
	#static-events .pagination.text-center.center.bordered {border-color:#B5E61D;background:#FFFFF0}
	#static-events .page-link:not(.active) {background:#B5E61D;box-shadow:2px 2px 2px silver;color: slategray;}
	#static-events .page-link:not(.active):hover {background:#a6d11b;}
	</style>';
	
	#############################
	# extraction et maj variables
	#############################
	
	# extraction de l'url
	$url = $this->plxMotor->urlRewrite('?'.$mlng.$plug->getParam('url').'/');
	
	# generation du lien
	$link = $this->plxMotor->urlRewrite($url."/page");                
	// On calcule le nombre de pages total
	$nbr = $num;
	$pages = ceil( $nbr / $bypage);
	$position = 1;
	if($showLast==1) $position = $pages;                
	# extraction du numéro de page dans l'URL 
	$currentPage = preg_match('#\bpage(\d*)#',$_SERVER['REQUEST_URI'], $capture) ? intval($capture[1]) : $position;  
	
	# indice de début, premier article à afficher
	$start = ($currentPage - 1) * $bypage;  
	
	// Calcul du 1er commentaire de la page
	$premier = ($currentPage * $bypage) - $bypage;
	$orderedByDate = array_slice($orderedByDate, $premier, $bypage);
	
	// fin mise en place pagination

	echo '<div>'.$plug->getLang('L_INTRO_STATIC').'('.$num.')</div>';
	
	############################
	# Affichage des articles ev.
	############################
	$articles=array();
	

	foreach($orderedByDate as $k => $v){
	if(isset($this->plxMotor->plxGlob_arts->aFiles[substr($k,0,4)])){
		$articles[substr($k,0,4)]=$this->plxMotor->plxGlob_arts->aFiles[substr($k,0,4)];
		$art = $this->plxMotor->parseArticle('data/articles/'.$mlng.$this->plxMotor->plxGlob_arts->aFiles[substr($k,0,4)]);
		$linkToArticle = $this->plxMotor->urlRewrite('index.php?'.$mlng.'article'.intval($art['numero']).'/'.$art['url']);
		include 'tpl.article.php';
	}
	}	
	############################
	# Affichage de la pagination
	############################
	if($pages>1){
	?>
	<nav id="static-events">
		<ul class="pagination text-center center bordered">
			<!-- Lien vers la page précédente (si on ne se trouve pas sur la 1ère page) -->
			<?= ($currentPage > 1)  ? "<li class=\"page-item\" ><a href=\"".$link . ($currentPage - 1) ."\" class=\"page-link\">".L_PAGINATION_PREVIOUS."</a></li>" : "" ?>			
			<?php if($intermediaire == 1)  {
				for($page = 1; $page <= $pages; $page++) {
					# Lien vers chacune des pages (activé si on se trouve sur la page correspondante
					echo '<li class="page-item ';
					if($currentPage == $page)  echo 'active';
					echo "\"><a href=\"".$link.$page ."\" class=\"page-link\">".$page."</a></li>";
				}
			}
			else {
				echo "<li class=\"page-item page-link  active \">
				".$currentPage." / ". $pages."
				</li>"; 				
			}   ?>
			<!-- Lien vers la page suivante (si on ne se trouve pas sur la dernière page) -->
			<?= ($currentPage < $pages) ? " <li class=\"page-item\"><a href=\"".$link.($currentPage + 1 )."\" class=\"page-link\">".L_PAGINATION_NEXT."</a></li>" : "" ?>			
		</ul>
	</nav>
<?php   }