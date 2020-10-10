<?php

namespace App\Repository;

use App\Entity\Farm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class FarmRepository
 * @package App\Repository
 * @method findByFarm(Farm $farm): array<Farm>
 */
class FarmRepository extends ServiceEntityRepository
{
    /**
     * @inheritDoc
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Farm::class);
    }

    public function getNextSlug(string $slug): string
    {
        $foundSlugs = $this->createQueryBuilder("f")
            ->select("f.slug")
            ->where("REGEXP(f.slug, :pattern) > 0")
            ->setParameter("pattern", "^" . $slug)
            ->getQuery()
            ->getScalarResult();

        if (count($foundSlugs) === 0) {
            return $slug;
        }

        $foundSlugs = array_map(function (string $foundSlug) use ($slug) {
            preg_match("/^" . $slug . "-([0-9]*)$/", $foundSlug, $matches);
            return !isset($matches[1]) ? 0 : intval($matches[1]);
        }, array_column($foundSlugs, "slug"));

        rsort($foundSlugs);

        return sprintf("%s-%d", $slug, $foundSlugs[0] + 1);
    }
}
