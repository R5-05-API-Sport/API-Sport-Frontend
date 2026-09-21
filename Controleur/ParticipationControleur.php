<?php

namespace r401_frontend\Controleur;

require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/constants.php';

use r401_frontend\Modele\Joueur\Joueur;
use r401_frontend\Modele\Participation\FeuilleDeMatch;
use r401_frontend\Modele\Participation\Participation;
use r401_frontend\Modele\Participation\Performance;
use r401_frontend\Modele\Participation\Poste;
use r401_frontend\Modele\Participation\TitulaireOuRemplacant;

class ParticipationControleur {
    private static ?ParticipationControleur $instance = null;
    private readonly JoueurControleur $joueurs;
    private readonly RencontreControleur $rencontres;

    private function __construct(JoueurControleur $joueurs) {
        $this->joueurs = $joueurs;
        $this->rencontres = RencontreControleur::getInstance();
    }

    public static function getInstance(): ParticipationControleur {
        if (self::$instance == null) {
            self::$instance = new ParticipationControleur(JoueurControleur::getInstance());
        }
        return self::$instance;
    }

    //Cette méthode permet de briser la dépendance cyclique entre les deux controleurs
    public static function getInstanceFromJoueurControleur(JoueurControleur $joueurs): ParticipationControleur {
        if (self::$instance == null) {
            self::$instance = new ParticipationControleur($joueurs);
        }
        return self::$instance;
    }

    public function lejoueurEstDejaSurLaFeuilleDeMatch(int $rencontreId, int $joueurId) : bool {
        $requestResult = getRequest(BACKEND_BASE_URL."ParticipationAPI.php?id=$rencontreId&idJoueur=$joueurId", $_COOKIE["token"]);
        return $requestResult["data"] == true; 
    }

    public function listerToutesLesParticipations() : array {
        $requestResult = getRequest(BACKEND_BASE_URL."ParticipationAPI.php", $_COOKIE["token"]);
        $data = $requestResult["data"];
        $participations = [];
        foreach ($data as $participation) {
            $participations[] = Participation::jsonDeserialize($participation);
        }
        return $participations;
    }

    public function getFeuilleDeMatch(int $rencontreId) : FeuilleDeMatch {
        $requestResult = getRequest(BACKEND_BASE_URL."ParticipationAPI.php?id=".$rencontreId, $_COOKIE["token"]);
        $data = $requestResult["data"];
        $participants = array();
        foreach ($data["participants"] as $participationJson) {
            array_push($participants, Participation::jsonDeserialize($participationJson));
        }
        return new FeuilleDeMatch($participants);
    }

    public function assignerUnParticipant(
        int $joueurId,
        int $rencontreId,
        Poste $poste,
        TitulaireOuRemplacant $titulaireOuRemplacant
    ) : bool {
            $data = array(
                "joueurId" => $joueurId,
                "rencontreId" => $rencontreId,
                "poste" => $poste->name,
                "titulaireOuRemplacant" => $titulaireOuRemplacant->name,
            );

            var_dump($data);

            $token = $_COOKIE["token"];
            $requestResult = postRequest(BACKEND_BASE_URL."ParticipationAPI.php", $data, $token);

            var_dump($requestResult);

            return $requestResult["status_code"] == 201;
        }

    public function modifierParticipation(
        int $participationId,
        Poste $poste,
        TitulaireOuRemplacant $titulaireOuRemplacant,
        int $joueurId
    ) : bool {
        $data = array(
            "poste" => $poste->name,
            "titulaireOuRemplacant" => $titulaireOuRemplacant->name,
            "joueurId" => $joueurId,
        );
        $requestResult = putRequest(BACKEND_BASE_URL."ParticipationAPI.php?id=$participationId", $data, $_COOKIE["token"]);
        return $requestResult["status_code"] == 200;
    }

    public function supprimerLaParticipation(int $participationId) : bool {
        $requestResult = deleteRequest(BACKEND_BASE_URL."ParticipationAPI.php?id=$participationId", $_COOKIE["token"]);
        return $requestResult["status_code"] == 200;
    }

    public function mettreAJourLaPerformance(
        int $participationId,
        string $performance
    ) : bool {
        $data = array("performance" => $performance);
        $requestResult = patchRequest(BACKEND_BASE_URL."PerformanceAPI.php?id=$participationId", $data, $_COOKIE["token"]);
        return $requestResult["status_code"] == 200;
    }

    public function supprimerLaPerformance(int $participationId) : bool {
        $requestResult = deleteRequest(BACKEND_BASE_URL."PerformanceAPI.php?id=$participationId", $_COOKIE["token"]);
        return $requestResult["status_code"] == 200;
    }
}