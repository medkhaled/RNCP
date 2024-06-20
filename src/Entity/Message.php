<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MessageRepository;
use DateTime;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sender')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $sender = null;

    #[ORM\ManyToOne(inversedBy: 'recipient')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $recipient = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(nullable: true)]
    private ?bool $isRead = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
       
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?user
    {
        return $this->sender;
    }

    public function setSender(?user $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?user
    {
        return $this->recipient;
    }

    public function setRecipient(?user $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(?bool $isRead): static
    {
        $this->isRead = $isRead;

        return $this;
    }
   
}
