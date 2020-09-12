<?php
namespace App\Security;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\ApiTokenRepository;
use App\Service\HttpRequestService;
use App\Service\SecurityService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $securityService;
    private $passwordEncoder;
    private $apiTokenRepository;
    private $httpRequestService;
    private $userService;

    public function __construct(EntityManagerInterface $em, SecurityService $securityService,
                                UserPasswordEncoderInterface $passwordEncoder,
                                ApiTokenRepository $apiTokenRepository, HttpRequestService $httpRequestService,
                                UserService $userService)
    {
        $this->em = $em;
        $this->securityService = $securityService;
        $this->passwordEncoder = $passwordEncoder;
        $this->apiTokenRepository = $apiTokenRepository;
        $this->httpRequestService = $httpRequestService;
        $this->userService = $userService;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {
        return $this->securityService->isSupported($request);
    }

    public function getCredentials(Request $request)
    {
        return $this->securityService->getCredentials($request);
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            return null;
        }

        $user = $this->userService->getUser($credentials["email"]);
        if ($user === null) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException($credentials['email'] . ': Email could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication is needed, but it's not sent
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }

}