<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class CollectMenuBuilder
{
    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'Collect';

    public function __construct(FactoryInterface $factory, Security $security)
    {
        $this->factory = $factory;
        $this->security = $security;
        $this->user = $security->getUser();
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setExtra('module', self::MODULE_NAME);
        if ($this->user->hasRoleOnModule(self::MODULE_NAME)) {
            $menu->addChild(self::MODULE_NAME, ['label' => 'Gestion']);
        }

        if (isset($menu[self::MODULE_NAME])) {
            //$menu->addChild('parametre.index', ['route' => 'app_parametre_dashboard_index', 'label' => 'Paramètres'])->setExtra('icon', 'bi bi-gear');
            $menu->addChild('marque', ['route' => 'app_marque_marque_index', 'label' => 'Marques'])->setExtra('icon', 'bi bi-person');
            $menu->addChild('baniere', ['route' => 'app_marque_baniere_index', 'label' => 'Banières'])->setExtra('icon', 'bi bi-people-fill');
        }

        return $menu;
    }
}
