<?php

namespace App\EntityListener;

use App\Entity\Product;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;

/**
 * Class ProductListener
 * @package App\EntityListener
 */
class ProductListener
{
    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var string
     */
    private string $uploadAbsoluteDir;

    /**
     * @var string
     */
    private string $uploadWebDir;

    /**
     * ProductListener constructor.
     * @param Security $security
     * @param string $uploadWebDir
     * @param string $uploadAbsoluteDir
     */
    public function __construct(Security $security, string $uploadWebDir, string $uploadAbsoluteDir)
    {
        $this->security = $security;
        $this->uploadWebDir = $uploadWebDir;
        $this->uploadAbsoluteDir = $uploadAbsoluteDir;
    }

    /**
     * @param Product $product
     */
    public function prePersist(Product $product): void
    {
        $this->upload($product);

        if ($product->getFarm() !== null) {
            return; // @codeCoverageIgnore
        }

        $product->setFarm($this->security->getUser()->getFarm());
    }

    /**
     * @param Product $product
     */
    public function preUpdate(Product $product): void
    {
        $this->upload($product);
    }

    /**
     * @param Product $product
     */
    private function upload(Product $product): void
    {
        if ($product->getImage() === null || $product->getImage()->getFile() === null) {
            return;
        }

        $filename = Uuid::v4() . $product->getImage()->getFile()->getClientOriginalExtension();

        $product->getImage()->getFile()->move($this->uploadAbsoluteDir, $filename);

        $product->getImage()->setPath($this->uploadWebDir . $filename);
    }
}
