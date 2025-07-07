<?php
use app\models\AppModel;
require_once __DIR__ . '/../models/TypePretModel.php';
require_once __DIR__ . '/../helpers/Utils.php';

class TypePretController {
    public static function ajouterTypePret() {    
        // Activer l'affichage des erreurs pour le debug
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        // Headers CORS si nécessaire
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');
        
        // Gérer les requêtes OPTIONS (preflight)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }
        
        try {
            // Nettoyage du buffer de sortie pour éviter les caractères indésirables
            if (ob_get_level()) {
                ob_clean();
            }
            
            // Récupération des données avec meilleure gestion
            $nom = $_POST['nom'] ?? null;
            $taux_interet_annuel = $_POST['taux_interet_annuel'] ?? null;
            $duree_remboursement_en_mois = $_POST['duree_remboursement_en_mois'] ?? null;
            $montant_min = $_POST['montant_min'] ?? null;
            $montant_max = $_POST['montant_max'] ?? null;
            $frais = $_POST['frais'] ?? null;
            $remboursement_fixe = isset($_POST['remboursement_fixe']) ? 1 : 0;
            
            // Validation des données
            if (empty($nom) || empty($taux_interet_annuel) || empty($frais)) {
                throw new Exception("Les champs nom, taux d'intérêt et frais sont obligatoires");
            }
            
            // Validation des types numériques
            if (!is_numeric($taux_interet_annuel) || !is_numeric($frais)) {
                throw new Exception("Les taux d'intérêt et frais doivent être numériques");
            }
            
            if ($duree_remboursement_en_mois !== null && $duree_remboursement_en_mois !== '' && !is_numeric($duree_remboursement_en_mois)) {
                throw new Exception("La durée de remboursement doit être numérique");
            }
            
            if ($montant_min !== null && $montant_min !== '' && !is_numeric($montant_min)) {
                throw new Exception("Le montant minimum doit être numérique");
            }
            
            if ($montant_max !== null && $montant_max !== '' && !is_numeric($montant_max)) {
                throw new Exception("Le montant maximum doit être numérique");
            }
            
            $data = (object) [
                'nom' => trim($nom),
                'taux_interet_annuel' => floatval($taux_interet_annuel),
                'duree_remboursement_en_mois' => ($duree_remboursement_en_mois !== null && $duree_remboursement_en_mois !== '') ? intval($duree_remboursement_en_mois) : null,
                'montant_min' => ($montant_min !== null && $montant_min !== '') ? intval($montant_min) : null,
                'montant_max' => ($montant_max !== null && $montant_max !== '') ? intval($montant_max) : null,
                'frais' => intval($frais),
                'remboursement_fixe' => $remboursement_fixe
            ];
            
            TypePretModel::create($data);
            
            // Réponse JSON propre
            $response = [
                'success' => true, 
                'message' => 'Type de prêt ajouté avec succès'
            ];
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            // Log l'erreur pour le debug
            error_log("Erreur TypePretController: " . $e->getMessage());
            
            http_response_code(400);
            
            $response = [
                'success' => false, 
                'error' => $e->getMessage()
            ];
            
            echo json_encode($response);
        }
        
        // Forcer l'arrêt du script pour éviter d'autres sorties
        exit();
    }
}