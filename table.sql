CREATE TABLE type_utilisateur(
   Id_type_utilisateur INT,
   libelle VARCHAR(50) NOT NULL,
   PRIMARY KEY(Id_type_utilisateur)
);

CREATE TABLE type_mouvement(
   Id_type_mouvement INT,
   type_mouvement LOGICAL,
   PRIMARY KEY(Id_type_mouvement)
);

CREATE TABLE type_pret(
   Id_type_pret COUNTER,
   nom VARCHAR(50) NOT NULL,
   taux_interet_annuel DECIMAL(15,2) NOT NULL,
   duree_remboursement_en_mois BIGINT,
   montant_min BIGINT,
   montant_max BIGINT,
   frais INT NOT NULL,
   remboursement_fixe LOGICAL NOT NULL,
   PRIMARY KEY(Id_type_pret)
);

CREATE TABLE usage(
   Id_usage COUNTER,
   libelle VARCHAR(50),
   PRIMARY KEY(Id_usage)
);

CREATE TABLE type_remboursement_(
   Id_type_remboursement_ COUNTER,
   libelle VARCHAR(50),
   mois INT,
   PRIMARY KEY(Id_type_remboursement_)
);

CREATE TABLE status(
   Id_status INT,
   status VARCHAR(50) NOT NULL,
   PRIMARY KEY(Id_status)
);

CREATE TABLE utilisateur(
   Id_utilisateur INT,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50),
   email VARCHAR(50) NOT NULL,
   mdp VARCHAR(50) NOT NULL,
   Id_type_utilisateur INT NOT NULL,
   PRIMARY KEY(Id_utilisateur),
   FOREIGN KEY(Id_type_utilisateur) REFERENCES type_utilisateur(Id_type_utilisateur)
);

CREATE TABLE mouvement_fond(
   Id_mouvement_fond INT,
   montant_ INT NOT NULL,
   date_ DATE NOT NULL,
   description VARCHAR(340) NOT NULL,
   Id_type_mouvement INT,
   PRIMARY KEY(Id_mouvement_fond),
   FOREIGN KEY(Id_type_mouvement) REFERENCES type_mouvement(Id_type_mouvement)
);

CREATE TABLE client(
   Id_client COUNTER,
   salaire_mensuel INT NOT NULL,
   Id_utilisateur INT NOT NULL,
   PRIMARY KEY(Id_client),
   FOREIGN KEY(Id_utilisateur) REFERENCES utilisateur(Id_utilisateur)
);

CREATE TABLE pret(
   Id_pret COUNTER,
   date_debut INT NOT NULL,
   date_fin INT NOT NULL,
   montant_total INT,
   Id_type_remboursement_ INT NOT NULL,
   Id_usage INT NOT NULL,
   Id_type_pret INT NOT NULL,
   PRIMARY KEY(Id_pret),
   FOREIGN KEY(Id_type_remboursement_) REFERENCES type_remboursement_(Id_type_remboursement_),
   FOREIGN KEY(Id_usage) REFERENCES usage(Id_usage),
   FOREIGN KEY(Id_type_pret) REFERENCES type_pret(Id_type_pret)
);

CREATE TABLE remboursement(
   Id_remboursement INT,
   date_remboursement_ DATE,
   montant DECIMAL(15,2),
   Id_pret INT NOT NULL,
   PRIMARY KEY(Id_remboursement),
   FOREIGN KEY(Id_pret) REFERENCES pret(Id_pret)
);

CREATE TABLE historique_pret(
   Id_historique_pret INT,
   dateI_pret_hist VARCHAR(50) NOT NULL,
   Id_status INT NOT NULL,
   Id_pret INT NOT NULL,
   PRIMARY KEY(Id_historique_pret),
   FOREIGN KEY(Id_status) REFERENCES status(Id_status),
   FOREIGN KEY(Id_pret) REFERENCES pret(Id_pret)
);

CREATE TABLE historique_remb(
   Id_historique_remb INT,
   date_remb DATE,
   Id_remboursement INT,
   Id_status INT NOT NULL,
   PRIMARY KEY(Id_historique_remb),
   FOREIGN KEY(Id_remboursement) REFERENCES remboursement(Id_remboursement),
   FOREIGN KEY(Id_status) REFERENCES status(Id_status)
);
