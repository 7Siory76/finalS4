<?php
require_once __DIR__ . '/../db.php';

class FinanceModel {

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO etudiant (nom, prenom, email, age) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data->nom, $data->prenom, $data->email, $data->age]);
        return $db->lastInsertId();
    }
}
