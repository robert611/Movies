<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Model\ChangePassword;
use App\Form\Model\ChangeEmail;
use App\Form\ChangePasswordType;
use App\Form\ChangeEmailType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\UserWatchingHistoryRepository;

class AccountController extends AbstractController
{
    private $passwordEncoder;
    private $changePasswordModel;
    private $changeEmailModel;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->changePasswordModel = new ChangePassword();
        $this->changeEmailModel = new ChangeEmail();
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/account', name: 'account')]
    public function index(Request $request): Response
    {
        $changePasswordForm = $this->createForm(ChangePasswordType::class, $this->changePasswordModel);
        $changePasswordForm->handleRequest($request);

        $changeEmailForm = $this->createForm(ChangeEmailType::class, $this->changeEmailModel);
        $changeEmailForm->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();

        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid())
        {
            $newPassword = $request->request->get('change_password')['newPassword']['first'];

            /* Encode password */
            $this->getUser()->setPassword(
                $this->passwordEncoder->encodePassword(
                    $this->getUser(),
                    $newPassword
                )
            );

            $entityManager->persist($this->getUser());
            $entityManager->flush();

            $this->addFlash('account_success', 'Twoje hasło zostało zmienione');

            return $this->redirectToRoute('account');
        }

        if ($changeEmailForm->isSubmitted() && $changeEmailForm->isValid())
        {
            $newEmail = $request->request->get('change_email')['newEmail'];

            $this->getUser()->setEmail($newEmail);

            $entityManager->persist($this->getUser());
            $entityManager->flush();

            $this->addFlash('account_success', 'Twój email został zmieniony');

            return $this->redirectToRoute('account');
        }

        return $this->render('account/index.html.twig', [
            'changePasswordForm' => $changePasswordForm->createView(),
            'changeEmailForm' => $changeEmailForm->createView(),
        ]);
    }

    #[Route('account/watching/history', name: 'account_watching_history')]
    public function watchingHistory(UserWatchingHistoryRepository $userWatchingHistoryRepository): Response
    {
        $watchingHistory = $userWatchingHistoryRepository->findBy(['user' => $this->getUser()]);

        return $this->render('account/watching_history.html.twig', ['watchingHistory' => $watchingHistory]);
    }
}
