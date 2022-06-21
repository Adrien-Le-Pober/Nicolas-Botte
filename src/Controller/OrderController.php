<?php

namespace App\Controller;

use Dompdf\Dompdf;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/bill/{id}', name: 'app_bill')]
    public function bill(Order $order): Response
    {
        $dompdf = new Dompdf();

        $dompdf->loadHtml($this->renderView('order/bill.html.twig', [
            'order' => $order
        ]));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream();

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
}
