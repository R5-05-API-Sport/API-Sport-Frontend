<?php

namespace r401_frontend\Controleur;

require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/constants.php';

use DateTime;
use Exception;
use r401_frontend\Modele\Rencontre\Rencontre;
use r401_frontend\Modele\Rencontre\RencontreLieu;

class RencontreControleur
{
    private static ?RencontreControleur $instance = null;

    public static function getInstance(): RencontreControleur
    {
        if (self::$instance == null) {
            self::$instance = new RencontreControleur();
        }
        return self::$instance;
    }

    private function token(): ?string
    {
        return $_COOKIE["token"] ?? null;
    }

    public function ajouterRencontre(
        DateTime $dateHeure,
        string $equipeAdverse,
        string $adresse,
        RencontreLieu $lieu
    ): bool {

        if ($dateHeure < new DateTime()) {
            return false;
        }

        $data = array(
            "dateHeure" => $dateHeure->format("Y-m-d\TH:i:s"),
            "equipeAdverse" => $equipeAdverse,
            "adresse" => $adresse,
            "lieu" => $lieu->name,
        );

        $requestResult = postRequest(BACKEND_BASE_URL . "rencontres", $data, $this->token());

        return $requestResult["status_code"] == 201;
    }

    public function enregistrerResultat(
        int $rencontreId,
        string $resultat
    ): bool {

        $data = array("resultat" => $resultat);

        $requestResult = patchRequest(BACKEND_BASE_URL . "rencontres/$rencontreId", $data, $this->token());

        return $requestResult["status_code"] == 200;
    }

    public function getRencontreById(int $rencontreId): Rencontre
    {
        $requestResult = getRequest(BACKEND_BASE_URL . "rencontres/$rencontreId", $this->token());

        if ($requestResult["status_code"] != 200 || !is_array($requestResult["data"])) {
            throw new Exception("Rencontre introuvable : $rencontreId");
        }

        return Rencontre::JsonDeserialize($requestResult["data"]);
    }

    public function listerToutesLesRencontres(): array
    {
        $requestResult = getRequest(BACKEND_BASE_URL . "rencontres", $this->token());

        $rencontres = array();

        if ($requestResult["status_code"] == 200 && is_array($requestResult["data"])) {
            foreach ($requestResult["data"] as $rencontreJson) {
                $rencontres[] = Rencontre::JsonDeserialize($rencontreJson);
            }
        }

        return $rencontres;
    }

    public function modifierRencontre(
        int $rencontreId,
        DateTime $dateHeure,
        string $equipeAdverse,
        string $adresse,
        RencontreLieu $lieu
    ): bool {

        if ($dateHeure < new DateTime()) {
            return false;
        }

        $data = array(
            "dateHeure" => $dateHeure->format("Y-m-d\TH:i:s"),
            "equipeAdverse" => $equipeAdverse,
            "adresse" => $adresse,
            "lieu" => $lieu->name,
        );

        $requestResult = putRequest(BACKEND_BASE_URL . "rencontres/" . $rencontreId, $data, $this->token());
        return $requestResult["status_code"] == 200;
    }

    public function supprimerRencontre(int $rencontreId): bool
    {
        try {
            $rencontreASupprimer = $this->getRencontreById($rencontreId);
        } catch (Exception $e) {
            return false;
        }

        if ($rencontreASupprimer->getResultat() != null) {
            return false;
        }

        // L'API répond 204 (et 409 si la rencontre a encore des participations)
        $result = deleteRequest(BACKEND_BASE_URL . "rencontres/" . $rencontreId, $this->token());
        return $result["status_code"] == 204;
    }
}
