<?php

declare(strict_types=1);

namespace App\Serializer;


use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PostContextBuilder implements SerializerContextBuilderInterface
{
    
    public function __construct(
        private SerializerContextBuilderInterface $decorated,
        private AuthorizationCheckerInterface $authorizationChecker
    ) {
    }
    
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;
     
        if ($resourceClass === Post::class
            && isset($context['groups'])
            && $this->authorizationChecker->isGranted('ROLE_USER')
        ) {
            $context['groups'][] = 'read:collection:user';
        }
        
        return $context;
    }
}
