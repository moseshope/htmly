<?php if (!empty($breadcrumb)):?><div class="breadcrumb"><?php echo $breadcrumb ?></div><?php endif;?>
<?php $i = 0; $len = count($posts);?>
<?php foreach($posts as $p):?>
    <?php 
		if ($i == 0) {
			$class = 'first';
		} 
		elseif ($i == $len - 1) {
			$class = 'last';
		}
		else {
			$class = '';
		}
		$i++;		
	?>
	<div class="post <?php echo $class ?>" itemprop="blogPost" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
		<div class="main">
			<h2 class="title-index" itemprop="name"><a href="<?php echo $p->url?>"><?php echo $p->title ?></a></h2>
			<div class="date"><span itemprop="datePublished"><?php echo date('d F Y', $p->date)?></span> - Posted in <span itemprop="articleSection"><a href="<?php echo $p->tagurl ?>"><?php echo $p->tag ?></a></span> by <span itemprop="author"><a href="<?php echo $p->authorurl ?>"><?php echo $p->author ?></a></span><?php if (disqus_count() == true):?> - <span><a href="<?php echo $p->url?>#disqus_thread">Comments</a></span><?php endif;?></div>
			<div class="body" itemprop="articleBody">
				<?php if (config('img.thumbnail') == 'true'):?>
					<?php echo get_thumbnail($p->body)?>
				<?php endif;?>
				<?php echo get_teaser($p->body, $p->url)?>
			</div>
		</div>
	</div>
<?php endforeach;?>
<?php if (!empty($pagination['prev']) || !empty($pagination['next'])):?>
	<div class="pager">
		<?php if (!empty($pagination['prev'])):?>
			<span><a href="?page=<?php echo $page-1?>" class="pagination-arrow newer" rel="prev">Newer</a></span>
		<?php endif;?>
		<?php if (!empty($pagination['next'])):?>
			<span><a href="?page=<?php echo $page+1?>" class="pagination-arrow older" rel="next">Older</a></span>
		<?php endif;?>
	</div>
<?php endif;?>
<?php if (disqus_count() == true):?>
	<?php echo disqus_count() ?>
<?php endif;?>