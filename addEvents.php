<?php if(!defined('PLX_ROOT')) exit;
	/**
		* Plugin 			addEvents
		*
		* @CMS required		PluXml 
		* @version			4.1.0
		* @date				2024-12-08
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
		public $langParam='';
		
		private $url = ''; # parametre de l'url pour accèder à la page static		
		
		
		public function __construct($default_lang) {
			
			# gestion du multilingue plxMyMultiLingue
			$this->lang='';
			if(defined('PLX_MYMULTILINGUE')) {
				$lang = plxMyMultiLingue::_Lang();
				if(!empty($lang)) {
					if(isset($_SESSION['default_lang']) AND $_SESSION['default_lang']!=$lang) {
						$this->lang = $lang.'/';
					}
				}
			}			
			
			
			# appel du constructeur de la classe plxPlugin (obligatoire)
			parent::__construct($default_lang);
			
			# droits pour accèder à la page config.php du plugin
			$this->setConfigProfil(PROFIL_ADMIN, PROFIL_MANAGER);	
			
			# droits pour accèder à la page admin.php du plugin
			$this->setAdminProfil(PROFIL_ADMIN);
			
			// url Page static
			$this->url = $this->getParam('url')=='' ? strtolower(basename(__DIR__)) : $this->getParam('url');		
			
			# Declaration des hooks		
			$this->addHook('AdminTopBottom', 'AdminTopBottom');
			$this->addHook('addEventswidget', 'addEventswidget');
			$this->addHook('wizard', 'wizard');
			$this->addHook('MyHEvent', 'MyHEvent');
			$this->addHook('AdminArticlePrepend', 'AdminArticlePrepend');
			$this->addHook('AdminArticleTop', 'AdminArticleTop');
			$this->addHook('plxMotorParseArticle','plxMotorParseArticle');
			$this->addHook('AdminIndexTop','AdminIndexTop');
			
			# Si le fichier de langue existe on peut mettre en place la partie visiteur
			if(file_exists(PLX_PLUGINS.$this->plug['name'].'/lang/'.$default_lang.'.php')) {
				$this->addHook('plxShowConstruct', 'plxShowConstruct');
				$this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
				$this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
				$this->addHook('plxShowPageTitle', 'plxShowPageTitle');
				$this->addHook('SitemapStatics', 'SitemapStatics');
				if(defined('PLX_MYMULTILINGUE')) {
					$this->addHook('ThemeEndHead', 'ThemeEndHead');
				}			
			}
			
			# parametrage multilingue
			if($this->lang !=='') $this->langParam= '_'.substr($this->lang, 0, strlen($this->lang)-1);
			$this->dateFormat = $this->getParam('dateFormat'.$this->langParam) == '' ? '#day #num_day #month #num_year(4)' : $this->getParam('dateFormat'.$this->langParam);
			$this->event_cat =  $this->getParam('event_cat'.$this->langParam) == '' ? '' : $this->getParam('event_cat'.$this->langParam);
		}
		
		# Activation / desactivation
		
		public function OnActivate() {
			# code à executer à l’activation du plugin
			# activation du wizard
			$_SESSION['justactivated'.basename(__DIR__)] = true;
			if($this->getParam('event_cat'.$this->langParam) =='') include('cat.php');
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
		public function MyFEvent($date) {
			# code à executer
			
			$this->dateFormat = $this->getParam('dateFormat'.$this->langParam) == '' ? '#day #num_day #month #num_year(4)' : $this->getParam('dateFormat'.$this->langParam);

			
			
			return plxDate::formatDate(str_replace('-','',$date).'0800',$this->dateFormat);
			
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
		public function  AdminArticlePrepend() {
			
			# code à executer
			echo '<?php
			if(!empty($_POST)) {
				$cat[]=\'000\';
				if(isset($_POST[\'catId\'])) $cat = $_POST[\'catId\'];
				$plug = $plxAdmin->plxPlugins->getInstance(\''.basename(__DIR__).'\');	
				if(empty($plug->event_cat)) $plug->checkCatLang();
				if(class_exists(\'plxMyMultiLingue\')) $mtlng =\'_\'.$plxAdmin->aConf[\'default_lang\'];
				else $mtlng =\'\';
				
				if($_POST[\'artId\'] ==\'0000\') {
					$artNum = $plxAdmin->nextIdArticle();
				}
				else {
					$artNum = $_POST[\'artId\'] ;
				}
				if(!empty($_POST[\'dateEvent\']) || $_POST[\'dateEvent\']!=\'\') {	
					if( $plug->event_cat !=\'\' && !in_array($plug->event_cat,$cat) && !isset($_POST[\'draft\'])) {
						$_POST[\'catId\'][] = $plug->event_cat;
					}
					$infosEvent[] = $_POST[\'dateEvent\'];
					$infosEvent[] = $_POST[\'dateEventLastDay\'];
					$infosEvent[] = $_POST[\'dateEventTimeStart\'];
					$infosEvent[] = $_POST[\'dateEventTimeEnd\'];
					$dates = json_encode($infosEvent);
					$plug->setParam($artNum.$mtlng, $dates , \'cdata\');
					echo json_encode($infosEvent);
					$plug->saveParams();
				
				}
				else {
					$plug->setParam($artNum.$mtlng, $_POST[\'dateEvent\'], \'string\');
					$plug->saveParams();
					if(in_array($plug->event_cat,$cat)) {
						$delCat = array_search($plug->event_cat, $cat);
						unset($_POST[\'catId\'][$delCat]);
					}
				}
				
				if(isset($_POST[\'draft\'])) {
					$delCat = array_search($plug->event_cat, $cat);
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
			<div>
				<p style="display:grid;grid-template-columns:repeat(4,auto);gap:.5em"><label for="id_dateEvent"><?= $this->getLang('L_EVENT')?>&nbsp;:</label>
					<?php echo '<?php	
						if(class_exists(\'plxMyMultiLingue\')) $mtlng =\'_\'.$plxAdmin->aConf[\'default_lang\'];
						else $mtlng =\'\';
						$plug = $plxAdmin->plxPlugins->getInstance(\''.basename(__DIR__).'\');
						$EventInfos = json_decode($plug->getParam($artId.$mtlng),true)	== \'\' ? json_decode($plug->getParam($artId.$mtlng),true) : json_decode($plug->getParam($artId.$mtlng)); 
						if(isset($EventInfos[0])) $dateEvent = $EventInfos[0] ; else $dateEvent =\'\';
						if(isset($EventInfos[1])) $dateEventLastDay=$EventInfos[1]; else $dateEventLastDay =\'\';
						if(isset($EventInfos[2])) $dateEventTimeStart =$EventInfos[2]; else $dateEventTimeStart  =\'\';
						if(isset($EventInfos[3])) $dateEventTimeEnd =$EventInfos[3]; else $dateEventTimeEnd =\'\';
						plxUtils::printInput(\'dateEvent\', $dateEvent  , \'date\'); 
					?>'; ?>		
					<label for="id_dateEventLastDay"><?= $this->getLang('L_EVENT_LAST_DAY')?>&nbsp;:</label>
					<?php echo '<?php plxUtils::printInput(\'dateEventLastDay\', $dateEventLastDay  , \'date\');
					?>';?>
					
					<label for="id_dateEventTimeStart"><?= $this->getLang('L_EVENT_TIME_START')?>&nbsp;:</label>
					<?php echo '<?php plxUtils::printInput(\'dateEventTimeStart\', $dateEventTimeStart  , \'time\');
					?>';?>
					
					<label for="id_dateEventTimeEnd"><?= $this->getLang('L_EVENT_TIME_END')?>&nbsp;:</label>
					<?php echo '<?php plxUtils::printInput(\'dateEventTimeEnd\', $dateEventTimeEnd  , \'time\');
					?>';?>
				</p>
			</div>
			<img src="<?= PLX_PLUGINS.'/'.basename(__DIR__).'/event-icon.png' ; ?>" style="height:50px;">
		</div>
		<style> :is(#id_dateEvent[value=''] , #id_dateEvent:not([value]) ) + img { filter: grayscale(100);}</style>
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
			if(class_exists(\'plxMyMultiLingue\')) $mtlng =\'(_\'.$plxAdmin->aConf[\'default_lang\'].\')\';
			else $mtlng =\'\';
			$pattern = \'/^\d{4}\'.$mtlng.\'$/\';
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
				$is_event = json_decode($plug->getParam($artId))[0];
				$childStyle[]	= \'[value="\'.substr($artId,0,4).\'"]\';
				$dateEventShow .=\'tr:has([value="\'.substr($artId,0,4).\'"]) td:nth-child(3)::after {content:"\'.$is_event.\'";display:block;font-size:0.7em;color:crimson;line-height:0}\'.PHP_EOL;
			}
			$selector = implode(",",$childStyle);
			echo \'<style>tr:has(\'.$selector.\')td:nth-child(4) {padding-left:2.5em;background:url(../../plugins/addEvents/event-icon.png) 0 50% / auto 80% no-repeat;}\'.$dateEventShow.\'</style>\';
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
				
				$plxMotor = plxMotor::getInstance();			
				#nettoyage des utilisateurs
				$deletedUsers=array();
				$users=$plxMotor->aUsers;
				foreach( $users as $user => $userData) {
					if($userData[\'delete\'] == 1) $deletedUsers[$userData[\'name\']] = $userData[\'name\'];
				}
				if(class_exists(\'plxMyMultiLingue\')) $mtlng =\'_\'.$this->aConf[\'default_lang\'];
				else $mtlng =\'\';
				
				$plug = $this->plxPlugins->getInstance(\''.basename(__DIR__).'\');
				$event =\'\';
				$date_is_event = array("0");
				if(isset(json_decode($plug->getParam($art[\'numero\'].$mtlng))[0]))$date_is_event = json_decode($plug->getParam($art[\'numero\'].$mtlng));		
				
				$date=date(\'Y-m-d\');		
				$futur=\'\';		
				$posted=\'\';
				$yes=\'\';
				$maybe=\'\';
				$no =\'selected\';		
				if($date_is_event[0] < $date  && $date_is_event[0] !=0) $futur=\'past\';
				if($date_is_event[0] !=\'\'   && $date_is_event[0] !=0) $event = \'<p><time class="eventTime i-end \'.$futur.\'" datetime="\'.$date_is_event[0].\'" title="\'.$date_is_event[0].\'"><b>\'.plxDate::formatDate(str_replace(\'-\',\'\',str_pad($date_is_event[0],4, \'0\', STR_PAD_LEFT)).\'8000\', $plug->dateFormat ).\'</b><img src="plugins/addEvents/event-icon.png" class="icon-event">✔</time></p>\' ;
				if($date_is_event[0] !=\'\'   && $date_is_event[0] !=0 && $this->mode==$plug->getParam(\'url\')) $event= \'<h3 class="event  \'.$futur.\'"><time class="eventTime" datetime="\'.$date_is_event[0].\'" title="\'.$date_is_event[0].\'"><img src="plugins/addEvents/event-icon.png" class="icon-event">\'. $plug->MyFEvent($date_is_event[0]).\'</time></h3>\';
	
				if($date_is_event[0]!=\'\'  && $date_is_event[0] !=0) {
					$lngDir = $mtlng;
					if(strlen($lngDir) > 0 ) $lngDir = $this->aConf[\'default_lang\'].\'/\';
					
					$file = PLX_ROOT.\'data/articles/\'.$lngDir.$art[\'numero\'].$mtlng.\'.json\';
					if(!file_exists($file )){
						touch($file);
						file_put_contents($file,\'[]\');
					} 
					$people = json_decode(file_get_contents($file),true);
					$nbPeople= "";
					foreach($people as $k => $membre){if(in_array($k,$deletedUsers)) unset($people[$k]);}
				}
				if (isset($_SESSION[\'profil\']) && $date_is_event[0]!=\'\'  && $date_is_event[0] !=0 && $futur !=\'past\') {
					$activeUser = $plxMotor->aUsers[$_SESSION[\'user\']][\'name\'];
					if(isset($_POST[$date_is_event[0]])) {
						$people[$activeUser] = $_POST[\'beThere\'];
						$posted= \'<p class="alert green text-center">\'.$plug->getLang(\'L_THKS_ANSWERING\').\'</p>\';
						$_POST = array();
					}
					if(isset($people[$activeUser]) &&  $people[$activeUser] ==\'L_YES\') {
						file_put_contents($file, json_encode($people, JSON_PRETTY_PRINT ));	
						$yes="selected";
						$no ="";
					}
					elseif(isset($people[$activeUser]) &&  $people[$activeUser] ==\'L_MAYBE\') {
						file_put_contents($file, json_encode($people, JSON_PRETTY_PRINT ));	
						$maybe="selected";
						$no ="";
					}
					else {
						if(isset($people[$activeUser])) {
							unset($people[$activeUser]);
							file_put_contents($file, json_encode($people, JSON_PRETTY_PRINT ));
						}
					}
					
					if($this->mode == \''.$this->getParam('url').'\' OR $this->mode == \'article\') {
					if(count($people)>1 && ($plug->getParam(\'showParticipation\') == 0 || $_SESSION[\'profil\'] == 0)) {
						$nbPeople =\'<details><summary class="text-center">>> \'.count($people).\' \'.$plug->getLang(\'L_PARTICIPANTS\').\'</summary>
						<table style="margin:auto">
						<tr>
						<th><img src="/plugins/addEvents/img/user.png" style="height:25px;filter: drop-shadow(8px -2px #777) drop-shadow(8px -2px 0px #333) drop-shadow(0 0 3px );"></th><td>\'.$plug->getLang(\'L_YES\').\'</td><td>\'.$plug->getLang(\'L_MAYBE\').\'</td></tr>\';
						foreach($people as $k => $membre){
							if($membre == \'L_YES\')
								$nbPeople .=\'<tr><th>\'.$k.\'</th><td style="color:#B5E61D">✔</td><td></td></tr>\';
							else
								$nbPeople .=\'<tr><th>\'.$k.\'</th><td></td><td style="color:orange;text-align:center">⁇</td></tr>\';
						}
						$nbPeople .= \'</table></details>\';
					}
					else  {
					$nbPeople .=\'<p>\'.count($people).\' \'.$plug->getLang(\'L_PARTICIPANTS\').\'</p>\';
					}
					$event .= \'<form method="post" action="#form-art-\'.$art[\'numero\'].\'" class="beThere vanilla-calendar" id="form-art-\'.$art[\'numero\'].\'">\'.$posted.\' 			
					<p style="display:grid;grid-template-columns:auto auto;place-content:center;gap:1em;">
						<label for="beThere">\'.$plug->getLang(\'L_ILL_BE_THERE\').\' :</label>
						<select name="beThere">
							<option \'.$yes.\' value="L_YES">\'.$plug->getLang(\'L_YES\').\'</option>
							<option \'.$maybe.\' value="L_MAYBE">\'.$plug->getLang(\'L_MAYBE\').\'</option>
							<option \'.$no.\' value="L_NO">\'.$plug->getLang(\'L_NO\').\'</option>
						</select>
						<input type="hidden" name="username" value="\'.$plxMotor->aUsers[$_SESSION[\'user\']][\'name\'].\'">
					</p>
					<p class="text-center">				
						<input type="submit" name="\'.$date_is_event[0].\'" value="\'.$plug->getLang(\'L_SAVE\').\'">
					</p>
					\'.$nbPeople.\'
					</form>\';	
				}
				}
					# affichage durées
					$event .=\'<p class="event_time">\';
					if(isset($date_is_event[1]) && strlen($date_is_event[1]) > 1) {
						$end = $plug->MyFEvent($date_is_event[1]);
						$event .= $plug->getLang(\'L_EVENT_LAST_DAY\') .\'<br>\'. $end ;
					}
					
					if(isset($date_is_event[2]) && strlen($date_is_event[2]) > 1) {
						$from = $date_is_event[2];
						$event .= \'<br>\'.$plug->getLang(\'L_FROM\') .\' \'. str_replace(\':\',\'h\',ltrim($from,\'0\')) .\' \' ;
					}
					if(isset($date_is_event[3]) && strlen($date_is_event[3]) > 1) {
						$to = $date_is_event[3];
						$event .= $plug->getLang(\'L_TO\') .\' \'. str_replace(\':\',\'h\',ltrim($to,\'0\')) ;
					}
					$event .=\'</p>\';
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
		public function checkCatLang() {
			include('cat.php');		
		}
		
	}			