<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<?php $i = 0; $len = count($posts); ?>
<?php foreach ($posts as $p): ?>
    <?php if ($i == 0) {
        $class = 'post first';
    } elseif ($i == $len - 1) {
        $class = 'post last';
    } else {
        $class = 'post';
    }
    $i++; ?>
    <div class="<?php echo $class ?>" itemprop="blogPost" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
        <div class="main">
            <h2 class="title-index" itemprop="name"><a href="<?php echo $p->url ?>"><?php echo $p->title ?></a></h2>
            <div class="date">
                <span itemprop="datePublished"><?php echo date('d F Y', $p->date) ?></span> - Posted in
                <span itemprop="articleSection"><?php echo $p->tag ?></span> by
                <span itemprop="author"><a href="<?php echo $p->authorUrl ?>"><?php echo $p->author ?></a></span>
                <?php if (disqus_count()) { ?> - 
                    <span><a href="<?php echo $p->url ?>#disqus_thread">Comments</a></span>
                <?php } elseif (facebook()) { ?> -
                    <a href="<?php echo $p->url ?>#comments"><span><fb:comments-count href=<?php echo $p->url ?>></fb:comments-count> Comments</span></a>
                <?php } ?>
            </div>
            <?php if (!empty($p->image)) { ?>
                <div class="featured-image">
                    <a href="<?php echo $p->url ?>"><img src="<?php echo $p->image; ?>" alt="<?php echo $p->title ?>"/></a>
                </div>
            <?php } ?>
            <?php if (!empty($p->video)) { ?>
                <div class="featured-video">
                   <iframe src="https://www.youtube.com/embed/<?php echo $p->video; ?>" width="560" height="315" frameborder="0" allowfullscreen></iframe>
                </div>
            <?php } ?>
            <div class="teaser-body" itemprop="articleBody">
                <?php echo get_thumbnail($p->body) ?>
                <p><?php echo get_teaser($p->body) ?>
                    <?php if (config('teaser.type') === 'trimmed'):?> ... <a href="<?php echo $p->url;?>#more">more</a><?php endif;?>
                </p>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
    <div class="pager">
        <?php if (!empty($pagination['prev'])): ?>
            <span><a href="?page=<?php echo $page - 1 ?>" class="pagination-arrow newer" rel="prev">Newer</a></span>
        <?php endif; ?>
        <?php if (!empty($pagination['next'])): ?>
            <span><a href="?page=<?php echo $page + 1 ?>" class="pagination-arrow older" rel="next">Older</a></span>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php if (disqus_count()): ?>
    <?php echo disqus_count() ?>
<?php endif; ?>