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
use App\Entity\Producer;
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
        $producer = $manager->getRepository(Producer::class)->findOneByEmail("producer@email.com");

        $products = $manager->getRepository(Product::class)->findBy(["farm" => $producer->getFarm()], [], 0, 5);

        $customer = $manager->getRepository(Customer::class)->findOneBy([]);

        $order = new Order();
        $order->setCustomer($customer);
        $order->setFarm($producer->getFarm());
        foreach ($products as $product) {
            $line = new OrderLine();
            $line->setOrder($order);
            $line->setQuantity(rand(1, 5));
            $line->setProduct($product);
            $line->setPrice($product->getPrice());
            $order->getLines()->add($line);
        }
        $order->setState("accepted");
        $manager->persist($order);
        $manager->flush();


        $customers = $manager->getRepository(Customer::class)->findAll();
        $farms = $manager->getRepository(Farm::class)->findAll();

        /** @var Customer $customer */
        foreach ($customers as $k => $customer) {
            foreach ($farms as $farm) {
                $products = $manager->getRepository(Product::class)->findBy(["farm" => $farm], [], 0, 5);

                $order = new Order();
                $order->setCustomer($customer);
                $order->setFarm($farm);
                $manager->persist($order);

                foreach ($products as $product) {
                    $line = new OrderLine();
                    $line->setOrder($order);
                    $line->setQuantity(rand(1, 5));
                    $line->setProduct($product);
                    $line->setPrice($product->getPrice());
                    $order->getLines()->add($line);
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
