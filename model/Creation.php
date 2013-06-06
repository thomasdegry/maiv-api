<?php

class Creation
{
    protected $db;

    public function __construct ($db)
    {
        $this->db = $db;
    }

    public function add($post) {
        $sql = 'INSERT INTO mrb_creations(hamburger_id, user_id, ingredient_id) VALUES(:hamburger_id, :user_id, :ingredient_id)';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':hamburger_id', $post["hamburger_id"]);
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
        $sql = 'SELECT id, ingredient_id, user_id FROM mrb_creations WHERE hamburger_id = :hamburger_id';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':hamburger_id', $id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {}

        return false;
    }
    
}