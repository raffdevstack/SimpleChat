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

    public function userFinder($email) {
        $con = $this->connect();
        $arr['email'] = $email;

        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $con->prepare($query);
        $check = $stmt->execute($arr);

        if ($check) {
            $result = $stmt->fetchAll(PDO::FETCH_OBJ); // it needs to be an object
            if (is_array($result) && count($result) > 0) {
                return $result[0];
            } return false;
        } return false;

    }

    public function userFinderId($id) {
        $con = $this->connect();
        $arr['userid'] = $id;

        $query = "SELECT * FROM users WHERE userid = :userid LIMIT 1";
        $stmt = $con->prepare($query);
        $check = $stmt->execute($arr);

        if ($check) {
            $result = $stmt->fetchAll(PDO::FETCH_OBJ); // it needs to be an object
            if (is_array($result) && count($result) > 0) {
                return $result[0];
            } return false;
        } return false;
    }

    public function groupFinderId($group_id) {
        $con = $this->connect();
        $arr['group_id'] = $group_id;

        $query = "SELECT * FROM `groups` WHERE `id` = :group_id ";
        $stmt = $con->prepare($query);
        $check = $stmt->execute($arr);

        if ($check) {
            $result = $stmt->fetchAll(PDO::FETCH_OBJ); // it needs to be an object
            if (is_array($result) && count($result) > 0) {
                return $result[0];
            } return false;
        } return false;
    }

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
              WHERE (`group_id` IS NULL) AND  
                    ((`receiver` = :userid AND `deleted_receiver` = 0) OR (`sender` = :userid AND `deleted_sender` = 0))
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

    public function getChatMessages($chat_id, $receiver_id, $sender_id) {
        $con = $this->connect();
        $arr['chat_id'] = $chat_id;
        $arr['receiver_id'] = $receiver_id;
        $arr['sender_id'] = $sender_id;
        $query = "SELECT * FROM `messages` 
             WHERE `chat_id` = :chat_id AND 
                   (
                       (`receiver` = :receiver_id AND `sender` = :sender_id AND `deleted_sender` = 0) OR 
                       (`receiver` = :sender_id AND `sender` = :receiver_id AND `deleted_receiver` = 0) 
                    ) 
             ORDER BY id DESC LIMIT 10";
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
        $query = "SELECT * FROM `users` WHERE `userid` = :receiver_userid LIMIT 1";
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

    public function hasPermission($userId, $groupId, $permissionName) {
        $con = $this->connect();

        $data = [
            'user_id' => $userId,
            'group_id' => $groupId,
            'permission_name' => $permissionName
        ];

        $query = "SELECT COUNT(*) as count
              FROM group_member_roles gmr
              JOIN role_permissions rp ON rp.role_id = gmr.role_id
              JOIN permissions p ON p.id = rp.permission_id
              WHERE gmr.user_id = :user_id 
              AND gmr.group_id = :group_id
              AND p.name = :permission_name";

        $stmt = $con->prepare($query);
        $check = $stmt->execute($data);

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