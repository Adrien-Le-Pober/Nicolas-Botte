<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Services\StripeService;
use App\Repository\AtelierRepository;
use Symfony\Component\HttpFoundation\Request;
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

    return $this->redirectToRoute("cart_index", ['_fragment' => 'cart']);
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
    }

    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => $this->generateUrl('cart_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
        'cancel_url' => $this->generateUrl('cart_canceled', [], UrlGeneratorInterface::ABSOLUTE_URL),
    ]);

    return new JsonResponse(['id' => $checkout_session->id]);
  }

  #[Route("/commande/validation", name:"_success")]
  public function cartSuccess()
  {
      return new Response('yoouuuuuupppipiiiiii');
  }

  #[Route("/commande/annulation", name:"_canceled")]
  public function cartCancel()
  {
      return new Response('oh noooooooo');
  }
}