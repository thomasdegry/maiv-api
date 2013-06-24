<?php

class Rating
{
    protected $db;

    public function __construct ($db)
    {
        $this->db = $db;
    }

    public function rate($post) {
        $sql = 'INSERT INTO mrb_ratings(burger_id, voter_id, rating) VALUES(:burger_id, :voter_id, :rating)';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':burger_id', $post["burger_id"]);
            $stmt->bindValue(':voter_id', $post["voter_id"]);
            $stmt->bindValue(':rating', $post["rating"]);
            $stmt->execute();

            return true;
        } catch(PDOException $e) {
            die($e->getMessage());
        }

        return false;
    }
}