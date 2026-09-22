<?php

namespace r401_frontend\Controleur;

require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/constants.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use DateTime;
use r401_frontend\Modele\Joueur\Commentaire\Commentaire;
use r401_frontend\Modele\Joueur\Joueur;

class CommentaireControleur {
    private static ?CommentaireControleur $instance = null;

    public static function getInstance(): CommentaireControleur {
        if (self::$instance == null) {
            self::$instance = new CommentaireControleur();
        }
        return self::$instance;
    }

    public function ajouterCommentaire(
        string $contenu,
        string $joueurId
    ) : bool {

        $data = array(
            "contenu" => $contenu,
            "joueurId" => $joueurId
        );

        $result = postRequest(BACKEND_BASE_URL."commentaires", $data, $_COOKIE["token"]);

        return $result["status_code"] == 200;
    }

    public function listerLesCommentairesDuJoueur(Joueur $joueur) : array {
        $result = getRequest(BACKEND_BASE_URL."commentaires/joueur/".$joueur->getJoueurId(), $_COOKIE["token"]);
        $data = $result["data"];

        $commentaires = array();

        foreach ($data as $commentaireJson) {
            $commentaireId = $commentaireJson["commentaireId"];
            $contenu = $commentaireJson["contenu"];
            $date = new DateTime($commentaireJson["date"]);
            $newCommentaire = new Commentaire($commentaireId, $contenu, $date);
            array_push($commentaires, $newCommentaire);
        }

        return $commentaires;
    }

    public function supprimerCommentaire(string $commentaireId) : bool {
        $result = deleteRequest(BACKEND_BASE_URL."commentaires/".$commentaireId, $_COOKIE["token"]);
        return $result["status_code"] == 200;
    }
}