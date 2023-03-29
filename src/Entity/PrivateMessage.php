<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PrivateMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;

#[ORM\Entity(repositoryClass: PrivateMessageRepository::class)]
#[ApiResource(
    operations: [
//        new GetCollection(normalizationContext: ['groups' => ['thread:read']]),
        new Post(denormalizationContext: ['groups' => ['message:write']], security: "is_granted('ROLE_USER')"),
//        new Get(normalizationContext: ['groups' => ['thread:read']]),
        new Delete(security: "is_granted('ROLE_ADMIN') or object.owner == user"),
        new Patch(
            denormalizationContext: ['groups' => ['message:write']],
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_USER') and object.owner == user"
        ),
        new Patch(
            uriTemplate: '/messages/{id}/vote',
            controller: VoteMessageController::class,
            description: 'Vote for a message',
            name: 'vote_message',
            security: "is_granted('ROLE_USER')",
            denormalizationContext: ['groups' => ['request:empty']]
        )
    ],
)]
#[ApiResource(
    uriTemplate: '/threads/{thread_id}/messages',
    operations: [ new GetCollection() ],
    uriVariables: [
        'thread_id' => new Link(toProperty: 'thread', fromClass: Thread::class),
    ],
    denormalizationContext: ['groups' => ['message:read']]
)]
class PrivateMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ownedPrivateMessage')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'privateMessage')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Conversation $relatedConversation = null;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRelatedConversation(): ?Conversation
    {
        return $this->relatedConversation;
    }

    public function setRelatedConversation(?Conversation $relatedConversation): self
    {
        $this->relatedConversation = $relatedConversation;

        return $this;
    }
}
