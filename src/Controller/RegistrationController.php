<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Form\RegistrationFormType;
use App\Repository\CompteRepository;
use App\Repository\ParametresRepository;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use App\Service\ToolsHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    protected $logger;

    protected $translator;

    public function __construct(EmailVerifier $emailVerifier, LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->emailVerifier = $emailVerifier;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    #[Route('/creer-un-compte', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_index');
        }
        $params = ToolsHelper::params($entityManager);
        $user = new Compte();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address($params["Mail: Email d'envoie"], $params["Mail: Nom d'envoie"]))
                    ->to($user->getEmail())
                    ->subject($this->translator->trans('Please Confirm your Email'))
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verification/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, CompteRepository $compteRepository): Response
    {
        $id = $request->get('id');
        /* ---------------------------- si il manque l'id --------------------------- */
        if (null === $id) {
            $this->addFlash('verify_email_error', $this->translator->trans('The link of validation has a error.'));
            $this->logger->error(
                'lien de validation de compte par email ',
                [
                    'link' => $request->getQueryString(),
                ]
            );
            return $this->redirectToRoute('app_register');
        }

        $user = $compteRepository->find($id);
        /* ------------------------- si l'user n'existe pas ------------------------- */
        if (null === $user) {
            $this->addFlash('verify_email_error', $this->translator->trans('The link of validation has a error.'));
            $this->logger->error(
                'lien de validation de compte par email avec un id qui n\'existe pas',
                [
                    'link' => $request->getQueryString(),
                ]
            );
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('admin_index');
        }
        $this->addFlash('success', $this->translator->trans('Your email address has been verified.'));
        return $this->redirectToRoute('admin_index');
    }
}
