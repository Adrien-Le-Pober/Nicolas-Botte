<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\SearchOrderType;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/order/admin')]
class OrderAdminController extends AbstractController
{
    #[Route('/', name: 'app_order_admin_index', methods: ['GET', 'POST'])]
    public function index(
        OrderRepository $orderRepository,
        Request $request
        ): Response
    {
        //on définit le nombre d'éléments par page
        $limit = 2;
        //on récupère le numero de page ou 1
        $page = (int)$request->query->get('page', 1);
        $orders = $orderRepository->getPaginatedOrders($page, $limit);
        //on récupère le nombre total d'articles
        $total = $orderRepository->getTotalOrders();


        $form = $this->createForm(SearchOrderType::class);
        $search = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orders = $orderRepository->search($search->get('searchReference')->getData());
        }

        return $this->render('order_admin/index.html.twig', [
            'orders' => $orders,
            'form' => $form->createView(),
            'limit' => $limit,
            'page' => $page,
            'total' => $total
        ]);
    }

    #[Route('/{id}', name: 'app_order_admin_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order_admin/show.html.twig', [
            'order' => $order,
        ]);
    }
}
