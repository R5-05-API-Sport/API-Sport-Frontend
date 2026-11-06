<?php

namespace r401_frontend\Controleur;

require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/constants.php';

use r401_frontend\Modele\Participation\FeuilleDeMatch;
use r401_frontend\Modele\Participation\Participation;
use r401_frontend\Modele\Participation\Performance;
use r401_frontend\Modele\Participation\Poste;
use r401_frontend\Modele\Participation\TitulaireOuRemplacant;

class ParticipationControleur
{
    private static ?ParticipationControleur $instance = null;
    private readonly JoueurControleur $joueurs;
    private readonly RencontreControleur $rencontres;

    private function __construct(JoueurControleur $joueurs)
    {
        $this->joueurs = $joueurs;
        $this->rencontres = RencontreControleur::getInstance();
    }

    public static function getInstance(): ParticipationControleur
    {
        if (self::$instance == null) {
            self::$instance = new ParticipationControleur(JoueurControleur::getInstance());
        }
        return self::$instance;
    }

    //Cette méthode permet de briser la dépendance cyclique entre les deux controleurs
    public static function getInstanceFromJoueurControleur(JoueurControleur $joueurs): ParticipationControleur
    {
        if (self::$instance == null) {
            self::$instance = new ParticipationControleur($joueurs);
        }
        return self::$instance;
    }

    private function token(): ?string
    {
        return $_COOKIE["token"] ?? null;
    }

    // Désérialise une liste JSON de participations. Une participation sans
    // rencontre (autorisé par l'API) est ignorée : le modèle exige une rencontre.
    private function deserialiser(mixed $data): array
    {
        $participations = [];
        if (!is_array($data)) {
            return $participations;
        }
        foreach ($data as $participationJson) {
            if (empty($participationJson["rencontre"]) || empty($participationJson["joueur"])) {
                continue;
            }
            $participations[] = Participation::jsonDeserialize($participationJson);
        }
        return $participations;
    }

    // Identifiants des joueurs déjà présents sur la feuille de match (1 seule requête)
    public function getIdsDesJoueursSurLaFeuilleDeMatch(int $rencontreId): array
    {
        $requestResult = getRequest(BACKEND_BASE_URL . "participations/rencontre/" . $rencontreId, $this->token());
        $ids = [];
        if ($requestResult["status_code"] == 200 && is_array($requestResult["data"])) {
            foreach ($requestResult["data"] as $participationJson) {
                if (isset($participationJson["joueur"]["joueurId"])) {
                    $ids[] = $participationJson["joueur"]["joueurId"];
                }
            }
        }
        return $ids;
    }

    public function lejoueurEstDejaSurLaFeuilleDeMatch(int $rencontreId, int $joueurId): bool
    {
        return in_array($joueurId, $this->getIdsDesJoueursSurLaFeuilleDeMatch($rencontreId));
    }

    public function listerToutesLesParticipations(): array
    {
        $requestResult = getRequest(BACKEND_BASE_URL . "participations", $this->token());
        if ($requestResult["status_code"] != 200) {
            return [];
        }
        return $this->deserialiser($requestResult["data"]);
    }

    public function getFeuilleDeMatch(int $rencontreId): FeuilleDeMatch
    {
        $requestResult = getRequest(BACKEND_BASE_URL . "participations/rencontre/" . $rencontreId, $this->token());
        $participants = $requestResult["status_code"] == 200 ? $this->deserialiser($requestResult["data"]) : [];
        return new FeuilleDeMatch($participants);
    }

    public function assignerUnParticipant(
        int $joueurId,
        int $rencontreId,
        Poste $poste,
        TitulaireOuRemplacant $titulaireOuRemplacant
    ): bool {
        $data = array(
            "joueur" => ["joueurId" => $joueurId],
            "rencontre" => ["rencontreId" => $rencontreId],
            "poste" => $poste->name,
            "titulaireOuRemplacant" => $titulaireOuRemplacant->name,
        );

        $requestResult = postRequest(BACKEND_BASE_URL . "participations", $data, $this->token());

        return $requestResult["status_code"] == 201;
    }

    public function modifierParticipation(
        int $participationId,
        Poste $poste,
        TitulaireOuRemplacant $titulaireOuRemplacant,
        int $joueurId
    ): bool {
        $data = [
            "joueur" => ["joueurId" => $joueurId],
            "poste" => $poste->name,
            "titulaireOuRemplacant" => $titulaireOuRemplacant->name,
        ];
        $requestResult = patchRequest(BACKEND_BASE_URL . "participations/$participationId", $data, $this->token());
        return $requestResult["status_code"] == 200;
    }

    public function supprimerLaParticipation(int $participationId): bool
    {
        // L'API répond 204 No Content
        $requestResult = deleteRequest(BACKEND_BASE_URL . "participations/$participationId", $this->token());
        return $requestResult["status_code"] == 204;
    }

    public function mettreAJourLaPerformance(int $participationId, string $performance): bool
    {
        $enum = is_numeric($performance)
            ? Performance::fromValue((int) $performance)
            : Performance::fromName($performance);

        if ($enum === null) {
            return false; // valeur invalide, on ne l'envoie pas
        }

        $data = ["performance" => $enum->value];
        $requestResult = patchRequest(BACKEND_BASE_URL . "participations/$participationId", $data, $this->token());
        return $requestResult["status_code"] == 200;
    }

    public function supprimerLaPerformance(int $participationId): bool
    {
        $requestResult = deleteRequest(BACKEND_BASE_URL . "participations/$participationId/performance", $this->token());
        return $requestResult["status_code"] == 200;
    }
}
