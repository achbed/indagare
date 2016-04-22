<?php namespace indagare\trips;

    class Itinerary {
       private $id; 
       public $created_at;
       public $description;
       public $title;
       public $updated_at;
       public $user_id;
       
       public function __construct($id, $memberId) {
           $this->id = $id;
           $this->user_id = $memberId;
       }
    }
    
    class ItineraryItem {
       private $id; 
       public $created_at;
       public $article_id;
       public $article_title;
       public $article_abst;
       public $itinerary_id;
       public $updated_at;
       public $position;
       
       public function __construct($id, $itinerayId) {
           $this->id = $id;
           $this->itinerary_id = $itinerayId;
       }
       
       public function toJSON() {
           return "{id: $this->id, pos: $this->position, aid: $this->article_id}";
       }
    }
