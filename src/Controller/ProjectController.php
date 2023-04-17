<?php

namespace App\Controller;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use App\Form\ProjectType;

#[Route('/project', name: 'project_')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/addProject', name: 'add')]
    public function addProject(EntityManagerInterface $entityManager, Request $request): Response
    {
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash('success', 'Project was created you rock !');

            return $this->redirectToRoute('project_index');
        }

        return $this->renderForm('project/new.html.twig', [
            'projectForm' => $form,
            'project' => $project,
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function showProject(Project $project): Response
    {

        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/project/edit/{id}', name: 'edit')]
    public function editProject(Request $request, ProjectRepository $projectRepository, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectRepository->save($project, true);

            $this->addFlash('success', 'Modification successful.');

            return $this->redirectToRoute('project_show', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/edit.html.twig', [
            'projectForm' => $form,
            'project' => $project,
        ]);
    }
}
