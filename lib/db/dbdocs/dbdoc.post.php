<?php
class PostDBDoc extends DBDoc {

    public $id;
    public $title;
    public $body;
    public $created;
    public $author_id;
    public $tags;
    
    function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
    }

    public function loadPost($id) {
        $sql = 'select p.id, p.title, p.body, p.created, p.tags, a.nickname as author, a.id as author_id from posts p, users a where p.id=? and p.author_id=a.id';
        if(!$result = DB::fetchOne($sql, $id)) { Log::error(__CLASS__.': Could not load post with id '.$id); return false; }
        else return $result;
    }

    public function insertPost() {
        if(isset($this->id) && is_numeric($this->id)) return false;
        $this->tags = isset($this->tags) ? $this->cleanTags($this->tags) : '';
        $sql = 'insert into posts (title, body, created, author_id, tags) values(?, ?, now(), ?, ?)';
        Log::debug(__CLASS__.': Saving post: '.$this->title);
        if(DB::q($sql, $this->title, $this->body, $this->author_id, $this->tags) && is_numeric($this->id = DB::getLastInsertId())) {
            Log::debug(__CLASS__.': Saved new post: '.$this->id.':'.$this->title);
            return $this->id;
        } else {
            Log::error(__CLASS__.': Could not save post: '.DB::error());
            return false;
        }
    }

    public function updatePost($post) {
        $sql = 'update posts set title=?, body=?, tags=? where id=?';
        return DB::q($sql, $post->title, $post->body, $post->tags, $post->id);
    }

    public function deletePost($id) {
        $sql = 'delete from posts where id=?';
        return DB::q($sql, $id);
    }

    public function cleanTags($tags) {
        Log::debug(__CLASS__.': Cleaning tags: '.$tags);
        if(is_array($tags)) $tags = strtolower(implode(' ', $tags));
        $tags = preg_replace('/[^a-z0-9_]+/', ' ', $tags);
        $tags = preg_replace('/[\s]{2,}/', ' ', $tags);
        Log::debug(__CLASS__.': Cleaned tags: '.$tags);
        return $tags;
    }
}
?>
