<!DOCTYPE html>
<html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- php ici pour modifier le titre de la page  -->
        <title>Sujet</title>
        <?php
            include_once('../connexion/connexion.php');
            session_start();
            $_SESSION['provenance'] = 'discussion.php';
            $connecte = false;
            $sujet = null;
            $pseudo = "";

            if (array_key_exists('connection', $_SESSION) && $_SESSION['connection']) 
            {
                $connecte = true;
                $pseudo = $_SESSION['pseudo'];

            }

            // Y'a de l'idée mais ça fonctionne pas (nombre de paramètres je pense)
            if (array_key_exists('commentaire', $_POST)) {
                $commentaire = $_POST['commentaire'];


                // Préparation insertion
                $insert_stmt = $objPdo->prepare("INSERT INTO reponse (texteReponse) VALUES( ? )");

                $insert_stmt->bindValue(1, $commentaire, PDO::PARAM_STR);
            }
        ?>
    </head>

    <body>
        <?php
            if (array_key_exists('idSujet', $_GET)) 
            {
                //On recupere uniquement les attributs necessaires ainsi que le pseudo du redacteur
                $select_stmt = $objPdo->prepare('SELECT idSujet, titreSujet, texteSujet, pseudo, dateSujet 
                                                        FROM sujet s, redacteur r
                                                        WHERE idSujet = ? 
                                                        AND s.idRedacteur = r.idRedacteur');
                $select_stmt->bindValue(1, trim($_GET['idSujet']), PDO::PARAM_INT);

                $select_stmt->execute();

                $sujet = $select_stmt->fetch();

                if ($sujet != null) 
                {
                    $dateSujet = date('d/m/Y', strtotime($sujet['dateSujet']));

                    echo ('Titre : ' . $sujet['titreSujet'] . '<br>Par le rédacteur : ' . $sujet['pseudo'] . ' le ' . $dateSujet . '<br><br><br>');
                    //Ouverture de la table avec le texte du sujet et les reponses correspondantes
                    echo ('<table>');

                    echo ('<tr><td>' . $sujet['texteSujet'] . '</td></tr>');

                    //Inclure toute les reponses avec un select et un foreach
                    $result = $objPdo->query('SELECT texteReponse, pseudo 
                                              FROM reponse rep, redacteur redac
                                              WHERE idSujet = '.$sujet['idSujet'].'
                                              AND rep.idRedacteur = redac.idRedacteur');
                    
                    if ($result != null) 
                    {
                        foreach ($result as $row) 
                        {
                            echo ('<tr>');
                            if ($row['pseudo'] != $sujet['pseudo']) 
                            {
                                echo ('<td></td>');
                            }
                            echo ('<td>'.$row["texteReponse"].'<br> Par '.$row["pseudo"].'<br><br></td>');
                            echo ('</tr>');
                        }    
                    }
                    
                    echo ('<table>');

                    // Afficher la saisie de commentaire uniquement si l'on est connecté
                    if ($connecte) 
                    {
                        // Section reponse
                        echo ('<br>');
                        echo ('<h3>Ajouter un commentaire</h3>');

                        echo ('<form method="POST">');
                        //Affichage du pseudo, et d'un formulaire de commentaire
                        echo ('Votre pseudo : '.$pseudo.'<br><br>');

                        echo ('<textarea name="reponse" placeholder="Votre réponse..." rows="5" cols="45"></textarea><br><br>');
                        echo ('<input type="submit" value="Poster ma réponse" name="submit_reponse"/>');

                        echo ('</form>');
                    } 
                    else 
                    {
                        echo ('Veuillez vous connecter pour pouvoir ajouter un commentaire');
                        
                        /*

                        PROPOSER LA CONNEXON ICI

                        */
                    }


                    echo ('<br /><br />');
                } else {
                    echo ("Il y a une erreur dans le chargement de la page, merci de revenir à l'accueil<br><br>");
                    echo ('<a href="accueil.php"><input type="button" value="Retour a l\'accueil"></a>');
                }
            }
        ?>
        <a href="accueil.php">Retour à l'accueil</a>
    </body>
</html>