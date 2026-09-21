<?php

namespace r401_frontend\Modele\Rencontre;

enum RencontreResultat
{
    case VICTOIRE;
    case DEFAITE;
    case NUL;

    public static function fromName(string $name): ?RencontreResultat
    {
        foreach (self::cases() as $resultat) {
            if( $name === $resultat->name ){
                return $resultat;
            }
        }

        return null;
    }
}
