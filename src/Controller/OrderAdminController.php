<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order/admin')]
class OrderAdminController extends AbstractController
{
    #[Route('/', name: 'app_order_admin_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('order_admin/index.html.twig', [
            'orders' => $orderRepository->findAll(),
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
