<?php

class Burger
{
    protected $db;

    public function __construct ($db)
    {
        $this->db = $db;
    }

    public function add($data) {
        $sql = 'INSERT INTO mrb_burgers(created, event_id) VALUES(:created, :event_id)';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':created', date('Y-d-m H:i:s'));
            $stmt->bindValue(':event_id', $data['event_id']);
            $stmt->execute();

            return $this->db->lastInsertId();
        } catch(PDOException $e) {}

        return false;
    }

    public function getAll() {
        $sql = 'SELECT 
                    mrb_burgers.*,
                    mrb_events.id AS event_id
                FROM mrb_burgers
                INNER JOIN mrb_events
                    ON mrb_burgers.event_id = mrb_events.id';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            for($i = 0; $i < count($raw); $i++) {
                $raw[$i]['ingredients'] = array();
                $creation = new Creation($this->db);
                $raw[$i]['ingredients'] = $creation->get($raw[$i]['id']);
            }

            return $raw;
        } catch(PDOException $e) {die($e->getMessage($id));}

        return false;
    }

    public function get($id) {
        $sql = 'SELECT
                    mrb_burgers.id,
                    mrb_burgers.created,
                    mrb_burgers.event_id,
                    mrb_events.name,
                    mrb_events.start AS event_start,
                    mrb_events.end AS event_end,
                    mrb_creations.*,
                    mrb_ingredients.id,
                    mrb_ingredients.name AS ingredient_name,
                    mrb_ingredients.type,
                    mrb_users.name
                FROM mrb_creations
                INNER JOIN mrb_ingredients
                    ON mrb_creations.ingredient_id = mrb_ingredients.id
                INNER JOIN mrb_users
                    ON mrb_creations.user_id = mrb_users.id
                INNER JOIN mrb_burgers
                    ON mrb_burgers.id = mrb_creations.hamburger_id
                INNER JOIN mrb_events
                    ON mrb_burgers.event_id = mrb_events.id
                WHERE mrb_creations.hamburger_id = :id';  

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // die(var_dump($raw));

            $clean = array();
            $clean["id"] = $raw[0]["hamburger_id"];
            $clean["created"] = $raw[0]["created"];
            $clean["event_id"] = $raw[0]["event_id"];
            $clean["ingredients"] = array();

            foreach($raw as $raw_ingredient) {
                $ingredient = array();
                $ingredient["id"] = $raw_ingredient["id"];
                $ingredient["user_id"] = $raw_ingredient["user_id"];
                $ingredient["user_name"] = $raw_ingredient["name"];
                $ingredient["type"] = $raw_ingredient["type"];
                $ingredient["name"] = $raw_ingredient["ingredient_name"];
                array_push($clean["ingredients"], $ingredient);
            }


            return $clean;
        } catch(PDOException $e) {die($e->getMessage());}

        return false;
    }
    
}