<?php

namespace App\Repository;

use App\Entity\ResetPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResetPassword>
 *
 * @method ResetPassword|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResetPassword|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResetPassword[]    findAll()
 * @method ResetPassword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResetPasswordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetPassword::class);
    }


    // CHECK TOKEN //

    public function checkMailToken($email, $token) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT
                *
                FROM reset_password
                WHERE email = :email AND token = :token';

        $result = $conn->executeQuery($sql, [
            'email' => $email,
            'token' => $token
        ]);

        return $result->fetchAllAssociative();
    }




    // INSERT TOKEN //

    public function insertToken($email, $token, $created_at, $udpated_at) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'INSERT
                INTO reset_password
                (email, token, created_at, updated_at)
                VALUES
                (:email, :token, :created_at, :updated_at)';

        $result = $conn->executeQuery($sql, [
            'email' => $email,
            'token' => $token,
            'created_at' => $created_at,
            'updated_at' => $udpated_at
        ]);
    }

//    /**
//     * @return ResetPassword[] Returns an array of ResetPassword objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ResetPassword
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
