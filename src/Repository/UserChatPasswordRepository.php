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

    // GET CHAT PASSWORD DATA BY ID //

    public function getById($chat_id) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT `user_chat_password`.`id` as user_chat_password_id,
                `user_chat_password`.`user_id` as user_chat_password_user_id,
                `user_chat_password`.`user_receive` as user_chat_password_user_receive,
                `user_chat_password`.`chat_password` as user_chat_password_chat_password,
                `user_chat_password`.`created_at` as user_chat_password_created_at,
                `user_chat_password`.`updated_at` as user_chat_password_updated_at,
                
                t1.`id` as user_send_id,
                t1.`name` as user_send_name,
                t1.`firstname` as user_send_firstname,
                t1.`email` as user_send_email,
                t1.`birthdate` as user_send_birthdate,
                t1.`address` as user_send_address,
                t1.`zip` as user_send_zip,
                t1.`city` as user_send_city,
                t1.`password` as user_send_password,
                t1.`gender` as user_send_gender,
                
                t2.`id` as user_receive_id,
                t2.`name` as user_receive_name,
                t2.`firstname` as user_receive_firstname,
                t2.`email` as user_receive_email,
                t2.`birthdate` as user_receive_birthdate,
                t2.`address` as user_receive_address,
                t2.`zip` as user_receive_zip,
                t2.`city` as user_receive_city,
                t2.`password` as user_receive_password,
                t2.`gender` as user_receive_gender

                FROM `user_chat_password`

                INNER JOIN `user` as t1 ON t1.`id` = `user_chat_password`.`user_id`
                INNER JOIN `user` as t2 ON t2.id = `user_chat_password`.`user_receive`
                
                WHERE `user_chat_password`.`id` = :chat_id';

        $result = $conn->executeQuery($sql, [
            'chat_id' => $chat_id
        ]);

        return $result->fetchAllAssociative();
    }





    // GET BY ID GENERAL CHAT //

    public function getByIdGeneral($chat_id) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT
                *
                FROM user_chat_password
                WHERE id = :chat_id';

        $result = $conn->executeQuery($sql, [
            'chat_id' => $chat_id
        ]);

        return $result->fetchAllAssociative();
    }




    // INSERT CHAT PASSWORD //

    public function insertPassword($user_id, $user_receive, $chat_password, $created_at, $updated_at, $user_owner_id) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'INSERT
                INTO user_chat_password (user_id, user_receive, chat_password, created_at, updated_at, user_owner_id)
                VALUES (:user_id, :user_receive, :chat_password, :created_at, :updated_at, :user_owner_id)';

        $result = $conn->executeQuery($sql, [
            'user_id' => $user_id,
            'user_receive' => $user_receive,
            'chat_password' => $chat_password,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'user_owner_id' => $user_owner_id
        ]);
    }



    // FETCH INSERT PASSWORD //

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




    // FETCH INSERT PASSWORD GENERAL //

    public function fetchPasswordGeneral($user_owner_id, $date_created_at) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT
                *
                FROM user_chat_password
                WHERE user_owner_id = :user_owner_id
                AND created_at = :created_at';

        $result = $conn->executeQuery($sql, [
            'user_owner_id' => $user_owner_id,
            'created_at' => $date_created_at
        ]);

        return $result->fetchAllAssociative();
    }




    // UPDATE USER_ID GENERAL CHAT //

    public function updateUserGeneralChatPasswordUserId($user_id, $chat_id) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'UPDATE
                user_chat_password
                SET user_id = :user_id
                WHERE id = :chat_id';

        $result = $conn->executeQuery($sql, [
            'user_id' => $user_id,
            'chat_id' => $chat_id
        ]);

    }





    // UPDATE USER_RECEIVE GENERAL CHAT //

    public function updateUserGeneralChatPasswordUserReceive($user_receive, $chat_id) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'UPDATE
                user_chat_password
                SET user_receive = :user_receive
                WHERE id = :chat_id';

        $result = $conn->executeQuery($sql, [
            'user_receive' => $user_receive,
            'chat_id' => $chat_id
        ]);
        
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
