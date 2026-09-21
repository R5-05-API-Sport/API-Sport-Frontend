<?php

namespace r401_frontend\Controleur;

require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/constants.php';

use DateTime;
use Exception;
use r401_frontend\Modele\Joueur\Joueur;
use r401_frontend\Modele\Joueur\JoueurStatut;
use r401_frontend\Controleur\ParticipationControleur;

class JoueurControleur {
    private static ?JoueurControleur $instance = null;
    private readonly ParticipationControleur $participationControleur;

    private function __construct() {
        $this->participationControleur = ParticipationControleur::getInstanceFromJoueurControleur($this);
    }

    public static function getInstance(): JoueurControleur {
        if (self::$instance == null) {
            self::$instance = new JoueurControleur();
        }
        return self::$instance;
    }

    public function ajouterJoueur(
        string $nom,
        string $prenom,
        string $numeroDeLicence,
        DateTime $dateDeNaissance,
        int $tailleEnCm,
        int $poidsEnKg,
        string $statut
    ) : bool {
        
        $data = array(
            "nom" => $nom,
            "prenom" => $prenom,
            "numeroDeLicence" => $numeroDeLicence,
            "dateDeNaissance" => $dateDeNaissance->format('Y-m-d'),
            "tailleEnCm" => $tailleEnCm,
            "poidsEnKg" => $poidsEnKg,
            "statut" => $statut,
        );

        $token = $_COOKIE["token"];
        $result = postRequest(BACKEND_BASE_URL."JoueurAPI.php", $data, $token);

        return $result["status_code"] == 201;
    }

    public function getJoueurById(int $joueurId) : Joueur {
        $result = getRequest(BACKEND_BASE_URL."JoueurAPI.php?id=".$joueurId, $_COOKIE["token"]);
        $joueurJson = $result["data"];
        return Joueur::JsonDeserialize($joueurJson);
    }

    public function listerLesJoueursSelectionnablesPourUnMatch(int $rencontreId) : array {
        $toutLesJoueurs = $this->listerTousLesJoueurs();
        $joueursSelectionnables = [];

        foreach ($toutLesJoueurs as $joueur) {
            if ($joueur->getStatut() == JoueurStatut::ACTIF &&
            !$this->participationControleur->lejoueurEstDejaSurLaFeuilleDeMatch($rencontreId, $joueur->getJoueurId())) {
                $joueursSelectionnables[] = $joueur;
            }
        }

        return $joueursSelectionnables;
    }

    public function listerTousLesJoueurs() : array {
        $requestResult = getRequest(BACKEND_BASE_URL."JoueurAPI.php", $_COOKIE["token"]);

        $joueurs = array();

        if ($requestResult["status_code"] == 200) {
            $data = $requestResult["data"];
            foreach ($data as $joueurJson) {
                array_push($joueurs, Joueur::JsonDeserialize($joueurJson));
            }
        }

        return $joueurs;
    }

    public function modifierJoueur(
        int $joueurId,
        string $nom,
        string $prenom,
        string $numeroDeLicence,
        DateTime $dateDeNaissance,
        int $tailleEnCm,
        int $poidsEnKg,
        string $statut
    ) : bool {

        $data = array(
            "nom" => $nom,
            "prenom" => $prenom,
            "numeroDeLicence" => $numeroDeLicence,
            "dateDeNaissance" => $dateDeNaissance->format('Y-m-d'),
            "tailleEnCm" => $tailleEnCm,
            "poidsEnKg" => $poidsEnKg,
            "statut" => $statut,
        );

        $result = putRequest(BACKEND_BASE_URL."JoueurAPI.php?id=".$joueurId, $data, $_COOKIE["token"]);

        return $result["status_code"] == 200;
    }

    public function rechercherLesJoueurs(string $recherche, string $statut) : array {
        $tousLesjoueurs = $this->listerTousLesJoueurs();
        $joueursTrouves = [];

        foreach ($tousLesjoueurs as $joueur) {
            $conserverDansLaListe = true;

            if ($recherche !== "") {
                $conserverDansLaListe = $joueur->nomOuPrenomContient($recherche);
            }

            if ($conserverDansLaListe && $statut !== "") {
                $conserverDansLaListe = $joueur->getStatut() == JoueurStatut::fromName($statut);
            }

            if ($conserverDansLaListe) {
                $joueursTrouves[] = $joueur;
            }
        }

        return $joueursTrouves;
    }

    public function supprimerJoueur(int $joueurId) : bool {
        $result = deleteRequest(BACKEND_BASE_URL."JoueurAPI.php?id=".$joueurId, $_COOKIE["token"]);
        return $result["status_code"] == 200;
}
}