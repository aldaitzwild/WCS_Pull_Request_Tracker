<?php

namespace App\Form;

use App\Entity\Contributor;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContributorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Contributor name',
                'required' => 'Name is required',
                'constraints' => [
                    new NotBlank(['message' => "Contributor name can't be empty"]),
                ],
                'attr' => [
                    'placeholder' => 'Contributor name',
                ],
            ])
            ->add('githubAccount', UrlType::class, [
                'label' => 'Github student',
                'required' => 'URL is required',
                'constraints' => [
                    new NotBlank(['message' => "The github URL can't be empty"]),
                ],
                'attr' => [
                    'placeholder' => 'Github account link',
                ],
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'name',
                'label' => 'Project',
                'required' => false,
                'placeholder' => 'Choose a project',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contributor::class,
        ]);
    }
}
