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

            if($stmt->execute()) {
                $ratings = $this->getRatings($post["burger_id"]);

                $average = 0;
                $totalCount = 0;

                foreach ($ratings as $rating) {
                    $totalCount += $rating["rating"];
                }

                if(count($ratings) > 0) {
                    $average = $totalCount / count($ratings);
                }

                return $average;
            }

            return false;
        } catch(PDOException $e) {
            die($e->getMessage());
        }

        return false;
    }

    public function getRatings($burger_id) {
        $sql = 'SELECT rating FROM mrb_ratings WHERE burger_id = :burger_id';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':burger_id', $burger_id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }
}