<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Simulation et Ajout de Prêt</title>
    <style>
        .container {
            display: flex;
            gap: 20px;
        }
        .form-container, .simulation-container {
            flex: 1;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: inline-block;
            width: 150px;
        }
        input, select {
            padding: 5px;
            width: 250px;
        }
        button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .simulation-result {
            margin-top: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .error {
            color: red;
            font-size: 12px;
        }
        .success {
            color: green;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Simulation et Ajout de prêt</h2>
    
    <div class="container">
        <div class="form-container">
            <h3>Formulaire de prêt</h3>
            <form id="loanForm">
            <div id="fund-info" class="fund-info">
                <h4>💰 Fond disponible</h4>
                <p id="fund-amount">Chargement...</p>
            </div>
            
                <div class="form-group">
                    <label for="date_debut">Date de début :</label>
                    <input type="date" id="date_debut" name="date_debut" required onchange="calculerDuree()">
                </div>

                <div class="form-group">
                    <label for="date_fin">Date de fin :</label>
                    <input type="date" id="date_fin" name="date_fin" required onchange="calculerDuree()">
                </div>

                <div class="form-group">
                    <label for="montant_total">Montant total :</label>
                    <input type="number" id="montant_total" name="montant_total" value="20000" required min="0" step="0.01">
                </div>

                <div class="form-group">
                    <label for="id_client">Client :</label>
                    <select id="id_client" name="id_client" required>
                        <option value="">Chargement...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_type_pret">Type de prêt :</label>
                    <select id="id_type_pret" name="id_type_pret" required onchange="afficherDetailsPret()">
                        <option value="">Chargement...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_usage">Usage :</label>
                    <select id="id_usage" name="id_usage" required>
                        <option value="">Chargement...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_assurance">Type assurance :</label>
                    <select id="id_assurance" name="id_assurance" required>
                        <option value="">Chargement...</option>
                    </select>
                </div>
                
                <input type="hidden" id="montant_total_rembourser" name="montant_total_rembourser">
                <div id="messages"></div>
                
                <button type="button" onclick="chargerDonnees()">Charger les options</button>
                <button type="button" onclick="simulerPret()">Simuler le prêt</button>
                <button type="button" onclick="ajouterUnpret()">Enregistrer le prêt</button>
                <button type="button" class="pdf-button" onclick="generatePDF()">Télécharger en PDF</button>
            </form>

        </div>

        <div class="simulation-container">
            <h3>Résultats de simulation</h3>
            <div id="simulation-details" class="simulation-result">
                <p>Veuillez remplir le formulaire et cliquer sur "Simuler le prêt"</p>
            </div>
        </div>
    </div>

    <script>
    const apiBase = "http://localhost/finalS4/ws";
    let allPretData = []; // Pour stocker toutes les données des prêts
    let fondDisponible=0;

    function showMessage(message, type = 'info') {
        const messagesDiv = document.getElementById('messages');
        messagesDiv.innerHTML = `<div class="${type}">${message}</div>`;
        setTimeout(() => {
            messagesDiv.innerHTML = '';
        }, 5000);
    }

    function ajax(method, url, data, callback, errorCallback) {
        const xhr = new XMLHttpRequest();
        xhr.open(method, apiBase + url, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        callback(response);
                    } catch (e) {
                        console.error("Erreur de parsing JSON:", e);
                        if (errorCallback) errorCallback("Erreur de format de réponse");
                    }
                } else {
                    console.error("Erreur HTTP:", xhr.status, xhr.statusText);
                    if (errorCallback) errorCallback(`Erreur HTTP: ${xhr.status}`);
                }
            }
        };
        
        xhr.onerror = () => {
            console.error("Erreur réseau");
            if (errorCallback) errorCallback("Erreur de connexion");
        };
        
        xhr.send(data);
    }
//     function verifierFondDisponible() {
//     const montant = parseFloat(document.getElementById("montant_total").value);
//     const fundInfo = document.getElementById("fund-info");
//     const btnEnregistrer = document.querySelector("button[onclick='ajouterUnpret()']");
    
//     if (montant && fondDisponible > 0) {
//         if (montant > fondDisponible) {
//             fundInfo.className = "fund-info insufficient-fund";
//             fundInfo.innerHTML = `
//                 <h4>❌ Fond insuffisant</h4>
//                 <p>Montant demandé: <strong>${montant.toFixed(2)} €</strong></p>
//                 <p>Fond disponible: <strong>${fondDisponible.toFixed(2)} €</strong></p>
//             `;
//             btnEnregistrer.disabled = true;
//             showMessage("Fond insuffisant pour ce montant de prêt", "error");
//             return false;
//         } else {
//             fundInfo.className = "fund-info";
//             fundInfo.innerHTML = `
//                 <h4>💰 Fond disponible</h4>
//                 <p>Montant demandé: <strong>${montant.toFixed(2)} €</strong></p>
//                 <p>Fond disponible: <strong>${fondDisponible.toFixed(2)} €</strong></p>
//                 <p class="success">Reste après prêt: ${(fondDisponible - montant).toFixed(2)} €</p>
//             `;
//             btnEnregistrer.disabled = false;
//             return true;
//         }
//     }
//     return false;
// }
    function chargerDonnees() {
        showMessage("Chargement des données...", "info");
        
        ajax("GET", "/pret", null, (data) => {
            if (!data || data.error) {
                console.error("Erreur de chargement:", data ? data.error : "Pas de données");
                showMessage("Erreur de chargement des données", "error");
                return;
            }
            
            allPretData = data; // Stocker les données pour les utiliser ailleurs
            
            // if (data.length > 0 && data[0].dernier_montant_fond !== null) {
            //     fondDisponible = parseFloat(data[0].dernier_montant_fond);
            //     document.getElementById("fund-amount").textContent = `${fondDisponible.toFixed(2)} €`;
            // } else {
            //     fondDisponible = 0;
            //     document.getElementById("fund-amount").textContent = "Aucun fond disponible";
            // }

            // verifierFondDisponible();

            // Remplir les clients (en éliminant les doublons)
            const clients = {};
            const selectClient = document.getElementById("id_client");
            selectClient.innerHTML = '<option value="">-- Sélectionnez un client --</option>';
            
            data.forEach(item => {
                if (!clients[item.Id_client]) {
                    clients[item.Id_client] = item.client_email;
                    const option = document.createElement("option");
                    option.value = item.Id_client;
                    option.textContent = `${item.client_email} (Salaire: ${item.salaire_mensuel})`;
                    selectClient.appendChild(option);
                }
            });

            // Remplir les types de prêt (en éliminant les doublons)
            const typesPret = {};
            const selectTypePret = document.getElementById("id_type_pret");
            selectTypePret.innerHTML = '<option value="">-- Sélectionnez un type --</option>';
            
            data.forEach(item => {
                if (!typesPret[item.Id_type_pret]) {
                    typesPret[item.Id_type_pret] = item.type_pret_nom;
                    const option = document.createElement("option");
                    option.value = item.Id_type_pret;
                    option.textContent = `${item.type_pret_nom} (Taux: ${item.taux_interet_annuel}%)`;
                    selectTypePret.appendChild(option);
                }
            });

            // Remplir les usages (en éliminant les doublons)
            const usages = {};
            const selectUsage = document.getElementById("id_usage");
            selectUsage.innerHTML = '<option value="">-- Sélectionnez un usage --</option>';
            
            data.forEach(item => {
                if (!usages[item.Id_usage]) {
                    usages[item.Id_usage] = item.usage_libelle;
                    const option = document.createElement("option");
                    option.value = item.Id_usage;
                    option.textContent = item.usage_libelle;
                    selectUsage.appendChild(option);
                }
            });

            // Remplir les types d'assurance (en éliminant les doublons)
            const typesAssurance = {};
            const selectTypeAssurance = document.getElementById("id_assurance");
            selectTypeAssurance.innerHTML = '<option value="">-- Sélectionnez un type --</option>';
            
            data.forEach(item => {
                // Utiliser 'nom' comme clé unique puisqu'il n'y a pas d'Id_type_assurance dans la vue
                if (!typesAssurance[item.nom]) {
                    typesAssurance[item.nom] = item.nom;
                    const option = document.createElement("option");
                    option.value = item.Id_type_assurance; // Utiliser le nom comme valeur
                    option.textContent = `${item.nom} (${item.taux_assurance}%)`;
                    selectTypeAssurance.appendChild(option);
                }
            });

            showMessage("Données chargées avec succès", "success");
        }, (error) => {
            showMessage(`Erreur: ${error}`, "error");
        });
    }
    function generatePDF() {
        //const nom = document.getElementById("nom").value;
        const typePret = document.getElementById("id_type_pret").value;
        // const duree = document.getElementById("duree").value;
        // const montantEmprunter = document.getElementById("montant_emprunter").value;
        // const interetsTotaux = document.getElementById("interets_totaux").value;
        // const montantTotalEstime = document.getElementById("montant_total_estime").value;
        
        // Vérifier que tous les champs PDF sont remplis
        // if (!nom || !typePret || !duree || !montantEmprunter || !interetsTotaux || !montantTotalEstime || !mensualiteEstimee) {
        //   showMessage("Veuillez remplir tous les champs pour générer le PDF", "error");
        //   return;
        // }
        
        // Désactiver le bouton pendant la requête
        const pdfBtn = document.querySelector('.pdf-button');
        pdfBtn.disabled = true;
        pdfBtn.textContent = "Génération en cours...";
        
        // Créer un formulaire temporaire pour le téléchargement
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = apiBase + '/generatePDF';
        form.style.display = 'none';
        
        // Ajouter les champs du formulaire
        const fields = {
          //nom: nom,
          typePret: typePret,
        //   duree: duree,
        //   montantEmprunter: montantEmprunter,
        //   interetsTotaux: interetsTotaux,
        //   montantTotalEstime: montantTotalEstime,
        //   mensualiteEstimee: mensualiteEstimee
        };
        
        Object.keys(fields).forEach(key => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = key;
          input.value = fields[key];
          form.appendChild(input);
        });
        
        // Ajouter le formulaire au document et le soumettre
        document.body.appendChild(form);
        form.submit();
        
        // Nettoyer et réactiver le bouton
        document.body.removeChild(form);
        setTimeout(() => {
          pdfBtn.disabled = false;
          pdfBtn.textContent = "Télécharger en PDF";
          showMessage("PDF généré avec succès!", "success");
        }, 1000);
      }
    function calculerDuree() {
        const dateDebut = document.getElementById("date_debut").value;
        const dateFin = document.getElementById("date_fin").value;
        
        if (dateDebut && dateFin) {
            const diffTime = Math.abs(new Date(dateFin) - new Date(dateDebut));
            const diffMonths = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 30));
            return diffMonths;
        }
        return 0;
    }

    function afficherDetailsPret() {
        const montant = parseFloat(document.getElementById("montant_total").value);
        const typePretId = document.getElementById("id_type_pret").value;
        
        if (!typePretId) return;
        
        const typePret = allPretData.find(item => item.Id_type_pret == typePretId);
        if (typePret) {
            let alertMessage = '';
            // if (montant && montant < typePret.montant_min) {
            //     alertMessage = `<p class="error">⚠️ Montant trop faible (min: ${typePret.montant_min})</p>`;
            // }
            // if (montant && montant > typePret.montant_max) {
            //     alertMessage = `<p class="error">⚠️ Montant trop élevé (max: ${typePret.montant_max})</p>`;
            // }
            
            const details = `
                <h4>Détails du type de prêt</h4>
                ${alertMessage}
                <p><strong>Nom:</strong> ${typePret.type_pret_nom}</p>
                <p><strong>Taux annuel:</strong> ${typePret.taux_interet_annuel}%</p>
                <p><strong>Durée max:</strong> ${typePret.duree_remboursement_en_mois} mois</p>
                <p><strong>Montant min:</strong> ${typePret.montant_min}</p>
                <p><strong>Montant max:</strong> ${typePret.montant_max}</p>
                <p><strong>Frais:</strong> ${typePret.frais}</p>
            `;
            
            document.getElementById("simulation-details").innerHTML = details;
        }
    }

    function simulerPret() {
        // Récupérer les valeurs du formulaire
        const montant = parseFloat(document.getElementById("montant_total").value);
        const typePretId = document.getElementById("id_type_pret").value;
        const clientId = document.getElementById("id_client").value;
        const assuranceId = document.getElementById("id_assurance").value;
        const dateDebut = document.getElementById("date_debut").value;
        const dateFin = document.getElementById("date_fin").value;
        
        // Validation corrigée
        if (!montant || !typePretId || !clientId || !dateDebut || !dateFin || !assuranceId) {
            showMessage("Veuillez remplir tous les champs du formulaire", "error");
            return;
        }
        
        const dureeMois = calculerDuree();
        const typePret = allPretData.find(item => item.Id_type_pret == typePretId);
        const client = allPretData.find(item => item.Id_client == clientId);
        
        // Debug: vérifier les données d'assurance disponibles
        console.log("Nom d'assurance recherché:", assuranceId);
        console.log("Données disponibles:", allPretData.map(item => ({
            nom: item.nom,
            taux: item.taux_assurance
        })));
        
        const assurance = allPretData.find(item => item.Id_type_assurance == assuranceId);
        
        if (!typePret) {
            showMessage("Type de prêt non trouvé", "error");
            return;
        }
        if (!client) {
            showMessage("Client non trouvé", "error");
            return;
        }
        if (!assurance) {
            showMessage("Type d'assurance non trouvé", "error");
            console.log("Assurance non trouvée pour nom:", assuranceId);
            return;
        }
        
        // // Validation des montants
        // if (montant < typePret.montant_min || montant > typePret.montant_max) {
        //     showMessage(`Le montant doit être entre ${typePret.montant_min} et ${typePret.montant_max}`, "error");
        //     return;
        // }
        
        // // Validation de la durée
        // if (dureeMois > typePret.duree_remboursement_en_mois) {
        //     showMessage(`La durée ne peut pas dépasser ${typePret.duree_remboursement_en_mois} mois`, "error");
        //     return;
        // }
        
        // Calculs de simulation
        const tauxMensuel = typePret.taux_interet_annuel / 12 / 100;
        const interetTotal = montant * tauxMensuel * dureeMois;
        const montantTotal = montant + interetTotal;

        // Vérifier si taux_assurance existe et est un nombre valide
        const tauxAssurance = (assurance.taux_assurance || 0) / 100;
        const interetAssurance = montant * tauxAssurance * dureeMois;
        const assuranceTotal = montantTotal + interetAssurance;
        
        // Stocker le montant total à rembourser
        document.getElementById("montant_total_rembourser").value = assuranceTotal.toFixed(2);
        
        // Afficher les résultats
        const simulationHTML = `
            <h4>Résultats de simulation</h4>
            <table>
                <tr>
                    <th>Client</th>
                    <td>${client.client_email} (Salaire: ${client.salaire_mensuel})</td>
                </tr>
                <tr>
                    <th>Type de prêt</th>
                    <td>${typePret.type_pret_nom} (${typePret.taux_interet_annuel}%)</td>
                </tr>
                <tr>
                    <th>Assurance</th>
                    <td>${assurance.nom || 'N/A'} (${assurance.taux_assurance || 0}%)</td>
                </tr>
                <tr>
                    <th>Durée</th>
                    <td>${dureeMois} mois (du ${dateDebut} au ${dateFin})</td>
                </tr>
                <tr>
                    <th>Montant emprunté</th>
                    <td>${montant.toFixed(2)} €</td>
                </tr>
                <tr>
                    <th>Intérêts totaux</th>
                    <td>${interetTotal.toFixed(2)} €</td>
                </tr>
                <tr>
                    <th>Montant total sans assurance</th>
                    <td>${montantTotal.toFixed(2)} €</td>
                </tr>
                <tr>
                    <th>Coût assurance</th>
                    <td>${interetAssurance.toFixed(2)} €</td>
                </tr>
                <tr>
                    <th>Montant total à rembourser</th>
                    <td><strong>${assuranceTotal.toFixed(2)} €</strong></td>
                </tr>
            </table>
        `;
        
        document.getElementById("simulation-details").innerHTML = simulationHTML;
        showMessage("Simulation terminée avec succès", "success");
    }

    function ajouterUnpret() {
    //     if (!verifierFondDisponible()) {
    //     showMessage("Impossible d'enregistrer - fonds insuffisants", "error");
    //     return;
    // }
        // Validation simple côté client
        const dateDebut = document.getElementById("date_debut").value;
        const dateFin = document.getElementById("date_fin").value;
        const montantTotal = document.getElementById("montant_total").value;
        const idClient = document.getElementById("id_client").value;
        const idTypePret = document.getElementById("id_type_pret").value;
        const idUsage = document.getElementById("id_usage").value;
        const idAssurance = document.getElementById("id_assurance").value;
        const montantTotalRembourser = document.getElementById("montant_total_rembourser").value;

        if (!dateDebut || !dateFin || !montantTotal || !idClient || !idTypePret || !idUsage || !idAssurance) {
            showMessage("Veuillez remplir tous les champs et simuler le prêt avant d'enregistrer !", "error");
            return;
        }

        if (!montantTotalRembourser) {
            showMessage("Veuillez d'abord simuler le prêt avant de l'enregistrer !", "error");
            return;
        }console.log("Valeurs du formulaire:");
    console.log("- dateDebut:", dateDebut);
    console.log("- dateFin:", dateFin);
    console.log("- montantTotal:", montantTotal);
    console.log("- idClient:", idClient);
    console.log("- idTypePret:", idTypePret);
    console.log("- idUsage:", idUsage);
    console.log("- idAssurance:", idAssurance);
    console.log("- montantTotalRembourser:", montantTotalRembourser);


        // Construire la chaîne de données au format URL-encodé
        const dataToSend = `date_debut=${encodeURIComponent(dateDebut)}` +
                           `&date_fin=${encodeURIComponent(dateFin)}` +
                           `&montant_total=${encodeURIComponent(montantTotal)}` +
                           `&montant_total_rembourser=${encodeURIComponent(montantTotalRembourser)}` +
                           `&id_client=${encodeURIComponent(idClient)}` +
                           `&id_type_pret=${encodeURIComponent(idTypePret)}` +
                           `&id_usage=${encodeURIComponent(idUsage)}` +
                           `&id_assurance=${encodeURIComponent(idAssurance)}`; // Utiliser le nom au lieu de l'ID

        showMessage("Enregistrement en cours...", "info");

        // Envoyer les données au serveur
        ajax("POST", "/pret", dataToSend, (response) => {
            console.log("Réponse du serveur:", response);
            if (response && response.message && response.message.includes('succès')) {
                showMessage("Prêt enregistré avec succès!", "success");
                // Réinitialiser le formulaire
                document.getElementById('loanForm').reset();
                document.getElementById("simulation-details").innerHTML = '<p>Veuillez remplir le formulaire et cliquer sur "Simuler le prêt"</p>';
            } else {
                showMessage("Erreur lors de l'enregistrement: " + (response.message || "Erreur inconnue"), "error");
            }
        }, (error) => {
            showMessage(`Erreur lors de l'enregistrement: ${error}`, "error");
        });
    }

    // Charger les données au chargement de la page
    window.onload = chargerDonnees;
    </script>
</body>
</html>