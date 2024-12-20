<div class="help">
	<h1>Help for the addEvents plugin</h1>
	<h2>Associate an article with a date</h2>
	<p>Edit or create a new article by filling in the date field in the blue box at the top right of the editing area</p>
	<h2>Dissociate an article from a date</h2>
	<p>Edit the article by deleting the date entered in the blue box at the top right of the editing area</p>
	<h2>static page or category</h2>
	<p>Both are available</p>
	<h3>The "Rendez-Vous" category</h3>
	<p>upon activation, the plugin searches to see if a category is already dedicated to events, otherwise, it searches for a "rendez-vous" category and will create it if it does not exist. You can rename it later.</p>
	<p>Articles with a date field will be automatically added to this category when saving</p>
	<p>This category is only available for these articles, by emptying the "event date" field, they will be automatically unsubscribed from this category when saving</p>
	<h3>The static page</h3>
	<p>You have the choice to activate it or not and to rename it, it sorts your articles by event date and <b>not by article date</b>.</p>
	<h2>A widget?</h2>
	<p>Yes</p>
	<p>By default this will display the list of 5 events starting with the date of the last</p>
<p>To display the widget, you must integrate the following code into your theme, at the location where you want it to be displayed</p></p>
<div class="ico"><b style="color:blue">&lt;?php eval($plxShow->callHook('addEventswidget')); ?&gt;</b> &#128203;</div>
<h3>Modules</h3>
<p>The widget can display another module of your choice</p>
<p>for example, to display the list you can also indicate it in the hook by the name of its module:</p>
<div class="ico" title="click to copy the widget code"><b style="color:blue">&lt;?php eval($plxShow->callHook('addEventswidget','list')); ?&gt;</b> &#128203;</div>
<p>A calendar module is available, you can display it by indicating its module name:</p>
<div class="ico" title="click to copy the widget code"><b style="color:blue">&lt;?php eval($plxShow->callHook('addEventswidget','calendrier')); ?&gt;</b> &#128203;</div><
<p>A "today" module is available. It displays an insert with the teaser image and the
link to the article linked to an event of the day if there is one. You can display it by specifying its module name:</p>
<div class="ico" title="click to copy widget code"><b style="color:blue">&lt;?php eval($plxShow->callHook('addEventswidget','today')); ?&gt;</b> &#128203;</div>
<p>You can create your own module and display it by specifying its name</p>
</div>
<script>
	function copyDivToClipboard(el) {
		var range = document.createRange();
		range.selectNode(el);
		window.getSelection().removeAllRanges();
		window.getSelection().addRange(range);
		document.execCommand("copy");
		window.getSelection().removeAllRanges();
		alert('Copied:\n\n'+el.textContent) } for (let e of document.querySelectorAll("div.ico ")) { e.addEventListener('click',function(){ let el=e.querySelector('b'); copyDivToClipboard(el); });
	} </script> 
	<style> 
	.help {max-width:960px;margin:auto;} 
	.ico b {border:solid 1px slategray;background:#bee;border-radius:5px;padding:3px;} 
	.help :is(h2,h3,h4) {color:#009EEB;text-decoration: 1px underline red} 
	.help h1 {display:grid; grid-template-columns:1fr 2fr;align-items:center;color:#B5E61D;font-size:3.5em;font-weight:bold;-webkit-text-stroke:2px #009EEB;text-shadow:1px 1px 5px #009EEB;} 
	.help h1::before {content:url(../../plugins/addEvents/event-icon.png);} 
	</style>	