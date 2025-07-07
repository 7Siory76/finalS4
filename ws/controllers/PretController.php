<?php
require_once __DIR__ . '/../models/PretModel.php';
require_once __DIR__ . '/../helpers/Utils.php';



class PretController {
    public static function getAll() {
        $Prets = PretModel::getAll();
        Flight::json($Prets);
    }
    public static function getDetailPret() {
        $Prets = PretModel::getPretDetail();
        Flight::json($Prets);
    }


    public static function getById($id) {
        $Pret = PretModel::getById($id);
        Flight::json($Pret);
    }

//     public static function create() {
//         // $date_debut = Flight::request()->data->date_debut;
//         // $date_fin = Flight::request()->data->date_fin;
//         // $montant_total = Flight::request()->data->montant_total;
//         // $id_c = Flight::request()->data->id_client;
//         // $id_t = Flight::request()->data->id_type_pret;
//         // $id_us = Flight::request()->data->id_usage;

//         //     $data=['email'=>$email,
//         //             'Pret_mensuel'=>$Pret];
//         //     $id = PretModel::create($data);
//         //     Flight::json(['message' => 'Étudiant ajouté']);
//         // }
//         // Récupération des données de la requête
//     $request = Flight::request()->data;

//     $data = [
//         'date_debut' => $request->date_debut,
//         'date_fin' => $request->date_fin,
//         'montant_total' => $request->montant_total,
//         'Id_type_remboursement_' => $request->Id_type_remboursement_,
//         'Id_usage' => $request->Id_usage,
//         'Id_type_pret' => $request->Id_type_pret,
//         'Id_client' => $request->Id_client,
//         'Id_type_assurance' => $request->Id_type_assurance ?? null
//     ];

//     // Création du prêt
//     $id = PretModel::createPret($data);

//     // Maintenant vous pouvez créer les échéances (montant_a_payer_par_mois)
//     $start = new DateTime($data['date_debut']);
//     $end = new DateTime($data['date_fin']);
//     $diff = $start->diff($end);
//     $months = $diff->y * 12 + $diff->m;
//     $montantMensuel = $data['montant_total'] / $months;

//     $current = clone $start;
//     for ($i = 0; $i < $months; $i++) {
//         $echeance = [
//             'Id_pret' => $idPret,
//             'mois' => (int)$current->format('m'),
//             'annee' => (int)$current->format('Y'),
//             'montant' => $montantMensuel,
//             'Id_status' => 1 // 1 = non payé par défaut
//         ];

//         createMontantAPayer($echeance);
//         $current->add(new DateInterval('P1M')); // Ajoute 1 mois
//     }
public static function create() {
    $request = Flight::request()->data;

    // Validation des données
    if (empty($request->date_debut) || empty($request->date_fin) || 
        empty($request->montant_total) || empty($request->id_type_remboursement) || 
        empty($request->Id_usage) || empty($request->id_type_pret) || 
        empty($request->Id_client)) {
        Flight::json(['message' => 'Tous les champs obligatoires doivent être remplis'], 400);
        return;
    }

    // Création du prêt
    $data = [
        'date_debut' => $request->date_debut,
        'date_fin' => $request->date_fin,
        'montant_total' => $request->montant_total,
        'Id_type_remboursement_' => $request->id_type_remboursement,
        'Id_usage' => $request->id_usage,
        'Id_type_pret' => $request->id_type_pret,
        'Id_client' => $request->id_client,
        //'Id_type_assurance' => $request->Id_type_assurance ?? null
    ];

    $idPret = PretModel::createPret($data);
    $id=PretModel::getLastPretId();
    if (!$idPret) {
        Flight::json(['message' => 'Erreur lors de la création du prêt'], 500);
        return;
    }

    // Calcul des échéances
    $start = new DateTime($data['date_debut']);
    $end = new DateTime($data['date_fin']);
    
    // Calcul du nombre total de mois
    $diff = $start->diff($end);
    $totalMonths = $diff->y * 12 + $diff->m;
    
    // Si la durée est inférieure à 1 mois, on considère 1 mois
    if ($totalMonths < 1) $totalMonths = 1;
    
    // Calcul du montant mensuel
    $montantTotal = (float)$data['montant_total_rembourser'];
    $montantMensuelBase = $montantTotal / $totalMonths;
    
    // Création des échéances
    $current = clone $start;
    $resteARembourser = $montantTotal;
    
    for ($i = 1; $i <= $totalMonths; $i++) {
        // Pour le dernier mois, on prend tout ce qui reste à rembourser
        $montantEcheance = ($i == $totalMonths) ? $resteARembourser : $montantMensuelBase;
        
        $echeance = [
            'Id_pret' => $id,
            'mois' => (int)$current->format('m'),
            'annee' => (int)$current->format('Y'),
            'montant' => round($montantEcheance, 2),
            'Id_status' => 2 // 1 = non payé par défaut
        ];

        // Insertion de l'échéance
        $success = PretModel::createMontantAPayer($echeance);
        
        if (!$success) {
            // En cas d'erreur, on supprime le prêt créé et on retourne une erreur
           // PretModel::deletePret($idPret);
            Flight::json(['message' => 'Erreur lors de la création des échéances'], 500);
            return;
        }
        
        $resteARembourser -= $montantEcheance;
        $current->add(new DateInterval('P1M')); // Ajoute 1 mois
    }

    Flight::json([
        'message' => 'Prêt créé avec succès',
        'id_pret' => $idPret,
        'nombre_echeances' => $totalMonths
    ]);

    
    }
}
