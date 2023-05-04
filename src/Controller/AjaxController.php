<?php

namespace App\Controller;

use App\Repository\ContributorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    #[Route("/ajax/search_contributor", name: "ajax_search_contributor")]
    public function searchContributor(Request $request, ContributorRepository $contributorRepository): JsonResponse
    {
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
        } else {
            $responseData = [
                'success' => false,
                'message' => 'Aucun contributeur trouvé',
            ];
        }

        return new JsonResponse($responseData);
    }
}
