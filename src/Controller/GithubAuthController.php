<?php

namespace App\Controller;

use App\Entity\User;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\GithubOauth2;

#[IsGranted('ROLE_USER')]
class GithubAuthController extends AbstractController
{
    #[Route('/afterLogin', name: 'afterLogin')]
    public function connectToGithub(SessionInterface $session): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('login');
        }

        /** @var User $user * */
        $user = $this->getUser();
        $userGithubId = $user->getGithubId();
        $userGithubSecret = $user->getGithubSecret();
        $githubSession = new GithubOauth2($userGithubId, $userGithubSecret);

        return $this->redirect($githubSession->getGithubLoginUrl());
    }

    /**
     * @throws IdentityProviderException
     */
    #[Route('connect/github/check', name: 'github_callback')]
    public function githubCallback(Request $request): Response
    {
        $code = $request->query->get('code');

        if (!$code) {
            throw new \RuntimeException('No code provided');
        }
        /** @var User $user * */
        $user = $this->getUser();
        $userGithubId = $user->getGithubId();
        $userGithubSecret = $user->getGithubSecret();

        $githubSession = new GithubOauth2($userGithubId, $userGithubSecret);
        $githubSession->handleGithubCallback($code, $request->getSession());

        return $this->redirectToRoute('project_index');
    }
}
