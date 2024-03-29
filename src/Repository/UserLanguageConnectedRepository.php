<?php

namespace App\Repository;

use App\Entity\UserLanguageConnected;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserLanguageConnected>
 *
 * @method UserLanguageConnected|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLanguageConnected|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLanguageConnected[]    findAll()
 * @method UserLanguageConnected[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLanguageConnectedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLanguageConnected::class);
    }

    // INSERT USER LANGUAGE CONNECTED //

    public function insertUserLanguageConnected($user_id, $language_id, $created_at, $updated_at) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'INSERT INTO user_language_connected
                (user_id, language_id, created_at, updated_at)
                SELECT :user_id_1, :language_id_1, :created_at_1, :updated_at_1
                WHERE NOT EXISTS
                    (SELECT *
                     FROM user_language_connected
                     WHERE user_id = :user_id_2
                     AND language_id = :language_id_2)';

        $result = $conn->executeQuery($sql, [
            'user_id_1' => $user_id,
            'language_id_1' => $language_id,
            'created_at_1' => $created_at,
            'updated_at_1' => $updated_at,
            'user_id_2' => $user_id,
            'language_id_2' => $language_id
        ]);
    }

    // DELETE USER LANGUAGE CONNECTED //

    public function deleteUserLanguageConnected($user_id, $language_id) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'DELETE
                FROM user_language_connected
                WHERE user_id = :user_id
                AND language_id = :language_id';

        $result = $conn->executeQuery($sql, [
            'user_id' => $user_id,
            'language_id' => $language_id
        ]);
    }

    // DELETE ALL BY ID -> USER LANGUAGE CONNECTED ( LOGOUT ) //

    public function deleteUserLanguageConnectedAllById($user_id) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'DELETE
                FROM user_language_connected
                WHERE user_id = :user_id';

        $result = $conn->executeQuery($sql, [
            'user_id' => $user_id
        ]);
    }

    // GET USERS CONNECTED BY LANGUAGE //

    public function getUsersConnectedByLanguage($language_id) {

        $conn= $this->getEntityManager()->getConnection();

        $sql = 'SELECT user_language_connected.*, language.name as language_name, user.firstname
                FROM user_language_connected
                INNER JOIN language ON language.id = user_language_connected.language_id
                INNER JOIN user ON user.id = user_language_connected.user_id
                WHERE user_language_connected.language_id = :language_id
                ORDER BY user.firstname ASC';

        $result = $conn->executeQuery($sql, [
            'language_id' => $language_id
        ]);

        return $result->fetchAllAssociative();
    }




    // GET ALL USERS CONNECTED //

    public function getAllUsersConnected() {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT
                *
                FROM user_language_connected';

        $result = $conn->executeQuery($sql);

        return $result->fetchAllAssociative();
    }





    // UPDATE LAST ACTIVITY USER CONNECTED //

    public function updateActivity($user_id, $dateTimeFormat) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'UPDATE
                user_language_connected
                SET
                updated_at = :updated_at
                WHERE
                user_id = :user_id';

        $result = $conn->executeQuery($sql, [
            'updated_at' => $dateTimeFormat,
            'user_id' => $user_id
        ]);
    }

//    /**
//     * @return UserLanguageConnected[] Returns an array of UserLanguageConnected objects
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

//    public function findOneBySomeField($value): ?UserLanguageConnected
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
