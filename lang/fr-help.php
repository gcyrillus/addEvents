<div class="help">
	<h1>Aide du plugin addEvents</h1>
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
<div class="ico"><b style="color:blue">&lt;?php eval($plxShow->callHook('addEventswidget')); ?&gt;</b> &#128203;</div>
<h3>Des modules</h3>
<p>Le widget peut afficher un autre module au choix</p>
<p>par exemple, pour afficher la liste vous pouvez aussi l'indiquer dans le hook par le nom de son module:</p>
<div class="ico" title="cliquez pour copier le code du widget"><b style="color:blue">&lt;?php eval($plxShow->callHook('addEventswidget','list')); ?&gt;</b> &#128203;</div>
<p>Un module calendrier est disponible, vous pouvez l'afficher en indiquant son nom de module :</p>
<div class="ico" title="cliquez pour copier le code du widget"><b style="color:blue">&lt;?php eval($plxShow->callHook('addEventswidget','calendrier')); ?&gt;</b> &#128203;</div><
<p>Un module "aujourd'hui"(today) est disponible. Il affiche un encart avec l'image d'accroche et le 
lien vers l'article lié à un évenement du jour si il y en a un. Vous pouvez l'afficher en indiquant son nom de module:</p>
<div class="ico" title="cliquez pour copier le code du widget"><b style="color:blue">&lt;?php eval($plxShow->callHook('addEventswidget','today')); ?&gt;</b> &#128203;</div>
<p>Vous pouvez créer votre propre module et l'afficher en indiquant son nom</p>
</div>
<script>
	function copyDivToClipboard(el) {
		var range = document.createRange();
		range.selectNode(el);
		window.getSelection().removeAllRanges(); 
		window.getSelection().addRange(range); 
		document.execCommand("copy");
		window.getSelection().removeAllRanges();
		alert('Copié:\n\n'+el.textContent)
	}
	for (let e of document.querySelectorAll("div.ico ")) {
		e.addEventListener('click',function(){
			let el=e.querySelector('b');
			copyDivToClipboard(el);
		});
	}
</script>
<style>
	.help {max-width:960px;margin:auto;}
	.ico b {border:solid 1px slategray;background:#bee;border-radius:5px;padding:3px;}
	.help :is(h2,h3,h4) {color:#009EEB;text-decoration: 1px underline red}
	.help h1 {display:grid;grid-template-columns:1fr 2fr;align-items:center;color:#B5E61D;font-size:3.5em;font-weight:bold;-webkit-text-stroke:2px #009EEB;text-shadow:1px 1px 5px #009EEB;}
	.help h1::before {content:url(../../plugins/addEvents/event-icon.png);}
</style>