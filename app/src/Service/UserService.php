<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUser(int $userId): ?User
    {
        try {
            $user = $this->entityManager->getRepository(User::class)->find($userId);
            return $user;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function saveUser(User $user): void
{
    try {
        $this->entityManager->beginTransaction();
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->entityManager->commit();
    } catch (\Exception $e) {
        $this->entityManager->rollback();
        throw $e;
    }
}

    public function deleteUser(int $userId): void
    {
        try {
            $this->entityManager->beginTransaction();
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            if ($user) {
                $this->entityManager->remove($user);
                $this->entityManager->flush();
            }

            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    public function getAllUsers(): array
    {
        try {
            $users = $this->entityManager->getRepository(User::class)->findAll();
            return $users;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
