<?php


namespace App\Service\Api\Client\Oauth;


use App\Entity\OauthAccessTokens;
use App\Entity\Provider;
use App\Repository\OauthAccessTokensRepository;
use App\Service\Api\Client\ApiClientHandler;
use App\Service\Api\Client\Entity\ApiRequest;
use App\Service\ProviderService;
use App\Service\SerializerService;
use DateTime;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Oauth extends ApiClientHandler
{
    private $provider = null;
    private $providerService;
    private $serializerService;
    private $oathTokenRepository;

    public function __construct(OauthAccessTokensRepository $oathTokenRepository, SerializerService $serializerService,
                                ProviderService $providerService)
    {
        parent::__construct();
        $this->oathTokenRepository = $oathTokenRepository;
        $this->serializerService = $serializerService;
        $this->providerService = $providerService;
    }

    public function getAccessToken() {
        if ($this->provider === null) {
            return false;
        }
        $accessToken = $this->checkAccessToken();
        if ($accessToken !== null) {
            return $accessToken;
        }
        $sendRequest = $this->sendAccessTokenRequest();

        return $this->setAccessToken(
            $sendRequest["access_token"],
            $this->getExpiryDatetime($sendRequest["expires_in"])
        );
    }

    private function sendAccessTokenRequest() {
        $apiRequest = new ApiRequest();

        $grantTypeName = $this->getPropertyValue(self::OAUTH_GRANT_TYPE_FIELD_NAME);
        $grantTypeValue = $this->getPropertyValue(self::OAUTH_GRANT_TYPE_FIELD_VALUE);
        $scopeName = $this->getPropertyValue(self::OAUTH_SCOPE_FIELD_NAME);
        $scopeValue = $this->getPropertyValue(self::OAUTH_SCOPE_FIELD_VALUE);

        $apiRequest->setMethod("POST");
        $apiRequest->setUrl($this->getPropertyValue(self::OAUTH_TOKEN_URL_KEY));
        $apiRequest->setAuthentication([
            "auth_basic" => [
                $this->provider->getProviderAccessKey(),
                $this->provider->getProviderSecretKey()
            ]
        ]);
        $apiRequest->setHeaders(['Content-Type' => 'application/x-www-form-urlencoded']);
        $apiRequest->setBody([
            $grantTypeName => $grantTypeValue,
            $scopeName => $scopeValue
        ]);

        $response = $this->sendRequest($apiRequest);
        if ($response->getStatusCode() !== 200) {
            throw new BadRequestHttpException("Error retrieving access token.");
        }
        return $response->toArray(true);
    }

    private function getPropertyValue(string $propertyName) {
        return $this->providerService->getProviderPropertyValue($this->provider, $propertyName);
    }

    private function checkAccessToken() {
        return $this->oathTokenRepository->getLatestAccessToken($this->provider);
    }

    private function setAccessToken(string $access_token, DateTime $expiry) {
        return $this->oathTokenRepository->saveOathToken(
            $this->setOathTokenObject(new OauthAccessTokens(), $access_token, $expiry), $this->provider);
    }

    private function setOathTokenObject(OauthAccessTokens $oathToken, string $access_token, \DateTime $expiry) {
        $oathToken->setAccessToken($access_token);
        $oathToken->setExpiry($expiry);
        return $oathToken;
    }

    private function getExpiryDatetime(int $expirySeconds) {
        $expiryDate = new DateTime();
        return $expiryDate->setTimestamp(time() + $expirySeconds);
    }

    /**
     * @param mixed $provider
     */
    public function setProvider(Provider $provider): void
    {
        $this->provider = $provider;
    }
}