<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post as ApiPost;
use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;


use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    description: 'Post Entity with title, body and category',
    operations: [
        new Get(
            normalizationContext: ['groups' => ['read', 'read:item']],
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['read', 'read:collection']], //GET
        ),
        new Patch(),
        new ApiPost()
    ],
//    normalizationContext: ['groups' => ['read']], //GET
    denormalizationContext: ['groups' => ['write']], //POST, PUT, PATCH
    paginationItemsPerPage: 8,
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'body' => 'partial',
    'category.name' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id'
])]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'write'])]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:item', 'write'])]
    #[Assert\NotBlank]
    private ?string $body = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    #[Assert\NotBlank]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    #[Groups(['read:collection'])]
    public function getSummary($len = 70): ?string
    {
        if (mb_strlen($this->body) <= $len) {
            return $this->body;

        }
        return mb_substr($this->body, 0, 70) . '[...]';
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
