<?php if(!defined('PLX_ROOT')) exit;
	/**
		* Plugin 			addEvents
		*
		* @CMS required		PluXml 
		* @version			2.3
		* @date				2024-10-04
		* @author 			G.Cyrillus
		░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
		░       ░░  ░░░░░░░  ░░░░  ░  ░░░░  ░░      ░░       ░░░      ░░  ░░░░░░░        ░░      ░░░░░   ░░░  ░        ░        ░
		▒  ▒▒▒▒  ▒  ▒▒▒▒▒▒▒  ▒▒▒▒  ▒▒  ▒▒  ▒▒  ▒▒▒▒  ▒  ▒▒▒▒  ▒  ▒▒▒▒  ▒  ▒▒▒▒▒▒▒▒▒▒  ▒▒▒▒  ▒▒▒▒▒▒▒▒▒▒    ▒▒  ▒  ▒▒▒▒▒▒▒▒▒▒  ▒▒▒▒
		▓       ▓▓  ▓▓▓▓▓▓▓  ▓▓▓▓  ▓▓▓    ▓▓▓  ▓▓▓▓  ▓       ▓▓  ▓▓▓▓  ▓  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓      ▓▓▓▓▓  ▓  ▓  ▓      ▓▓▓▓▓▓  ▓▓▓▓
		█  ███████  ███████  ████  ██  ██  ██  ████  █  ███████  ████  █  ██████████  ██████████  ████  ██    █  ██████████  ████
		█  ███████        ██      ██  ████  ██      ██  ████████      ██        █        ██      ██  █  ███   █        ████  ████
		█████████████████████████████████████████████████████████████████████████████████████████████████████████████████████████
	**/	
	class addEvents extends plxPlugin {
		
		
		
		const BEGIN_CODE = '<?php' . PHP_EOL;
		const END_CODE = PHP_EOL . '?>';
		public $lang = ''; 
		public $dateFormat;
		public $event_cat ;
		
		private $url = ''; # parametre de l'url pour accèder à la page static		
		
		
		public function __construct($default_lang) {
			# appel du constructeur de la classe plxPlugin (obligatoire)
			parent::__construct($default_lang);
			
			# droits pour accèder à la page config.php du plugin
			$this->setConfigProfil(PROFIL_ADMIN, PROFIL_MANAGER);		
			// url Page static
			$this->url = $this->getParam('url')=='' ? strtolower(basename(__DIR__)) : $this->getParam('url');	
			
			# Declaration des hooks		
			$this->addHook('AdminTopBottom', 'AdminTopBottom');
			$this->addHook('ThemeEndHead', 'ThemeEndHead');
			$this->addHook('plxShowPageTitle','plxShowPageTitle');
			$this->addHook('plxShowConstruct', 'plxShowConstruct');
			$this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
			$this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
			$this->addHook('SitemapStatics', 'SitemapStatics');
			$this->addHook('addEventswidget', 'addEventswidget');
			$this->addHook('wizard', 'wizard');
			$this->addHook('MyHEvent', 'MyHEvent');
			$this->addHook('AdminArticlePrepend', 'AdminArticlePrepend');
			$this->addHook('AdminArticleTop', 'AdminArticleTop');
			$this->addHook('plxMotorParseArticle','plxMotorParseArticle');
			$this->addHook('AdminIndexTop','AdminIndexTop');
			
			
			$this->dateFormat = $this->getParam('dateFormat') == '' ? '#day #num_day #month #num_year(4)' : $this->getParam('dateFormat');
			$this->event_cat =  $this->getParam('event_cat') == '' ? '' : $this->getParam('event_cat');
		}
		
		# Activation / desactivation
		
		public function OnActivate() {
			# code à executer à l’activation du plugin
			# activation du wizard
			$_SESSION['justactivated'.basename(__DIR__)] = true;
			if($this->getParam('event_cat') =='') include('cat.php');
			else {
				$plxAdmin = plxAdmin::getInstance();
				$plug = $plxAdmin->plxPlugins->getInstance(basename(__DIR__));
				$pattern = '/^\d{4}$/';
				$childStyle=array();
				$keys = array_keys($plug->getParams());
				$result = preg_grep($pattern, $keys);
				$result = array_flip($result);
				foreach($result as $artId => $v) { 
				$art = $plxAdmin->plxGlob_arts->query('/^'.$artId.'.(.*).xml$/','','sort',0,1,'all');
					$artFile = explode('.',$art[0]);
					$cats= explode(',',$artFile[1]);
					// on inscrit l'article dans la catégorie dédié aux évenements
					if (($key = array_search($this->event_cat, $cats)) === false){ 
							$cats[] = $this->event_cat;
							$cats = implode(',',$cats);
							$oldFile= $art[0];
							$artFile[1] = $cats;
							$artFile = implode('.',$artFile);
							if($oldFile != $artFile) rename (PLX_ROOT.'data/articles/'.$oldFile, PLX_ROOT.'data/articles/'.$artFile);
					}				
				}
			}
		}
		
		public function OnDeactivate() { echo'<pre>';
			# code à executer à la désactivation du plugin
			$plxAdmin = plxAdmin::getInstance();
			$plug = $plxAdmin->plxPlugins->getInstance(basename(__DIR__));
			$pattern = '/^\d{4}$/';
			$childStyle=array();
			$keys = array_keys($plug->getParams());
			$result = preg_grep($pattern, $keys);
			$result = array_flip($result);
			foreach($result as $artId => $v) {
				$art = $plxAdmin->plxGlob_arts->query('/^'.$artId.'.(.*).xml$/','','sort',0,1,'all');
				$artFile = explode('.',$art[0]);
				$cats= explode(',',$artFile[1]);
				// on désinscrit l'article d la catégorie dédié au évement
				if (($key = array_search($this->event_cat, $cats)) !== false){
					unset($cats[$key]);
				}
				if(count($cats) ==0) $cats[]='000'; 
				$cats = implode(',',$cats);
				$oldFile= $art[0];
				$artFile[1] = $cats;
				$artFile = implode('.',$artFile);
				if($oldFile != $artFile)	rename (PLX_ROOT.'data/articles/'.$oldFile, PLX_ROOT.'data/articles/'.$artFile);
			}
		}	
		
		
		public function ThemeEndHead() {
			#gestion multilingue
			if(defined('PLX_MYMULTILINGUE')) {		
				$plxMML = is_array(PLX_MYMULTILINGUE) ? PLX_MYMULTILINGUE : unserialize(PLX_MYMULTILINGUE);
				$langues = empty($plxMML['langs']) ? array() : explode(',', $plxMML['langs']);
				$string = '';
				foreach($langues as $k=>$v)	{
				$url_lang="";
				if($_SESSION['default_lang'] != $v) $url_lang = $v.'/';
				$string .= 'echo "\\t<link rel=\\"alternate\\" hreflang=\\"'.$v.'\\" href=\\"".$plxMotor->urlRewrite("?'.$url_lang.$this->getParam('url').'")."\" />\\n";';
				}
				echo '<?php if($plxMotor->mode=="'.$this->getParam('url').'") { '.$string.'} ?>';
				}
			
			echo ' 		<link href="'.PLX_PLUGINS.basename(__DIR__).'/css/static.css" rel="stylesheet" type="text/css" />'."\n";
			// ajouter ici vos propre codes (insertion balises link, script , ou autre)
			}
			
			/**
			* Méthode qui affiche un message si le plugin n'a pas la langue du site dans sa traduction
			* Ajout gestion du wizard si inclus au plugin
			* @return	stdio
			* @author	Stephane F
		**/
		public function AdminTopBottom() {
			
			echo '<?php
			$file = PLX_PLUGINS."'.$this->plug['name'].'/lang/".$plxAdmin->aConf["default_lang"].".php";
			if(!file_exists($file)) {
			echo "<p class=\\"warning\\">'.basename(__DIR__).'<br />".sprintf("'.$this->getLang('L_LANG_UNAVAILABLE').'", $file)."</p>";
			plxMsg::Display();
			}
			?>';
			
			# affichage du wizard à la demande
			if(isset($_GET['wizard'])) {$_SESSION['justactivated'.basename(__DIR__)] = true;}
			# fermeture session wizard
			if (isset($_SESSION['justactivated'.basename(__DIR__)])) {
				unset($_SESSION['justactivated'.basename(__DIR__)]);
				$this->wizard();
			}
			
		}
		
		/**
			* Hook, Méthode statique qui affiche le widget
			*
		**/
		public static function addEventswidget($module=false) {
			
			# récupération d'une instance de plxMotor
			$plxMotor = plxMotor::getInstance();
			$plxPlug = $plxMotor->plxPlugins->getInstance(basename(__DIR__));		
			include(PLX_PLUGINS.basename(__DIR__).'/widget.'.basename(__DIR__).'.php');
		}
		
		
		/** 
			* Méthode wizard
			* 
			* Descrition	: Affiche le wizard dans l'administration
			* @author		: G.Cyrille
			* 
		**/
		# insertion du wizard
		public function wizard() {
			# uniquement dans les page d'administration du plugin.
			if(basename(
			$_SERVER['SCRIPT_FILENAME']) 			=='parametres_plugins.php' || 
			basename($_SERVER['SCRIPT_FILENAME']) 	=='parametres_plugin.php' || 
			basename($_SERVER['SCRIPT_FILENAME']) 	=='plugin.php'
			) 	{	
				include(PLX_PLUGINS.__CLASS__.'/lang/'.$this->default_lang.'-wizard.php');
			}
		}
		
		/**
			* Méthode de traitement du hook plxShowConstruct
			*
			* @return	stdio
			* @author	Stephane F
		**/
		public function plxShowConstruct() {
			
			# infos sur la page statique
			$string  = "if(\$this->plxMotor->mode=='".$this->url."') {";
			$string .= "	\$array = array();";
			$string .= "	\$array[\$this->plxMotor->cible] = array(
			'name'		=> '".$this->getParam('mnuName_'.$this->default_lang)."',
			'menu'		=> '',
			'url'		=>  '".basename(__DIR__)."',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
			);";
			$string .= "	\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
			$string .= "}";
			echo "<?php ".$string." ?>";
		}
		
		/**
			* Méthode de traitement du hook plxMotorPreChauffageBegin
			*
			* @return	stdio
			* @author	Stephane F
		**/
		public function plxMotorPreChauffageBegin() {				
			$template = $this->getParam('template')==''?'static.php':$this->getParam('template');
			
			$string = "
			if(\$this->get && preg_match('/^".$this->url."\/?/',\$this->get)) {
			\$this->mode = '".$this->url."';
			\$prefix = str_repeat('../', substr_count(trim(PLX_ROOT.\$this->aConf['racine_statiques'], '/'), '/'));
			\$this->cible = \$prefix.\$this->aConf['racine_plugins'].'".basename(__DIR__)."/static';
			\$this->template = '".$template."';
			return true;
			}
			";
			
			echo "<?php ".$string." ?>";
		}
		
		
		/**
			* Méthode de traitement du hook plxShowStaticListEnd
			*
			* @return	stdio
			* @author	Stephane F
		**/
		public function plxShowStaticListEnd() {
			
			# ajout du menu pour accèder à la page de recherche
			if($this->getParam('mnuDisplay')) {
				echo "<?php \$status = \$this->plxMotor->mode=='".$this->url."'?'active':'noactive'; ?>";
				echo "<?php array_splice(\$menus, ".($this->getParam('mnuPos')-1).", 0, '<li class=\"static menu '.\$status.'\" id=\"static-".basename(__DIR__)."\"><a href=\"'.\$this->plxMotor->urlRewrite('?".$this->lang.$this->url."').'\" title=\"".$this->getParam('mnuName_'.$this->default_lang)."\">".$this->getParam('mnuName_'.$this->default_lang)."</a></li>'); ?>";
			}
		}
		
		/**
			* Méthode qui renseigne le titre de la page dans la balise html <title>
			*
			* @return	stdio
			* @author	Stephane F
		**/
		public function plxShowPageTitle() {
			echo '<?php
			if($this->plxMotor->mode == "'.$this->url.'") {
			$this->plxMotor->plxPlugins->aPlugins["'.basename(__DIR__).'"]->lang("L_PAGE_TITLE");
			return true;
			}
			?>';
		}
		
		/**
			* Méthode qui référence la page statique dans le sitemap
			*
			* @return	stdio
			* @author	Stephane F
		**/
		public function SitemapStatics() {
			echo '<?php
			echo "\n";
			echo "\t<url>\n";
			echo "\t\t<loc>".$plxMotor->urlRewrite("?'.$this->lang.$this->url.'")."</loc>\n";
			echo "\t\t<changefreq>monthly</changefreq>\n";
			echo "\t\t<priority>0.8</priority>\n";
			echo "\t</url>\n";
			?>';
		}
		
		
		
		/** 
			* Méthode MyFcalendar
			* 
			* Descrition	:
			* @author		: TheCrok/G.Cyrillus
			* 
		**/
		public function MyFEvent($artId) {
			# code à executer
			return plxDate::formatDate(str_replace('-','',$this->getParam(str_pad($artId,4, '0', STR_PAD_LEFT))).'0800',$this->dateFormat);
			
		}
		
		
		/** 
			* Méthode MyHEvent
			* 
			* Descrition	:
			* @author		: TheCrok/G.Cyrillus
			* 
		**/
		public function MyHEvent($artId) {
			# code à executer
			echo '<?php  
			$plug = $plxShow->plxMotor->plxPlugins->getInstance(\''.basename(__DIR__).'\');
			echo plxDate::formatDate(str_replace(\'-\',\'\',$plug->getParam(str_pad(\''.$artId.'\',4, \'0\', STR_PAD_LEFT))).\'8000\',\''.$this->dateFormat.'\');
			?>'; 
		}
		
		
		/** 
			* Méthode AdminArticleInitData
			* 
			* Descrition	:
			* @author		: TheCrok/G.Cyrillus
			* 
		**/
		public function AdminArticlePrepend() {
			# code à executer
			echo '<?php
			if(!empty($_POST)) {		
				$plug = $plxAdmin->plxPlugins->getInstance(\''.basename(__DIR__).'\');		
				if(!empty($_POST[\'dateEvent\']) || $_POST[\'dateEvent\']==\'\') {
					if($plug->event_cat !=\'\' && !in_array($plug->event_cat,$_POST[\'catId\']) && !isset($_POST[\'draft\'])) {
						$_POST[\'catId\'][] = $plug->event_cat;
					}
					
					if($_POST[\'artId\'] ==\'0000\') {
						$artNum = $plxAdmin->nextIdArticle();
					}
					else {
						$artNum = $_POST[\'artId\'] ;
					}
						$plug->setParam($artNum, $_POST[\'dateEvent\'], \'string\');
						$plug->saveParams();
				}
				else 
				{
					$plug->setParam($artNum, $_POST[\'dateEvent\'], \'string\');
					$plug->saveParams();
					if(in_array($plug->event_cat,$_POST[\'catId\'])) {
						$delCat = array_search($plug->event_cat, $_POST[\'catId\']);
						unset($_POST[\'catId\'][$delCat]);
					}
				}
				
				if(isset($_POST[\'draft\'])) {
					$delCat = array_search($plug->event_cat, $_POST[\'catId\']);
					unset($_POST[\'catId\'][$delCat]);
				}
			}
			?>';	
		}
		
		
		/** 
			* Méthode AdminArticleTop
			* 
			* Descrition	:
			* @author		: TheCrok/G.Cyrillus
			* 
		**/
		public function AdminArticleTop() {
		?>
		<div class ="alert blue" style="display:flex;flex-wrap:wrap;gap:1em;justify-content:flex-end;align-items:center">
			<label for="id_dateEvent"><?= $this->getLang('L_EVENT')?>&nbsp;:</label>
			<?php echo '<?php		
				$plug = $plxAdmin->plxPlugins->getInstance(\''.basename(__DIR__).'\');
				$dateEvent = $plug->getParam($artId)	== \'\' ? $plug->getParam($artId) : $plug->getParam($artId); 
			plxUtils::printInput(\'dateEvent\', $dateEvent  , \'date\'); ?>'; ?><img src="<?= PLX_PLUGINS.'/'.basename(__DIR__).'/event-icon.png' ; ?>" style="height:50px;">
		</div>
		<?php
		}
		/** 
			* Méthode AdminIndexTop
			* 
			* Descrition	: ajoute une icone aux article associés à une date
			* @author		: TheCrok/G.Cyrillus
			* 
		**/
		public function AdminIndexTop() {
			echo '<?php
			$pattern = \'/^\d{4}$/\';
			$childStyle=array();
			$dateEventShow=PHP_EOL;
			$plug = $plxAdmin->plxPlugins->getInstance(\''.basename(__DIR__).'\');
			$keys = array_keys($plug->getParams());
			$result = preg_grep($pattern, $keys);
			$result = array_flip($result);
			foreach($result as $artId => $v) {
			
			if($plug->getParam($artId) == \'\' ) {
			$plug->delParam($artId);
			$plug->saveParams();
			continue;
			}
			$childStyle[]	= \'[value="\'.$artId.\'"]\';
			$dateEventShow .=\'tr:has([value="\'.$artId.\'"]) td:nth-child(3)::after {content:"\'.$plug->getParam($artId).\'";display:block;font-size:0.7em;color:crimson;line-height:0}\'.PHP_EOL;
			}
			$selector = implode(",",$childStyle);
			echo \'<style>tr:has(\'.$selector.\') td:nth-child(4) {padding-left:2.5em;background:url(../../plugins/addEvents/event-icon.png) 0 50% / auto 80% no-repeat;}\'.$dateEventShow.\'</style>\';
			?>';
		}
		
		
		/** 
			* Méthode plxFeedRssArticlesXml
			* 
			* Descrition	:
			* @author		: TheCrok/G.Cyrillus
			* 
		**/
		public function plxFeedRssArticlesXml() {
			# code à executer
			
			
			
		}
		
		
		/** 
			* Méthode plxMotorParseArticle
			* 
			* Descrition	:
			* @author		: TheCrok/G.Cyrillus
			* 
		**/
		public function plxMotorParseArticle() {
			
			# code à injecter
			echo '<?php
			if(!isset($_GET[\'a\'])) {
			$plug = $this->plxPlugins->getInstance(\''.basename(__DIR__).'\');
			$event =\'\';
			$date=date(\'Y-m-d\');
			$futur=\'\';
			if($plug->getParam($art[\'numero\'])<$date) $futur=\'past\';
			if($plug->getParam($art[\'numero\'])!=\'\') $event = \'<p><time class="eventTime i-end \'.$futur.\'" datetime="\'.$plug->getParam($art[\'numero\']).\'" title="\'.$plug->getParam($art[\'numero\']).\'"><img src="plugins/addEvents/event-icon.png" class="icon-event">✔</time></p>\' ;
			if($plug->getParam($art[\'numero\'])!=\'\'  && $this->mode==$plug->getParam(\'url\')) $event= \'<h3 class="event  \'.$futur.\'"><time class="eventTime" datetime="\'.$plug->getParam($art[\'numero\']).\'" title="\'.$plug->getParam($art[\'numero\']).\'"><img src="plugins/addEvents/event-icon.png" class="icon-event">\'. $plug->MyFEvent($art[\'numero\']).\'</time></h3>\';
			if($art[\'chapo\'] !==\'\') $art[\'chapo\']  = $event . $art[\'chapo\'];
			else $art[\'content\'] = $event . $art[\'content\'];
			}
			?>';
		}
		
		
		/** 
			* Méthode SitemapArticles
			* 
			* Descrition	:
			* @author		: TheCrok/G.Cyrillus
			* 
		**/
		public function SitemapArticles() {
			# code à executer
			
			
			
		}
		
		
	}	