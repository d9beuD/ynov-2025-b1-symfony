<?php

namespace App\Form\DataTransformer;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PostToIdTransformer implements DataTransformerInterface
{
    public function __construct(
        private PostRepository $postRepository,
    ) {
    }

    public function transform(mixed $value): mixed
    {
        if (!$value instanceof Post) {
            return '';
        }

        return $value->getId();
    }

    public function reverseTransform(mixed $value): mixed
    {
        if (!$value) {
            return null;
        }

        $post = $this->postRepository->find($value);

        if (!$post instanceof Post) {
            throw new TransformationFailedException(sprintf(
                'Post with id %s not found.',
                $value,
            ));
        }

        return $post;
    }
}
