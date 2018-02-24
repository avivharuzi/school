<?php

class Administrator extends Person {
    private $Password;
    private $RoleId;
    
    public function __construct() {
        if (func_num_args() > 0) {
            $_id            = func_get_arg(0);
            $_fullName      = func_get_arg(1);
            $_email         = func_get_arg(2);
            $_phone         = func_get_arg(3);
            $_image         = func_get_arg(4);
            parent::__construct($_id, $_fullName, $_email, $_phone, $_image);
            $this->Password = func_get_arg(5);
            $this->RoleId   = func_get_arg(6);
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
            return "images/uploads/administrators/" . $this->Image;
        }
    }

    public function getImageWithout() {
        return $this->Image;
    }
      
    public function getPassword() {
        return $this->Password;
    }

    public function getRoleId() {
        return $this->RoleId;
    }

    public function getRoleName() {
        $sql =
        "SELECT role.Name FROM administrator
        LEFT JOIN role on administrator.RoleId = role.Id
        WHERE administrator.Id = $this->Id LIMIT 1";
        $role = DatabaseHandler::single($sql);

        if ($role) {
            return $role->Name;
        } else {
            return false;
        }
    }

    public function setSession() {
        $_SESSION["isLoggedIn"] = true;
        $_SESSION["role"] = $this->getRoleName();
        $_SESSION["administratorId"] = $this->Id;
        header("Location: index.php");
    }

    public function add() {
        $sql = "INSERT INTO administrator (FullName, Email, Phone, Password, RoleId)
        VALUES ('$this->FullName', '$this->Email', '$this->Phone', '$this->Password', '$this->RoleId')";
        return DatabaseHandler::insert($sql);
    }

    public function update() {
        if ($this->Image !== NULL) {
            $sql = "UPDATE administrator SET FullName = '$this->FullName', Email = '$this->Email', Phone = '$this->Phone', RoleId = '$this->RoleId', Image = '$this->Image' WHERE Id = $this->Id";
            return DatabaseHandler::update($sql);
        } else {
            $sql = "UPDATE administrator SET FullName = '$this->FullName', Email = '$this->Email', Phone = '$this->Phone', RoleId = '$this->RoleId' WHERE Id = $this->Id";
            return DatabaseHandler::update($sql);
        }
    }
}

?>