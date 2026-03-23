<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProcessorInterface<Comment, void>
 */
class AddCommentProcessor implements ProcessorInterface
{
    public function __construct(
        protected Security $security,
        protected EntityManagerInterface $entityManager,
    ) {}
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->security->getUser();
        $data->setAuthor($user);

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
