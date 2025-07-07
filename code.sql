CREATE TABLE user (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50),
    mot_de_passe VARCHAR(30),
    rolee VARCHAR(10) CHECK (rolee IN ('admin', 'user'))
);
CREATE TABLE commande (
    commande_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    date_commande DATE,
    etat VARCHAR(20) CHECK (etat IN ('en attente', 'validée', 'annulée')),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

CREATE TABLE panier (
    user_id INT,
    item_id INT,
    quantite INT,
    PRIMARY KEY (user_id, item_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (item_id) REFERENCES item(item_id)
);

CREATE TABLE detailcommandes (
    detail_id INT PRIMARY KEY AUTO_INCREMENT,
    commande_id INT,
    item_id INT,
    quantite INT,
    prix_unitaire INT,
    FOREIGN KEY (commande_id) REFERENCES commande(commande_id),
    FOREIGN KEY (item_id) REFERENCES item(item_id)
);

CREATE TABLE item (
    item_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50),
    description VARCHAR(800),
    categorie VARCHAR(40),
    prix INT,
    stock INT,
    imageSrc VARCHAR(100)
);

CREATE TABLE historique_commandes (
    historique_id INT PRIMARY KEY AUTO_INCREMENT,
    commande_id INT,
    user_id INT,
    date_commande DATE,
    date_annulation DATE,
    raison VARCHAR(100),
    FOREIGN KEY (commande_id) REFERENCES commande(commande_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

CREATE TABLE historique_annulee (
    commande_id INT,
    user_id INT,
    PRIMARY KEY (commande_id, user_id),
    CONSTRAINT fk_commande FOREIGN KEY (commande_id) REFERENCES commande(commande_id),
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES user(user_id)
);

-- 1 : afficherDetailsCommande
DELIMITER $$

CREATE PROCEDURE afficherDetailsCommande(IN p_commande_id INT)
BEGIN
    DECLARE v_total_final DECIMAL(10, 2) DEFAULT 0;
    DECLARE v_result TEXT DEFAULT '';
    
    -- Construction de la chaîne de résultat
    SELECT 
        CONCAT_WS('\n',
            GROUP_CONCAT(
                CONCAT('Description: ', i.description, 
                      ' | Prix: ', i.prix, 
                      ' | Quantité: ', dc.quantite, 
                      ' | Total: ', (i.prix * dc.quantite)
                ) SEPARATOR '\n'
            ),
            CONCAT('Total à payer: ', SUM(i.prix * dc.quantite), ' DA')
        ) INTO v_result
    FROM detailcommandes dc
    JOIN item i ON dc.item_id = i.item_id
    WHERE dc.commande_id = p_commande_id;
    
  
    SELECT v_result AS message;
END$$

DELIMITER ;

-- 2 :finaliserToutesCommandes
DELIMITER $$

CREATE PROCEDURE finaliserToutesCommandes (IN p_user_id INT)
BEGIN
    -- Mise à jour de l'état de la commande
    UPDATE commande
    SET etat = 'validée'
    WHERE user_id = p_user_id;
    
    -- Suppression des articles dans le panier
    DELETE FROM panier
    WHERE user_id = p_user_id;
    
END$$

DELIMITER ;


-- 3 :afficherHistoriqueClient
DELIMITER $$

CREATE PROCEDURE afficherHistoriqueClient (IN p_user_id INT)
BEGIN
    -- Vérification si l'utilisateur existe
    IF NOT EXISTS (SELECT 1 FROM users WHERE user_id = p_user_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Utilisateur non trouvé.';
    END IF;

    -- Retourner tout l'historique de commandes directement
    SELECT 
        c.commande_id,
        c.date_commande,
        c.etat,
        h.date_annulation,
        h.raison
    FROM 
        commande c
    LEFT JOIN 
        historique_commande h ON c.commande_id = h.commande_id
    WHERE 
        c.user_id = p_user_id
    ORDER BY 
        c.date_commande DESC;
END$$

DELIMITER ;

-- 4 : trg_MajStock_ApresCommande
DELIMITER $$

CREATE TRIGGER trg_MajStock_ApresCommande
AFTER INSERT ON detailcommandes
FOR EACH ROW
BEGIN
    DECLARE v_stock_actuel INT;

    SELECT stock INTO v_stock_actuel FROM item WHERE item_id = NEW.item_id;

    IF v_stock_actuel >= NEW.quantite THEN
        UPDATE item
        SET stock = stock - NEW.quantite
        WHERE item_id = NEW.item_id;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stock insuffisant pour cet item';
    END IF;
END$$

DELIMITER ;

-- 5 : trg_VerifStock_AvantCommande
DELIMITER $$

CREATE TRIGGER trg_VerifStock_AvantCommande
BEFORE INSERT ON detailcommandes
FOR EACH ROW
BEGIN
    DECLARE v_stock_actuel INT;

    SELECT stock INTO v_stock_actuel
    FROM item
    WHERE item_id = NEW.item_id;

    IF NEW.quantite > v_stock_actuel THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Commande refusée : Stock insuffisant';
    END IF;
END$$

DELIMITER ;

-- 6 :rg_RestaurerStock_ApresAnnulation
DELIMITER $$

CREATE TRIGGER trg_RestaurerStock_ApresAnnulation
AFTER UPDATE ON commande
FOR EACH ROW
BEGIN
    IF NEW.etat = 'Annulée' AND OLD.etat <> 'Annulée' THEN
        UPDATE item
        SET stock = stock + (
            SELECT SUM(quantite)
            FROM detailcommandes
            WHERE commande_id = NEW.commande_id
              AND item.item_id = detailcommandes.item_id
        )
        WHERE EXISTS (
            SELECT 1
            FROM detailcommandes
            WHERE commande_id = NEW.commande_id
              AND item.item_id = detailcommandes.item_id
        );
    END IF;
END$$

DELIMITER ;

-- 7 :trg_Trace_Commandes_Annulees
DELIMITER $$

CREATE TRIGGER trg_Trace_Commandes_Annulees
AFTER UPDATE ON commande
FOR EACH ROW
BEGIN
    IF NEW.etat = 'Annulée' AND OLD.etat <> 'Annulée' THEN
        INSERT INTO historique_annulee (
            commande_id,
            user_id
        )
        VALUES (
            NEW.commande_id,
            NEW.user_id
        );
    END IF;
END$$

DELIMITER ;


