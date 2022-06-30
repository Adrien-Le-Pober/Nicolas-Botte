<?php

namespace App\Services;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class StripeService
{
    private $privateKey;

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        if ($_ENV['APP_ENV'] === 'dev') {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
        } else {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_LIVE'];
        }
        $this->em = $em;
    }

    public function createOrder($ateliers, $price, $user)
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
        
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    public function getPrivateKey()
    {
        return $this->privateKey;
    }
}