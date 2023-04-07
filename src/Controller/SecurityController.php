<?php

namespace App\Controller;

use App\Repository\UserRepository;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route(path: '/magic', name: 'app.magic', methods: ['GET', 'POST'])]
    public function magic(UserRepository $userRepository, LoginLinkHandlerInterface $loginLinkHandler, MailerInterface $mailer): Response
    {
        $users = $userRepository->findAll();

        foreach ($users as $user) {
            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
            $email = (new Email())
                ->from('bot@test.com')
                ->to($user->getEmail())
                ->subject('Magic login link')
                ->text('Magic login link: ' . $loginLinkDetails->getUrl());
           $mailer->send($email);
        }

        return new Response('Magic !');
    }
}
