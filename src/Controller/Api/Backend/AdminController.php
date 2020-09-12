<?php
namespace App\Controller\Api\Backend;

use App\Controller\Api\BaseController;
use App\Entity\ApiToken;
use App\Entity\User;
use App\Service\HttpRequestService;
use App\Service\SecurityService;
use App\Service\SerializerService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contains api endpoint functions for admin related tasks
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends BaseController
{
    private $userService;
    private $serializerService;
    private $httpRequestService;

    /**
     * AdminController constructor.
     * Initialises services used in this controller
     *
     * @param UserService $userService
     * @param SerializerService $serializerService
     * @param HttpRequestService $httpRequestService
     */
    public function __construct(UserService $userService, SerializerService $serializerService,
                                HttpRequestService $httpRequestService)
    {
        $this->userService = $userService;
        $this->serializerService = $serializerService;
        $this->httpRequestService = $httpRequestService;
    }

    /**
     * Gets a list of users based on the request query data
     * Returns successful json response and array of user objects
     *
     * @Route("/api/admin/users", name="api_get_users", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUsersList(Request $request)
    {
        $getUsers = $this->userService->findByParams(
            $request->get('sort', "id"),
            $request->get('order', "asc"),
            (int) $request->get('count', null)
        );
        return $this->jsonResponseSuccess("success", $this->serializerService->entityArrayToArray($getUsers));
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/api/admin/user/{id}", name="api_get_single_user", methods={"GET"})
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSingleUser(User $user)
    {
        return $this->jsonResponseSuccess("success", $this->serializerService->entityToArray($user));
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/api/admin/token/user", name="api_get_user_by_token", methods={"POST"})
     * @param Request $request
     * @param SecurityService $securityService
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSingleUserByApiToken(Request $request, SecurityService $securityService)
    {
        $apiTokenValue = $securityService->getTokenFromHeader($request->headers->get('Authorization'));
        $apiToken = $this->userService->getTokenByValue($apiTokenValue);
        if ($apiToken === null) {
            return $this->jsonResponseFail("Api Token not found.", []);
        }
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityToArray($apiToken->getUser()));
    }

    /**
     * Gets a single api token based on the api token id in the request url
     *
     * @Route("/api/admin/user/api-token/{id}", name="api_get_single_api_token", methods={"GET"})
     * @param ApiToken $apiToken
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getApiToken(ApiToken $apiToken)
    {
        return $this->jsonResponseSuccess("success", $this->serializerService->entityToArray($apiToken));
    }

    /**
     * Gets a list of usee api tokens based on the user id in the request url
     *
     * @Route("/api/admin/user/{id}/api-tokens", name="api_get_user_api_tokens", methods={"GET"})
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUserApiTokens(User $user, Request $request)
    {
        $getApiTokens = $this->userService->findApiTokensByParams(
            $user,
            $request->get('sort', "id"),
            $request->get('order', "asc"),
            (int) $request->get('count', null)
        );
        return $this->jsonResponseSuccess("success", $this->serializerService->entityArrayToArray($getApiTokens));
    }

    /**
     * Generates a new api token for a single user
     * User is based on the id in the request url
     *
     * @Route("/api/admin/user/{id}/api-token/generate", name="generate_user_api_token", methods={"GET"})
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function generateNewApiToken(User $user)
    {
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityToArray($this->userService->setApiToken($user)));
    }

    /**
     * Updates a single token expiry date based on the request post data
     *
     * @Route("/api/admin/user/api-token/update", name="user_api_token_expiry", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateApiTokenExpiry(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityToArray($this->userService->updateApiTokenExpiry($requestData)));
    }

    /**
     * Delete a single api token based the request post data.
     *
     * @Route("/api/admin/user/api-token/delete", name="user_api_token_delete", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteApiToken(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $delete = $this->userService->deleteApiToken($requestData['item_id']);
        if (!$delete) {
            return $this->jsonResponseFail("Error deleting api token", $this->serializerService->entityToArray($delete, ['main']));
        }
        return $this->jsonResponseSuccess("Api Token deleted.", $this->serializerService->entityToArray($delete, ['main']));
    }

    /**
     * Creates a user based on the request post data
     *
     * @param Request $request
     * @Route("/api/admin/user/create", name="api_create_user", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createUser(Request $request) {
        $create = $this->userService->createUser(
            $this->httpRequestService->getRequestData($request, true));

        if(!$create) {
            return $this->jsonResponseFail("Error inserting user");
        }
        return $this->jsonResponseSuccess("User inserted",
            $this->serializerService->entityToArray($create, ['main']));
    }

    /**
     * Updates a user based on the post request data
     *
     * @param Request $request
     * @Route("/api/admin/user/update", name="api_update_user", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateUser(Request $request)
    {
        $update = $this->userService->updateUser(
            $this->httpRequestService->getRequestData($request, true));
        if(!$update) {
            return $this->jsonResponseFail("Error updating user");
        }
        return $this->jsonResponseSuccess("User updated",
            $this->serializerService->entityToArray($update, ['main']));
    }

    /**
     * Deletes a user based on the post request data
     *
     * @param Request $request
     * @Route("/api/admin/user/delete", name="api_delete_user", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteUser(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $delete = $this->userService->deleteUser($requestData['item_id']);
        if (!$delete) {
            return $this->jsonResponseFail("Error deleting user", $this->serializerService->entityToArray($delete, ['main']));
        }
        return $this->jsonResponseSuccess("User deleted.", $this->serializerService->entityToArray($delete, ['main']));
    }
}