<?php

class User
{
    protected $db;

    public function __construct ($db)
    {
        $this->db = $db;
    }

    public function get($id) {
        $sql = 'SELECT * FROM mrb_users WHERE id = :id';
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {}

        return false;
    }

    public function add ($data) {
        $sql = 'INSERT INTO mrb_users (id, name, gender, device_token) VALUES (:id, :name, :gender, :device_token)';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $data['id']);
            $stmt->bindValue(':name', $data['name']);
            $stmt->bindValue(':gender', $data['gender']);
            $stmt->bindValue(':device_token', $data['device_token']);
            $stmt->execute();
            return true;
        } catch(PDOException $e) {}

        return false;
    }

    public function hasFree ($user_id) {
        //$sql = 'SELECT * FROM mrb_creations WHERE user_id = :user_id AND event_id = :event_id AND used = 0';
        $sql = "SELECT mrb_burgers.event_id,
                        mrb_burgers.id AS burger_id,
                        mrb_burgers.created,
                              mrb_creations.*
                       FROM mrb_burgers
                       INNER JOIN mrb_creations
                          ON mrb_burgers.id = mrb_creations.burger_id
                        WHERE mrb_burgers.event_id = :event_id
                        AND mrb_creations.user_id = :user_id";

        try {
            $event = new Event($this->db);
            $currentEvent = $event->getCurrentEvent('2013-06-07');
            $eventID = $currentEvent["id"];

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $user_id);
            $stmt->bindValue(':event_id', $eventID);

            $stmt->execute();
            $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($all) > 0) {
                return false;
            } else {
                return true;
            }
            return count($stmt->fetch(PDO::FETCH_ASSOC));
        } catch(PDOException $e) {echo $e->getMessage();}

        return false;
    }

    public function getUsersWithFreeBurgers($limit) {
        $sql = "SELECT mrb_users.*, mrb_burgers.event_id
                        FROM mrb_users
                        INNER JOIN mrb_creations
                            ON mrb_creations.user_id = mrb_users.id
                        INNER JOIN mrb_burgers
                            ON mrb_burgers.id = mrb_creations.burger_id
                        WHERE mrb_creations.used = 0 AND mrb_burgers.event_id = :event_id
                        GROUP BY mrb_users.id
                        LIMIT 0, :stop";

        try {
            $event = new Event($this->db);
            $currentEvent = $event->getCurrentEvent('2013-06-07');
            $eventID = $currentEvent["id"];

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':event_id', $eventID);
            $stmt->bindValue(':stop', $limit);

            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $users;
        } catch(PDOException $e) {echo $e->getMessage();}
        
        return false;
    }
    
}