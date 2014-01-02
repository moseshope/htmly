<?php

// Change this to your timezone
date_default_timezone_set('Asia/Jakarta');

use dflydev\markdown\MarkdownParser;
use \Suin\RSSWriter\Feed;
use \Suin\RSSWriter\Channel;
use \Suin\RSSWriter\Item;

// Get blog post
function get_post_names(){

	static $_cache = array();

	if(empty($_cache)){

		// Get the names of all the
		// posts (newest first):

		$_cache = array_reverse(glob('content/blog/*.md'));
	}

	return $_cache;
}

// Get static page 
function get_spage_names(){

	static $_cache = array();

	if(empty($_cache)){

		// Get the names of all the
		// static page (newest first):

		$_cache = array_reverse(glob('content/static/*.md'));
	}

	return $_cache;
}

// Return blog post
function get_posts($page = 1, $perpage = 0){

	if($perpage == 0){
		$perpage = config('posts.perpage');
	}

	$posts = get_post_names();

	// Extract a specific page with results
	$posts = array_slice($posts, ($page-1) * $perpage, $perpage);
	
	$tmp = array();

	// Create a new instance of the markdown parser
	$md = new MarkdownParser();
	
	foreach($posts as $k=>$v){

		$post = new stdClass;

		// Extract the date
		$arr = explode('_', $v);
		
		// The post author + author url
		$post->author = config('blog.author');
		$post->authorurl = site_url() . 'author/' .  config('blog.authorid');
		
		// The post date
		$post->date = strtotime(str_replace('content/blog/','',$arr[0]));
		
		// The archive per day
		$post->archive = site_url(). str_replace('content/blog/','archive/',$arr[0]);

		// The post URL
		$post->url = site_url().date('Y/m', $post->date).'/'.str_replace('.md','',$arr[2]);
		
		// The post tag
		$post->tag = str_replace('content/blog/','',$arr[1]);
		
		// The post tag url
		$post->tagurl = site_url(). 'tag/' . $arr[1];

		// Get the contents and convert it to HTML
		$content = $md->transformMarkdown(file_get_contents($v));

		// Extract the title and body
		$arr = explode('</h1>', $content);
		$post->title = str_replace('<h1>','',$arr[0]);
		$post->body = $arr[1];

		$tmp[] = $post;
	}

	return $tmp;
}

// Find post by year, month and name, previous, and next.
function find_post($year, $month, $name){

	$posts = get_post_names();

	foreach($posts as $index => $v){
		if( strpos($v, "$year-$month") !== false && strpos($v, $name.'.md') !== false){

			// Use the get_posts method to return
			// a properly parsed object

			$ar = get_posts($index+1,1);
			$nx = get_posts($index,1);
			$pr = get_posts($index+2,1);
			
			if ($index == 0) {
				if(isset($pr[0])) {
					return array(
						'current'=> $ar[0],
						'prev'=> $pr[0]
					);
				}
				else {
					return array(
						'current'=> $ar[0],
						'prev'=> null
					);
				}
			}
			elseif (count($posts) == $index+1) {
				return array(
					'current'=> $ar[0],
					'next'=> $nx[0]
				);
			}
			else {
				return array(
					'current'=> $ar[0],
					'next'=> $nx[0],
					'prev'=> $pr[0]
				);
			}
		}
	}
	return false;
}

// Return tag page
function get_tag($tag){

	$posts = get_post_names();
	$tmp = array();

	// Create a new instance of the markdown parser
	$md = new MarkdownParser();

	foreach($posts as $index => $v){
		if( strpos($v, "$tag") !== false){

			$post = new stdClass;

			// Extract the date
			$arr = explode('_', $v);
			
			// Make sure the tag request available
			if ($tag === $arr[1]) {
			
				// The post author + author url
				$post->author = config('blog.author');
				$post->authorurl = site_url() . 'author/' .  config('blog.authorid');
				
				// The post date
				$post->date = strtotime(str_replace('content/blog/','',$arr[0]));

				// The post URL
				$post->url = site_url().date('Y/m', $post->date).'/'.str_replace('.md','',$arr[2]);
				
				// The post tag
				$post->tag = str_replace('content/blog/','',$arr[1]);
				
				// The post tag URL
				$post->tagurl = site_url(). 'tag/' . $arr[1];

				// Get the contents and convert it to HTML
				$content = $md->transformMarkdown(file_get_contents($v));

				// Extract the title and body
				$arr = explode('</h1>', $content);
				$post->title = str_replace('<h1>','',$arr[0]);
				$post->body = $arr[1];

				$tmp[] = $post;
			}
			else {
				not_found();
			}
		}
	}

	return $tmp;
}

// Return an archive page
function get_archive($req){

	$posts = get_post_names();
	$tmp = array();

	// Create a new instance of the markdown parser
	$md = new MarkdownParser();

	foreach($posts as $index => $v){
		if( strpos($v, "$req") !== false){

			$post = new stdClass;

			// Extract the date
			$arr = explode('_', $v);
			
			// The post author + author url
			$post->author = config('blog.author');
			$post->authorurl = site_url() . 'author/' .  config('blog.authorid');
			
			// The post date
			$post->date = strtotime(str_replace('content/blog/','',$arr[0]));

			// The post URL
			$post->url = site_url().date('Y/m', $post->date).'/'.str_replace('.md','',$arr[2]);
			
			// The post tag
			$post->tag = str_replace('content/blog/','',$arr[1]);
			
			// The post tag URL
			$post->tagurl = site_url(). 'tag/' . $arr[1];

			// Get the contents and convert it to HTML
			$content = $md->transformMarkdown(file_get_contents($v));

			// Extract the title and body
			$arr = explode('</h1>', $content);
			$post->title = str_replace('<h1>','',$arr[0]);
			$post->body = $arr[1];

			$tmp[] = $post;
		}
	}

	return $tmp;
}

// Return an archive list, categorized by year and month
function archive_list() {

	$posts = get_post_names();
	$by_year = array();
	$col = array();
	
	foreach($posts as $index => $v){
	
		$arr = explode('_', $v);
		$date = str_replace('content/blog/','',$arr[0]);
		$data = explode('-', $date);
		$col[] = $data;
		
	}
	
	foreach ($col as $row){
	
		$y = $row['0'];
		$m = $row['1'];
		$by_year[$y][] = $m;

	}
	
	# Most recent year first
	krsort($by_year);
	# Iterate for display
	foreach ($by_year as $year => $months){
	
		echo '<span class="year"><a href="' . site_url() . 'archive/' . $year . '">' . $year . '</a></span> ';
		echo '(' . count($months) . ')';
		echo '<ul class="month">';

		# Sort the months
		ksort($months);
		$by_month = array_count_values($months);
		foreach ($by_month as $month => $count){
			$name = date('F', mktime(0,0,0,$month,1,2010));
			echo '<li class="item"><a href="' . site_url() .  'archive/' . $year . '-' . $month . '">' . $name .  '</a>';
			echo ' (' . $count . ')</li>';
			echo '</li>';
		}

		echo '</ul>';
		
	}

}

// Return static page
function get_spage($posts, $spage){

	$tmp = array();

	// Create a new instance of the markdown parser
	$md = new MarkdownParser();

	foreach($posts as $index => $v){
		if( strpos($v, "$spage") !== false && strpos($v, $spage.'.md') !== false){

			$post = new stdClass;

			// Extract the array
			$arr = explode('_', $v);
			
			// The static page URL
			$url = str_replace('content/static/','',$arr[0]);
			$post->url = site_url() . str_replace('.md','',$url);
			
			// Get the contents and convert it to HTML
			$content = $md->transformMarkdown(file_get_contents($v));

			// Extract the title and body
			$arr = explode('</h1>', $content);
			$post->title = str_replace('<h1>','',$arr[0]);
			$post->body = $arr[1];

			$tmp[] = $post;
		}
	}

	return $tmp;
}

// Find static page
function find_spage($spage){
	
	$posts = get_spage_names();

	foreach($posts as $index => $v){
		if( strpos($v, "$spage") !== false && strpos($v, $spage.'.md') !== false){
	
			// Use the get_spage method to return
			// a properly parsed object

			$arr = get_spage($posts, $spage);
			return $arr[0];
		}
	}

	return false;
}

// Return search page
function get_keyword($keyword){

	$posts = get_post_names();
	$tmp = array();

	// Create a new instance of the markdown parser
	$md = new MarkdownParser();

	foreach($posts as $index => $v){
	
		$content = $md->transformMarkdown(file_get_contents($v));
		
		if(strpos(strtolower(strip_tags($content)), strtolower($keyword)) !== false){
		
			$post = new stdClass;

			// Extract the date
			$arr = explode('_', $v);
			
			// Make sure the tag request available
			
			// The post author + author url
			$post->author = config('blog.author');
			$post->authorurl = site_url() . 'author/' .  config('blog.authorid');
			
			// The post date
			$post->date = strtotime(str_replace('content/blog/','',$arr[0]));

			// The post URL
			$post->url = site_url().date('Y/m', $post->date).'/'.str_replace('.md','',$arr[2]);
			
			// The post tag
			$post->tag = str_replace('content/blog/','',$arr[1]);
			
			// The post tag URL
			$post->tagurl = site_url(). 'tag/' . $arr[1];

			// Extract the title and body
			$arr = explode('</h1>', $content);
			$post->title = str_replace('<h1>','',$arr[0]);
			$post->body = $arr[1];
			$tmp[] = $post;
		
		}
	}

	return $tmp;
}

// Helper function to determine whether
// to show the previous buttons
function has_prev($prev){
	if(!empty($prev)) {
		return array(
			'url'=> $prev->url,
			'title'=> $prev->title
		);
	}
}

// Helper function to determine whether
// to show the next buttons
function has_next($next){
	if(!empty($next)) {
		return array(
			'url'=> $next->url,
			'title'=> $next->title
		);
	}
}

// Helper function to determine whether
// to show the pagination buttons
function has_pagination($total, $perpage, $page = 1){
	if(!$total) {
		$total = count(get_post_names());
	}
	return array(
		'prev'=> $page > 1,
		'next'=> $total > $page*$perpage
	);
}

// Get the meta description
function get_description($text) {
	
	$string = explode('</p>', $text);
	$string = preg_replace('@[\s]{2,}@',' ', strip_tags($string[0] . '</p>'));
	
	if (strlen($string) > 1) {
		return $string;
	}
	else {
		$string = preg_replace('@[\s]{2,}@',' ', strip_tags($text));
		if (strlen($string) < config('description.char')) {
			return $string;
		}
		else {
			return $string = substr($string, 0, strpos($string, ' ', config('description.char')));
		}
	}

}

// Get the teaser
function get_teaser($text, $url) {
	
	if (strlen(strip_tags($text)) < config('teaser.char')) {
		$string = preg_replace('@[\s]{2,}@',' ', strip_tags($text));
		$body = $string . '...' . ' <a class="readmore" href="' . $url . '#more">more</a>' ;
		echo '<p>' . $body . '</p>';
	}
	else {
		$string = preg_replace('@[\s]{2,}@',' ', strip_tags($text));
		$string = substr($string, 0, strpos($string, ' ', config('teaser.char')));
		$body = $string . '...' . ' <a class="readmore" href="' . $url . '#more">more</a>' ;
		echo '<p>' . $body . '</p>';
	}

}

// Get thumbnail from image and Youtube.
function get_thumbnail($text) {

	$default = config('default.thumbnail');
    $dom = new DOMDocument();
    $dom->loadHtml($text);
    $imgTags = $dom->getElementsByTagName('img');
	$vidTags = $dom->getElementsByTagName('iframe');
    if ($imgTags->length > 0) {
        $imgElement = $imgTags->item(0);
		$imgSource = $imgElement->getAttribute('src');
        return '<div class="thumbnail" style="background-image:url(' . $imgSource . ');"></div>';
    }
    elseif ($vidTags->length > 0) {
        $vidElement = $vidTags->item(0);
		$vidSource = $vidElement->getAttribute('src');
        $fetch = explode("embed/", $vidSource);
		if(isset($fetch[1])) {
			$vidThumb = '//img.youtube.com/vi/' . $fetch[1] . '/default.jpg';
			return '<div class="thumbnail" style="background-image:url(' . $vidThumb . ');"></div>';
		}
    }
	else {
		if (!empty($default)) {
			return '<div class="thumbnail" style="background-image:url(' . $default . ');"></div>';
		}
    }
	
}

// Use base64 encode image to speed up page load time.
function base64_encode_image($filename=string,$filetype=string) {
    if ($filename) {
        $imgbinary = fread(fopen($filename, "r"), filesize($filename));
        return 'data:image/' . $filetype . ';base64,' . base64_encode($imgbinary);
    }
}

// Social links
function social(){

	$twitter = config('social.twitter'); 
	$facebook = config('social.facebook'); 
	$google = config('social.google'); 
	$tumblr = config('social.tumblr');
	$rss = site_url() . 'feed/rss';
	
	if (!empty($twitter)) {
		echo '<a href="' . $twitter . '" target="_blank"><img src="' . site_url() . 'themes/default/img/twitter.png" width="32" height="32" alt="Twitter"/></a>';
	}
	
	if (!empty($facebook)) {
		echo '<a href="' . $facebook . '" target="_blank"><img src="' . site_url() . 'themes/default/img/facebook.png" width="32" height="32" alt="Facebook"/></a>';
	}
	
	if (!empty($google)) {
		echo '<a href="' . $google . '" target="_blank" rel="author"><img src="' . site_url() . 'themes/default/img/googleplus.png" width="32" height="32" alt="Google+"/></a>';
	}
	
	if (!empty($tumblr)) {
		echo '<a href="' . $tumblr . '" target="_blank"><img src="' . site_url() . 'themes/default/img/tumblr.png" width="32" height="32" alt="Tumblr"/></a>';
	}
	
	echo '<a href="' . site_url() . 'feed/rss" target="_blank"><img src="' . site_url() . 'themes/default/img/rss.png" width="32" height="32" alt="RSS Feed"/></a>';
	
}

// Copyright
function copyright(){

	$blogcp = config('blog.copyright');
	$credit = 'Proudly powered by <a href="http://www.htmly.com" target="_blank">HTMLy</a>.';
	
	if (!empty($blogcp)) {
		return $copyright = '<p>' . $blogcp .  '</p><p>' . $credit . '</p>';
	}
	else {
		return $credit = '<p>' . $credit . '</p>';
	}
	
}

// Disqus
function disqus($title, $url){
	$disqus = config('disqus.shortname');
	$script = <<<EOF
	<script type="text/javascript">
		var disqus_shortname = '{$disqus}';
		var disqus_title = '{$title}';
		var disqus_url = '{$url}';
		(function() {
			var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
			dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
			(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		})();
	</script>
EOF;
	if (!empty($disqus)) {
		return $script;
	}
}

// Disqus
function disqus_count(){
	$disqus = config('disqus.shortname');
	$script = <<<EOF
	<script type="text/javascript">
		var disqus_shortname = '{$disqus}';
		(function() {
			var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
			dsq.src = '//' + disqus_shortname + '.disqus.com/count.js';
			(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		})();
	</script>
EOF;
	if (!empty($disqus)) {
		return $script;
	}
}

// Google Publisher
function publisher(){
	$publisher = config('google.publisher');
	if (!empty($publisher)) {
		return $publisher;
	}
}

function analytics(){
	$analytics = config('google.analytics.js');
	if (!empty($analytics)) {
		return $analytics;
	}
}

// Menu
function menu(){
	$menu = config('blog.menu');
	if (!empty($menu)) {
		return $menu;
	}
}

// The not found error
function not_found(){
	error(404, render('404', null, false));
}

// Turn an array of posts into an RSS feed
function generate_rss($posts){
	
	$feed = new Feed();
	$channel = new Channel();
	
	$channel
		->title(config('blog.title'))
		->description(config('blog.description'))
		->url(site_url())
		->appendTo($feed);

	foreach($posts as $p){
		
		$item = new Item();
		$item
			->title($p->title)
			->description($p->body)
			->url($p->url)
			->appendTo($channel);
	}
	
	echo $feed;
}

// Turn an array of posts into a JSON
function generate_json($posts){
	return json_encode($posts);
}
