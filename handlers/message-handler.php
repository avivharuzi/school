<?php

class MessageHandler {
    private function __construct() {
    }

    public static function error($msg) {
        $response = 
        "<div class='alert alert-danger alert-dismissible fade show'>
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
        if (is_array($msg)) {
            foreach ($msg as $value) {
                $response .= "<p class='lead'><i class='fa fa-exclamation-circle mr-2'></i>$value</p>";
            }
        } else {
            $response .= "<p class='lead'><i class='fa fa-exclamation-circle mr-2'></i>$msg</p>";
        }
        $response .= "</div>";
        return $response;
    }
    
    public static function warning($msg) {
        $response = 
        "<div class='alert alert-warning alert-dismissible fade show'>
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
        if (is_array($msg)) {
            foreach ($msg as $value) {
                $response .= "<p class='lead'><i class='fa fa-exclamation-circle mr-2'></i>$value</p>";
            }
        } else {
            $response .= "<p class='lead'><i class='fa fa-exclamation-circle mr-2'></i>$msg</p>";
        }
        $response .= "</div>";
        return $response;
    }
    
    public static function info($msg) {
        $response = 
        "<div class='alert alert-info alert-dismissible fade show'>
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
        if (is_array($msg)) {
            foreach ($msg as $value) {
                $response .= "<p class='lead'><i class='fa fa-info mr-2'></i>$value</p>";
            }
        } else {
            $response .= "<p class='lead'><i class='fa fa-info mr-2'></i>$msg</p>";
        }
        $response .= "</div>";
        return $response;
    }
    
    public static function success($msg) {
        $response = 
        "<div class='alert alert-success alert-dismissible fade show'>
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
        if (is_array($msg)) {
            foreach ($msg as $value) {
                $response .= "<p class='lead'><i class='fa fa-check-circle-o mr-2'></i>$value</p>";
            }
        } else {
            $response .= "<p class='lead'><i class='fa fa-check-circle-o mr-2'></i>$msg</p>";
        }
        $response .= "</div>";
        return $response;
    }
    
    public static function errorBig($msg) {
        return
        "<div class='jumbotron p-3 text-center text-light bg-danger mt-5'>
            <h3><i class='fa fa-exclamation-circle mr-2'></i>$msg</h3>
        </div>";
    }
    
    public static function warningBig($msg) {
        return
        "<div class='jumbotron p-3 text-center text-light bg-warning mt-5'>
            <h3><i class='fa fa-exclamation-circle mr-2'></i>$msg</h3>
        </div>";
    }
    
    public static function infoBig($msg) {
        return
        "<div class='jumbotron p-3 text-center text-light bg-warning mt-5'>
            <h3><i class='fa fa-info mr-2'></i>$msg</h3>
        </div>";
    }

    public static function successBig($msg) {
        return
        "<div class='jumbotron p-3 text-center text-light bg-success mt-5'>
            <h3><i class='fa fa-check-circle-o mr-2'></i>$msg</h3>
        </div>";
    }
}

?>