<?php

namespace App\Controller;

use App\Entity\Tva;
use App\Form\TvaType;
use App\Repository\TvaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tva')]
class TvaController extends AbstractController
{
    #[Route('/{id}/edit', name: 'app_tva_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tva $tva, TvaRepository $tvaRepository): Response
    {
        $form = $this->createForm(TvaType::class, $tva);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tvaRepository->add($tva, true);

            $this->addFlash('success', 'TVA modifiée avec succès');
            return $this->redirectToRoute('app_tva_edit', ['id' => 1, '_fragment' => 'tva-edit'], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tva/edit.html.twig', [
            'tva' => $tva,
            'form' => $form,
        ]);
    }
}
