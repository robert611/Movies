<?php

namespace App\Repository;

use App\Entity\ShowTheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShowTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShowTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShowTheme[]    findAll()
 * @method ShowTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShowThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShowTheme::class);
    }

    public function findThemesContainingShows(): array | bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT DISTINCT show_theme_id FROM shows_show_theme";

        try {
            $stmt = $conn->prepare($sql);

            $stmt->execute();
        } catch (DBALException $e) {
            return $e->getMessage();
        }

        $themesIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $queryBuilder = $this->createQueryBuilder('t')
            ->where('t.id IN (:themesIds)')
            ->setParameter('themesIds', $themesIds);

        $query = $queryBuilder->getQuery();

        return $query->execute();
    }
}
