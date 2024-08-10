<?php

namespace App\Controller\DataRachelle;

use App\Service\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/parametre/dashboard')]
class DashBoardController extends AbstractController
{

    #[Route(path: '/', name: 'app_config_point_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Breadcrumb $breadcrumb): Response
    {



        /* if($this->menu->getPermission()){
             $redirect = $this->generateUrl('app_default');
             return $this->redirect($redirect);
             //dd($this->menu->getPermission());
         }*/
        $modules = [
            [
                'label' => 'Les Jeunes',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_point_jeune_index')

            ],
            [
                'label' => 'Les Adultes',
                'icon' => 'bi bi-truck',
                'href' => $this->generateUrl('app_point_adulte_index')
            ],
            /*  [
                'label' => 'Gestion utilisateur',
                'icon' => 'bi bi-users',
                'href' => $this->generateUrl('app_config_location_ls', ['module' => 'utilisateur'])
            ],*/


        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);

        return $this->render('datarachelle/config/index.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb,
        ]);
    }
}
