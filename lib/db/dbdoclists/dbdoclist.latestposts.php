<?php
class LatestPostsDBDocList extends DBDocList {
    
    const DEFAULT_LIMIT = 10;
    const DOCTYPE = 'post';

    private $limit = self::DEFAULT_LIMIT;

    function __construct($limit = self::DEFAULT_LIMIT) {
        $this->setLimit($limit);
        $this->doctype = self::DOCTYPE;
        $query = 'select p.id, p.title, p.body, p.tags, p.created, a.nickname as author, a.id as author_id from posts p, users a
                         where p.author_id=a.id order by p.created desc limit '.(int)$limit;
        parent::__construct($query, $this->limit);
    }

    private function setLimit($limit) {
        $this->limit = is_numeric($limit) ? $limit : self::DEFAULT_LIMIT;
    }
}
?>
