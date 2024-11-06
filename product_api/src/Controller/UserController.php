<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserController extends AbstractController
{
    private $userProvider;
    private $passwordHaser;
    private $entityManager;
    private $JWTTokenManager;

    public function __construct(
        UserProviderInterface $userProvider,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $JWTTokenManager
    )

    {
        $this->userProvider = $userProvider;
        $this->passwordHaser = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->JWTTokenManager = $JWTTokenManager;
    }

    public function register(Request $request)
    {
        $user = new User();

        $user->setEmail($request->get('email'));
        $user->setPassword($this->passwordHaser->hashPassword($user, $request->get('password')));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $token = $this->JWTTokenManager->create($user);

        return $this->json(['message' => 'User created!', 'token' => $token ], Response::HTTP_CREATED);
    }

    public function login_check(Request $request) 
    {
        $user = $this->userProvider->loadUserByIdentifier($request->get('email'));

        if (!$user || !$this->passwordHaser->isPasswordValid($user, $request->get('password'))) {
            return $this->json(['message' => 'Bas credentials'], Response::HTTP_BAD_REQUEST);
        }

        $token = $this->JWTTokenManager->create($user);

        return $this->json(['token' => $token]);
    }
}
