<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/conversations',
            controller: GetThreadCollectionController::class,
            normalizationContext: ['groups' => ['conversation:read']],
            security: "is_granted('ROLE_USER')",
            name: 'conversations_limited'
        ),
        new Post(denormalizationContext: ['groups' => ['conversation:write']], security: "is_granted('ROLE_USER')"),
        new Get(
            uriTemplate: '/conversations/{id}',
            controller: GetThreadController::class,
            normalizationContext: ['groups' => ['conversation:read', 'conversation:inspect']],
            security: "is_granted('ROLE_USER')",
            name: 'conversation_limited'
        ),
        new Delete(security: "is_granted('ROLE_ADMIN') or object.owner == user"),
    ],
)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ownedConversation')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'guestConversation')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $guest = null;

    #[ORM\OneToMany(mappedBy: 'relatedConversation', targetEntity: PrivateMessage::class, orphanRemoval: true)]
    private Collection $privateMessage;

    public function __construct()
    {
        $this->privateMessage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getGuest(): ?User
    {
        return $this->guest;
    }

    public function setGuest(?User $guest): self
    {
        $this->guest = $guest;

        return $this;
    }

    /**
     * @return Collection<int, PrivateMessage>
     */
    public function getPrivateMessage(): Collection
    {
        return $this->privateMessage;
    }

    public function addPrivateMessage(PrivateMessage $privateMessage): self
    {
        if (!$this->privateMessage->contains($privateMessage)) {
            $this->privateMessage->add($privateMessage);
            $privateMessage->setRelatedConversation($this);
        }

        return $this;
    }

    public function removePrivateMessage(PrivateMessage $privateMessage): self
    {
        if ($this->privateMessage->removeElement($privateMessage)) {
            // set the owning side to null (unless already changed)
            if ($privateMessage->getRelatedConversation() === $this) {
                $privateMessage->setRelatedConversation(null);
            }
        }

        return $this;
    }
}
