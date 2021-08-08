<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;

class PostImageController
{
    public function __invoke(Request $request)
    {
        $post = $request->attributes->get('data');
        if (!($post instanceof Post)) {
            throw new \RuntimeException('Article attendu !');
        }
        $post->setFile($request->files->get('file'));
        $post->setUpdatedAt(new \DateTime());
        return $post;
    }
}
