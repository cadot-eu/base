<?php

namespace App\EventSubscriber;

use App\Service\CrudHelper;
use App\Service\ToolsHelper;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class TwigGlobalSubscriber implements EventSubscriberInterface
{
    private $twig;

    private $em;

    public function __construct(Environment $twig, EntityManagerInterface $em)
    {
        $this->twig = $twig;
        $this->em = $em;
    }

    public function injectGlobalVariables()
    {
        $this->twig->addGlobal('parametres', ToolsHelper::params($this->em));
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'injectGlobalVariables',
        ];
    }

    public function onKernelRequest()
    {
    }
}
