<?php

use app\models\AppModel;

require_once __DIR__ . '/../models/FinanceModel.php';
require_once __DIR__ . '/../models/AppModel.php';
require_once __DIR__ . '/../helpers/Utils.php';


class FinanceController {
    public static function ajouterFond() {     
        $db=getDB();
        $data = Flight::request()->data;
        $app_model= new AppModel($db);
        $app_model->insert('mouvement_fond:', [
            'montant_' => $data->montant,
            'date_' => date('Y-m-d H:i:s'),  
            'description_' => $data->description
        ]);
        Flight::render('form_ajout.html');
    }

//     public static function getAll() {
//         $finances = FinanceModel::getAll();
//         Flight::json($finances);
//     }

//     public static function getById($id) {
//         $finance = FinanceModel::getById($id);
//         Flight::json($finance);
//     }

//     public static function create() {
//         $data = Flight::request()->data;
//         $id = FinanceModel::create($data);
//         Flight::json(['message' => 'Finance ajoutée', 'id' => $id]);
//     }

//     public static function update($id) {
//         $data = Flight::request()->data;
//         FinanceModel::update($id, $data);
//         Flight::json(['message' => 'Finance modifiée']);
//     }

//     public static function delete($id) {
//         FinanceModel::delete($id);
//         Flight::json(['message' => 'Finance supprimée']);
//     }
}