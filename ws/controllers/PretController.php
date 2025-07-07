<?php
// Fichier : PretController.php
require_once __DIR__ . '/../models/PretModel.php';

class PretController
{
    public static function getTaux()
    {
        $moisDebut = Flight::request()->query['moisDebut'];
        $anneeDebut = Flight::request()->query['anneeDebut'];
        $moisFin = Flight::request()->query['moisFin'];
        $anneeFin = Flight::request()->query['anneeFin'];
        
        try {
            $resultats = PretModel::getTaux($moisDebut, $anneeDebut, $moisFin, $anneeFin);
            
            if (empty($resultats)) {
                Flight::json(['message' => 'Aucun taux trouvé pour la période spécifiée']);
            } else {
                Flight::json($resultats);
            }
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des taux: ' . $e->getMessage()], 500);
        }
    }
}