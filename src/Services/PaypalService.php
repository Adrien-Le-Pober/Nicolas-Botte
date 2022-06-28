<?php

namespace App\Services;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class PaypalService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createOrder($ateliers, $price, $user)
    {
        $order = new Order();
        $order->setUser($user);
        $order->setPrice($price);
        foreach($ateliers as $atelier){
            $order->addAtelier($atelier);
        }
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setReference(hexdec(uniqid()));
        
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }
}