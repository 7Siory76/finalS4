<?php
require_once __DIR__ . '/../db.php';

class TypePretModel {
    public static function create($data) {
        try {
            $db = getDB();
            
            // Vérifier la connexion à la base de données
            if (!$db) {
                throw new Exception("Erreur de connexion à la base de données");
            }
            
            $stmt = $db->prepare("INSERT INTO type_pret
                                 (nom, taux_interet_annuel, duree_remboursement_en_mois,
                                  montant_min, montant_max, frais, remboursement_fixe)
                                 VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            if (!$stmt) {
                throw new Exception("Erreur de préparation de la requête: " . $db->errorInfo()[2]);
            }
            
            $result = $stmt->execute([
                $data->nom,
                $data->taux_interet_annuel,
                $data->duree_remboursement_en_mois,
                $data->montant_min,
                $data->montant_max,
                $data->frais,
                $data->remboursement_fixe ? 1 : 0
            ]);
            
            if (!$result) {
                throw new Exception("Erreur d'exécution de la requête: " . $stmt->errorInfo()[2]);
            }
            
            return $db->lastInsertId();
            
        } catch (PDOException $e) {
            throw new Exception("Erreur base de données: " . $e->getMessage());
        }
    }
}