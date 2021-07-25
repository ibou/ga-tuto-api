<?php

declare(strict_types=1);

namespace App\Entity;


use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: [
    'get',
    'put' => [
        'denormalization_context' => [
            'groups' => ['put:dependency'],
        ],
    ],
        'delete'
],
    paginationEnabled: false
)
]
class Dependency
{
    #[ApiProperty(
        identifier: true
    )]
    private string $uuid;
    #[ApiProperty(
        description: 'Nom de la dependance'
    ),
        NotBlank(),
        Length(min: 2),
    ]
    private string $name;
    #[ApiProperty(
        description: 'Version de la dépendance',
        openapiContext: [
            'example' => '5.3.*',
        ]
    ), NotBlank(),
        Groups(['put:dependency'])
    ]
    private string $version;
    
    
    public function __construct(
        string $name,
        string $version
    ) {
        $this->uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString();
        $this->name = $name;
        $this->version = $version;
    }
    
    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
    
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }
    
    
}
