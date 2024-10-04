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
<div class="wizard">	
	<div class="container">	
		<div class='title-wizard'>
			<h2><?= $plxPlugin->aInfos['title']?><br><?= $plxPlugin->aInfos['version']?></h2>
			<img src="<?php echo PLX_PLUGINS. 'addEvents'?>/icon.png">
			<div><q> Made in <?= $plxPlugin->aInfos['author']?> </q></div>
		</div>
		<p></p>
		
		<div id="tab-status">
			<span class="tab active">1</span>
		</div>		
		<form action="parametres_plugin.php?p=<?php echo 'addEvents' ?>"  method="post">
			<div role="tab-list">		
				<div role="tabpanel" id="tab1" class="tabpanel">
					<h2>Bienvenue dans l’extension <b style="font-family:cursive;color:crimson;font-variant:small-caps;font-size:2em;vertical-align:-.5rem;display:inline-block;"><?= $plxPlugin->aInfos['title']?></b></h2>
					<p>Ce plugin vous permet d'associé des articles à vos événement dans votre PluXml.</p>
				</div>	
				<div role="tabpanel" id="tab2" class="tabpanel hidden title">
					<h2>la Configuration</h2>
					<p>la configuration par défaut ne vous convient pas ?</p>	
					<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
				</div>		
				<div role="tabpanel" id="tab3" class="tabpanel hidden title">
					<h2>la page statique</h2>
					<p>les paramêtres d'affichage et d'accés.</p>	
					<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
				</div>	
				<div role="tabpanel" id="tab4" class="tabpanel hidden">
					<p>
						<h3>Modifier les valeurs de champs que vous souhaitez.</h3>
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
				</div>
				
				<div role="tabpanel" id="tab5" class="tabpanel hidden title">
					<h2>la page statique</h2>
					<p>les paramêtres d'affichages  des articles.</p>	
					<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
				</div>	
				<div role="tabpanel" id="tab6" class="tabpanel hidden">
					<h3>Ajuster les valeurs.</h3>
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
				</div>
				
				<div role="tabpanel" id="tab7" class="tabpanel hidden title">
					<h2>Les dates</h2>
					<p>format d'affichage des dates d'événement</p>	
					<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
				</div>	
				
				<div role="tabpanel" id="tab8" class="tabpanel hidden">
					<h2>Le format des dates</h2>
					<p>Plusieurs options vous permettent de définir comment écrire la date dans la page statique et dans la liste de des derniers évenements.</p>
					<p>
						<label for="dateFormat"><?= $plxPlugin->getLang('L_DATE_FORMAT') ?>)</label> 
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
					<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
				</div>		
				
				<div role="tabpanel" id="tab9" class="tabpanel hidden">
					<h2>Associer une date et un article</h2>
					<p>L'association se fait par un champ date, que le plugin insére dans la page d'édition et création des articles. il suffit simplement d'y inserer une date.</p>
					<p>&Agrave; l'opposé, il suffit d'éffacer la date ou laisser ce champs vide pour qu'il n'y ait plus de date d'événement associé.</p>
				</div>
				
				<div role="tabpanel" id="tab10" class="tabpanel hidden title">
					<h2>afficher une liste.</h2>
					<p>Un widget permet d'afficher une liste de liens vers les derniers evénements.</p>	
					<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
				</div>	
				
				<div role="tabpanel" id="tab11" class="tabpanel hidden">
					<h2>Afficher une liste de liens</h2>
					<p>Le widget affiche une liste de liens des 5 dernieres dates avec le format d'affichage des dates, vers les articles correspondant.</p>
					<p> Ce widget est à intégré dans votre thème à l'endroit que vous souhaitez, il reprend la structure utilisé dans la sidebar. 
					un titre H3 et une liste en ajoutant une icone calendrier devant chaque liens.</p>
					<p>Le code à inserer est le suivant <code style="color:blue">&lt;?php eval($plxShow->callHook('addEventswidget')); ?&gt;</code></p>
				</div>	
				
				
				<div role="tabpanel" id="tab12" class="tabpanel hidden title">
					<h2>plugin multilingue</h2>
					<p>Votre plugin est compatbile avec le plugin plxMultilingue.</p>	
					<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
				</div>	
				
				<div role="tabpanel" id="tab13" class="tabpanel hidden">
					<h2>Donner un titre</h2>
					<p>Ce titre servira à identifier le lien vers votre page statique dans le menu et sera le titre de votre page statique.</p>
					<p>Si vous êtes l'un des rares sites à utiliser le plugin plxMyMultilingue, ce titre pourra être different pour chaque langues activées</p>
					<?php 	$aLangs = array($plxAdmin->aConf['default_lang']);
					?>
					<?php foreach($aLangs as $lang){ ?>
						<p>
							<label for="id_mnuName_<?php echo $lang ?>"><?php $plxPlugin->lang('L_MENU_TITLE') ?>&nbsp;(<?php echo $lang ?>):</label>
							<?php plxUtils::printInput('mnuName_'.$lang,$var[$lang]['mnuName'],'text','20-20') ?>
						</p>
					<?php }  ?>		
				</div>		
				
				<div role="tabpanel" id="tabEnd" class="tabpanel hidden title">
					<h2>The End</h2>
					<p>Aller à la <a href="/core/admin/parametres_plugin.php?p=addEvents">page de configuration  addEvents</a> ou fermer</p>
					<!-- Ci-dessous , valide le passage à une autre page si d'autre champs required existe dans le formulaire -->
					<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
				</div>		
				<div class="pagination">
					<a class="btn hidden" id="prev"><?php $plxPlugin->lang('L_PREVIOUS') ?></a>
					<a class="btn" id="next"><?php $plxPlugin->lang('L_NEXT') ?></a>
					<?php echo plxToken::getTokenPostMethod().PHP_EOL ?>
					<button class="btn btn-submit hidden" id="submit"><?php $plxPlugin->lang('L_SAVE') ?></button>
				</div>
			</div>		
		</form>			
		<p class="idConfig">
			<label for="closeWizard"> Fermer </label>
		</p>	
	</div>	
	<script src="<?= PLX_PLUGINS ?>addEvents/js/wizard.js"></script>
	</div>
	<?php end: // FIN! ?>				
