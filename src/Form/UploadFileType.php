<?php

namespace App\Form;

use App\Classes;
use App\Classes\UploadFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('upload_file', FileType::class, [
                'label' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add(
                'categorie',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisir une catégorie',
                    'label' => 'Catégorie',
                    'required'     => false,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        'Adulte' => 'Adulte',
                        'Jeune' => 'Jeune',

                    ]),
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UploadFile::class,
        ]);
    }
}
