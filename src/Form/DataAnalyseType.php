<?php

namespace App\Form;

use App\Entity\DataAnalyse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataAnalyseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etablissement')
            ->add('nomEtablissement')
            ->add('sexeGenre')
            ->add('description')
            ->add('total')
            ->add('annee')
            ->add('statut')
            ->add('regime')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DataAnalyse::class,
        ]);
    }
}
