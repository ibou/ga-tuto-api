<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        /** @var PathItem $path */
        foreach ($openApi->getPaths()->getPaths() as $key => $path) {
            if ($path->getGet() && $path->getGet()->getSummary() === 'hidden') {
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            }
        }

        $schemas = $openApi->getComponents()->getSecuritySchemes();
        $schemas['cookieAuth'] = new \ArrayObject([
                                                      'type' => 'apiKey',
                                                      'in' => 'cookie',
                                                      'name' => 'PHPSESSID',
                                                  ]);
        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new \ArrayObject([
                       'type' => 'object',
                       'properties' => [
                           'username' => [
                               'type' => 'string',
                               'example' => 'idiallo@gmail.com',
                           ],
                           'password' => [
                               'type' => 'string',
                               'example' => 'dev',
                           ],
                        ],
                    ]);

        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiLogin',
                tags: ['Auth'],
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                  'application/json' => [
                     'schema' => [
                         '$ref' => "#/components/schemas/Credentials",
                     ],
                 ],
                 ])
                ),
                responses: [
                       '200' => [
                           'description' => 'Utilisateur connecté',
                           'content' => [
                               'application/json' => [
                                   'schema' => [
                                       '$ref' => "#/components/schemas/User-read.user",
                                   ],
                               ],
                           ],
                       ],
                   ]
            )
        );
        $openApi->getPaths()->addPath('/api/login', $pathItem);
        return $openApi;
    }
}
