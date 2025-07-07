<?php
    require_once __DIR__ . '/../db.php';

    class PretModel {
        public static function getAll() {
            $db = getDB();
            $stmt = $db->query("SELECT * FROM client");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public static function getById($id) {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM client WHERE Id_client= ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public static function create($data) {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO client (email, salaire_mensuel) VALUES (?, ?)");
            $stmt->execute([$data['email'], $data['salaire_mensuel']]);
            return $db->lastInsertId();
        }
        public static function getPretDetail() {
            $db = getDB();
            $stmt = $db->query("SELECT * FROM vue_usage_type_pret_client");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
