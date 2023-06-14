<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ContributorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use App\Service\GithubOauth2;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Security("is_granted('ROLE_USER')")]
#[Route('/project', name: 'project_')]
class ProjectController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/projectOnGithub', name: 'projectOnGithub')]
    public function fetchGitHubInformation(SessionInterface $session, HttpClientInterface $httpClient, ProjectRepository $projectRepository): Response
    {
        $token = $session->get('user')['access_token'];

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/vnd.github.v3+json',
            'X-GitHub-Api-Version' => '2022-11-28'
        ];

        $url = 'https://api.github.com/user/repos';

        $response = $httpClient->request('GET', $url, [
            'headers' => $headers
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            $repos =  $response->toArray();
                foreach ($repos as $repo) {
                    $project = new Project();
                    $project->setName($repo['name']);
                    $project->setGithubLink($repo['html_url']);
                    $projectRepository->save($project, true);
                }
        } else {
            throw new \Exception('Error: ' . $statusCode);
        }
    }

    #[Route('/', name: 'index')]
    public function index(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
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
    public function showProject(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project
        ]);
    }


    #[Route('/{projectId}/addContributor/{contributorId}', name: 'addContributor')]
    public function addContributorToProject(
        int                   $projectId,
        int                   $contributorId,
        ProjectRepository     $projectRepository,
        ContributorRepository $contributorRepository
    ): Response
    {

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
}
