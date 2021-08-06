<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\PostCountController;
use App\Controller\PostPublishController;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
#[ApiResource(
    collectionOperations: [
    'get',
    'post',
    'count' => [
        'method' => 'GET',
        'path' => '/posts/count',
        'controller' => PostCountController::class,
        'read' => false,
        'pagination_enabled' => false,
        'filters' => [],
        'openapi_context' => [
            'summary' => 'Compte les articles',
            'parameters' => [
                [
                    'in' => 'query',
                    'name' => 'online',
                    'schema' => [
                        'type' => 'integer',
                        'minimum' => 0,
                        'maximum' => 1,
                    ],
                    'description' => 'Filtre les articles en ligne',
                ],
            ],

            'responses' => [
                '200' => [
                    'description' => 'Nombre d\'articles publiés',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'integer',
                                'exemple' => 3,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
],
    itemOperations: [
    'get' => [
        'normalization_context' => ['groups' => ['read:collection', 'read:item', 'read:Post'],
            'openapi_definition_name' => 'Detail',
        ],
    ],
    'put',
    'delete',
    'publish' => [
        'method' => 'POST',
        'path' => '/posts/{id}/publish',
        'controller' => PostPublishController::class,
        'openapi_context' => [
            'summary' => 'Publier un article',
            "description" => "Publication d'un article",
            'requestBody' => [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                        ],
                    ],
                ],
            ],

        ],
    ],
],
    denormalizationContext: ['groups' => ['write:Post']],
    normalizationContext: ['groups' => ['read:collection'], 'openapi_definition_name' => 'Collection'],
    paginationClientItemsPerPage: true,
    paginationItemsPerPage: 20,
),
    ApiFilter(
        SearchFilter::class,
        properties: [
            "id" => "exact",
            "title" => "partial",
        ]
    )]
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:collection'])]
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Groups(['read:collection', 'write:Post']),
        Length(min: 4, groups: ['create:Post']),
        NotBlank(),
    ]
    private string $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Groups(['read:collection', 'write:Post']),
        Length(min: 4, groups: ['create:Post']),
    ]
    private string $slug;

    /**
     * @ORM\Column(type="text")
     */
    #[Groups(['read:item', 'write:Post'])]
    private string $content;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:item'])]
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private \DateTimeInterface $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="posts", cascade={"persist"})
     */
    #[
        Groups(['read:item', 'write:Post']),
        Valid()
    ]
    private ?Category $category = null;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    #[
        Groups(['read:collection']),
        ApiProperty(openapiContext: [
            'type' => 'boolean',
            'description' => 'Est en ligne ?',
        ])
    ]
    private bool $online = false;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public static function validationGroups(self $post): array
    {
        return ['create:Post'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category = null): self
    {
        $this->category = $category;

        return $this;
    }

    public function getOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }
}
