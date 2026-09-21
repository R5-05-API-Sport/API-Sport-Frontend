<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use r401_frontend\Controleur\JoueurControleur;

require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/constants.php';

$joueurControleur = JoueurControleur::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $idJoueur = $_POST['id'];
        $joueurControleur->supprimerJoueur($idJoueur);
    }
}

header('Location: /joueur');