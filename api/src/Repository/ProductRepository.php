<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Product[]    getBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getBy(array $criteria = null, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder("product");

        /**************************************************************************************************************/

        $termo = (isset($criteria["term"]) ? strtolower($criteria["term"]) : null);
        if (!empty($termo)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like("product.id", $qb->expr()->literal("%" . $termo . "%")),
                    $qb->expr()->like("LOWER(product.name)", $qb->expr()->literal("%" . $termo . "%")),
                    $qb->expr()->like("LOWER(product.identifier)", $qb->expr()->literal("%" . $termo . "%"))
                )
            );
        }

        /**************************************************************************************************************/

        $offset = (($offset == 0) ? 1 : $offset);
        $qb->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($qb->getQuery(), true);
        $paginator->setUseOutputWalkers(false);

        $totalResultados = $paginator->count();

        if ($totalResultados == 0) {
            $offset = 0;
            $totalPaginas = 0;
        } elseif (($totalResultados > 0 && $totalResultados < $limit) || ($limit == null)) {
            $offset = 1;
            $totalPaginas = 1;
        } else {
            $totalPaginas = (int) ceil($totalResultados / $limit);
        }

        /**************************************************************************************************************/

        return array(
            "data" => $paginator,
            "page" => $offset,
            "total" => $totalResultados,
            "rowsPerPage" => $totalPaginas
        );
    }
}
