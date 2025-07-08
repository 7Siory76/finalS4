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


INSERT INTO status (status)
VALUES 
('En attente'),
('Validé'),
('Rejeté'),
('Payé');

INSERT INTO type_assurance (nom, taux_assurance)
VALUES 
('credit', 2.5),
('boblo', 1.5);
