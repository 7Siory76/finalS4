<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Simulation et Ajout de Prêt</title>
    <style>
        :root {
            --primary-color: #1a3a1a; /* Vert foncé */
            --secondary-color: #000000; /* Noir */
            --accent-color: #2a5a2a; /* Vert un peu plus clair */
            --text-color: #e0e0e0; /* Gris clair */
            --hover-color: #3a7a3a; /* Vert plus vif pour les hover */
            --light-bg: #f5f5f5; /* Fond clair */
            --success-color: #dff0d8;
            --success-text: #3c763d;
            --error-color: #f2dede;
            --error-text: #a94442;
            --table-header: #f2f2f2;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: var(--secondary-color);
        }
        
        header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        header h1 {
            margin: 0;
            font-size: 1.8rem;
        }
        
        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            flex-grow: 1;
            width: 90%;
        }
        
        .container {
            display: flex;
            gap: 2rem;
            margin-top: 1.5rem;
        }
        
        .form-container, .simulation-container {
            flex: 1;
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .form-title {
            color: var(--primary-color);
            margin-top: 0;
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        input, select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        
        input:focus, select:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(42, 90, 42, 0.2);
        }
        
        .button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        button {
            background-color: var(--primary-color);
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            flex: 1;
            min-width: 200px;
        }
        
        button:hover {
            background-color: var(--hover-color);
            transform: translateY(-2px);
        }
        
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .simulation-result {
            margin-top: 1.5rem;
            padding: 1.5rem;
            background-color: var(--light-bg);
            border-radius: 10px;
            border-left: 4px solid var(--accent-color);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        
        th {
            background-color: var(--table-header);
            font-weight: 600;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .message {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .message.success {
            background-color: var(--success-color);
            color: var(--success-text);
            border-left: 4px solid var(--success-text);
        }
        
        .message.error {
            background-color: var(--error-color);
            color: var(--error-text);
            border-left: 4px solid var(--error-text);
        }
        
        footer {
            background-color: var(--primary-color);
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: 2rem;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <h2>Simulation et Ajout de prêt</h2>
    
    <div class="container">
        <div class="form-container">
            <h3>Formulaire de prêt</h3>
            <form>
                <div class="form-group">
                    <label for="date_debut">Date de début :</label>
                    <input type="date" id="date_debut" name="date_debut"  required onchange="calculerDuree()">
                </div>

                <div class="form-group">
                    <label for="date_fin">Date de fin :</label>
                    <input type="date" id="date_fin" name="date_fin"  required onchange="calculerDuree()">
                </div>

                <div class="form-group">
                    <label for="montant_total">Montant total :</label>
                    <input type="number" id="montant_total" name="montant_total" value="20000" required>
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

                <!-- Champs cachés pour la génération PDF -->
                <input type="hidden" id="montant_total_rembourser" name="montant_total_rembourser">
                <input type="hidden" id="nom" name="nom">
                <input type="hidden" id="duree" name="duree">
                <input type="hidden" id="montant_emprunter" name="montant_emprunter">
                <input type="hidden" id="interets_totaux" name="interets_totaux">
                <input type="hidden" id="montant_total_estime" name="montant_total_estime">
                <input type="hidden" id="mensualite_estimee" name="mensualite_estimee">

                <button type="button" onclick="chargerDonnees()">Charger les options</button>
                <button type="button" onclick="simulerPret()">Simuler le prêt</button>
                <button type="button" onclick="ajouterUnpret()">Enregistrer le prêt</button>
                <button type="button" class="pdf-button" onclick="generatePDF()">Télécharger en PDF</button>
            </form>

            <div id="message-container"></div>
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

    function ajax(method, url, data, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open(method, apiBase + url, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4 && xhr.status === 200) {
                callback(JSON.parse(xhr.responseText));
            }
        };
        xhr.send(data);
    }

    function showMessage(message, type) {
        const messageContainer = document.getElementById('message-container');
        messageContainer.innerHTML = `<div class="message ${type}">${message}</div>`;
        setTimeout(() => {
            messageContainer.innerHTML = '';
        }, 5000);
    }

    function chargerDonnees() {
        // Charger toutes les données depuis la vue
        ajax("GET", "/pret", null, (data) => {
            if (!data || data.error) {
                console.error("Erreur de chargement:", data ? data.error : "Pas de données");
                return;
            }
            
            allPretData = data; // Stocker les données pour les utiliser ailleurs

            // Remplir les clients (en éliminant les doublons)
            const clients = {};
            const selectClient = document.getElementById("id_client");
            selectClient.innerHTML = '<option value="">-- Sélectionnez un client --</option>';
            
            data.forEach(item => {
                if (!clients[item.Id_client]) {
                    clients[item.Id_client] = item.client_email;
                    const option = document.createElement("option");
                    option.value = item.Id_client;
                    option.textContent = item.client_email + " (Salaire: " + item.salaire_mensuel + ")";
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
                    option.textContent = item.type_pret_nom + 
                                      " (Taux: " + item.taux_interet_annuel + "%)";
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
        });
    }

    function generatePDF() {
        const nom = document.getElementById("nom").value;
        const typePret = document.getElementById("id_type_pret").value;
        const duree = document.getElementById("duree").value;
        const montantEmprunter = document.getElementById("montant_emprunter").value;
        const interetsTotaux = document.getElementById("interets_totaux").value;
        const montantTotalEstime = document.getElementById("montant_total_estime").value;
        const mensualiteEstimee = document.getElementById("mensualite_estimee").value;
        
        // Vérifier que tous les champs PDF sont remplis
        if (!nom || !typePret || !duree || !montantEmprunter || !interetsTotaux || !montantTotalEstime || !mensualiteEstimee) {
            showMessage("Veuillez remplir tous les champs et simuler le prêt avant de générer le PDF", "error");
            return;
        }
        
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
            nom: nom,
            typePret: typePret,
            duree: duree,
            montantEmprunter: montantEmprunter,
            interetsTotaux: interetsTotaux,
            montantTotalEstime: montantTotalEstime,
            mensualiteEstimee: mensualiteEstimee
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
            const details = `
                <h4>Détails du type de prêt</h4>
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
        const dateDebut = document.getElementById("date_debut").value;
        const dateFin = document.getElementById("date_fin").value;
        
        if (!montant || !typePretId || !clientId || !dateDebut || !dateFin) {
            showMessage("Veuillez remplir tous les champs du formulaire", "error");
            return;
        }
        
        const dureeMois = calculerDuree();
        const typePret = allPretData.find(item => item.Id_type_pret == typePretId);
        const client = allPretData.find(item => item.Id_client == clientId);
        
        if (!typePret || !client) {
            showMessage("Données non trouvées pour la simulation", "error");
            return;
        }
        
        // Calculs de simulation
        const tauxMensuel = typePret.taux_interet_annuel / 12 / 100;
        const interetTotal = montant * tauxMensuel * dureeMois;
        const montantTotal = montant + interetTotal;
        const mensualite = montantTotal / dureeMois;
        
        // Remplir les champs cachés pour le PDF
        document.getElementById("montant_total_rembourser").value = montantTotal.toFixed(2);
        document.getElementById("nom").value = client.client_email;
        document.getElementById("duree").value = dureeMois;
        document.getElementById("montant_emprunter").value = montant.toFixed(2);
        document.getElementById("interets_totaux").value = interetTotal.toFixed(2);
        document.getElementById("montant_total_estime").value = montantTotal.toFixed(2);
        document.getElementById("mensualite_estimee").value = mensualite.toFixed(2);
        
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
                    <th>Durée</th>
                    <td>${dureeMois} mois (du ${dateDebut} au ${dateFin})</td>
                </tr>
                <tr>
                    <th>Montant emprunté</th>
                    <td>${montant.toFixed(2)}</td>
                </tr>
                <tr>
                    <th>Intérêts totaux</th>
                    <td>${interetTotal.toFixed(2)}</td>
                </tr>
                <tr>
                    <th>Montant total à rembourser</th>
                    <td>${montantTotal.toFixed(2)}</td>
                </tr>
                <tr>
                    <th>Mensualité estimée</th>
                    <td>${mensualite.toFixed(2)}</td>
                </tr>
            </table>
        `;
        
        document.getElementById("simulation-details").innerHTML = simulationHTML;
        showMessage("Simulation effectuée avec succès!", "success");
    }

    function ajouterUnpret() {
        // Validation simple côté client
        const dateDebut = document.getElementById("date_debut").value;
        const dateFin = document.getElementById("date_fin").value;
        const montantTotal = document.getElementById("montant_total").value;
        const idClient = document.getElementById("id_client").value;
        const idTypePret = document.getElementById("id_type_pret").value;
        const idUsage = document.getElementById("id_usage").value;
        const montantTotalRembourser = document.getElementById("montant_total_rembourser").value;

        if (!dateDebut || !dateFin || !montantTotal || !idClient || !idTypePret || !idUsage) {
            showMessage("Veuillez remplir tous les champs et simuler le prêt avant d'enregistrer !", "error");
            return;
        }

        // Construire la chaîne de données au format URL-encodé (noms cohérents)
        const dataToSend = `date_debut=${encodeURIComponent(dateDebut)}` +
                           `&date_fin=${encodeURIComponent(dateFin)}` +
                           `&montant_total=${encodeURIComponent(montantTotal)}` +
                           `&montant_total_rembourser=${encodeURIComponent(montantTotalRembourser)}` +
                           `&Id_client=${encodeURIComponent(idClient)}` +
                           `&Id_type_pret=${encodeURIComponent(idTypePret)}` +
                           `&Id_usage=${encodeURIComponent(idUsage)}`;

        // Envoyer les données au serveur
        ajax("POST", "/pret", dataToSend, (response) => {
            console.log("Réponse du serveur:", response);
            if (response && response.message && response.message.includes('succès')) {
                showMessage("Prêt enregistré avec succès!", "success");
                // Réinitialiser le formulaire
                document.querySelector('form').reset();
                document.getElementById("simulation-details").innerHTML = '<p>Veuillez remplir le formulaire et cliquer sur "Simuler le prêt"</p>';
            } else {
                showMessage("Erreur lors de l'enregistrement: " + (response.message || "Erreur inconnue"), "error");
            }
        });
    }
    
    // Charger les données au chargement de la page
    window.onload = chargerDonnees;
    </script>
</body>
</html>