<!DOCTYPE html>
<html>
<head>
    <link href='<?php echo site_url() ?>favicon.ico' rel='icon' type='image/x-icon'/>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" user-scalable="no"/>
    <title>Search results not found! - <?php echo blog_title() ?></title>
    <link href="<?php echo site_url() ?>themes/clean/css/style.css" rel="stylesheet"/>
    <link href="//fonts.googleapis.com/css?family=Open+Sans+Condensed:700&subset=latin,cyrillic-ext" rel="stylesheet"/>
</head>
<body>
<div class="center message">
    <h1>Search results not found!</h1>
    <div class="search">
        <?php echo search() ?>
    </div>
    <p>Please search again, or would you like to try our <a href="<?php echo site_url() ?>">homepage</a> instead?</p>
</div>
</body>
</html>