<?php

namespace App\Form;

use App\Entity\Baniere;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaniereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'photo',
                FichierType::class,
                [
                    /*   'label' => 'Fichier', */
                    'label' => 'Document',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true,
                    'validation_groups' => $options['validation_groups'],
                ]
            )
            ->add('libelle')
            ->add('active', CheckboxType::class, [
                'label' => "Active",
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Baniere::class,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['validation_groups']);
    }
}
