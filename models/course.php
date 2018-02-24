<?php 

class Course {
    private $Id;
    private $Name;
    private $Description;
    private $Price;
    private $Image;

    public function __construct() {
        if (func_num_args() > 0) {
            $this->Id          = func_get_arg(0);
            $this->Name        = func_get_arg(1);
            $this->Description = func_get_arg(2);
            $this->Price       = func_get_arg(3);
            $this->Image       = func_get_arg(4);
        }
    }

    public function getId() {
        return $this->Id;
    }
    
    public function getName() {
        return strtoupper($this->Name);
    }

    public function getDescription() {
        return $this->Description;
    }

    public function getPrice() {
        return "$" . $this->Price;
    }

    public function getPriceWithout() {
        return $this->Price;
    }

    public function getImage() {
        if ($this->Image === NULL) {
            return "images/uploads/defaults/default-course.png";
        } else {
            return "images/uploads/courses/" . $this->Image;;
        }
    }

    public function getImageWithout() {
        return $this->Image;
    }

    public function add() {
        $sql = "INSERT INTO course (Name, Description, Price) VALUES ('$this->Name', '$this->Description', '$this->Price')";
        return DatabaseHandler::insert($sql);
    }

    public function update() {
        if ($this->Image !== NULL) {
            $sql = "UPDATE course SET Name = '$this->Name', Description = '$this->Description', Price = $this->Price, Image = '$this->Image' WHERE Id = $this->Id";
            return DatabaseHandler::update($sql);
        } else {
            $sql = "UPDATE course SET Name = '$this->Name', Description = '$this->Description', Price = $this->Price WHERE Id = $this->Id";
            return DatabaseHandler::update($sql);
        }
    }
}

?>