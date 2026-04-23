<?php

namespace Dayploy\DartDtoBundle\Generator;

class StringCase
{
    public static function snakeToCamel($string)
    {
        // Remplace les underscores par des espaces
        $string = str_replace('_', ' ', $string);
        // Met chaque mot en majuscule au début
        $string = ucwords($string);
        // Enlève les espaces
        $string = str_replace(' ', '', $string);
        // Met la première lettre en minuscule
        $string = lcfirst($string);

        return $string;
    }

    public static function camelToSnake($string)
    {
        // Remplace les majuscules par un underscore suivi de la lettre en minuscule
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }
}
