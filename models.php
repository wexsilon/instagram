<?php

class User {
    public $username;
    public $photo;
    
    public function __construct($u, $p) {
        $this->username = $u;
        $this->photo = $p;
    }
    
}


class Comment {
    public $author;
    public $text;
    
    public function __construct($a, $t) {
        $this->author = $a;
        $this->text = $t;
    }
    
}

class Post {
    public $id;
    public $is_saved;
    public $is_liked;
    public $comments; // array(Comment)
    public $user;     // User
    public $like_count;
    public $caption;
    public $comment_count;
    public $photo;
    public $created_at;
    public function __construct() {
        $this->comments = array();
    }
    
    public function addComment($a, $t) {
        array_push($this->comments, new Comment($a, $t));
    }
}