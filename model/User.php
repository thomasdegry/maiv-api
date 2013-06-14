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
    
}