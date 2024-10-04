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
	# Control du token du formulaire
	plxToken::validateFormToken($_POST);
	
	# Liste des langues disponibles et prises en charge par le plugin
	$aLangs = array($plxAdmin->aConf['default_lang']);	
	
	if(!empty($_POST)) {
	
	$plxPlugin->setParam('dateFormat', $_POST['dateFormat'], 'cdata');
		
	#multilingue
	$plxPlugin->setParam('mnuDisplay', $_POST['mnuDisplay'], 'numeric');
	$plxPlugin->setParam('mnuPos', $_POST['mnuPos'], 'numeric');
	$plxPlugin->setParam('bypage', $_POST['bypage'], 'numeric');
	$plxPlugin->setParam('template', $_POST['template'], 'string');
	$plxPlugin->setParam('past', $_POST['past'], 'numeric');
	$plxPlugin->setParam('tri', $_POST['tri'], 'string');
	$plxPlugin->setParam('url', plxUtils::title2url($_POST['url']), 'string');
	foreach($aLangs as $lang) {
	$plxPlugin->setParam('mnuName_'.$lang, $_POST['mnuName_'.$lang], 'string');
	}
	
	$plxPlugin->saveParams();	
	header("Location: parametres_plugin.php?p=".basename(__DIR__));
	exit;
	}
	# formatage de la date à l'affichage
	$var['dateFormat'] = $plxPlugin->getParam('dateFormat') == '' ? '#day #num_day #month #num_year(4)' : $plxPlugin->getParam('dateFormat');	


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
	
	# Tableau du tri
	$aTriArts = array(
		'desc'		=> L_SORT_DESCENDING_DATE,
		'asc'		=> L_SORT_ASCENDING_DATE,
	);
	
	# On récupère les templates des pages statiques
	$glob = plxGlob::getInstance(PLX_ROOT . $plxAdmin->aConf['racine_themes'] . $plxAdmin->aConf['style'], false, true, '#^^static(?:-[\\w-]+)?\\.php$#');
	if (!empty($glob->aFiles)) {
	$aTemplates = array();
	foreach($glob->aFiles as $v)
	$aTemplates[$v] = basename($v, '.php');
	} else {
	$aTemplates = array('' => L_NONE1);
	}
	/* end template */
	
	# affichage du wizard à la demande
	if(isset($_GET['wizard'])) {$_SESSION['justactivated'.basename(__DIR__)] = true;}
	# fermeture session wizard
	if (isset($_SESSION['justactivated'.basename(__DIR__)])) {unset($_SESSION['justactivated'.basename(__DIR__)]);}
			
	?>
	<link rel="stylesheet" href="<?php echo PLX_PLUGINS."addEvents/css/tabs.css" ?>" media="all" />
	<p>Permet d'associer un article à un ou plusieurs événements.</p><ul>
<li> Insère un champs "Événement" dans l’édition, création des articles.</li>
<li> Insère une notification visuelle sur vos articles associés à un événement</li>
<li> genére une page statique virtuelle listant vos événements par dates d'évenements.</li></ul>	
	<h2><?php $plxPlugin->lang("L_CONFIG") ?></h2>
	
	<div id="tabContainer">
	<form action="parametres_plugin.php?p=<?= basename(__DIR__) ?>" method="post" >
	<div class="tabs">
	<ul>
	<li id="tabHeader_Param"><?php $plxPlugin->lang('L_PARAMS') ?></li>
		<li id="tabHeader_main"><?php $plxPlugin->lang('L_MAIN') ?></li>
	<?php
	foreach($aLangs as $lang) {
	echo '<li id="tabHeader_'.$lang.'">'.strtoupper($lang).'</li>';
	} ?>
	</ul>
	</div>
	<div class="tabscontent">
	<div class="tabpage" id="tabpage_Param">	
	<fieldset><legend><?php $plxPlugin->lang("L_CONFIG") ?></legend>

	<fieldset style="display:flex;gap:1em;place-content:center;flex-wrap:wrap;"><legend>- Date -</legend>
		<p>
			<label for="dateFormat"><?= $plxPlugin->getLang('L_DATE_FORMAT') ?></label> 
			<?php plxUtils::printInput('dateFormat',$var['dateFormat'],'text','40-255') ?>
			<br><br><span style="display:grid;grid-template-columns:auto,1fr;text-align-last:justify;" class="alert orange"><b style="grid-row:1/3;">Ex.</b> <code style="border-bottom:solid 1px;"><?= $var['dateFormat'] ?></code><?= plxDate::formatDate(date('Ymd'),$var['dateFormat']); ?></span>
		</p>
		<div><h4 style="text-align:center" class="alert green"><?= $plxPlugin->getLang('L_PARAMS_HELP') ?></h4>
			<ul>
				<li><b>#day</b> : affiche le jour (au format texte : lundi, mardi, etc…)</li>
				<li><b>#month</b> : affiche le mois (au format texte : janvier, février, mars, etc…)</li>
				<li><b>#num_day</b> : affiche le numéro du jour du mois (1, 15, …, 31,)</li>
				<li><b>#num_month</b> : affiche le numéro du mois (1, 2, 5, …, 12)</li>
				<li><b>#num_year(4)</b> : affiche l’année sur 4 chiffres (ex: 2024)</li>
				<li><b>#num_year(2</b>) : affiche l’année sur 2 chiffres (ex: 24)</li>
				<li><b>valeur libre</b> : chaîne de caractère de son choix</li>
			</ul>
		</div>
	</fieldset>
	</div>
		
	<div class="tabpage" style="grid-template-columns:repeat(auto-fit,minmax(350px,1fr))" id="tabpage_main">
	<fieldset>
	<legend><?php $plxPlugin->lang('L_PARAMS'). $plxPlugin->lang('L_MAIN') ?></legend>
	<p>
	<label for="id_url"><?php $plxPlugin->lang('L_PARAM_URL') ?>&nbsp;:</label>
	<?php plxUtils::printInput('url',$var['url'],'text','20-20') ?>
	</p>
	<p>
	<label for="id_mnuDisplay"><?php echo $plxPlugin->lang('L_MENU_DISPLAY') ?>&nbsp;:</label>
	<?php plxUtils::printSelect('mnuDisplay',array('1'=>L_YES,'0'=>L_NO),$var['mnuDisplay']); ?>
	</p>
	<p>
	<label for="id_mnuPos"><?php $plxPlugin->lang('L_MENU_POS') ?>&nbsp;:</label>
	<?php plxUtils::printInput('mnuPos',$var['mnuPos'],'text','2-5') ?>
	</p>
	<p>
	<label for="id_template"><?php $plxPlugin->lang('L_TEMPLATE') ?>&nbsp;:</label>
	<?php plxUtils::printSelect('template', $aTemplates, $var['template']) ?>
	</p>	
	</fieldset>
	<fieldset>
	<legend><?php $plxPlugin->lang('L_CONFIG_VIEW_STATIC') ?></legend>
	<p>
	<label for="id_tri"><?= L_CONFIG_VIEW_SORT ?>&nbsp;:</label>
	<?php plxUtils::printSelect('tri', $aTriArts, $var['tri']);?>
	</p>
	<p>
	<label for="id_tri"><?php $plxPlugin->lang('L_CONFIG_VIEW_PAST') ?>&nbsp;:</label>
	<?php plxUtils::printSelect('past', array('1'=>L_YES,'0'=>L_NO), $var['past']);?>
	</p>
	<p>
	<label for="id_bypage"><?php echo L_CONFIG_VIEW_BYPAGE ?>&nbsp;:</label>
	<?php plxUtils::printInput('bypage', $var['bypage'], 'text', '2-4',false,'fieldnum'); ?>
	</p>
	</fieldset>
	</div>
	<?php foreach($aLangs as $lang) : ?>
	<div class="tabpage" id="tabpage_<?php echo $lang ?>">
	<?php if(!file_exists(PLX_PLUGINS.basename(__DIR__).'/lang/'.$lang.'.php')) : ?>
	<p><?php printf($plxPlugin->getLang('L_LANG_UNAVAILABLE'), PLX_PLUGINS.basename(__DIR__).'/lang/'.$lang.'.php') ?></p>
	<?php else : ?>
	<fieldset>
	<p>
	<label for="id_mnuName_<?php echo $lang ?>"><?php $plxPlugin->lang('L_MENU_TITLE') ?>&nbsp;:</label>
	<?php plxUtils::printInput('mnuName_'.$lang,$var[$lang]['mnuName'],'text','20-20') ?>
	</p>
	</fieldset>
	<?php endif; ?>
	</div>
	<?php endforeach; ?>
	</div>
	<fieldset>
	<p class="in-action-bar">
	<?php echo plxToken::getTokenPostMethod() ?><br>
	<input type="submit" name="submit" value="<?= $plxPlugin->getLang('L_SAVE') ?>"/>
	</p>
	</fieldset>
	</form>
	</div>
	<script type="text/javascript" src="<?php echo PLX_PLUGINS."addEvents/js/tabs.js" ?>"></script>
	<hr>
	<a href="parametres_plugin.php?p=<?= basename(__DIR__) ?>&wizard" class="aWizard"><img src="<?= PLX_PLUGINS.basename(__DIR__)?>/img/wizard.png" style="height:2em;vertical-align:middle" alt="Wizard"> Wizard</a>