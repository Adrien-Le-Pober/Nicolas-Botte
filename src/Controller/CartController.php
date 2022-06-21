<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Atelier;
use App\Form\UserInfosType;
use App\Services\StripeService;
use App\Repository\UserRepository;
use App\Repository\AtelierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/cart", name:"cart")]
class CartController extends AbstractController
{

    #[Route("/", name:"_index")]
    public function index(Request $request, AtelierRepository $atelierRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $cart = $request->getSession()->get("cart", []);

        $lineItems = [];
        $total = 0;

        foreach($cart as $id => $quantity) {
            $atelier = $atelierRepository->find($id);
            $line = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $atelier->getPrice(),
                    'product_data' => [
                        'name' => $atelier->getTitle(),
                    ],
                ],
                'quantity' => $quantity,
                'atelier_id' => $atelier->getId(),
                'atelier_date' => $atelier->getDate()
            ];

            $lineItems[] = $line;
          $total += $atelier->getPrice() * $quantity;
        }

        return $this->render("cart/index.html.twig", compact
        ("lineItems", "total"));
    }

    #[Route("/add/{id}", name:"_add")]
    public function add(Atelier $atelier, Request $request)
    {
        $cart = $request->getSession()->get("cart", []);
        $id = $atelier->getId();

        if (empty($cart[$id])) {
            $cart[$id] = 1;
        }
        //sauvegarde de la session
        $request->getSession()->set("cart", $cart);
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute("cart_index", ['_fragment' => 'cart']);
        } else {
            return $this->redirectToRoute("login", ['_fragment' => 'login']);
        }

    }

    #[Route("/delete/{id}", name:"_delete")]
    public function delete(Atelier $atelier, Request $request)
    {
        $cart = $request->getSession()->get("cart", []);
        $id = $atelier->getId();

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }
        //sauvegarde de la session
        $request->getSession()->set("cart", $cart);

        return $this->redirectToRoute("cart_index", ['_fragment' => 'cart']);
    }

    #[Route('/{id}/user-infos', name: '_user_infos', methods: ['GET', 'POST'])]
    public function userInfos(
        Request $request,
        User $user,
        UserRepository $userRepository
    )
    : Response
    {
        if($this->getUser()->getUserIdentifier() !== $user->getEmail()) {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(UserInfosType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            return $this->redirectToRoute("cart_payment_validation", ['_fragment' => 'payment-validation'], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cart/user_infos.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route("/payment-validation", name:"_payment_validation")]
    public function paymentValidation(Request $request, AtelierRepository $atelierRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $cart = $request->getSession()->get("cart", []);

        $lineItems = [];
        $total = 0;

        foreach($cart as $id => $quantity) {
            $atelier = $atelierRepository->find($id);
            $line = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $atelier->getPrice(),
                    'product_data' => [
                        'name' => $atelier->getTitle(),
                    ],
                ],
                'quantity' => $quantity,
                'atelier_id' => $atelier->getId(),
                'atelier_date' => $atelier->getDate()
            ];

            $lineItems[] = $line;
            $total += $atelier->getPrice() * $quantity;
        }

        return $this->render("cart/payment_validation.html.twig", compact
        ("lineItems", "total"));
    }

    #[Route("/stripe/create/session", name:"_stripe_create_session", methods: ["GET"])]
    public function stripeCreateSession(Request $request, StripeService $stripeService, AtelierRepository $atelierRepository)
    {
        $cart = $request->getSession()->get("cart", []);
        \Stripe\Stripe::setApiKey($stripeService->getPrivateKey());

        $lineItems = [];
        $total = 0;

        foreach($cart as $id => $quantity) {
            $atelier = $atelierRepository->find($id);
            $line = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $atelier->getPrice()*100,
                    'product_data' => [
                        'name' => $atelier->getTitle(),
                    ],
                ],
                'quantity' => $quantity,
            ];

            $lineItems[] = $line;
            $total += $atelier->getPrice() * $quantity;
            $ateliers[] = $atelier;
        }

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('cart_success', ['_fragment' => 'payment_success'], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cart_canceled', ['_fragment' => 'payment_canceled'], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return new JsonResponse(['id' => $checkout_session->id]);
    }

    #[Route("/commande/validation", name:"_success")]
    public function cartSuccess(
        Request $request,
        StripeService $stripeService,
        AtelierRepository $atelierRepository,
        EntityManagerInterface $entityManagerInterface,
        MailerInterface $mailer
    )
    {
        $cart = $request->getSession()->get("cart", []);

        if (!empty($cart)) {
            $ateliers = [];
            $total = 0;
    
            foreach($cart as $id => $quantity) {
                $atelier = $atelierRepository->find($id);
                $total += $atelier->getPrice() * $quantity;
                $ateliers[] = $atelier;
            }
    
            $order = $stripeService->createOrder($ateliers, $total, $this->getUser());
            $order->setStatus("success");
            $entityManagerInterface->persist($order);
            $entityManagerInterface->flush();

            $email = (new TemplatedEmail())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->subject('Merci pour votre commande et votre confiance !')
            ->htmlTemplate('emails/payment_success.html.twig')
            ->context([
                'order' => $order,
                'total' => $total
            ]);

            $mailer->send($email);
            
            $request->getSession()->remove("cart");
        }

        return $this->render("cart/payment_success.html.twig");
    }

    #[Route("/commande/annulation", name:"_canceled")]
    public function cartCancel()
    {
        return $this->render("cart/payment_canceled.html.twig");
    }
}