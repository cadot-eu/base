<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\TBExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TBExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('dd', [TBExtensionRuntime::class, 'dd']),
            new TwigFunction('is_numeric', [TBExtensionRuntime::class, 'is_numeric']),
            new TwigFunction('ddump', [TBExtensionRuntime::class, 'ddump']),
            new TwigFunction('value', [TBExtensionRuntime::class, 'value']), // Extract raw value from parameter
        ];
    }
}

/**
 * Fonction Twig 'value' :
 * Utilisation : {{ TBparametres['prix du kwh']|value }}
 * Retourne la valeur brute (texte) du paramètre, sans HTML.
 * Compatible array['value'] ou objet->value.
 */
