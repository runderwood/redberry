<div class="post" id="post-<?php print $post->id; ?>">
<h4><?php print $post->title; ?></h4>
<h5>posted by <?php print $post->author; ?> <?php print $post->created; ?></h5>
<div class="post-body">
    <?php print $post->body; ?>
</div>
</div>
