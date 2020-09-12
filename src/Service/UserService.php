<?php
namespace App\Service;
use App\Entity\ApiToken;
use App\Entity\Provider;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService {

    protected $em;
    protected $userRepository;
    protected $apiTokenRepository;
    protected $httpRequestService;
    protected $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, HttpRequestService $httpRequestService,
                                UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $entityManager;
        $this->userRepository = $this->em->getRepository(User::class);
        $this->apiTokenRepository = $this->em->getRepository(ApiToken::class);
        $this->httpRequestService = $httpRequestService;
        $this->passwordEncoder = $passwordEncoder;

    }

    public function getUser($email) {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function setApiToken(User $user) {
        $apiTokenRepository = $this->em->getRepository(ApiToken::class);
        return $apiTokenRepository->setToken($user);
    }

    public function updateApiTokenExpiry(array $data) {
        $apiToken = $this->apiTokenRepository->findOneBy(["id" => $data["id"]]);
        if ($apiToken === null) {
            throw new BadRequestHttpException("ApiToken does not exist in database...");
        }
        return $this->apiTokenRepository->updateTokenExpiry($apiToken, new \DateTime($data["expires_at"]));
    }

    public function getLatestToken(User $user) {
        return $this->apiTokenRepository->getLatestToken($user);
    }

    public function getTokenByValue(string $tokenValue) {
        return $this->apiTokenRepository->findOneBy(["token" => $tokenValue]);
    }

    public function findApiTokensByParams(User $user, string $sort,  string $order, int $count)
    {
        return $this->userRepository->findApiTokensByParams($user, $sort, $order, $count);
    }

    public function findByParams(string $sort,  string $order, int $count)
    {
        return $this->userRepository->findByParams($sort, $order, $count);
    }

    private function setUserObject(User $user, array $data, string $type = "insert") {
        if ($type === "insert") {
            $getUser = $this->userRepository->findOneBy(["email" => $data["email"]]);
            if ($getUser !== null) {
                throw new BadRequestHttpException("User already exists");
            }
        }
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);

        $roles = $data["roles"];
        if (!is_array($roles)) {
            $roles = json_decode($data['roles'], true);
        }
        $user->setRoles($roles);
        $password = $this->generateUserPassword($user, $data, $type);
        if ($password) {
            $user->setPassword($password);
        }
        return $user;
    }

    public function generateUserPassword(User $user, array $data, $type) {
        if ((array_key_exists("change_password", $data) && $data["change_password"]) || $type === "insert") {
            if (!array_key_exists("confirm_password", $data) || !array_key_exists("new_password", $data)) {
                throw new BadRequestHttpException("confirm_password or new_password is not in request.");
            }
            if ($data["confirm_password"] === "" || $data["confirm_password"] === null ||
                $data["new_password"] === "" || $data["new_password"] === null) {
                throw new BadRequestHttpException("Confirm or New Password fields have empty values.");
            }
            if ($data["confirm_password"] !== $data["new_password"]) {
                throw new BadRequestHttpException("Confirm and New Password fields don't match.");
            }
            return $this->passwordEncoder->encodePassword($user, $data['new_password']);
        }
        return false;
    }

    public function createUser(array $data) {
        $user = $this->setUserObject(new User(), $data, "insert");
        if ($this->httpRequestService->validateData($user)) {
            return $this->userRepository->createUser($user);
        }
        return false;
    }

    public function updateUser(array $data) {
        if (!array_key_exists("id", $data)){
            throw new BadRequestHttpException("User id doesnt exist in request.");
        }
        $getUser = $this->userRepository->findOneBy(['id' => $data["id"]]);
        $user = $this->setUserObject($getUser, $data, "update");
        if($this->httpRequestService->validateData($user)) {
            return $this->userRepository->updateUser($user);
        }
        return false;
    }

    public function deleteUser(int $userId) {
        $user = $this->userRepository->findOneBy(["id" => $userId]);
        if ($user === null) {
            throw new BadRequestHttpException(sprintf("User id: %s not found in database.", $userId));
        }
        return $this->userRepository->deleteUser($user);
    }

    public function deleteApiToken(int $id) {
        $apiToken = $this->apiTokenRepository->findOneBy(["id" => $id]);
        if ($apiToken === null) {
            throw new BadRequestHttpException("ApiToken does not exist in database...");
        }
        return $this->apiTokenRepository->deleteApiToken($apiToken);
    }
}