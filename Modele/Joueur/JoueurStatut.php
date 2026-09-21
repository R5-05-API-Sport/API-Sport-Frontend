<?php
namespace r401_frontend\Modele\Joueur;

enum JoueurStatut: String
{
    case ACTIF = "ACTIF";
    case BLESSE = "BLESSE";
    case ABSENT = "ABSENT";
    case SUSPENDU = "SUSPENDU";

    public static function fromName(string $name): ?JoueurStatut
    {
        foreach (self::cases() as $status) {
            if( $name === $status->name ){
                return $status;
            }
        }

        return null;
    }
}
