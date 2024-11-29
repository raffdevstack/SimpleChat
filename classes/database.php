<?php

Class Database {
    private $con;

    function __construct() {
        $this->con = $this->connect();
    }

    private function connect() {
        $info = "mysql:host=localhost;dbname=simple_chat";
        try {
            return new PDO($info, DB_USER, DB_PASS);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die;
        }
    }

    public function write($query, $data_array=[]) { // if no data, the default is empty array
        $con = $this->connect();
        $stmt = $con->prepare($query);
        $check = $stmt->execute($data_array);
        if ($check) {
            return true;
        }
        return false;
    }

    public function read($query, $data_array=[]) { // if no data, the default is empty array (ex. put data if you are searching)
        $con = $this->connect();
        $stmt = $con->prepare($query);
        $check = $stmt->execute($data_array);
        if ($check) {
            $result = $stmt->fetchAll(PDO::FETCH_OBJ); // it needs to be an object
            if (is_array($result) && count($result) > 0) {
                return $result; // return the result object
            }
            return false;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public function chatFinder($receiver_id, $sender_id) {
        $con = $this->connect();
        $arr['receiver_userid'] = $receiver_id;
        $arr['sender_userid'] = $sender_id;

        $query = "SELECT * FROM `messages` 
          WHERE (`receiver` = :receiver_userid AND `sender` = :sender_userid) 
          OR (`receiver` = :sender_userid AND `sender` = :receiver_userid)
          ORDER BY `date` DESC
          LIMIT 1";

        $stmt = $con->prepare($query);
        $check = $stmt->execute($arr);

        if ($check) {
            $result = $stmt->fetchAll(PDO::FETCH_OBJ); // it needs to be an object
            if (is_array($result) && count($result) > 0) {
                return $result[0];
            } return false;
        } return false;
    }

    public function findAllMyChat($user_id) {
        $con = $this->connect();
        $arr['userid'] = $user_id;
        $query = "SELECT m.* FROM `messages` m
          INNER JOIN (
              SELECT `chat_id`, MAX(id) AS latest
              FROM `messages`
              WHERE `receiver` = :userid OR `sender` = :userid
              GROUP BY `chat_id`
          ) latest_messages ON m.`chat_id` = latest_messages.`chat_id` AND m.`id` = latest_messages.latest
          ORDER BY latest_messages.latest DESC";


        $stmt = $con->prepare($query);
        $check = $stmt->execute($arr);

        if ($check) {
            $result = $stmt->fetchAll(PDO::FETCH_OBJ); // it needs to be an object
            if (is_array($result) && count($result) > 0) {
                return $result;
            } return false;
        } return false;
    }

    public function getChatMessages($chat_id) {
        $con = $this->connect();
        $arr['chat_id'] = $chat_id;
        $query = "SELECT * FROM `messages` 
             WHERE `chat_id` = :chat_id ORDER BY id DESC LIMIT 10";
        $stmt = $con->prepare($query);
        $check = $stmt->execute($arr);
        if ($check) {
            $result = $stmt->fetchAll(PDO::FETCH_OBJ); // it needs to be an object
            if (is_array($result) && count($result) > 0) {
                return array_reverse($result); // return the result object
            }
            return false;
        }
        return false;
    }

    public function getChatReceiver($receiver_id) {
        $con = $this->connect();
        $arr['receiver_userid'] = $receiver_id;
        $query = "SELECT * FROM `users` WHERE `userid` = :receiver_userid  LIMIT 1";
        $stmt = $con->prepare($query);
        $check = $stmt->execute($arr);
        if ($check) {
            $result = $stmt->fetchAll(PDO::FETCH_OBJ); // it needs to be an object
            if (is_array($result) && count($result) > 0) {
                return $result[0];
            }
            return false;
        }
        return false;
    }

    public function generate_id($max) {
        $generated_id = 0;
        for ($i = 0; $i < 10; $i++) {
            $generated_id += rand(4, $max);
        }
        return $generated_id;
    }
}