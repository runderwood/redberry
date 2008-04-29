<?php
class FrontPageHandler extends Handler {
    
    function construct($request) {
        parent::__construct($request);
    }

    function doGet() {
        return new Response(Template::render('frontpage', array('latest_posts' => DBDocList::get('latestposts')->getList())));
    }
}
?>
