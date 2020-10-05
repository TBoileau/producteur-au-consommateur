<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Customer;
use App\Entity\Farm;
use App\Entity\Image;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Position;
use App\Entity\Price;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

/**
 * Class OrderFixtures
 * @package App\DataFixtures
 */
class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $customers = $manager->getRepository(Customer::class)->findAll();

        /** @var Customer $customer */
        foreach ($customers as $k => $customer) {
            $farm = $manager->getRepository(Farm::class)->findOneBy([]);

            $products = $manager->getRepository(Product::class)->findBy(["farm" => $farm], [], 0, 5);
            if ($k % 2 === 0) {
                $order = new Order();
                $order->setCustomer($customer);
                $manager->persist($order);
            }

            foreach ($products as $product) {
                if ($k % 2 === 0) {
                    $line = new OrderLine();
                    $line->setOrder($order);
                    $line->setQuantity(rand(1, 5));
                    $line->setProduct($product);
                    $line->setPrice($product->getPrice());
                    $order->getLines()->add($line);
                } else {
                    $customer->addToCart($product);
                }
            }
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [UserFixtures::class, ProductFixtures::class];
    }
}
