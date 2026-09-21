<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/constants.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use r401_frontend\Controleur\JoueurControleur;
use r401_frontend\Controleur\CommentaireControleur;
use r401_frontend\Vue\Component\Formulaire;

if (!isset($_GET['id'])) {
    header('Location: /joueur');
    die();
}

$idJoueur = $_GET['id'];
$joueurControleur = JoueurControleur::getInstance();
$joueur = $joueurControleur->getJoueurById($idJoueur);

?>

<h1>Commentaires de <?php echo $joueur->toString(); ?></h1>

<?php
$form = new Formulaire("commentaire/ajouter");
$form->addTextArea("contenu");
$form->addHiddenInput("joueurId", $_GET['id']);
$form->addButton("submit", "create", "Publier le commentaire", "Publier le commentaire");
echo $form;

$commentaireControleur = CommentaireControleur::getInstance();

$commentaires = $commentaireControleur->listerLesCommentairesDuJoueur($joueur);

usort($commentaires, function ($a, $b) { return $b->getDate() <=> $a->getDate(); });

?>
<div class="container">
    <table>
        <tr>
            <th style="min-width: 100px; width: 1%">Date</th>
            <th style="width: 80%">Commentaire</th>
            <th style="width: 1%"></th>
        </tr>
        <?php foreach ($commentaires as $commentaire): ?>
        <form action="/joueur/commentaire/supprimer" method="post">
            <input type="hidden" name="commentaireId" value="<?php echo $commentaire->getCommentaireId(); ?>" />
            <input type="hidden" name="joueurId" value="<?php echo $_GET['id']; ?>" />
            <tr>
                <td><?php echo $commentaire->getDate()->format('d/m/Y H:i'); ?></td>
                <td><?php echo $commentaire->getContenu(); ?></td>
                <td class="actions">
                    <button class="delete" type="submit">Supprimer</button>
                </td>
            </tr>
        </form>
        <?php endforeach; ?>
    </table>
</div>
