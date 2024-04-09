<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\DBAL\DriverManager;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    private UserService $userService;
    private ValidatorInterface $validator;

    public function __construct(UserService $userService, ValidatorInterface $validator)
    {
        $this->userService = $userService;
        $this->validator = $validator;
    }

    #[Route('/user', name: 'user_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $this->prepareDatabase();
        $submittedToken = $request->getPayload()->get('token');

        if ($request->isMethod('POST') && $this->isCsrfTokenValid('user-item', $submittedToken)) {
            return $this->handlePostRequest($request);
        }

        if ($request->query->has('action') && $request->query->get('action') === 'delete') {
            $userId = $request->query->getInt('id');
            $this->userService->deleteUser($userId);
            return $this->redirectToRoute('user_index');
        }

        $users = $this->userService->getAllUsers();
        
        return $this->render('user.html.twig', [
            'users' => $users
        ]);
    }

    private function handlePostRequest(Request $request): Response
    {
        if ($request->request->has('id') && $request->request->getInt('id') > 0){
            $user = $this->userService->getUser($request->request->getInt('id'));
        } else {
            $user = new User();
        }
        $user->setFirstname($request->request->get('firstname'));
        $user->setLastname($request->request->get('lastname'));
        $user->setAddress($request->request->get('address'));

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return $this->render('user.html.twig', [
                'users' => $this->userService->getAllUsers(),
                'errors' => $errors,
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'address' => $user->getAddress(),
            ]);
        }

        $this->userService->saveUser($user);
        
        return $this->redirectToRoute('user_index');
    }

    private function prepareDatabase()
    {
        $connection = $this->getConnection();

        $tableExists = $this->executeRequest("SELECT * FROM information_schema.tables WHERE table_schema = 'symfony' AND table_name = 'user' LIMIT 1;", $connection);
        if (empty($tableExists)) {
            $this->executeRequest("CREATE TABLE user (id INT AUTO_INCREMENT PRIMARY KEY, firstname VARCHAR(255), lastname VARCHAR(255), address VARCHAR(255))", $connection);
        }

    }

    private function getConnection()
    {
        $connectionParams = [
            'dbname' => 'symfony',
            'user' => 'symfony',
            'password' => '',
            'host' => 'mariadb',
            'driver' => 'pdo_mysql',
        ];
        return DriverManager::getConnection($connectionParams);
    }

    private function executeRequest($sql, $connection)
    {
        $stmt = $connection->prepare($sql);
        return $stmt->executeQuery()->fetchAllAssociative();
    }
}