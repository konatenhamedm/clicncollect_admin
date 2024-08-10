<?php

namespace App\Controller\Marque;

use App\Controller\FileTrait;
use App\Entity\Marque;
use App\Form\MarqueType;
use App\Repository\MarqueRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/marque/marque')]
class MarqueController extends AbstractController
{
    use FileTrait;

    protected const UPLOAD_PATH = 'media_clicncollect';
    #[Route('/', name: 'app_marque_marque_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $projectDir = str_replace('\\', '/', $projectDir);
        $table = $dataTableFactory->create()
            ->add('photo', TextColumn::class, ['label' => 'Image', 'className' => ' w-100px', 'render' => function ($value, Marque $context) use ($projectDir) {
                //dd($projectDir . '/' . $context->getPhoto()->getFileNamePath());
                return '<img src="/' . $context->getPhoto()->getFileNamePath() . '" width="50px"/>';

                //  return '<img src="' . $projectDir . '/public/' . $context->getPhoto()->getFileNamePath() . '" width="50px"/>';
            }])
            ->add('libelle', TextColumn::class, ['label' => 'Libellé'])
            ->add('ordre', TextColumn::class, ['label' => 'Ordre', 'className' => ' w-200px',])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Marque::class,
            ])
            ->setName('dt_app_marque_marque');

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
                'label' => 'Actions',
                'orderable' => false,
                'globalSearchable' => false,
                'className' => 'grid_row_actions',
                'render' => function ($value, Marque $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_marque_marque_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_marque_marque_delete', ['id' => $value]),
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


        return $this->render('marque/marque/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_marque_marque_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $marque = new Marque();
        $validationGroups = ['Default', 'FileRequired', 'oui'];

        $form = $this->createForm(MarqueType::class, $marque, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_marque_marque_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_marque_marque_index');




            if ($form->isValid()) {

                $entityManager->persist($marque);
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

        return $this->render('marque/marque/new.html.twig', [
            'marque' => $marque,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_marque_marque_show', methods: ['GET'])]
    public function show(Marque $marque): Response
    {
        return $this->render('marque/marque/show.html.twig', [
            'marque' => $marque,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_marque_marque_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Marque $marque, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];

        $form = $this->createForm(MarqueType::class, $marque, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_marque_marque_edit', [
                'id' =>  $marque->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_marque_marque_index');




            if ($form->isValid()) {

                $entityManager->persist($marque);
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

        return $this->render('marque/marque/edit.html.twig', [
            'marque' => $marque,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_marque_marque_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Marque $marque, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_marque_marque_delete',
                    [
                        'id' => $marque->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($marque);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_marque_marque_index');

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

        return $this->render('marque/marque/delete.html.twig', [
            'marque' => $marque,
            'form' => $form,
        ]);
    }
}
