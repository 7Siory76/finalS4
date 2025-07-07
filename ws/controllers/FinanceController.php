<?php
use app\models\AppModel;
require_once __DIR__ . '/../models/FinanceModel.php';
require_once __DIR__ . '/../helpers/Utils.php';

class FinanceController {
    public static function ajouterFond() {    
        // Récupérer les données POST
        $montant = Flight::request()->data->montant ?? $_POST['montant'] ?? null;
        $date = Flight::request()->data->date ?? $_POST['date'] ?? null;
        $description = Flight::request()->data->description ?? $_POST['description'] ?? null;
        $type_mouvement = Flight::request()->data->type_mouvement ?? $_POST['type_mouvement'] ?? null;
        
        $data = (object) [
            'montant' => $montant,
            'date' => $date,
            'description' => $description,
            'type_mouvement' => $type_mouvement
        ];
        
        FinanceModel::create($data);
        Flight::json(['message' => 'Mouvement de fond ajouté']);
    }
}