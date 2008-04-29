<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<link href="/css/redberry.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div id="banner">
    <h1>Redberry</h1>
</div>
<div id="main">
<h3>There's not much to see here, <strong>yet</strong></h3>
    <div id="latest_posts">
        <?php if(is_array($latest_posts)): ?>
            <?php foreach($latest_posts as $post): ?>
                <?php print Template::render('post', array('post' => $post)); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <h3>Sorry, there are no posts to display right now.  Please check back soon.</h3>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
