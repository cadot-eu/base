<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class TBExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function dd($value)
    {
        //on met se code en haut de la page html en créant une div fixed
        echo '<div style="position: fixed; background-color: white; top: 0; left: 0; z-index: 9999; padding: 10px; border: 1px solid black;"><pre>';
        dd($value);
    }

    public function value($param)
    {
        // Retourne la valeur brute (texte) d'un paramètre, sans HTML
        if (is_array($param) && isset($param['value'])) {
            return $param['value'];
        }
        if (is_object($param) && property_exists($param, 'value')) {
            return $param->value;
        }
        return $param;
    }
}
