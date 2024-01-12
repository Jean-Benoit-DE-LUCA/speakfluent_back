<?php

namespace App\Repository;

use App\Entity\UserChatPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserChatPassword>
 *
 * @method UserChatPassword|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserChatPassword|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserChatPassword[]    findAll()
 * @method UserChatPassword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserChatPasswordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserChatPassword::class);
    }



    // insert password //

    public function insertPassword($user_id, $user_receive, $chat_password, $created_at, $updated_at) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'INSERT
                INTO user_chat_password (user_id, user_receive, chat_password, created_at, updated_at)
                VALUES (:user_id, :user_receive, :chat_password, :created_at, :updated_at)';

        $result = $conn->executeQuery($sql, [
            'user_id' => $user_id,
            'user_receive' => $user_receive,
            'chat_password' => $chat_password,
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ]);
    }



    // fetch inserted password //

    public function fetchPassword($user_id, $user_receive, $date_created_at) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT
                *
                FROM user_chat_password
                WHERE user_id = :user_id
                AND user_receive = :user_receive
                AND created_at = :created_at';

        $result = $conn->executeQuery($sql, [
            'user_id' => $user_id,
            'user_receive' => $user_receive,
            'created_at' => $date_created_at
        ]);

        return $result->fetchAllAssociative();
    }

//    /**
//     * @return UserChatPassword[] Returns an array of UserChatPassword objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserChatPassword
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
