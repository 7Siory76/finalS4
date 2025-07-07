INSERT INTO client (email, salaire_mensuel)
VALUES 
('client1@example.com', 1500000),
('client2@example.com', 2000000),
('client3@example.com', 1200000);

INSERT INTO type_pret (nom, taux_interet_annuel, duree_remboursement_en_mois, montant_min, montant_max, frais, remboursement_fixe)
VALUES 
('Crédit Immobilier', 8.5, 12, 10000000, 200000000, 500000, TRUE),
('Crédit Auto', 7.2,12, 3000000, 50000000, 300000, TRUE),
('Prêt Personnel', 9.8, 12, 1000000, 20000000, 150000, TRUE);

INSERT INTO usages (libelle)
VALUES 
('Achat de terrain'),
('Construction maison'),
('Achat véhicule'),
('Dépenses personnelles');
 
INSERT INTO type_remboursement_ (libelle, mois)
VALUES 
('Mensuel', 1);

INSERT INTO pret (date_debut, date_fin, montant_total, Id_type_remboursement_, Id_usage, Id_type_pret, Id_client)
VALUES 
('2025-07-01', '2045-07-01', 50000000, 1, 1, 1, 1),
('2025-08-15', '2030-08-15', 10000000, 2, 3, 2, 2),
('2025-06-10', '2028-06-10', 3000000, 1, 4, 3, 3);
