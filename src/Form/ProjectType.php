<?php

namespace App\Form;

use App\Entity\Contributor;
use App\Entity\Project;
use App\Repository\ContributorRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ])
            ->add('contributors', EntityType::class, [
                'label' => 'Contributors',
                'class' => Contributor::class,
                'query_builder' => function (ContributorRepository $contributorRepository) {
                    return $contributorRepository->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'attr' => [
                    'class' => 'd-flex flex-column'
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
