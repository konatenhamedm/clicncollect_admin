<?php

namespace App\Controller\DataRachelle;

use App\Classes\UploadFile;
use App\Entity\DataAnalyse;
use App\Form\DataAnalyseType;
use App\Form\UploadFileDeleteType;
use App\Form\UploadFileType;
use App\Repository\DataAnalyseRepository;
use App\Repository\RegimeRepository;
use App\Repository\StatutRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/datarachelle/data/analyse')]
class DataAnalyseController extends AbstractController
{
    #[Route('/', name: 'app_datarachelle_data_analyse_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('etablissement', TextColumn::class, ['label' => 'Etablissement'])
            //->add('email', TextColumn::class, ['label' => 'Email', 'field' => 'e.adresseMail'])
            ->add('nomEtablissement', TextColumn::class, ['label' => 'Nom etablissement'])
            ->add('sexeGenre', TextColumn::class, ['label' => 'Sexe/genre'])
            ->add('description', TextColumn::class, ['label' => 'Description sexe/genre',])
            ->add('statut', TextColumn::class, ['label' => 'Statut légal au Canada général', 'field' => 's.libelle'])
            ->add('regime', TextColumn::class, ['label' => "Régime d'études général", 'field' => 'r.libelle'])
            ->add('total', TextColumn::class, ['label' => "Total"])
            ->add('annee', TextColumn::class, ['label' => "Annee"])
            ->createAdapter(ORMAdapter::class, [
                'entity' => DataAnalyse::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select('u, s, r')
                        ->from(DataAnalyse::class, 'u')
                        ->join('u.statut', 's')
                        ->join('u.regime', 'r');
                }
            ])
            ->setName('dt_app_datarachelle_data_analyse');

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return true;
            }),
        ];


        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, DataAnalyse $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_datarachelle_data_analyse_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_datarachelle_data_analyse_delete', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-trash',
                                'attrs' => ['class' => 'btn-danger'],
                                'render' => $renders['delete']
                            ]
                        ]

                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('datarachelle/data_analyse/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_datarachelle_data_analyse_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        FormError $formError,
        RegimeRepository $regimeRepository,
        StatutRepository $statutRepository
    ): Response {
        $uploadFile = new UploadFile();
        $form = $this->createForm(UploadFileType::class, $uploadFile, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_datarachelle_data_analyse_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_datarachelle_data_analyse_index');




            if ($form->isValid()) {

                $file = $form->get("upload_file")->getData(); // get the file from the sent request
                $categorie = $form->get("categorie")->getData(); // get the file from the sent request


                $fileFolder = $this->getParameter('kernel.project_dir') . '/public/uploads/';  //choose the folder in which the uploaded file will be stored

                //dd($fileFolder);
                $filePathName = md5(uniqid()) . $file->getClientOriginalName();

                try {
                    $file->move($fileFolder, $filePathName);
                } catch (FileException $e) {
                    dd($e);
                }

                $spreadsheet = IOFactory::load($fileFolder . $filePathName); // Here we are able to read from the excel file

                $row = $spreadsheet->getActiveSheet()->removeRow(1); // I added this to be able to remove the first file line
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true); // here, the read data is turned into an array
                //dd($sheetData);
                /* if (str_contains($file->getClientOriginalName(),"article")){ */

                //dd($sheetData);
                foreach ($sheetData as $Row) {

                    $ref = $Row['A'];     // store the first_name on each iteration
                    $etablissement = $Row['B'];   // store the last_name on each iteration
                    $sexe = $Row['C'];  // store the email on each iteration
                    $description = $Row['D']; // store the phone on each iteration
                    $statut = $Row['E']; // store the phone on each iteration
                    $regime = $Row['F']; // store the phone on each iteration
                    $total = $Row['G']; // store the phone on each iteration
                    $annee = $Row['H']; // store the phone on each iteration

                    //  $article_existant = $articleRepository->findOneBy(array('reference' => $ref));


                    $article = new DataAnalyse();


                    $article->setEtablissement($ref);
                    $article->setNomEtablissement($etablissement);
                    if ($sexe != null) {
                        $article->setSexeGenre($sexe);
                    } else {
                        $article->setSexeGenre('NA');
                    }

                    if ($description != null) {
                        $article->setDescription($description);
                    } else {
                        $article->setDescription('NA');
                    }
                    $article->setStatut($statutRepository->findOneBy(array('libelle' => $statut)));
                    $article->setRegime($regimeRepository->findOneBy(array('libelle' => $regime)));
                    if ($total != null) {
                        $article->setTotal($total);
                    } else {
                        $article->setTotal(0);
                    }
                    if ($annee != null) {
                        $article->setAnnee($annee);
                    } else {
                        $article->setAnnee('NA');
                    }
                    $article->setCategorie($categorie);

                    //$article->setAnnee($annee);

                    // here Doctrine checks all the fields of all fetched data and make a transaction to the database.
                    $entityManager->persist($article);
                    $entityManager->flush();
                }




                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('parametre/uploadFile/upload_file_new.html.twig', [
            // 'data_analyse' => $dataAnalyse,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/new/delete', name: 'app_datarachelle_data_analyse_new_delete', methods: ['GET', 'POST'])]
    public function newDelete(
        Request $request,
        EntityManagerInterface $entityManager,
        FormError $formError,
        RegimeRepository $regimeRepository,
        StatutRepository $statutRepository,
        DataAnalyseRepository $dataAnalyseRepository
    ): Response {
        $uploadFile = new UploadFile();
        $form = $this->createForm(UploadFileDeleteType::class, $uploadFile, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_datarachelle_data_analyse_new_delete')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_datarachelle_data_analyse_index');




            if ($form->isValid()) {


                $categorie = $form->get("categorie")->getData(); // get the file from the sent request

                $data = $dataAnalyseRepository->findBy(array('categorie' => $categorie));

                foreach ($data as $key => $value) {
                    $entityManager->remove($value);
                    $entityManager->flush();
                }






                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('parametre/uploadFile/upload_file_delete.html.twig', [
            // 'data_analyse' => $dataAnalyse,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_datarachelle_data_analyse_show', methods: ['GET'])]
    public function show(DataAnalyse $dataAnalyse): Response
    {
        return $this->render('datarachelle/data_analyse/show.html.twig', [
            'data_analyse' => $dataAnalyse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_datarachelle_data_analyse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DataAnalyse $dataAnalyse, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(DataAnalyseType::class, $dataAnalyse, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_datarachelle_data_analyse_edit', [
                'id' =>  $dataAnalyse->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_datarachelle_data_analyse_index');




            if ($form->isValid()) {

                $entityManager->persist($dataAnalyse);
                $entityManager->flush();

                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('datarachelle/data_analyse/edit.html.twig', [
            'data_analyse' => $dataAnalyse,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_datarachelle_data_analyse_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, DataAnalyse $dataAnalyse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_datarachelle_data_analyse_delete',
                    [
                        'id' => $dataAnalyse->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($dataAnalyse);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_datarachelle_data_analyse_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
                'data' => $data
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->render('datarachelle/data_analyse/delete.html.twig', [
            'data_analyse' => $dataAnalyse,
            'form' => $form,
        ]);
    }
}
