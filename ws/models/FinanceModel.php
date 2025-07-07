<?php
require_once __DIR__ . '/../db.php';

class FinanceModel {
    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO mouvement_fond (montant_, date_, description, type_mouvement) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data->montant, $data->date, $data->description, $data->type_mouvement]);
        return $db->lastInsertId();
    }
}