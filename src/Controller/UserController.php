<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    )
    : Response
    {
        if($this->getUser()->getUserIdentifier() !== $user->getEmail()) {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $passwordForm = $this->createForm(UserPasswordType::class, $user);
        $passwordForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            $this->addFlash('success', 'Les informations de votre compte ont bien été modifiée');
            return $this->redirectToRoute('app_user_edit', ['id' => $user->getId(), '_fragment' => 'user-edit'], Response::HTTP_SEE_OTHER);
        }

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $password = $passwordForm->get('password')->getData();
            if ($userPasswordHasher->isPasswordValid($user, $password)) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                            $user,
                            $passwordForm->get('plainPassword')->getData()
                        )
                    );
            } else {
                $this->addFlash('danger', "L'ancien mot de passe n'est pas le même");
                return $this->redirectToRoute('app_user_edit', ['id' => $user->getId(), '_fragment' => 'user-edit'], Response::HTTP_SEE_OTHER);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe modifié avec succès');
            return $this->redirectToRoute('app_user_edit', ['id' => $user->getId(), '_fragment' => 'user-edit'], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'passwordForm' => $passwordForm
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if($this->getUser()->getUserIdentifier() !== $user->getEmail()) {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
