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
                empty($request->montant_total) || empty($request->id_usage) || 
                empty($request->id_type_pret) || empty($request->id_client)) {
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

            // CORRECTION: Gestion de l'ID d'assurance
            $idAssurance = null;
            if (!empty($request->id_assurance)) {
                // Si vous envoyez le nom de l'assurance, vous devez récupérer l'ID
                // Ou adapter selon votre logique
                $idAssurance = $request->id_assurance;
            } elseif (!empty($request->nom_assurance)) {
                // Récupérer l'ID à partir du nom
                $idAssurance = $request->nom_assurance;
            }

            // Création du prêt
            $data = [
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'montant_total' => $request->montant_total,
                'Id_usage' => $request->id_usage,
                'Id_type_pret' => $request->id_type_pret,
                'Id_client' => $request->id_client,
                'Id_type_assurance' => $idAssurance  // CORRECTION: Nom cohérent
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
                
                $result = PretModel::createMontantAPayer($echeance);
                if (!$result) {
                    error_log("Erreur lors de la création de l'échéance: " . print_r($echeance, true));
                }
                
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
    
    public static function generatePDF()
    {
        require_once 'fpdf186/fpdf.php';
        
        // Récupération des données POST
       // $nom = Flight::request()->data['nom'] ?? '';
        $typePret = Flight::request()->data['id_type_pret'] ?? '';
        //$duree = Flight::request()->data['duree'] ?? '';
        //$montantEmprunter = Flight::request()->data['montantEmprunter'] ?? '';
        //$interetsTotaux = Flight::request()->data['interetsTotaux'] ?? '';
        //$montantTotalEstime = Flight::request()->data['montantTotalEstime'] ?? '';
        //$mensualiteEstimee = Flight::request()->data['mensualiteEstimee'] ?? '';
        
        // Validation des données
        // if (empty($nom) || empty($typePret) || empty($duree) || empty($montantEmprunter) || 
        //     empty($interetsTotaux) || empty($montantTotalEstime) || empty($mensualiteEstimee)) {
        //     Flight::json(['error' => 'Tous les champs sont requis pour générer le PDF'], 400);
        //     return;
        // }
        
        try {
            
            $pdf = new FPDF();
            $pdf->AddPage();
            
            // En-tête du document
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 15, 'SIMULATION DE PRET', 0, 1, 'C');
            $pdf->Ln(5);
            
            // Informations générales
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Informations du demandeur', 0, 1, 'L');
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 8, 'Nom: ', 0, 1, 'L');
            $pdf->Cell(0, 8, 'Date de simulation: ' . date('d/m/Y'), 0, 1, 'L');
            $pdf->Ln(5);
            
            // Tableau des détails du prêt
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Details du pret', 0, 1, 'L');
            $pdf->Ln(2);
            
            // En-têtes du tableau
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetFillColor(230, 230, 230);
            
            $headers = [
                'Type de pret' => 35,
                'Duree (mois)' => 25,
                'Montant emprunte' => 35,
                'Interets totaux' => 35,
                'Montant total' => 35,
                'Mensualite' => 25
            ];
            
            foreach ($headers as $header => $width) {
                $pdf->Cell($width, 8, $header, 1, 0, 'C', true);
            }
            $pdf->Ln();
            
            // Données du tableau
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetFillColor(255, 255, 255);
            
            $data = [
                $typePret,
                $duree,
                number_format($montantEmprunter, 0, ',', ' ') . ' €',
                number_format($interetsTotaux, 2, ',', ' ') . ' €',
                number_format($montantTotalEstime, 2, ',', ' ') . ' €',
                number_format($mensualiteEstimee, 2, ',', ' ') . ' €'
            ];
            
            $i = 0;
            foreach ($headers as $header => $width) {
                $pdf->Cell($width, 8, $data[$i], 1, 0, 'C');
                $i++;
            }
            $pdf->Ln();
            
            // Calculs et informations supplémentaires
            $pdf->Ln(10);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Resume financier', 0, 1, 'L');
            $pdf->SetFont('Arial', '', 11);
            
            // Calcul du taux d'intérêt approximatif
            $tauxApprox = ($interetsTotaux / $montantEmprunter) * 100;
            $coutTotal = $montantTotalEstime - $montantEmprunter;
            
            $pdf->Cell(0, 8, 'Montant emprunte: ' . number_format($montantEmprunter, 0, ',', ' ') . ' €', 0, 1, 'L');
            $pdf->Cell(0, 8, 'Cout total du credit: ' . number_format($coutTotal, 2, ',', ' ') . ' €', 0, 1, 'L');
            $pdf->Cell(0, 8, 'Taux d\'interet approximatif: ' . number_format($tauxApprox, 2, ',', ' ') . ' %', 0, 1, 'L');
            $pdf->Cell(0, 8, 'Duree de remboursement: ' . ' mois (' . round($duree/12, 1) . ' ans)', 0, 1, 'L');
            $pdf->Cell(0, 8, 'Mensualite: ' . number_format($mensualiteEstimee, 2, ',', ' ') . ' €', 0, 1, 'L');
            
           
            
            // Génération du nom de fichier
            $fileName = 'simulation_pret_' . preg_replace('/[^a-zA-Z0-9]/', '_', $nom) . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            // Définir les en-têtes pour le téléchargement PDF
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            // Envoi du PDF au navigateur
            $pdf->Output('D', $fileName);
            exit; // Important : arrêter l'exécution après l'envoi du PDF
            
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()], 500);
        }
    }
    
}
    ?>
