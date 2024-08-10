<?php

namespace App\Repository;

use App\Entity\DataAnalyse;
use App\Entity\Regime;
use App\Entity\Statut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DataAnalyse>
 *
 * @method DataAnalyse|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataAnalyse|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataAnalyse[]    findAll()
 * @method DataAnalyse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataAnalyseRepository extends ServiceEntityRepository
{
    use TableInfoTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataAnalyse::class);
    }

    public function getAnneeRangeContrat($regime, $categorie, $statut)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableData = $this->getTableName(DataAnalyse::class, $em);
        $tableStatut = $this->getTableName(Statut::class, $em);
        $tableRegime = $this->getTableName(Regime::class, $em);
        $sql = <<<SQL
SELECT MIN(annee) AS min_year, MAX(annee) AS max_year
FROM {$tableData} d
JOIN {$tableStatut} s ON s.id = d.statut_id
JOIN {$tableRegime} r ON r.id = d.regime_id
WHERE r.libelle = :regime and d.categorie = :categorie and s.libelle = :statut

SQL;
        $params['regime'] = $regime;
        $params['statut'] = $statut;
        $params['categorie'] = $categorie;


        $stmt = $connection->executeQuery($sql, $params);
        return $stmt->fetchAssociative();
    }

    public function getData($regime, $categorie, $statut)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $tableData = $this->getTableName(DataAnalyse::class, $em);
        $tableStatut = $this->getTableName(Statut::class, $em);
        $tableRegime = $this->getTableName(Regime::class, $em);
        $sql = <<<SQL
SELECT sum(d.total) _total, d.annee ,d.sexe_genre genre
FROM {$tableData} d
JOIN {$tableStatut} s ON s.id = d.statut_id
JOIN {$tableRegime} r ON r.id = d.regime_id
WHERE r.libelle = :regime and d.categorie = :categorie and s.libelle = :statut
Group by d.sexe_genre ,d.annee
Order by d.annee


SQL;
        $params['regime'] = $regime;
        $params['statut'] = $statut;
        $params['categorie'] = $categorie;


        $stmt = $connection->executeQuery($sql, $params);
        return $stmt->fetchAllAssociative();
    }

    //    /**
    //     * @return DataAnalyse[] Returns an array of DataAnalyse objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?DataAnalyse
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
