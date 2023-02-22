<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function save(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param array $array clé valeur de champs pour recherche spécifique
     * @return Event[] Returns an array of Event objects
     */
    public function findByField($array): array
    {
        $query = $this->createQueryBuilder('event');
        if (isset($array['name'])&&$array['name']!="") {
            $query = $query
                ->andWhere('event.name LIKE :name')
                ->setParameter('name', '%' . $array['name'] . '%');
        }
        if (isset($array['school'])&&$array['school']!="") {
            $query = $query
                ->join('event.organizer', 'organizer')
                ->join('organizer.school', 'school')
                ->andWhere('school.id = :school')
                ->setParameter('school', (int) $array['school']);
        }
        if (isset($array['dateMin'])&&$array['dateMin']!="") {
            $query = $query
                ->andWhere('event.dateMin >= :dateMin')
                ->setParameter('dateMin', $array['dateMin']);
        }
        if (isset($array['dateMax'])&&$array['dateMax']!="") {
            $query = $query
                ->andWhere('event.dateMax <= :dateMax')
                ->setParameter('dateMax', $array['dateMax']);
        }
        if (isset($array['organizer'])&&$array['organizer']!="") {
            $query = $query
                ->andWhere('event.name LIKE :name')
                ->setParameter('name', '%' . $array['name'] . '%');
        }

        $query->orderBy('event.name', 'ASC')->setMaxResults(10);
        return $query->getQuery()->getResult();

    }
}
