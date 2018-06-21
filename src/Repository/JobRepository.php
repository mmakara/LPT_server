<?php

namespace App\Repository;

use App\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * @param $lat float szerokosc geograficzna
     * @param $lng float dlugosc geograficzna
     * @param $dystans int dystans w kilometrach
     * @return mixed
     */
    public function znajdz_w_poblizu($lat, $lng, $dystans)
    {
        $sql = "
                SELECT
              * (
              6371 * acos (
              cos ( radians($lat) )
              * cos( radians( lat ) )
              * cos( radians( lng ) - radians($lng) )
              + sin ( radians($lat) )
              * sin( radians( lat ) )
            )
                ) AS distance
                FROM user
                HAVING distance < $dystans
                ORDER BY distance
                LIMIT 0 , 20;
        ";

        return [];
//        $em = $this->getDoctrine()->getManager();
//        $query = $this
//            $em->getConnection()->prepare($sql);
//        $query->execute();
//        return $query->fetchAll();
    }
}
