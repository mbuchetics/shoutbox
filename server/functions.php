<?php

require_once 'helpers.php';
require_once 'db.php';

class Message {
    public $id;
    public $text;
    public $image;
    public $time;

    function __construct($data) {
        $this->id = (int)$data['id'];
        $this->text = $data['text'];
        $this->image = $data['image'];
        $this->time = $data['time'];
    }
}

function postMessage($text, $image = null) { 
    if($text) {
        $text = autolink($text);
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $conn = Database::getFactory()->getConnection();
    $sql = "INSERT INTO messages (text, image, time, ip, user_agent) VALUES ('$text', '$image', NOW(), '$ip', '$user_agent')";

    if ($conn->exec($sql)) {
        deleteMessages();
    }
}

function getMessages() {
    $conn = Database::getFactory()->getConnection();
    $sql = "SELECT * FROM messages ORDER BY id DESC";

    $messages = array();
    
    foreach ($conn->query($sql) as $row) {
        $messages[] = new Message($row);
    }

    return $messages;
}

function getLastMessage() {
    $conn = Database::getFactory()->getConnection();
    $sql = "SELECT * FROM messages ORDER BY id DESC LIMIT 1";

    $result = $conn->query($sql)->fetch();

    if ($result) {
        return new Message($result);
    }
    else  {
        return null;
    }
}

function deleteMessages() {
    $limit = getConfig('limit');

    $conn = Database::getFactory()->getConnection();
    $sql = "SELECT * FROM messages WHERE id NOT IN (SELECT id FROM (SELECT id FROM messages ORDER BY id DESC LIMIT $limit) temp)";

    foreach ($conn->query($sql) as $row) {
        $message = new Message($row);

        if ($message->image) {
            try {
                unlink('uploads/' . $message->image);
            }
            catch(Exception $e) {
            }
        }

        $conn->exec("DELETE FROM messages WHERE id = $message->id");
    }
}

function saveImage($tmp, $name, $resizeWidth) {
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $dest = 'uploads/' . $filename;

    if ($ext == 'jpg' || $ext == 'jpeg') {
        $img = imagecreatefromjpeg($tmp);
    }
    else if ($ext == 'png') {
        $img = imagecreatefrompng($tmp);
    }

    $width  = imagesx($img);  
    $height = imagesy($img);

    $resizeHeight = $resizeWidth * ($height / $width);

    $resized = imagecreatetruecolor($resizeWidth, $resizeHeight);
    imagecopyresampled($resized, $img, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $width, $height); 

    if ($ext == 'jpg' || $ext == 'jpeg') {
        imagejpeg($resized, $dest);
    }
    else if ($ext == 'png') {
        imagepng($resized, $dest);
    }

    postMessage(null, $filename);
}

?>