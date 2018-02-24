<?php

class Student extends Person {
    public function __construct() {
        if (func_num_args() > 0) {
            $_id       = func_get_arg(0);
            $_fullName = func_get_arg(1);
            $_email    = func_get_arg(2);
            $_phone    = func_get_arg(3);
            $_image    = func_get_arg(4);
            parent::__construct($_id, $_fullName, $_email, $_phone, $_image);
    	}
    }

    public function getId() {
        return $this->Id;
    }

    public function getFullName() {
        return ucwords($this->FullName);
    }

    public function getEmail() {
        return ucwords($this->Email);
    }

    public function getPhone() {
        return $this->Phone;
    }

    public function getImage() {
        if ($this->Image === NULL) {
            return "images/uploads/defaults/default-profile.png";
        } else {
            return "images/uploads/students/" . $this->Image;
        }
    }

    public function getImageWithout() {
        return $this->Image;
    }

    public function add() {
        $sql = "INSERT INTO student (FullName, Email, Phone) VALUES ('$this->FullName', '$this->Email', '$this->Phone')";
        return DatabaseHandler::insert($sql);
    }

    public function update() {
        if ($this->Image !== NULL) {
            $sql = "UPDATE student SET FullName = '$this->FullName', Email = '$this->Email', Phone = '$this->Phone', Image = '$this->Image' WHERE Id = $this->Id";
            return DatabaseHandler::update($sql);
        } else {
            $sql = "UPDATE student SET FullName = '$this->FullName', Email = '$this->Email', Phone = '$this->Phone' WHERE Id = $this->Id";
            return DatabaseHandler::update($sql);
        }
    }
}

?>