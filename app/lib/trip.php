<?php namespace indagare\trips;

class Trip {
    private $id;
    private $member_id;
    public $start_date;
    public $end_date;
    public $pdf_content_type;
    public $pdf_file_name;
    public $is_canceled;
    public $room_rate;
    
    public function __construct($id, $member_id) {
        $this->id = $id;
        $this->member_id = $member_id;
    }
    
    public function getId() {
        $this->id;
    }
    
    public function getMemberId() {
        $this->member_id;
    }
}

