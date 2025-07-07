<?php
require_once __DIR__ . '/../db.php';

class Pret_rModel {
    public static function getAll() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM vue_details_pret");
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ ceci retourne un tableau
}

}
