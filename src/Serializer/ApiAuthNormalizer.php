<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Attribute\ApiAuthGroups;
use App\Entity\Post;
use App\Security\Voter\UserOwnedVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class ApiAuthNormalizer implements
    ContextAwareNormalizerInterface,
    NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    
    private const ALREADY_CALLED = 'POST_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';
    
    public function __construct(private AuthorizationCheckerInterface $authorizationChecker)
    {
    }
    
    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if(!is_object($data)){
            return false;
        }
        $class = new \ReflectionClass(get_class($data));
        $classAttributes = $class->getAttributes(ApiAuthGroups::class);
        
        $alreadyCalled = $context[self::ALREADY_CALLED] ?? false;
        
        return false === $alreadyCalled && !empty($classAttributes);
    }
    
    
    public function normalize($object, string $format = null, array $context = [])
    {
        $class = new \ReflectionClass(get_class($object));
        $apiAuthGroups = $class->getAttributes(ApiAuthGroups::class)[0]->newInstance();
        
        foreach ($apiAuthGroups->groups as $role => $groups) {
            if (($this->authorizationChecker->isGranted($role, $object))) {
                $context['groups'] = array_merge($context['groups'] ?? [], $groups);
            }
        }
        
        $context[self::ALREADY_CALLED] = true;
        
        return $this->normalizer->normalize($object, $format, $context);
    }
}
