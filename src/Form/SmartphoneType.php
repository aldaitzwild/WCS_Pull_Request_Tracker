<?php

namespace App\Form;

use App\Entity\Smartphone;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class SmartphoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('name', TextType::class, [
            'label' => 'Nom'
        ])
        ->add('marque', ChoiceType::class, [
            'label' => 'Marque',
            'choices' => [
                'Samsung' => 'samsung',
                'Iphone' => 'iphone',
                'Huawei' => 'huawei',
                'Xiaomi' => 'xiaomi',


            ],
        ])
        ->add('etat', ChoiceType::class, [
            'label' => 'État',
            'choices' => [
                'Neuf' => 'neuf',
                'DEEE' => 'DEEE',
                'REPARABLE' => 'REPARABLE',
                'BLOQUE' => 'BLOQUE',
                'RECONDITIONNE' => 'RECONDITIONNE',
                'RECONDITIONABLE	' => 'RECONDITIONABLE'
                



                
            ],
            
        ])
        ->add('systeme', ChoiceType::class, [
            'label' => 'Système d\'exploitation',
            'choices' => [
                'Android' => 'android',
                'IOS' => 'IOS',
                'Windows 10 Mobile ' => 'Windows 10 Mobile ',
                'BlackBerry OS' => 'BlackBerry OS'

            ],
        ])
        ->add('stockage', ChoiceType::class, [
            'label' => 'Capacité de stockage (Go)',
            'choices' => [
                '16 Go' => '16 Go',
                '32 Go' => '32 Go',
                '64 Go' => '64 Go',
                '120 Go' => '120 Go'

            ],
        ])
        ->add('ram', ChoiceType::class, [
            'label' => 'RAM (Go)',
            'choices' => [
                '2 Go' => '2 Go',
                '4 Go' => '4 Go',
                '8 Go' => '8 Go',
                '16 Go' => '16 Go',
                '32 Go' => '32 Go',


            ],
        ])
        ->add('taille_ecran', ChoiceType::class, [
            'label' => 'Taille de l\'écran (pouces)',
            'choices' => [
                "3,5'' " => "3,5'' ",
                "4'' " => "4'' ",
                "5'' " => "5'' ",

                "6'' " => "6'' ",
                "7'' " => "7'' ",



            ],
        ])
        ->add('reseau', ChoiceType::class, [
            'label' => 'Réseau',
            'choices' => [
                '3G' => '3g',
                '4G' => '4g',
                '5G' => '5g'
            ],
            
        ])
        ->add('photo', FileType::class, [
            'label' => 'Photo',
            'required' => false
        ])
    ;
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Smartphone::class,
        ]);
    }
}
