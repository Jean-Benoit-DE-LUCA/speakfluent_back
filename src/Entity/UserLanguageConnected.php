<?php

namespace App\Entity;

use App\Repository\UserLanguageConnectedRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserLanguageConnectedRepository::class)]
class UserLanguageConnected
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column]
    private ?int $language_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?string $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?string $updated_at = null;

    public function __contruct() {

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

    public function getLanguageId(): ?int
    {
        return $this->language_id;
    }

    public function setLanguageId(int $language_id): static
    {
        $this->language_id = $language_id;

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
