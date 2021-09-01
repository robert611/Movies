<?php

namespace App\Repository;

use App\Entity\ShowCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShowCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShowCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShowCategory[]    findAll()
 * @method ShowCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShowCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShowCategory::class);
    }

    public function findCategoriesContainingShows(): array | bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT c.id, c.name FROM show_category c where id IN(SELECT DISTINCT category_id FROM shows)";

        try {
            $stmt = $conn->prepare($sql);

            $stmt->execute();
        } catch (DBALException $e) {
            return $e->getMessage();
        }

        return $stmt->fetchAll();
    }
}
