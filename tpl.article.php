<?php if(!defined('PLX_ROOT')) exit; ?>
	<section class="article events" id="post-<?php echo intval($art['numero']) ?>">			
		<header>
			<h2>
				<a href="<?= $linkToArticle ?>"><?= $art['title']?></a>
			</h2>
		</header>
		<?php if($art['thumbnail'] !='') echo '<img class="art_thumbnail" src="'.$art['thumbnail'].'" alt="'.$art['thumbnail_alt'].'"/>'; ?>
		<?php if($art['chapo'] !='') {
			echo $art['chapo'];
			echo '<p class="more"><a href="'.$linkToArticle.'">'.str_replace('#art_title', $art['title'] , L_ARTCHAPO ).'</a></p>'; 
		}else echo $art['content']; ?>			
	</section>