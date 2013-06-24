<?php

class Creation
{
    protected $db;

    public function __construct ($db)
    {
        $this->db = $db;
    }

    public function add($post) {
        $sql = 'INSERT INTO mrb_creations(burger_id, user_id, ingredient_id) VALUES(:burger_id, :user_id, :ingredient_id)';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':burger_id', $post["burger_id"]);
            $stmt->bindValue(':user_id', $post["user_id"]);
            $stmt->bindValue(':ingredient_id', $post["ingredient_id"]);
            $stmt->execute();

            return $this->db->lastInsertId();
        } catch(PDOException $e) {
            die($e->getMessage());
        }

        return false;
    }

    public function get($id) {
        $sql = 'SELECT id, ingredient_id, user_id FROM mrb_creations WHERE burger_id = :burger_id';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':burger_id', $id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {}

        return false;
    }

    public function pay($user_id, $burger_id) {
        $sql = 'UPDATE mrb_creations SET used = :used WHERE burger_id = :burger_id AND user_id = :user_id';

        try {
            $result = array();
            $isUsed = $this->isUsed($user_id, $burger_id);

            $user = new User($this->db);
            $user = $user->get($user_id);

            if(!$isUsed) {
                $url = 'https://api.parse.com/1/push';

                $appId = 'NtnxE18I4w5dIAHEec1UvRrRaMzwYQnY4EZ3VOwz';
                $restKey = 'zM4UDGMAzHbQfZoJMJBxmILBiVluznji6T0iVbFy';

                $push_payload = json_encode(array(
                        "where" => array(
                            "deviceToken" => $user["device_token"],
                        ),
                        "data" => array(
                            "alert" => "dismiss"
                        )
                ));

                ob_start();

                $rest = curl_init();
                curl_setopt($rest,CURLOPT_URL,$url);
                curl_setopt($rest,CURLOPT_PORT,443);
                curl_setopt($rest,CURLOPT_POST,1);
                curl_setopt($rest,CURLOPT_POSTFIELDS,$push_payload);
                curl_setopt($rest,CURLOPT_HTTPHEADER,
                    array("X-Parse-Application-Id: " . $appId,
                        "X-Parse-REST-API-Key: " . $restKey,
                        "Content-Type: application/json"));

                $response = curl_exec($rest);

                ob_end_clean();
            }

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':used', 1);
            $stmt->bindValue(':user_id', $user_id);
            $stmt->bindValue(':burger_id', $burger_id);
            $stmt->execute();

            $result["used"] = $isUsed;
            $result["user"] = $user;

            return $result;
        } catch(PDOException $e) {echo $e->getMessage();}

        return false;
    }

    public function isUsed($user_id, $burger_id) {
        $sql = 'SELECT * FROM mrb_creations WHERE user_id = :user_id AND burger_id = :burger_id AND used = 1';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $user_id);
            $stmt->bindValue(':burger_id', $burger_id);
            $stmt->execute();

            $creation = $stmt->fetch(PDO::FETCH_ASSOC);
            if($creation == false) {
                return false;
            } else {
                return true;
            }
        } catch(PDOException $e) {echo $e->getMessage();}

        return false;
    }
    
}