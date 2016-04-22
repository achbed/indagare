<?php namespace indagare\users;

class Favorite {
    var $id;
    var $article_id;
    var $user_id;
    var $created_at;
    var $updated_at;
    
    public function __construct($id, $article_id, $user_id, $created_at, $updated_at) {
        
        $this->article_id = $article_id;
        $this->created_at = $created_at;
        $this->id = $id;
        $this->updated_at = $updated_at;
        $this->user_id = $user_id;
        
    }
    
    
}

