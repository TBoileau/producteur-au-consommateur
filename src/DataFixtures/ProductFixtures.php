<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Farm;
use App\Entity\Image;
use App\Entity\Position;
use App\Entity\Price;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

/**
 * Class ProductFixtures
 * @package App\DataFixtures
 */
class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $farms = $manager->getRepository(Farm::class)->findAll();

        /** @var Farm $farm */
        foreach ($farms as $farm) {
            $position = new Position();
            $position->setLatitude(43.7195049426910);
            $position->setLongitude(7.2760391235352);
            $address = new Address();
            $address->setAddress("164 Avenue des Arènes de Cimiez");
            $address->setZipCode("06100");
            $address->setCity("Nice");
            $address->setPosition($position);
            $farm->setAddress($address);

            for ($i = 1; $i <= 10; $i++) {
                $product = new Product();
                $product->setFarm($farm);
                $product->setName("Product " . $i);
                $product->setDescription("Description");
                $price = new Price();
                $price->setUnitPrice(rand(100, 1000));
                $price->setVat(2.1);
                $product->setPrice($price);
                $image = new Image();
                $image->setFile($this->createImage());
                $product->setImage($image);
                $manager->persist($product);
            }
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [UserFixtures::class];
    }

    /**
     * @return UploadedFile
     */
    private function createImage(): UploadedFile
    {
        $filename = Uuid::v4() . '.png';
        copy(
            __DIR__ . '/../../public/uploads/image.png',
            __DIR__ . '/../../public/uploads/' . $filename
        );

        return new UploadedFile(
            __DIR__ . '/../../public/uploads/' . $filename,
            $filename,
            'image/png',
            null,
            true
        );
    }
}
