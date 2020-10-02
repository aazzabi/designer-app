<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class ClientController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(UserRepository $us ) {

        $users = $us->findAll();
        $clients = new ArrayCollection();
        foreach ($users  as $u) {
            if ($u->hasRole("ROLE_CLIENT")) {
                $clients->add($u);
            }
        }
        return $this->render('client/index.html.twig', [
            'clients' => $clients,
        ]);
    }

}