<?php


namespace App\Controller\Apis;

use App\Controller\Apis\Config\ApiInterface;
use App\Repository\BaniereRepository;
use App\Repository\MarqueRepository;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiGeneralController extends ApiInterface
{

    #[Route('/marque', methods: ['GET'])]
    public function getMarque(MarqueRepository $marqueRepository)
    {
        try {
            $marques = $marqueRepository->findBy([], ['ordre' => 'ASC']);

            $response = $this->responseNew($marques, 'groupe_marque');
        } catch (\Throwable $th) {
            //throw $th;
        }


        return $response;
    }
    #[Route('/baniere', methods: ['GET'])]
    public function getBaniere(BaniereRepository $marqueRepository)
    {
        try {
            $marques = $marqueRepository->findBy([], ['id' => 'ASC']);

            $response = $this->responseNew($marques, 'groupe_marque');
        } catch (\Throwable $th) {
            //throw $th;
        }


        return $response;
    }
}
