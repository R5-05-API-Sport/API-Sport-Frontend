<?php

namespace r401_frontend\Controleur;

require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/constants.php';

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

    private function token(): ?string {
        return $_COOKIE["token"] ?? null;
    }

    public function ajouterCommentaire(
        string $contenu,
        string $joueurId
    ) : bool {

        $data = array(
            "contenu" => $contenu,
            "joueurId" => (int) $joueurId
        );

        $result = postRequest(BACKEND_BASE_URL."commentaires", $data, $this->token());

        return $result["status_code"] == 201;
    }

    public function listerLesCommentairesDuJoueur(Joueur $joueur) : array {
        $result = getRequest(BACKEND_BASE_URL."commentaires/joueur/".$joueur->getJoueurId(), $this->token());

        $commentaires = array();

        if ($result["status_code"] != 200 || !is_array($result["data"])) {
            return $commentaires;
        }

        foreach ($result["data"] as $commentaireJson) {
            $commentaireId = $commentaireJson["commentaireId"];
            $contenu = $commentaireJson["contenu"] ?? "";
            $date = new DateTime($commentaireJson["date"] ?? "now");
            $commentaires[] = new Commentaire($commentaireId, $contenu, $date);
        }

        return $commentaires;
    }

    public function supprimerCommentaire(string $commentaireId) : bool {
        $result = deleteRequest(BACKEND_BASE_URL."commentaires/".$commentaireId, $this->token());
        return $result["status_code"] == 204;
    }
}
