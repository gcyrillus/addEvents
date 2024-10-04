<div class="help">
	<h1>Aide du plugin addEvents</h1>
  <img src="https://github.com/gcyrillus/addEvents/blob/main/event-icon.png?raw=true">
	<h2>Associer un article à une date</h2>
	<p>Editer ou créer un nouvel article en remplissant le champ date dans la boite bleu  en haut à droite de la zone d'édition</p>
	<h2>Dissocier un article d'une date</h2>
	<p>Editer l'article en effaçant la date inscrite dans la boite bleu  en haut à droite de la zone d'édition</p>
	<h2>page statique ou catégorie</h2>
	<p>Les deux sont disponibles</p>
	<h3>La catégorie "Rendez-Vous"</h3>
	<p>à l'activation , le plugin recherche si une catégorie est déja dédiées aux événement sinon, il recherche une catégorie "rendez vous" et va la créer si inexistante. Vous pourrez la renommer par la suite.</p>	
	<p>Les articles avec un champs date seront sustématiquement ajoutés à cette catégorie au moment de l'enregistrement</p>
	<p>Cette catégorie n'est disponible que pour ces articles, en vidant le champ "date de lévenement" , ils seront automatiquement désincrit de cette catégorie au moment de l'enregistrement</p>
	<h3>La page statique</h3>
	<p>Vous avez le choix de l'activer ou non et de la renommer, elle tri vos article par date d'événement et <b>non pas par date d'article</b>.</p>
	<h2>Un widget ?</h2>
	<p>Oui</p>
	<p>Par défaut celui-ci affichera la liste des 5 événements en commençant par la date du dernier</p>
<p>Pour afficher le widget, il faut intégrer dans votre théme, à l'emplacement ou vous voulez le voir s'afficher le code suivant</p></p>
<div class="ico"><b style="color:blue">&lt;?php eval($plxShow->callHook('addEventswidget')); ?&gt;</b>;</div>
<h3>Des modules</h3>
<p>Le widget peut afficher un autre module au choix</p>
<p>par exemple, pour afficher la liste vous pouvez aussi l'indiquer dans le hook par le nom de son module:</p>
<div class="ico" title="cliquez pour copier le code du widget"><b style="color:blue">&lt;?php eval($plxShow->callHook('addEventswidget','list')); ?&gt;</b></div>
<p>Un module calendrier est disponible, vous pouvez l'afficher en indiquant son nom de module :</p>
<div class="ico" title="cliquez pour copier le code du widget"><b style="color:blue">&lt;?php eval($plxShow->callHook('addEventswidget','calendrier')); ?&gt;</b> </div>
<p>Un module "aujourd'hui"(today) est disponible. Il affiche un encart avec l'image d'accroche et le 
lien vers l'article lié à un évenement du jour si il y en a un. Vous pouvez l'afficher en indiquant son nom de module:</p>
<div class="ico" title="cliquez pour copier le code du widget"><b style="color:blue">&lt;?php eval($plxShow->callHook('addEventswidget','today')); ?&gt;</b></div>
<p>Vous pouvez créer votre propre module et l'afficher en indiquant son nom</p>
</div>
