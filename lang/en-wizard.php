<?php
	if(!defined('PLX_ROOT')) exit; 
	/**
		* Plugin 			addEvents
		*
		* @CMS required		PluXml 
		* @page				config.php
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
	
	# pas d'affichage dans un autre plugin !	
	if(isset($_GET['p'])&& $_GET['p'] !== 'addEvents' ) {goto end;}
	
	# on charge la class du plugin pour y accéder
	$plxMotor = plxMotor::getInstance();
	$plxAdmin = plxAdmin::getInstance();
	$plxPlugin = $plxMotor->plxPlugins->getInstance( 'addEvents'); 
	$aLangs = array($plxAdmin->aConf['default_lang']);
	
	# On vide la valeur de session qui affiche le Wizard maintenant qu'il est visible.
	if (isset($_SESSION['justactivatedaddEvents'])) {unset($_SESSION['justactivatedaddEvents']);}
	
	# initialisation des variables propres à chaque lanque 
	$langs = array();
	
	# initialisation des variables.
	$var = array();
	# Tableau du tri
	$aTriArts = array(
	'desc'		=> L_SORT_DESCENDING_DATE,
	'asc'		=> L_SORT_ASCENDING_DATE,
	);
	
	# initialisation des variables propres à chaque lanque
	$langs = array();
	foreach($aLangs as $lang) {
		# chargement de chaque fichier de langue
		$langs[$lang] = $plxPlugin->loadLang(PLX_PLUGINS.'addEvents/lang/'.$lang.'.php');
		$var[$lang]['mnuName'] =  $plxPlugin->getParam('mnuName_'.$lang)=='' ? $plxPlugin->getLang('L_DEFAULT_MENU_NAME') : $plxPlugin->getParam('mnuName_'.$lang);
	}
	
	# initialisation des variables page statique
	$var['mnuDisplay'] =  $plxPlugin->getParam('mnuDisplay')=='' ? 1 : $plxPlugin->getParam('mnuDisplay');
	$var['mnuPos'] =  $plxPlugin->getParam('mnuPos')=='' ? 2 : $plxPlugin->getParam('mnuPos');
	$var['template'] = $plxPlugin->getParam('template')=='' ? 'static.php' : $plxPlugin->getParam('template');
	$var['url'] = $plxPlugin->getParam('url')=='' ? strtolower(basename(__DIR__)) : $plxPlugin->getParam('url');
	$var['tri'] = $plxPlugin->getParam('tri')=='' ? 'asc' : $plxPlugin->getParam('tri');
	$var['past'] = $plxPlugin->getParam('past')=='' ? 0 : $plxPlugin->getParam('past');
	$var['bypage'] = $plxPlugin->getParam('bypage')=='' ? 5 : $plxPlugin->getParam('bypage');	
	
	# formatage de la date à l'affichage
	$var['dateFormat'] = $plxPlugin->getParam('dateFormat') == '' ? '#day #num_day #month #num_year(4)' : $plxPlugin->getParam('dateFormat');	
	
	# On récupère les templates des pages statiques
	$plxAdmin =plxAdmin::getInstance();
	$glob = plxGlob::getInstance(PLX_ROOT . $plxAdmin->aConf['racine_themes'] . $plxAdmin->aConf['style'], false, true, '#^^static(?:-[\\w-]+)?\\.php$#');
	if (!empty($glob->aFiles)) {
		$aTemplates = array();
		foreach($glob->aFiles as $v)
		$aTemplates[$v] = basename($v, '.php');
		} else {
		$aTemplates = array('' => L_NONE1);
	}	
	
	#affichage
?>
<link rel="stylesheet" href="<?= PLX_PLUGINS ?>addEvents/css/wizard.css" media="all" />
<input id="closeWizard" type="checkbox">
<div class="wizard"> <div class="container"> <div class='title-wizard'> <h2><?= $plxPlugin->aInfos['title']?><br><?= $plxPlugin->aInfos['version']?></h2> <img src="<?php echo PLX_PLUGINS. 'addEvents'?>/icon.png"> <div><q> Made in <?= $ plxPlugin->aInfos['author']?> </q></div> </div> <p></p> <div id="tab-status"> <span class="tab active">1 </span> </div> <form action="parameters_plugin.php?p=<?php echo 'addEvents' ?>" method="post"> <div role="tab-list"> <div role=" tabpanel" id="tab1" class="tabpanel">
	<h2>Welcome to the extension <b style="font-family:cursive;color:crimson;font-variant:small-caps;font-size:2em;vertical- align:-.5rem;display:inline-block;"><?= $plxPlugin->aInfos['title']?></b></h2>
	<p>This plugin allows you to associate articles with your events in your PluXml.</p>
</div>
<div role="tabpanel" id="tab2" class="tabpanel hidden title">
	<h2>Configuration</h2>
	<p>Default configuration not suitable for you?</p>
	<!-- <input type="hidden" class="form-input" value="keepGoing"> -->
</div>
<div role="tabpanel" id=" tab3" class="tabpanel hidden title">
	<h2>the static page</h2>
	<p>the display and access parameters.</p>
	<!-- <input type="hidden" class="form-input" value="keepGoing" > -->
</div>
<div role="tabpanel" id="tab4" class="tabpanel hidden">
	<p>
		<h3>Change the field values you want.</h3>
		<label for= "id_url"><?php $plxPlugin->lang('L_PARAM_URL') ?>:</label>
		<?php plxUtils::printInput('url',$var['url'],'text', '20-20') ?>
	</p>
	<p>
		<label for="id_mnuDisplay"><?php echo $plxPlugin->lang('L_MENU_DISPLAY') ?>&nbsp;:</label> 
	<?php plxUtils::printSelect('mnuDisplay',array('1'=>L_YES,'0'=>L_NO),$var['mnuDisplay']); ?> </p> 
	<p> <label for="id_mnuPos"><?php $plxPlugin->lang('L_MENU_POS') ?>&nbsp;:</label> <?php plxUtils::printInput('mnuPos' ,$var['mnuPos'],'text','2-5') ?> </p> 
	<p> <label for="id_template"><?php $plxPlugin->lang('L_TEMPLATE') ? >&nbsp;:</label> <?php plxUtils::printSelect('template', $aTemplates, $var['template']) ?> </p> </div> 
		
		
		<div role="tabpanel" id= "tab5" class="tabpanel hidden title"> 
		<h2>the static page</h2> 
		<p>the display parameters articles.</p> 
		<!-- <input type="hidden" class="form-input" value="keepGoing"> --> 
		</div> 
		<div role="tabpanel" id="tab6" class="tabpanel hidden"> 
		<h3>Adjust values.</h3> 
		<p> <label for="id_tri"><?= L_CONFIG_VIEW_SORT ?>&nbsp;:</label> <?php plxUtils::printSelect( 'sort', $aTriArts, $var['sort']);?> </p> 
		<p> <label for="id_tri"><?php $plxPlugin->lang('L_CONFIG_VIEW_PAST') ?>&nbsp; :</label> <?php plxUtils::printSelect('past', array('1'=>L_YES,'0'=>L_NO), $var['past']);?> </p> 
		<p> <label for="id_bypage"><?php echo L_CONFIG_VIEW_BYPAGE ?>&nbsp;:</label> <?php plxUtils::printInput('bypage ', $var['bypage'], 'text', '2-4',false,'fieldnum'); ?></p>
		</div>
		
		<div role="tabpanel" id="tab7" class="tabpanel hidden title">
		<h2>Dates</h2>
		<p>date display format of event</p>
		<!-- <input type="hidden" class="form-input" value="keepGoing"> -->
		</div>
		
		<div role="tabpanel" id="tab8" class= "tabpanel hidden">
		<h2>Date format</h2>
		<p>Several options allow you to define how to write the date in the static page and in the list of latest events.</p>
		<p>
		< label for="dateFormat"><?= $plxPlugin->getLang('L_DATE_FORMAT') ?>)</label>
		<?php plxUtils::printInput('dateFormat',$var['dateFormat'],'text','40-255') ?> 
		<br>
		<br>
		<span style="display:grid;grid-template-columns: auto,1fr;text-align-last:justify;" class="alert orange"><b style="grid-row:1/3;">Ex.</b> <code style="border-bottom:solid 1px;"><?= $var['dateFormat '] ?></code><?= plxDate::formatDate(date('Ymd'),$var['dateFormat']); ?></span>
		</p>
		<div>
		<h4 style="text-align:center" class="alert green"><?= $plxPlugin->getLang('L_PARAMS_HELP') ?></h4> 
		<ul>
			<li><b>#day</b>: displays the day (in text format: Monday, Tuesday, etc.)</li>
			<li><b>#month</b>: displays the month (in text format: January, February, March, etc.)</li>
			<li><b>#num_day</b>: displays the number of the day of the month (1, 15, …, 31,)< /li>
			<li><b>#num_month</b> : displays the month number (1, 2, 5, …, 12)</li>
			<li><b>#num_year(4)</b > : displays the year on 4 digits (eg: 2024)</li>
			<li><b>#num_year(2</b>) : displays the year on 2 digits (ex: 24)</li>
			<li><b>free value</b> : character string of its choice</li>
		</ul>
		</div>
		<!-- <input type="hidden" class="form-input" value="keepGoing"> -->
		</div>
		
		<div role="tabpanel " id="tab9" class="tabpanel hidden">
		<h2>Associate a date and an article</h2>
		<p>The association is made by a date field, which the plugin inserts in the page of editing and creation of the articles. It is enough simply to insert a date there.</p>
		<p>At the opposite, you just have to delete the date or leave this field empty so that there is no more associated event date.</p>
		</div>
		
		<div role="tabpanel" id="tab10" class="tabpanel hidden title">
		<h2>display a list.</h2>
		<p>A widget allows you to display a list of links to the latest events.</p>
		<!-- <input type="hidden" class="form-input" value="keepGoing"> -->
		</div>
		
		<div role="tabpanel" id="tab11" class="tabpanel hidden">
		<h2>Display a list of links</h2>
		<p>The widget displays a list of links from the last 5 dates with the date display format, to the corresponding articles.</p>
		<p> This widget is to be integrated into your theme wherever you want, it uses the structure used in the sidebar.
		an H3 title and a list by adding a calendar icon in front of each link.</p>
		<p>The code to insert is as follows <code style="color:blue">&lt;?php eval($plxShow->callHook('addEventswidget')); ?&gt;</code></p>
		</div>
		
		<div role="tabpanel" id="tab12" class="tabpanel hidden title">
		<h2>multilingual plugin</h2>
		<p>Your plugin is compatible with the plxMultilingual plugin.</p>
		<!-- <input type="hidden" class="form-input" value="keepGoing"> -->
		</div>
		
		<div role="tabpanel" id="tab13" class="tabpanel hidden">
		<h2>Give a title</h2>
		<p>This title will be used to identify the link to your static page in the menu and will be the title of your static page.</p>
		<p>If you are one of the few sites to use the plxMyMultilingual plugin, this title may be different for each activated language</p>
		<?php $aLangs = array($plxAdmin->aConf['default_lang']);
			?> <?php foreach($aLangs as $lang){ ?> 
			<p> <label for="id_mnuName_<?php echo $lang ?>"><?php $plxPlugin->lang('L_MENU_TITLE') ?>&nbsp;(<?php echo $lang ?>):</label> 
		<?php plxUtils::printInput('mnuName_'.$lang,$var[$lang]['mnuName'],'text ','20-20') ?> </p> <?php } ?> 
		</div> <div role="tabpanel" id="tabEnd" class="tabpanel hidden title"> 
		<h2>The End</h2> 
		<p>Go to <a href="/core/admin/parameters_plugin.php?p=addEvents">addEvents configuration page</a> or close</p>
		<!-- Below, validates the move to another page if other required fields exist in the form --> 
		<!-- <input type="hidden" class="form-input" value="keepGoing"> --> 
		</div> 
		<div class="pagination"> 
		<a class="btn hidden" id="prev"><?php $plxPlugin->lang('L_PREVIOUS') ?></a> 
		<a class="btn" id="next"><?php $ plxPlugin->lang('L_NEXT') ?></a> 
		<?php echo plxToken::getTokenPostMethod().PHP_EOL ?> 
		<button class="btn btn-submit hidden" id="submit"><?php $plxPlugin->lang('L_SAVE') ?></button> </div> </div> </form> <p class="idConfig"> <label for="closeWizard"> Close </label> </p>	
		</div>	
		<script src="<?= PLX_PLUGINS ?>addEvents/js/wizard.js"></script>
		</div>
		<?php end: // FIN! ?>				
		