<?php

namespace App\Repository;

use App\Entity\Compte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method Compte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Compte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Compte[]    findAll()
 * @method Compte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompteRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Compte::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Compte $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Compte $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Compte) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return Compte[] Returns an array of Compte objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Compte
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findByRoleAndSituation(string $role)
    {
        $role = mb_strtoupper($role);

        return $this->createQueryBuilder('u')
            ->Where('u.situation = :situation')
            ->setParameter('situation', 'actif')
            ->andWhere('JSON_CONTAINS(u.roles, :role) = 1')
            ->setParameter('role', '"' . $role . '"')
            ->getQuery()
            ->getResult();
    }

    public function index($search, $sort = 'a.id', $direction = 'ASC', $deleted = false)
    {
        $qb = $this->createQueryBuilder('a');
        if ($deleted == false) $qb->where($qb->expr()->isNull('a.deletedAt'));
        else
            $qb->where($qb->expr()->isNotNull('a.deletedAt'));
        if ($search) $qb->andwhere($qb->expr()->orX(
            $qb->expr()->like('a.email', ':val'),
            $qb->expr()->like('a.nom', ':val')
        ))->setParameter('val', "%$search%");
        return $qb->orderBy($sort, $direction)
            ->getQuery()
            ->getResult();
    }
}
