<?php

class Event
{
    protected $db;

    public function __construct ($db)
    {
        $this->db = $db;
    }

    public function getAll() {
        $sql = 'SELECT * FROM mrb_events';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {}

        return false;
    }

    public function get($id) {
        $sql = 'SELECT * FROM mrb_events WHERE id = :id';
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {}

        return false;
    }
    
}