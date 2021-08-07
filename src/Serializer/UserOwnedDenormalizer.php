<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\UserOwnedInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class UserOwnedDenormalizer implements
    ContextAwareDenormalizerInterface,
    DenormalizerAwareInterface
{
    
    use DenormalizerAwareTrait;
    
    private const ALREADY_CALLED_DENORMALIZER = 'UserOwnedDenormalizerCalled';
    
    public function __construct(private Security $security)
    {
    }
    
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        
        $reflexionClass = new \ReflectionClass($type);
        
        $alreadyDenormalized = $data[self::ALREADY_CALLED_DENORMALIZER] ?? false;
        
        return false === $alreadyDenormalized && $reflexionClass->implementsInterface(UserOwnedInterface::class);
    }
    
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
    
        $data[self::ALREADY_CALLED_DENORMALIZER] = true;
        /** @var UserOwnedInterface $object */
        $object = $this->denormalizer->denormalize($data, $type, $format, $context);
        $object->setUser($this->security->getUser());
      
        return $object;
    }
    
   
    
}
