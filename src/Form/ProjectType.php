<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Project name',
                'required' => 'Name is required',
                'constraints' => [
                    new NotBlank(['message' => 'The project name can be empty']),
                ],
                'attr' => [
                    'placeholder' => 'Project name',
                ],
            ])
            ->add('githubLink', UrlType::class, [
                'label' => 'URL project',
                'required' => 'URL is required',
                'constraints' => [
                    new NotBlank(['message' => 'The project URL can be empty']),
                ],
                'attr' => [
                    'placeholder' => 'Github link',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
