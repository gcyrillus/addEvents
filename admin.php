<?php if(!defined('PLX_ROOT')) exit; ?>
<link rel="stylesheet" href="<?php echo PLX_PLUGINS."addEvents/css/admin.css" ?>" media="all" />
<h2><?= $plxPlugin->getLang('L_EVENTS_JOIGNIN_IN') ?></h2>
<p>
	<?php if(!isset($_GET['past'])) echo '<a href="plugin.php?p=addEvents&past=1">'.$plxPlugin->getLang('L_CONFIG_VIEW_PAST').'</a>'; ?>
	<?php if(isset($_GET['past'])) echo '<a href="plugin.php?p=addEvents">'.$plxPlugin->getLang('L_CONFIG_VIEW_FUTUR').'</a>'; ?>

	</p>
<?php
	# Liste des langues disponibles et prises en charge par le plugin
	$aLangs = array($plxAdmin->aConf['default_lang']);
	
	# Control du token du formulaire
	plxToken::validateFormToken($_POST);
	
	
	# On construit le tableau d'association d'articles
	$arts = $plxAdmin->plxGlob_arts->aFiles;
	$artList='';

	
	# On récupere les utilisateurs
	$users = $plxAdmin->aUsers;
	
	if(class_exists('plxMyMultiLingue')) {
		$mtlng =$plxAdmin->aConf['default_lang'].'/';
		$postfix = '_'.$plxAdmin->aConf['default_lang'];
	}
	else {
		$mtlng ='';
		$postfix='';
	}
	
	$pattern = '/^\d{4}'.$mtlng.'$/';
	$keys = array_keys($plxPlugin->getParams());
	$result = preg_grep($pattern, $keys);
	$result = array_flip($result);
	$orderedByDate = array();
	foreach($result as $artnum => $v) {
		if($plxPlugin->getParam('past') == 0 && !isset($_GET['past']) && json_decode($plxPlugin->getParam($artnum))[0] < date('Y-m-d') ) continue;
		if($plxPlugin->getParam($artnum) !='') $orderedByDate[$artnum]= json_decode($plxPlugin->getParam($artnum))[0];		
	}
	asort($orderedByDate);
	if($plxPlugin->getParam('tri') =='desc') arsort($orderedByDate);
	
	if(isset($_POST['articleEvent'])) {
	unset($_POST['token']);
		$file = PLX_ROOT.'data/articles/'.$mtlng.trim($_POST['articleEvent']).$postfix.'.json';
		unset($_POST['articleEvent']);
		if(file_exists($file)) {
			foreach($_POST as $post => $data) {
				if(isset($users[$post]) && trim($data) != 'L_NO') {
					$replied[$users[$post]['name']] = trim($data);
				} 
			}
			file_put_contents($file, json_encode($replied, JSON_PRETTY_PRINT ));
		}
	}
	//var_dump($orderedByDate);
	foreach($orderedByDate as $k => $v){
		$article = glob(PLX_ROOT.'data/articles/'.$mtlng.$k.'*.xml');
		$info = $plxAdmin->artInfoFromFilename($article[0]);
		$jsonFile = str_pad($info["artId"],  4, "0").$postfix.'.json';
		$file=PLX_ROOT.'data/articles/'.$mtlng.$jsonFile;
		
		if(file_exists($file)) {
			$people = json_decode(file_get_contents($file),true);
				echo'
				<form method="post" class="event_form_user">
				<input type="hidden" name="articleEvent" value="'.$info["artId"].'">
				<table style="border:solid 1px;margin:1em;">
				<thead>
				<tr>
				<th>'.$plxPlugin->MyFEvent(json_decode($plxPlugin->getParam($k))[0]).'</th>
				<td>
				<a href="'.$plxAdmin->urlRewrite('?article'.intval($info["artId"]).'/'.$info["artUrl"]).'" target="_blank">'.$info["artUrl"].'</a>
				<sup>('.count($people).' '.$plxPlugin->getLang('L_PARTICIPANTS').')</sup>
				</td>
				</tr>
				</thead>
				<tbody>';
				foreach($users as $user => $action) {
				if($action['delete'] == 1) continue;
					$yes=$no=$maybe='';
					
					echo '<tr>';
					echo '<td id="'.$user.'">'.$action['name'].'</td>';
					if(isset($people[$action['name']]) && $people[$action['name']]== 'L_YES') $yes="checked";
					elseif(isset($people[$action['name']]) && $people[$action['name']]== 'L_MAYBE') $maybe="checked";  
					else $no='checked';
					echo '<td class="grid">
					<label for="yes-'.$user.'">'.$plxPlugin->getLang('L_YES').'</label>
					<input type="radio" id="yes-'.$user.'" name="'.$user.'" '.$yes.' value="L_YES">
					
					<label for="maybe-'.$user.'">'.$plxPlugin->getLang('L_MAYBE').'</label>
					<input type="radio" id="maybe-'.$user.'" name="'.$user.'" '.$maybe.' value="L_MAYBE">				
					
					<label for="no-'.$user.'">'.$plxPlugin->getLang('L_NO').'</label>
					'. plxToken::getTokenPostMethod().'
					<input type="radio" id="no-'.$user.'" name="'.$user.'" '.$no.' value="L_NO"></td>';
					echo '</tr>';
					
				}
				echo '
				</tbody>
				<tfoot>
				<tr><td><input type="submit" value="update"></td><td><small>last update:<br> '. $plxPlugin->MyFEvent(date ("Ymd", filemtime($article[0]))).' - '.date ("H\Hi", filemtime($article[0])).'</small></td></tr>	
				</tfoot>
				</table>
				
				</form>';
			
		}
	}
	
?><a href="<?php echo PLX_ROOT ?>core/admin/"  class="in-action-bar"  title="<?php $plxPlugin->lang('L_ADMIN') ?>">⇦ <?php $plxPlugin->lang('L_ADMIN') ?> </a>
