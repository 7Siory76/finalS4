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
        $stmt = $db->prepare("SELECT * FROM client WHERE Id_client = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function createPret($data) {
        try {
            $db = getDB();
            
            // Vérifier que l'ID du type de remboursement par défaut existe
            $stmt = $db->prepare("SELECT COUNT(*) FROM type_remboursement_ WHERE Id_type_remboursement_ = 1");
            $stmt->execute();
            $exists = $stmt->fetchColumn();
            
            if (!$exists) {
                // Insérer un type de remboursement par défaut si il n'existe pas
                $stmt = $db->prepare("INSERT INTO type_remboursement_ (Id_type_remboursement_, libelle, mois) VALUES (1, 'Standard', 12)");
                $stmt->execute();
            }
            
            // Insérer le prêt
            $stmt = $db->prepare("INSERT INTO pret 
                     (date_debut, date_fin, montant_total, Id_type_remboursement_, 
                      Id_usage, Id_type_pret, Id_client,Id_type_assurance) 
                     VALUES (?, ?, ?, 1, ?, ?, ?, ?)");

$result = $stmt->execute([
    $data['date_debut'],           
    $data['date_fin'],             
    $data['montant_total'],        
    $data['Id_usage'],             
    $data['Id_type_pret'],         
    $data['Id_client'],            
    $data['Id_type_assurance']     
]);
            
            if (!$result) {
                error_log("Erreur SQL: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            
            return $db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Erreur PDO dans createPret: " . $e->getMessage());
            return false;
        }
    }

    public static function createMontantAPayer($data) {
        try {
            $db = getDB();
            
            // Vérifier que le status existe
            $stmt = $db->prepare("SELECT COUNT(*) FROM status WHERE Id_status = ?");
            $stmt->execute([$data['Id_status']]);
            $exists = $stmt->fetchColumn();
            
            if (!$exists) {
                // Insérer le status par défaut si il n'existe pas
                $stmt = $db->prepare("INSERT INTO status (Id_status, status) VALUES (?, 'Non payé')");
                $stmt->execute([$data['Id_status']]);
            }
            
            $stmt = $db->prepare("INSERT INTO montant_a_payer_par_mois 
                                 (Id_pret, mois, annee, montant, Id_status) 
                                 VALUES (?, ?, ?, ?, ?)");
            
            $result = $stmt->execute([
                $data['Id_pret'],
                $data['mois'],
                $data['annee'],
                $data['montant'],
                $data['Id_status']
            ]);
            
            if (!$result) {
                error_log("Erreur SQL montant: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            
            return $db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Erreur PDO dans createMontantAPayer: " . $e->getMessage());
            return false;
        }
    }

    public static function getPretDetail() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM vue_usage_type_pret_client");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLastPretId() {
        $db = getDB();
        $stmt = $db->query("SELECT MAX(Id_pret) FROM pret");
        return $stmt->fetchColumn();
    }
}