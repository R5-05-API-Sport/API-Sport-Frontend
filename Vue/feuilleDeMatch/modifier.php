<?php

use r401_frontend\Controleur\ParticipationControleur;
use r401_frontend\Controleur\Participation\SupprimerParticipation;
use r401_frontend\Modele\Participation\Poste;
use r401_frontend\Modele\Participation\TitulaireOuRemplacant;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$controleur = ParticipationControleur::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'])
    && isset($_POST['poste'])
    && isset($_POST['titulaireOuRemplacant'])
    && isset($_POST['joueurId']) && $_POST['joueurId'] !== ""
    && isset($_POST['rencontreId'])
) {
    switch($_POST['action']) {
        case "create":
            if (!$controleur->assignerUnParticipant(
                $_POST['joueurId'],
                $_POST['rencontreId'],
                Poste::fromName($_POST['poste']),
                TitulaireOuRemplacant::fromName($_POST['titulaireOuRemplacant'])
            )) {
                error_log("Erreur lors de l'ajout d'une participation");
            }
            break;
        case "update":
            if (isset($_POST['participationId'])) {
                if (!$controleur->modifierParticipation(
                    $_POST['participationId'],
                    Poste::fromName($_POST['poste']),
                    TitulaireOuRemplacant::fromName($_POST['titulaireOuRemplacant']),
                    $_POST['joueurId']
                )) {
                    error_log("Erreur lors de la modification de la participation");
                }
            }
            break;
        case "delete":
            if (isset($_POST['participationId'])) {
                if (!$controleur->supprimerLaParticipation($_POST['participationId'])) {
                    error_log("Erreur lors de la suppression de la participation");
                }
            }
            break;
        default:
    }
    header('Location: /feuilleDeMatch/feuilleDeMatch?id='.$_POST['rencontreId']);
} else {
    if (isset($_POST['rencontreId'])) {
        header('Location: /feuilleDeMatch/feuilleDeMatch?id='.$_POST['rencontreId']);
    } else {
        header('Location: /rencontre');
    }
}