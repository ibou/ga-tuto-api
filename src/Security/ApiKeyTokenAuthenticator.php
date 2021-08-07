<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiKeyTokenAuthenticator extends AbstractGuardAuthenticator
{
    

    
    
    public function __construct(private UserRepository $userRepository)
    {
    }
    
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];
        
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
 
    public function supports(Request $request): bool
    {
        return $request->headers->has('X-AUTH-TOKEN');
    }
    
    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        return $request->headers->get('X-AUTH-TOKEN');
    }
    
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        if (null === $credentials) {
            return null;
        }
        
        return $userProvider->loadUserByIdentifier($credentials);
    }
    
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $user instanceof UserInterface && $credentials === $user->getApiKey();
    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        // on success, let the request continue
        return null;
    }
    
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];
        
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
    
 
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
