<?php

namespace App\Service\Api;

class ApiBase
{
    const API_AUTH_TYPE = "api_authentication_type";
    const API_REQUEST_AUTH_TYPE = "api_request_authentication_type";
    const API_REQUEST_METHOD = "api_request_method";
    const OAUTH_TOKEN_URL_KEY = "oauth_token_url";
    const OAUTH_GRANT_TYPE_FIELD_NAME = "oauth_grant_type_field_name";
    const OAUTH_GRANT_TYPE_FIELD_VALUE = "oauth_grant_type_field_value";
    const OAUTH_SCOPE_FIELD_NAME = "oauth_scope_field_name";
    const OAUTH_SCOPE_FIELD_VALUE = "oauth_scope_field_value";
    const IMAGE_ARRAY = "image_array";
    const IMAGE_ARRAY_URL = "image_array_url";
    const IMAGE_ARRAY_HEIGHT = "image_array_height";
    const IMAGE_ARRAY_WIDTH = "image_array_width";
    const PARAM_FILTER_KEYS = [
        "API_BASE_URL" => "[API_BASE_URL]",
        "PROVIDER_USER_ID" => "[PROVIDER_USER_ID]",
        "SECRET_KEY" => "[SECRET_KEY]",
        "ACCESS_KEY" => "[ACCESS_KEY]",
        "CATEGORY" => "[CATEGORY]",
        "TIMESTAMP" => "[TIMESTAMP]",
        "QUERY" => "[QUERY]",
        "LIMIT" => "[LIMIT]",
    ];
}