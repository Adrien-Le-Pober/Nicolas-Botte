<?php

namespace App\Services;

use App\Entity\Order;
use App\Repository\TvaRepository;
use Doctrine\ORM\EntityManagerInterface;

class PaypalService
{
    private $em;
    private $tvaRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createOrder($ateliers, $price, $user, $tva)
    {
        $order = new Order();
        $order->setUser($user);
        $order->setPrice($price);
        foreach($ateliers as $atelier){
            $atelier->setStock($atelier->getStock() - 1);
            $order->addAtelier($atelier);
        }
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setReference(hexdec(uniqid()));
        $order->setTva($tva);
        
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }
}