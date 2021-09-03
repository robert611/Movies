<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Model\ChangePassword;
use App\Form\ChangePasswordType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/account', name: 'account')]
    public function index(Request $request): Response
    {
        $changePasswordModel = new ChangePassword();

        $changePasswordForm = $this->createForm(ChangePasswordType::class, $changePasswordModel);
        $changePasswordForm->handleRequest($request);

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

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($this->getUser());
            $entityManager->flush();

            $this->addFlash('account_success', 'Twoje hasło zostało zmienione');

            return $this->redirectToRoute('account');
        }

        return $this->render('account/index.html.twig', [
            'changePasswordForm' => $changePasswordForm->createView(),
        ]);
    }
}
