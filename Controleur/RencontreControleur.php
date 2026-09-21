<?php

namespace r401_frontend\Controleur;

require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/constants.php';

use DateTime;
use r401_frontend\Modele\Rencontre\Rencontre;
use r401_frontend\Modele\Rencontre\RencontreDAO;
use r401_frontend\Modele\Rencontre\RencontreLieu;
use r401_frontend\Modele\Rencontre\RencontreResultat;
use r401_frontend\rencontre\Lieu;
use rencontre\Resultat;
use rencontre\SensDuResultat;

class RencontreControleur {
    private static ?RencontreControleur $instance = null;

    public static function getInstance(): RencontreControleur {
        if (self::$instance == null) {
            self::$instance = new RencontreControleur();
        }
        return self::$instance;
    }

    public function ajouterRencontre(
        DateTime $dateHeure,
        string $equipeAdverse,
        string $adresse,
        RencontreLieu $lieu
    ) : bool {

        if ($dateHeure < date("Y-m-d H:i:s")) {
            return false;
        } else {
            $data = array(
                "dateEtHeure" => $dateHeure->format("Y-m-d H:i:s"),
                "equipeAdverse" => $equipeAdverse,
                "adresse" => $adresse,
                "lieu" => $lieu->name,
            );

            $requestResult = postRequest(BACKEND_BASE_URL."RencontreAPI.php", $data, $_COOKIE["token"]);

            return $requestResult["status_code"] == 201;
        }
    }

    public function enregistrerResultat(
        int $rencontreId,
        string $resultat
    ) : bool {

        $data = array("resultat" => $resultat);

        $requestResult = patchRequest(BACKEND_BASE_URL."RencontreAPI.php?id=$rencontreId", $data, $_COOKIE["token"]);

        return $requestResult["status_code"] == 200;
    }

    public function getRencontreById(int $rencontreId) : Rencontre {
        $requestResult = getRequest(BACKEND_BASE_URL."RencontreAPI.php?id=$rencontreId", $_COOKIE["token"]);

        $data = $requestResult["data"];
        return Rencontre::JsonDeserialize($data);
    }

    public function listerToutesLesRencontres() : array {
        $requestResult = getRequest(BACKEND_BASE_URL."RencontreAPI.php", $_COOKIE["token"]);

        $rencontres = array();

        if ($requestResult["status_code"] == 200) {
            $data = $requestResult["data"];
            foreach ($data as $rencontreJson) {
                array_push($rencontres, Rencontre::JsonDeserialize($rencontreJson));
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
    ) : bool {

        if ($dateHeure <  new DateTime()) {
            return false;
        } else {
            $data = array(
                "dateEtHeure" => $dateHeure->format("Y-m-d"),
                "equipeAdverse" => $equipeAdverse,
                "adresse" => $adresse,
                "lieu" => $lieu->name,
            );
            $requestResult = putRequest(BACKEND_BASE_URL."RencontreAPI.php?id=".$rencontreId, $data, $_COOKIE["token"]);
            return $requestResult["status_code"] == 200;
        }
    }

    public function supprimerRencontre(int $rencontreId) : bool {
        $rencontreASupprimer = $this->getRencontreById($rencontreId);

        if($rencontreASupprimer->getResultat() != null) {
            return false;
        } else {
            $result = deleteRequest(BACKEND_BASE_URL."RencontreAPI.php?id=".$rencontreId, $_COOKIE["token"]);
            return $result["status_code"] == 200;
        }
    }
}