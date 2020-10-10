<?php

namespace App\EntityListener;

use App\Entity\Farm;
use App\Repository\FarmRepository;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class FarmListener
 * @package App\EntityListener
 */
class FarmListener
{
    private SluggerInterface $slugger;

    private FarmRepository $farmRepository;

    public function __construct(SluggerInterface $slugger, FarmRepository $farmRepository)
    {
        $this->slugger = $slugger;
        $this->farmRepository = $farmRepository;
    }

    public function prePersist(Farm $farm): void
    {
        $this->setSlug($farm);
    }

    public function preUpdate(Farm $farm, PreUpdateEventArgs $args): void
    {
        if ($args->hasChangedField("name")) {
            $this->setSlug($farm);
        }
    }

    private function setSlug(Farm $farm): void
    {
        $slug = $this->farmRepository->getNextSlug(
            $this->slugger->slug($farm->getName())->lower()
        );
        $farm->setSlug($slug);
    }
}
