<?php

namespace App\Controller\Api;

use App\Repository\ApiTokenRepository;
use App\Service\HttpRequestService;
use App\Service\SerializerService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Contains api endpoint functions for user account tasks via email password login
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_USER")
 */
class AuthController extends BaseController
{
    private $userService;
    private $serializerService;
    private $httpRequestService;

    /**
     * AuthController constructor.
     * Initialise services for this class
     *
     * @param UserService $userService
     * @param SerializerService $serializerService
     * @param HttpRequestService $httpRequestService
     */
    public function __construct(UserService $userService, SerializerService $serializerService, HttpRequestService $httpRequestService)
    {
        $this->userService = $userService;
        $this->serializerService = $serializerService;
        $this->httpRequestService = $httpRequestService;
    }

    /**
     * API user login
     * Returns user api token data
     *
     * @Route("/api/account/login", name="api_account_login")
     * @param Request $request
     * @return Response
     */
    public function accountLogin(Request $request): Response
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $user = $this->userService->getUser($requestData["email"]);
        $apiToken = $this->userService->getLatestToken($user);
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => 'Successfully logged in.',
            'session' => [
                "access_token" => $apiToken->getToken(),
                "expires_at" => $apiToken->getExpiresAt()->getTimestamp()
            ],
        ];

        return $this->jsonResponseSuccess("success", $data);
    }

    /**
     * Gets user data
     *
     * @Route("/api/account/details", name="api_get_account_details", methods={ "POST" })
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAccountDetails(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $user = $this->userService->getUser($requestData["email"]);
        return $this->jsonResponseSuccess("Success",
            $this->serializerService->entityToArray($user));
    }

    /**
     * Generates a new token for a user
     *
     * @Route("/api/account/new-token", name="new_token", methods={"POST", "HEAD"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function newToken(Request $request) {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $user = $this->userService->getUser($requestData["email"]);
        $setApiToken = $this->userService->setApiToken($user);
        if(!$setApiToken) {
            return $this->jsonResponseFail("Error generating api token");
        }
        return $this->jsonResponseSuccess("Api token", [
            "token: " => $setApiToken->getToken(),
            "expiresAt" => $setApiToken->getExpiresAt()->format("Y-m-d H:i:s"),
            "email" => $setApiToken->getuser()->getEmail()
        ]);
    }
}
