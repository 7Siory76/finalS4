<?php
require_once __DIR__ . '/../models/SalaireModel.php';
require_once __DIR__ . '/../helpers/Utils.php';



class SalaireController {
    public static function getAll() {
        $Salaires = SalaireModel::getAll();
        Flight::json($Salaires);
    }

    public static function getById($id) {
        $Salaire = SalaireModel::getById($id);
        Flight::json($Salaire);
    }

    public static function create() {
        $email = Flight::request()->data->email;
        $salaire = Flight::request()->data->salaire;
        $data=['email'=>$email,
                'salaire_mensuel'=>$salaire];
        $id = SalaireModel::create($data);
        Flight::json(['message' => 'Étudiant ajouté']);
    }

    

    
}
