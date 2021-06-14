<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UploadFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($options['actionUrl'])
            ->add('file', FileType::class, [
            'mapped' => false,
            'required' => true,
            'attr' => [
                'accept' => implode(",", $options['mimeTypes'])
            ],
            'constraints' => [
                new File([
                    'maxSize' => $options['maxSize'].'m',
                    'mimeTypes' => $options['mimeTypes'],
                ])
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'maxSize' => '5',
            'mimeTypes' => [],
            'actionUrl' => '/'
        ]);
    }
}