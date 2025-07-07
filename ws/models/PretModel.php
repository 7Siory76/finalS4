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
        public static function createPret($data) {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO pret 
                                 (date_debut, date_fin, montant_total, Id_type_remboursement_, 
                                  Id_usage, Id_type_pret, Id_client, Id_type_assurance) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['date_debut'],
                $data['date_fin'],
                $data['montant_total'],
                $data['Id_type_remboursement_'],
                $data['Id_usage'],
                $data['Id_type_pret'],
                $data['Id_client'],
                //$data['Id_type_assurance'] ?? null // Optionnel
            ]);
            return $db->lastInsertId();
        }
        public static function createMontantAPayer($data) {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO montant_a_payer_par_mois 
                                 (Id_pret, mois, annee, montant, Id_status) 
                                 VALUES (?, ?, ?, ?, 2)");
            $stmt->execute([
                $data['Id_pret'],
                $data['mois'],
                $data['annee'],
                $data['montant'],
            ]);
            return $db->lastInsertId();
        }
        public static function getPretDetail() {
            $db = getDB();
            $stmt = $db->query("SELECT * FROM vue_usage_type_pret_client");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }public static function getLastPretId() {
            $db = getDB();
            $stmt = $db->query("SELECT MAX(Id_pret) FROM pret");
            return $stmt->fetchColumn(); // Retourne directement la valeur max
        }
    }
