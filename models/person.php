<?php

abstract class Person implements iPerson {
    public $Id;
    public $FullName;
    public $Email;
    public $Phone;
    public $Image;
    
    public function __construct($_id, $_fullName, $_email, $_phone, $_image) {
        $this->Id       = $_id;
        $this->FullName = $_fullName;
        $this->Email    = $_email;
        $this->Phone    = $_phone;
        $this->Image    = $_image;
    }
}

?>