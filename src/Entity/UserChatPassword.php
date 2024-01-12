<?php

namespace App\Entity;

use App\Repository\UserChatPasswordRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserChatPasswordRepository::class)]
class UserChatPassword
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column]
    private ?int $user_receive = null;

    #[ORM\Column(length: 255)]
    private ?string $chat_password = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?string $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?string $updated_at = null;



    public function __construct() {

        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getUserReceive(): ?int
    {
        return $this->user_receive;
    }

    public function setUserReceive(int $user_receive): static
    {
        $this->user_receive = $user_receive;

        return $this;
    }

    public function getChatPassword(): ?string
    {
        return $this->chat_password;
    }

    public function setChatPassword(string $chat_password): static
    {
        $this->chat_password = $chat_password;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTime $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
