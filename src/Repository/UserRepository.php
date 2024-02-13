<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }




    // FIND USER BY MAIL //

    public function findUserByMail(
        $email
    ) {

        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT user.*, role.name AS role_name 
                FROM user_role 
                INNER JOIN role ON role.id = user_role.role_id 
                INNER JOIN user ON user.id = user_role.user_id 
                WHERE user.email = :email';
        
        $result = $conn->executeQuery($sql, [
            'email' => $email
        ]);

        return $result->fetchAllAssociative();
    }




    // FIND USER BY ID //

    public function findUserById(
        $user_id
    ) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT user.*, role.name AS role_name 
                FROM user_role 
                INNER JOIN role ON role.id = user_role.role_id 
                INNER JOIN user ON user.id = user_role.user_id 
                WHERE user.id = :user_id';

        $result = $conn->executeQuery($sql, [
            'user_id' => $user_id
        ]);

        return $result->fetchAllAssociative();
    }






    // INSERT NEW USER //

    public function insertUser(
        $name,
        $firstname,
        $email,
        $birthdate,
        $gender,
        $address,
        $zip,
        $city,
        $password,
        $photo
    ) {

        $conn = $this->getEntityManager()->getConnection();
        $sql = 'INSERT
                INTO user (name, firstname, email, birthdate, gender, address, zip, city, password, photo)
                VALUES (:name, :firstname, :email, :birthdate, :gender, :address, :zip, :city, :password, :photo);';

        $result = $conn->executeQuery($sql, [
            'name' => $name,
            'firstname' => $firstname,
            'email' => $email,
            'birthdate' => $birthdate,
            'gender' => $gender,
            'address' => $address,
            'zip' => $zip,
            'city' => $city,
            'password' => $password,
            'photo' => $photo
        ]);

        return $conn->lastInsertId();
    }







    // UPDATE USER //

    public function updateUser($name, $firstname, $email, $birthdate, $gender, $address, $zip, $city, $password_hash, $photo, $user_id) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'UPDATE
                user
                SET name = :name,
                    firstname = :firstname,
                    email = :email,
                    birthdate = :birthdate,
                    gender = :gender,
                    address = :address,
                    zip = :zip,
                    city = :city,
                    password = :password_hash,
                    photo = :photo
                WHERE id = :user_id';

        $result = $conn->executeQuery($sql, [
            'name' => $name,
            'firstname' => $firstname,
            'email' => $email,
            'birthdate' => $birthdate,
            'gender' => $gender,
            'address' => $address,
            'zip' => $zip,
            'city' => $city,
            'password_hash' => $password_hash,
            'photo' => $photo,
            'user_id' => $user_id
        ]);
    }





    // UPDATE USER ( NO PHOTO UPDATE ) //

    public function updateUserNoPhoto($name, $firstname, $email, $birthdate, $gender, $address, $zip, $city, $password_hash, $user_id) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'UPDATE
                user
                SET name = :name,
                    firstname = :firstname,
                    email = :email,
                    birthdate = :birthdate,
                    gender = :gender,
                    address = :address,
                    zip = :zip,
                    city = :city,
                    password = :password_hash
                WHERE id = :user_id';

        $result = $conn->executeQuery($sql, [
            'name' => $name,
            'firstname' => $firstname,
            'email' => $email,
            'birthdate' => $birthdate,
            'gender' => $gender,
            'address' => $address,
            'zip' => $zip,
            'city' => $city,
            'password_hash' => $password_hash,
            'user_id' => $user_id
        ]);
    }





    // UPDATE PASSWORD (RESET PASSWORD) //

    public function setNewPassword($password, $email) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'UPDATE
                user
                SET password = :password
                WHERE email = :email';

        $result = $conn->executeQuery($sql, [
            'password' => $password,
            'email' => $email
        ]);
    }





    // DELETE USER //

    public function deleteUser($user_id) {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'DELETE
                FROM user
                where id = :user_id';

        $result = $conn->executeQuery($sql, [
            'user_id' => $user_id
        ]);
    }

//    /**
//     * @return User[] Returns an array of User objects
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

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
