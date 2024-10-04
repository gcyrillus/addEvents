<?php			# creation de la catégorie Events
			
			global $plxAdmin;
			$cat_id= $plxAdmin->nextIdCategory();			
				$plxAdmin->aCats[$cat_id]['name'] = 'Rendez-Vous';
				$plxAdmin->aCats[$cat_id]['url'] = plxUtils::urlify('Rendez-Vous');
				$plxAdmin->aCats[$cat_id]['tri'] = 'desc';
				$plxAdmin->aCats[$cat_id]['bypage'] = $plxAdmin->aConf['bypage'];
				$plxAdmin->aCats[$cat_id]['menu'] = 'oui';
				$plxAdmin->aCats[$cat_id]['active'] = 1;
				$plxAdmin->aCats[$cat_id]['homepage'] = 0;
				$plxAdmin->aCats[$cat_id]['description'] = 'tous nos événements par dates.';
				$plxAdmin->aCats[$cat_id]['template'] = 'categorie.php';
				$plxAdmin->aCats[$cat_id]['thumbnail'] = 'plugins/addEvents/icon.png';
				$plxAdmin->aCats[$cat_id]['thumbnail_title'] = 'Nos &Egrave;vénements';
				$plxAdmin->aCats[$cat_id]['thumbnail_alt'] = '';
				$plxAdmin->aCats[$cat_id]['title_htmltag'] = '';
				$plxAdmin->aCats[$cat_id]['meta_description'] = 'Rendez-Vous ! Tous nos événements passé et futur.';
				$plxAdmin->aCats[$cat_id]['meta_keywords'] = 'rendez-vous,evenement,events';
				$cats_name = array();
				$cats_url = array();
				# On génére le fichier XML
				$xml = "<?xml version=\"1.0\" encoding=\"".PLX_CHARSET."\"?>\n";
				$xml .= "<document>\n";
				foreach($plxAdmin->aCats as $cat_id => $cat) {
					# controle de l'unicité du nom de la categorie
					if(in_array($cat['name'], $cats_name)) {
						$plxAdmin->aCats = $save;
						return plxMsg::Error(L_ERR_CATEGORY_ALREADY_EXISTS.' : '.plxUtils::strCheck($cat['name']));
					}
					else
						$cats_name[] = $cat['name'];

					# controle de l'unicité de l'url de la catégorie
					if(in_array($cat['url'], $cats_url))
						return plxMsg::Error(L_ERR_URL_ALREADY_EXISTS.' : '.plxUtils::strCheck($cat['url']));
					else
						$cats_url[] = $cat['url'];
					$xml .= "\t<categorie number=\"".$cat_id."\" active=\"".$cat['active']."\" homepage=\"".$cat['homepage']."\" tri=\"".$cat['tri']."\" bypage=\"".$cat['bypage']."\" menu=\"".$cat['menu']."\" url=\"".$cat['url']."\" template=\"".basename($cat['template'])."\">";
					$xml .= "<name><![CDATA[".plxUtils::cdataCheck($cat['name'])."]]></name>";
					$xml .= "<description><![CDATA[".plxUtils::cdataCheck($cat['description'])."]]></description>";
					$xml .= "<meta_description><![CDATA[".plxUtils::cdataCheck($cat['meta_description'])."]]></meta_description>";
					$xml .= "<meta_keywords><![CDATA[".plxUtils::cdataCheck($cat['meta_keywords'])."]]></meta_keywords>";
					$xml .= "<title_htmltag><![CDATA[".plxUtils::cdataCheck($cat['title_htmltag'])."]]></title_htmltag>";
					$xml .= "<thumbnail><![CDATA[".plxUtils::cdataCheck($cat['thumbnail'])."]]></thumbnail>";
					$xml .= "<thumbnail_alt><![CDATA[".plxUtils::cdataCheck($cat['thumbnail_alt'])."]]></thumbnail_alt>";
					$xml .= "<thumbnail_title><![CDATA[".plxUtils::cdataCheck($cat['thumbnail_title'])."]]></thumbnail_title>";
					# Hook plugins
					eval($plxAdmin->plxPlugins->callHook('plxAdminEditCategoriesXml'));
					$xml .= "</categorie>\n";
				}
				$xml .= "</document>";
				# On met à jour les catégorie
				if(plxUtils::write($xml,path('XMLFILE_CATEGORIES'))) {
					$this->setParam('event_cat', $cat_id ,'string');
					$this->saveParams();
					return plxMsg::Info(L_SAVE_SUCCESSFUL.' categorie "Rendez-vous"');
				}
				else {
					$this->aCats = $save;
					return plxMsg::Error(L_SAVE_ERR.' '.path('XMLFILE_CATEGORIES'));
				}
		