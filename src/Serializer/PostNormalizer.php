<?php

namespace App\Serializer;

use App\Entity\Post;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Vich\UploaderBundle\Storage\StorageInterface;

class PostNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    
    private const ALREADY_CALLED = 'POST_API_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';
    
    public function __construct(private StorageInterface $storage)
    {
    }
    
    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return !isset($context[self::ALREADY_CALLED]) && $data instanceof Post ;
    }
    
    
    /**
     *
     * @param Post $object
     */
    public function normalize($object, string $format = null, array $context = [])
    {
     
        $object->setFileUrl($this->storage->resolveUri($object, 'file'));
        $context[self::ALREADY_CALLED] = true;
        return $this->normalizer->normalize($object, $format, $context);
    }
    
}
