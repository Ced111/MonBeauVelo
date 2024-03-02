
-- Suppression des données
DELETE FROM p_detailsCommande;
DELETE FROM p_commande;
DELETE FROM p_produit;
DELETE FROM p_utilisateur;
DELETE FROM p_categorie;

-- Suppression des tables
DROP TABLE p_detailsCommande;
DROP TABLE p_commande;
DROP TABLE p_produit;
DROP TABLE p_utilisateur;
DROP TABLE p_categorie;



CREATE TABLE p_categorie (
    idCategorie INT AUTO_INCREMENT PRIMARY KEY,
    nomCategorie VARCHAR(255) NOT NULL,
    descriptionCategorie TEXT
);

CREATE TABLE p_produit (
    idProduit INT AUTO_INCREMENT PRIMARY KEY,
    idCategorie INT,
    nom VARCHAR(255) NOT NULL,
    marque VARCHAR(255) DEFAULT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (idCategorie) REFERENCES p_categorie(idCategorie)
);

CREATE TABLE `p_utilisateur` (
    `idUtilisateur` INT AUTO_INCREMENT PRIMARY KEY,
    `nom` varchar(32) NOT NULL,
    `prenom` varchar(32) NOT NULL,
    `adresse` text,
    `telephone` varchar(15),
    `mdpHache` varchar(256) NOT NULL,
    `estAdmin` tinyint(1) NOT NULL,
    `email` varchar(256) DEFAULT NULL UNIQUE,
    `emailAValider` varchar(256) DEFAULT NULL,
    `nonce` varchar(32) DEFAULT NULL
);

CREATE TABLE p_commande (
    idCommande INT AUTO_INCREMENT PRIMARY KEY,
    idUtilisateur INT,
    dateCommande DATE NOT NULL,
    statut ENUM('panier', 'payee', 'envoyee', 'recue') DEFAULT 'panier',
    FOREIGN KEY (idUtilisateur) REFERENCES p_utilisateur(idUtilisateur)
);

CREATE TABLE p_detailsCommande (
    idCommande INT,
    idProduit INT,
    quantite INT NOT NULL,
    prixUnitaire DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (idCommande, idProduit),
    FOREIGN KEY (idCommande) REFERENCES p_commande(idCommande),
    FOREIGN KEY (idProduit) REFERENCES p_produit(idProduit)
);




INSERT INTO p_categorie (nomCategorie, descriptionCategorie)
VALUES
    ('VTT', 'Vélos tout-terrain'),
    ('Route', 'Vélos de route'),
    ('VTCAE', 'Vélo tout-chemin à assistance électrique');

INSERT INTO p_produit (idCategorie, nom, description, prix, stock, marque, image)
VALUES
    (1, 'Mountain Explorer', 'Un VTT solide pour les terrains difficiles', 999.99, 10, 'Specialice', 'https://production-privee.com/cdn/shop/products/ShanN_27Explorer_1400x.jpg?v=1614622140'),
    (1, 'Trail Blazer', 'Un VTT parfait pour les aventures en montagne', 850.00, 8, 'Crockrider', 'https://contents.mediadecathlon.com/p2623157/k$56a387f834df8816d5aabd60fd676bba/sq/velo-vtt-randonnee-explore-500-noir-29.jpg?f=3000x3000'),
    (2, 'Speedster 200', 'Vélo de route ultra-léger pour les courses', 749.50, 15, 'Canyote', 'https://www.canyon.com/dw/image/v2/BCML_PRD/on/demandware.static/-/Sites-canyon-master/default/dw634d262b/images/full/full_2023_/2023/full_2023_3267_grand-canyon-5_og_P5.jpg?sw=1300&sfrm=png&q=90&bgcolor=F2F2F2'),
    (2, 'Road Racer', 'Un vélo de route rapide et agile', 650.00, 12, 'Bitwin', 'https://contents.mediadecathlon.com/p1810523/k$cda463a1e4c437908bbd1e68db6afcd4/sq/velo-de-ville-elops-100-cadre-bas-noir.jpg?f=3000x3000'),
    (3, 'Crosswind VTCAE', 'Vélo polyvalent avec assistance électrique pour tous les terrains', 1500.00, 20, 'Riverface', 'https://contents.mediadecathlon.com/p2323525/k$fb4340a9e3d876ee4e520aa983282355/sq/velo-tout-chemin-electrique-riverside-520-e-gris.jpg?f=3000x3000');

INSERT INTO p_utilisateur (nom, prenom, adresse, telephone, mdpHache, estAdmin, email)
VALUES
    ('Dupont', 'Jean', '12 Rue de la République, Paris', '0123456789', 'pass1', 0, 'jean.dupont@email.com'),
    ('Martin', 'Pierre', '33 Avenue des Ternes, Lyon', '0123456780', 'pass2', 0, 'pierre.martin@email.fr'),
    ('Leroux', 'Claire', '15 Boulevard Saint Michel, Marseille', '0123456781', 'pass3', 1, 'claire.leroux@email.com'),
    ('Dubois', 'Marie', '88 Rue du Paradis, Lille', '0123456782', 'pass4', 0, 'marie.dubois@email.com'),
    ('Durand', 'Philippe', '47 Avenue des Lilas, Bordeaux', '0123456783', 'pass5', 0, 'philippe.durand@email.fr');

INSERT INTO p_commande (idUtilisateur, dateCommande, statut)
VALUES
    (1, '2023-01-01', 'panier'),
    (2, '2023-01-02', 'payee'),
    (3, '2023-01-03', 'envoyee'),
    (4, '2023-01-04', 'recue'),
    (1, '2023-01-05', 'panier'),
    (2, '2023-01-06', 'panier');

INSERT INTO p_detailsCommande (idCommande, idProduit, quantite, prixUnitaire)
VALUES
    (1, 2, 1, 749.50),
    (2, 3, 1, 849.00),
    (3, 1, 2, 999.99),
    (4, 2, 1, 749.50),
    (1, 3, 2, 849.00),
    (2, 1, 1, 999.99);





