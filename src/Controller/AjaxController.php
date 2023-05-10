<?php

namespace App\Controller;

use App\Repository\ContributorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    #[Route("/search_contributor", name: "ajax_search_contributor", methods: ['POST'])]
    public function searchContributor(Request $request, ContributorRepository $contributorRepository): JsonResponse
    {
        $token = $request->request->get('_token');
        if (!is_string($token) || !$this->isCsrfTokenValid('_searchContributor', $token)) {
            $responseData = [
                'success' => false,
                'message' => 'Invalid CSRF token',
            ];
            return new JsonResponse($responseData, Response::HTTP_FORBIDDEN);
        }

        $query = $request->request->get('contributorSearch');
        $result = $contributorRepository->findOneBy(['githubName' => $query]);
        if ($result) {
            $responseData = [
                'success' => true,
                'result' => [
                    'githubName' => $result->getGithubName(),
                    'id' => $result->getId(),
                ],
            ];
            return new JsonResponse($responseData, Response::HTTP_OK);
        }

        $responseData = [
            'success' => false,
            'message' => 'Contributor not found',
        ];
        return new JsonResponse($responseData, Response::HTTP_NOT_FOUND);
    }
}
