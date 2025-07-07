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

    public static function create() {
        try {
            $request = Flight::request()->data;
            
            // Debug : afficher les données reçues
            error_log("Données reçues: " . print_r($request, true));

            // Validation des données
            if (empty($request->date_debut) || empty($request->date_fin) || 
                empty($request->montant_total) || empty($request->Id_usage) || 
                empty($request->Id_type_pret) || empty($request->Id_client)) {
                Flight::json(['message' => 'Tous les champs obligatoires doivent être remplis'], 400);
                return;
            }

            // Vérifier que les dates sont valides
            $dateDebut = new DateTime($request->date_debut);
            $dateFin = new DateTime($request->date_fin);
            
            if ($dateDebut >= $dateFin) {
                Flight::json(['message' => 'La date de fin doit être postérieure à la date de début'], 400);
                return;
            }

            // Création du prêt
            $data = [
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'montant_total' => $request->montant_total,
                'Id_usage' => $request->Id_usage,
                'Id_type_pret' => $request->Id_type_pret,
                'Id_client' => $request->Id_client
            ];

            $idPret = PretModel::createPret($data);
            
            if (!$idPret) {
                Flight::json(['message' => 'Erreur lors de la création du prêt'], 500);
                return;
            }

            // Calcul des échéances
            $diff = $dateDebut->diff($dateFin);
            $totalMonths = ($diff->y * 12) + $diff->m;
            if ($diff->d > 0) $totalMonths++; // Arrondir au mois supérieur si il y a des jours

            if ($totalMonths == 0) {
                Flight::json(['message' => 'La durée du prêt doit être au moins d\'un mois'], 400);
                return;
            }

            // Utiliser le montant total à rembourser si fourni, sinon le montant initial
            $montantTotalARembourser = !empty($request->montant_total_rembourser) ? 
                                      (float)$request->montant_total_rembourser : 
                                      (float)$request->montant_total;
            
            $montantMensuelBase = $montantTotalARembourser / $totalMonths;

            // Création des échéances
            $current = clone $dateDebut;
            for ($i = 0; $i < $totalMonths; $i++) {
                $echeance = [
                    'Id_pret' => $idPret,
                    'mois' => (int)$current->format('m'),
                    'annee' => (int)$current->format('Y'),
                    'montant' => round($montantMensuelBase, 2),
                    'Id_status' => 1 // Assurez-vous que ce status existe dans votre table
                ];
                
                PretModel::createMontantAPayer($echeance);
                $current->add(new DateInterval('P1M'));
            }

            Flight::json([
                'message' => 'Prêt créé avec succès',
                'id_pret' => $idPret,
                'nombre_echeances' => $totalMonths
            ], 201);
            
        } catch (Exception $e) {
            error_log("Erreur dans create(): " . $e->getMessage());
            Flight::json(['message' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }
}