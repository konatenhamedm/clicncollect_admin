<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class EspaceEtudiantMenuBuilder
{
    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'espace';

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
            $menu->addChild(self::MODULE_NAME, ['label' => 'Rachelle data']);
        }

        if (isset($menu[self::MODULE_NAME])) {
            // $menu->addChild('information', ['route' => 'app_datarachelle_data_analyse_index', 'label' => 'Data Rachelle'])->setExtra('icon', 'bi bi-person');
            // $menu->addChild('stat', ['route' => 'app_config_point_index', 'label' => 'Statistique Rachelle'])->setExtra('icon', 'bi bi-person');

            // $menu->addChild('groupe.index', ['route' => 'app_utilisateur_groupe_index', 'label' => 'Groupes'])->setExtra('icon', 'bi bi-people-fill');
            //$menu->addChild('utilisateur.index', ['route' => 'app_utilisateur_utilisateur_index', 'label' => 'Utilisateurs'])->setExtra('icon', 'bi bi-person-fill');
        }

        return $menu;
    }
}
