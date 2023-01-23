<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
            Request $request,
            MailerInterface $mailer
        ): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $lastname = $data['lastname'];
            $firstname = $data['firstname'];
            $email = (new TemplatedEmail())
                ->from('nicolas-botte@nicolas-botte.com')
                ->to('nicolas-botte@nicolas-botte.com')
                ->subject("Nouveau message de $lastname $firstname")
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'data' => $data
                ]);

            $mailer->send($email);

            $session = $request->getSession();
            $session->getFlashBag()->add('success', 'Merci, votre message a bien été envoyé');

            return $this->redirectToRoute('app_contact', ['_fragment' => 'contact'], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
