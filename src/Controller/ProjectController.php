<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ContributorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use App\Repository\PullRequestRepository;
use App\Service\FetchGithubService;

#[IsGranted('ROLE_USER')]
#[Route('/project', name: 'project_')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        FetchGithubService $fetchGithubService,
        ProjectRepository $projectRepository,
        PullRequestRepository $pullRequestRepository
    ): Response {

        if ($fetchGithubService->fetchProject() === true) {
            $projects = $projectRepository->findBy([], ['createdAt' => 'DESC']);
            $lastPRs = [];

            foreach ($projects as $project) {
                $lastPRs[$project->getId()] = $pullRequestRepository->findLastPRForProject($project);
            }
            return $this->render('project/index.html.twig', [
                'projects' => $projects,
                'last_prs' => $lastPRs,
            ]);
        }

        throw $this->createNotFoundException("Can't fetch some project on github");
    }

    #[Route('/addProject', name: 'add')]
    public function addProject(ProjectRepository $projectRepository, Request $request): Response
    {
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectRepository->save($project, true);

            $this->addFlash('success', 'Project was created you rock !');

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/new.html.twig', [
            'projectForm' => $form,
            'project' => $project,
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function showProject(
        Project $project,
        ContributorRepository $contributorRepository,
        PullRequestRepository $pullRequestRepository
    ): Response {
        $contributors = $contributorRepository->getContributorsInProjectByOrderAlphabetic($project);

        $nbPullRequests = [];
        foreach ($contributors as $contributor) {
            $nbPullRequest = $pullRequestRepository->getNbOfPrForContributorInOneProject($contributor, $project);
            $nbPullRequests[$contributor->getId()] = $nbPullRequest;
        }

        $pullRequestSort = $pullRequestRepository->getSortedPullRequestsForProject($project);

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'contributors' => $contributors,
            'nbPullRequests' => $nbPullRequests,
            'pullRequestSort' => $pullRequestSort,
        ]);
    }

    #[Route('/{projectId}/addContributor/{contributorId}', name: 'addContributor')]
    public function addContributorToProject(
        int $projectId,
        int $contributorId,
        ProjectRepository $projectRepository,
        ContributorRepository $contributorRepository
    ): Response {
        $project = $projectRepository->findOneBy(['id' => $projectId]);
        $contributor = $contributorRepository->findOneBy(['id' => $contributorId]);

        if (!$project) {
            $this->addFlash('danger', 'Project not found.');
            return $this->redirectToRoute('project_index');
        }

        if (!$contributor) {
            $this->addFlash('danger', 'Contributor not found.');
            return $this->redirectToRoute('project_show', ['id' => $projectId]);
        }

        if ($project->getContributors()->contains($contributor)) {
            $this->addFlash('warning', 'This contributor is already on the project.');
            return $this->redirectToRoute('project_show', ['id' => $projectId]);
        }

        $project->addContributor($contributor);
        $projectRepository->save($project, true);

        $this->addFlash('success', 'Contributor added successfully to the project.');
        return $this->redirectToRoute('project_show', ['id' => $projectId], Response::HTTP_SEE_OTHER);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function editProject(Request $request, ProjectRepository $projectRepository, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectRepository->save($project, true);

            $this->addFlash('success', 'Modification successful.');

            return $this->redirectToRoute('project_show', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/edit.html.twig', [
            'projectForm' => $form,
            'project' => $project,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function deleteProject(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        if (is_string($request->request->get('_token')) || is_null($request->request->get('_token'))) {
            if ($this->isCsrfTokenValid('_delete' . $project->getId(), $request->request->get('_token'))) {
                $projectRepository->remove($project, true);
            }
        }

        $this->addFlash('success', 'Project deleted with success.');

        return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/pull_request/{id}', name: 'update_pullRequests')]
    public function updatePullRequest(FetchGithubService $fetchGithubService, Project $project): Response
    {
        $projectId = $project->getId();
        $fetchGithubService->fetchPullRequestsByProject($projectId);
        return $this->redirectToRoute('project_show', ['id' => $projectId]);
    }
}
