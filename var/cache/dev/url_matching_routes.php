<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/_profiler' => [[['_route' => '_profiler_home', '_controller' => 'web_profiler.controller.profiler::homeAction'], null, null, null, true, false, null]],
        '/_profiler/search' => [[['_route' => '_profiler_search', '_controller' => 'web_profiler.controller.profiler::searchAction'], null, null, null, false, false, null]],
        '/_profiler/search_bar' => [[['_route' => '_profiler_search_bar', '_controller' => 'web_profiler.controller.profiler::searchBarAction'], null, null, null, false, false, null]],
        '/_profiler/phpinfo' => [[['_route' => '_profiler_phpinfo', '_controller' => 'web_profiler.controller.profiler::phpinfoAction'], null, null, null, false, false, null]],
        '/_profiler/open' => [[['_route' => '_profiler_open_file', '_controller' => 'web_profiler.controller.profiler::openAction'], null, null, null, false, false, null]],
        '/api/account/login' => [[['_route' => 'api_account_login', '_controller' => 'App\\Controller\\Api\\AuthController::accountLogin'], null, null, null, false, false, null]],
        '/api/account/details' => [[['_route' => 'api_get_account_details', '_controller' => 'App\\Controller\\Api\\AuthController::getAccountDetails'], null, ['POST' => 0], null, false, false, null]],
        '/api/account/new-token' => [[['_route' => 'new_token', '_controller' => 'App\\Controller\\Api\\AuthController::newToken'], null, ['POST' => 0, 'HEAD' => 1], null, false, false, null]],
        '/api/admin/users' => [[['_route' => 'api_get_users', '_controller' => 'App\\Controller\\Api\\Backend\\AdminController::getUsersList'], null, ['GET' => 0], null, false, false, null]],
        '/api/admin/token/user' => [[['_route' => 'api_get_user_by_token', '_controller' => 'App\\Controller\\Api\\Backend\\AdminController::getSingleUserByApiToken'], null, ['POST' => 0], null, false, false, null]],
        '/api/categories' => [[['_route' => 'api_get_categories', '_controller' => 'App\\Controller\\Api\\Backend\\CategoryController::getCategories'], null, ['GET' => 0], null, false, false, null]],
        '/api/properties' => [[['_route' => 'api_get_properties', '_controller' => 'App\\Controller\\Api\\Backend\\PropertyController::getPropertyList'], null, ['GET' => 0], null, false, false, null]],
        '/api/providers' => [[['_route' => 'api_get_providers', '_controller' => 'App\\Controller\\Api\\Backend\\ProviderController::getProviderList'], null, ['GET' => 0], null, false, false, null]],
        '/api/provider/property/create' => [[['_route' => 'api_create_provider_property', '_controller' => 'App\\Controller\\Api\\Backend\\ProviderController::createProviderProperty'], null, ['POST' => 0], null, false, false, null]],
        '/api/provider/property/update' => [[['_route' => 'api_update_provider_property_relation', '_controller' => 'App\\Controller\\Api\\Backend\\ProviderController::updateProviderProperty'], null, ['POST' => 0], null, false, false, null]],
        '/api/provider/property/delete' => [[['_route' => 'api_delete_provider_property', '_controller' => 'App\\Controller\\Api\\Backend\\ProviderController::deleteProviderProperty'], null, ['POST' => 0], null, false, false, null]],
        '/api/services' => [[['_route' => 'api_get_services', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceController::getServices'], null, ['GET' => 0], null, false, false, null]],
        '/api/service/request/config/list' => [[['_route' => 'api_get_service_request_config_list', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestConfigController::getRequestConfigList'], null, ['GET' => 0], null, false, false, null]],
        '/api/service/request/list' => [[['_route' => 'api_get_service_request_list', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestController::getServiceRequestList'], null, ['GET' => 0], null, false, false, null]],
        '/api/provider/service/request' => [[['_route' => 'api_get_provider_service_request', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestController::getProviderServiceRequest'], null, ['GET' => 0], null, false, false, null]],
        '/api/service/api/request/run' => [[['_route' => 'run_service_api_request', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestController::runApiRequest'], null, ['GET' => 0], null, false, false, null]],
        '/api/service/request/parameter/list' => [[['_route' => 'api_get_service_request_parameter_list', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestParameterController::getServiceRequestParameterList'], null, ['GET' => 0], null, false, false, null]],
        '/api/service/request/response/key/create' => [[['_route' => 'api_create_request_response_key', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestResponseKeyController::createRequestResponseKey'], null, ['POST' => 0], null, false, false, null]],
        '/api/service/request/response/key/update' => [[['_route' => 'api_update_request_response_key', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestResponseKeyController::updateRequestResponseKey'], null, ['POST' => 0], null, false, false, null]],
        '/api/service/request/response/key/delete' => [[['_route' => 'api_delete_request_response_key', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestResponseKeyController::deleteRequestResponseKey'], null, ['POST' => 0], null, false, false, null]],
        '/api/service/response/key/list' => [[['_route' => 'api_get_service_response_key_list', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceResponseKeyController::getServiceResponseKeyList'], null, ['GET' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_(?'
                    .'|error/(\\d+)(?:\\.([^/]++))?(*:38)'
                    .'|wdt/([^/]++)(*:57)'
                    .'|profiler/([^/]++)(?'
                        .'|/(?'
                            .'|search/results(*:102)'
                            .'|router(*:116)'
                            .'|exception(?'
                                .'|(*:136)'
                                .'|\\.css(*:149)'
                            .')'
                        .')'
                        .'|(*:159)'
                    .')'
                .')'
                .'|/api/(?'
                    .'|admin/(?'
                        .'|user/(?'
                            .'|([^/]++)(*:202)'
                            .'|api\\-token/([^/]++)(*:229)'
                            .'|([^/]++)/api\\-token(?'
                                .'|s(*:260)'
                                .'|/generate(*:277)'
                            .')'
                            .'|api\\-token/(?'
                                .'|update(*:306)'
                                .'|delete(*:320)'
                            .')'
                            .'|create(*:335)'
                            .'|update(*:349)'
                            .'|delete(*:363)'
                        .')'
                        .'|search/([^/]++)(*:387)'
                    .')'
                    .'|category/(?'
                        .'|([^/]++)(*:416)'
                        .'|create(*:430)'
                        .'|update(*:444)'
                        .'|delete(*:458)'
                        .'|([^/]++)/providers(*:484)'
                    .')'
                    .'|pro(?'
                        .'|perty/(?'
                            .'|([^/]++)(*:516)'
                            .'|update(*:530)'
                            .'|create(*:544)'
                            .'|delete(*:558)'
                        .')'
                        .'|vider/(?'
                            .'|([^/]++)(*:584)'
                            .'|property/relation/([^/]++)(*:618)'
                            .'|([^/]++)/propert(?'
                                .'|y/([^/]++)(*:655)'
                                .'|ies(*:666)'
                            .')'
                            .'|create(*:681)'
                            .'|update(*:695)'
                            .'|delete(*:709)'
                        .')'
                    .')'
                    .'|service/(?'
                        .'|([^/]++)(*:738)'
                        .'|create(*:752)'
                        .'|update(*:766)'
                        .'|delete(*:780)'
                        .'|re(?'
                            .'|quest/(?'
                                .'|config/(?'
                                    .'|([^/]++)(*:820)'
                                    .'|create(*:834)'
                                    .'|update(*:848)'
                                    .'|delete(*:862)'
                                .')'
                                .'|([^/]++)(*:879)'
                                .'|create(*:893)'
                                .'|update(*:907)'
                                .'|d(?'
                                    .'|uplicate(*:927)'
                                    .'|elete(*:940)'
                                .')'
                                .'|parameter/(?'
                                    .'|([^/]++)(*:970)'
                                    .'|create(*:984)'
                                    .'|update(*:998)'
                                    .'|delete(*:1012)'
                                .')'
                                .'|([^/]++)/response/key/(?'
                                    .'|list(*:1051)'
                                    .'|([^/]++)(*:1068)'
                                .')'
                            .')'
                            .'|sponse/key/(?'
                                .'|([^/]++)(*:1101)'
                                .'|create(*:1116)'
                                .'|update(*:1131)'
                                .'|delete(*:1146)'
                            .')'
                        .')'
                    .')'
                    .'|operation/([^/]++)(*:1176)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        38 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        57 => [[['_route' => '_wdt', '_controller' => 'web_profiler.controller.profiler::toolbarAction'], ['token'], null, null, false, true, null]],
        102 => [[['_route' => '_profiler_search_results', '_controller' => 'web_profiler.controller.profiler::searchResultsAction'], ['token'], null, null, false, false, null]],
        116 => [[['_route' => '_profiler_router', '_controller' => 'web_profiler.controller.router::panelAction'], ['token'], null, null, false, false, null]],
        136 => [[['_route' => '_profiler_exception', '_controller' => 'web_profiler.controller.exception_panel::body'], ['token'], null, null, false, false, null]],
        149 => [[['_route' => '_profiler_exception_css', '_controller' => 'web_profiler.controller.exception_panel::stylesheet'], ['token'], null, null, false, false, null]],
        159 => [[['_route' => '_profiler', '_controller' => 'web_profiler.controller.profiler::panelAction'], ['token'], null, null, false, true, null]],
        202 => [[['_route' => 'api_get_single_user', '_controller' => 'App\\Controller\\Api\\Backend\\AdminController::getSingleUser'], ['id'], ['GET' => 0], null, false, true, null]],
        229 => [[['_route' => 'api_get_single_api_token', '_controller' => 'App\\Controller\\Api\\Backend\\AdminController::getApiToken'], ['id'], ['GET' => 0], null, false, true, null]],
        260 => [[['_route' => 'api_get_user_api_tokens', '_controller' => 'App\\Controller\\Api\\Backend\\AdminController::getUserApiTokens'], ['id'], ['GET' => 0], null, false, false, null]],
        277 => [[['_route' => 'generate_user_api_token', '_controller' => 'App\\Controller\\Api\\Backend\\AdminController::generateNewApiToken'], ['id'], ['GET' => 0], null, false, false, null]],
        306 => [[['_route' => 'user_api_token_expiry', '_controller' => 'App\\Controller\\Api\\Backend\\AdminController::updateApiTokenExpiry'], [], ['POST' => 0], null, false, false, null]],
        320 => [[['_route' => 'user_api_token_delete', '_controller' => 'App\\Controller\\Api\\Backend\\AdminController::deleteApiToken'], [], ['POST' => 0], null, false, false, null]],
        335 => [[['_route' => 'api_create_user', '_controller' => 'App\\Controller\\Api\\Backend\\AdminController::createUser'], [], ['POST' => 0], null, false, false, null]],
        349 => [[['_route' => 'api_update_user', '_controller' => 'App\\Controller\\Api\\Backend\\AdminController::updateUser'], [], ['POST' => 0], null, false, false, null]],
        363 => [[['_route' => 'api_delete_user', '_controller' => 'App\\Controller\\Api\\Backend\\AdminController::deleteUser'], [], ['POST' => 0], null, false, false, null]],
        387 => [[['_route' => 'api_admin_search', '_controller' => 'App\\Controller\\Api\\Backend\\SearchController::search'], ['query'], ['GET' => 0], null, false, true, null]],
        416 => [[['_route' => 'api_get_single_category', '_controller' => 'App\\Controller\\Api\\Backend\\CategoryController::getSingleCategory'], ['id'], ['GET' => 0], null, false, true, null]],
        430 => [[['_route' => 'api_create_category', '_controller' => 'App\\Controller\\Api\\Backend\\CategoryController::createCategory'], [], ['POST' => 0], null, false, false, null]],
        444 => [[['_route' => 'api_update_category', '_controller' => 'App\\Controller\\Api\\Backend\\CategoryController::updateCategory'], [], ['POST' => 0], null, false, false, null]],
        458 => [[['_route' => 'api_delete_category', '_controller' => 'App\\Controller\\Api\\Backend\\CategoryController::deleteCategory'], [], ['POST' => 0], null, false, false, null]],
        484 => [[['_route' => 'api_get_category_providerlist', '_controller' => 'App\\Controller\\Api\\Frontend\\ListController::getCategoryProviderList'], ['category_name'], ['GET' => 0], null, false, false, null]],
        516 => [[['_route' => 'api_get_property', '_controller' => 'App\\Controller\\Api\\Backend\\PropertyController::getProperty'], ['id'], ['GET' => 0], null, false, true, null]],
        530 => [[['_route' => 'api_update_property', '_controller' => 'App\\Controller\\Api\\Backend\\PropertyController::updateProperty'], [], ['POST' => 0], null, false, false, null]],
        544 => [[['_route' => 'api_create_property', '_controller' => 'App\\Controller\\Api\\Backend\\PropertyController::createProperty'], [], ['POST' => 0], null, false, false, null]],
        558 => [[['_route' => 'api_delete_property', '_controller' => 'App\\Controller\\Api\\Backend\\PropertyController::deleteProperty'], [], ['POST' => 0], null, false, false, null]],
        584 => [[['_route' => 'api_get_provider', '_controller' => 'App\\Controller\\Api\\Backend\\ProviderController::getProvider'], ['id'], ['GET' => 0], null, false, true, null]],
        618 => [[['_route' => 'api_get_provider_property_relation', '_controller' => 'App\\Controller\\Api\\Backend\\ProviderController::getProviderPropertyRelation'], ['id'], ['GET' => 0], null, false, true, null]],
        655 => [[['_route' => 'api_get_provider_property', '_controller' => 'App\\Controller\\Api\\Backend\\ProviderController::getProviderProperty'], ['id', 'property_id'], ['GET' => 0], null, false, true, null]],
        666 => [[['_route' => 'api_get_provider_property_list', '_controller' => 'App\\Controller\\Api\\Backend\\ProviderController::getProviderPropertyList'], ['id'], ['GET' => 0], null, false, false, null]],
        681 => [[['_route' => 'api_create_provider', '_controller' => 'App\\Controller\\Api\\Backend\\ProviderController::createProvider'], [], ['POST' => 0], null, false, false, null]],
        695 => [[['_route' => 'api_update_provider', '_controller' => 'App\\Controller\\Api\\Backend\\ProviderController::updateProvider'], [], ['POST' => 0], null, false, false, null]],
        709 => [[['_route' => 'api_delete_provider', '_controller' => 'App\\Controller\\Api\\Backend\\ProviderController::deleteProvider'], [], ['POST' => 0], null, false, false, null]],
        738 => [[['_route' => 'api_get_service', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceController::getService'], ['id'], ['GET' => 0], null, false, true, null]],
        752 => [[['_route' => 'api_create_service', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceController::createService'], [], ['POST' => 0], null, false, false, null]],
        766 => [[['_route' => 'api_update_service', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceController::updateService'], [], ['POST' => 0], null, false, false, null]],
        780 => [[['_route' => 'api_delete_service', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceController::deleteService'], [], ['POST' => 0], null, false, false, null]],
        820 => [[['_route' => 'api_get_service_request_config', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestConfigController::getServiceRequestConfig'], ['id'], ['GET' => 0], null, false, true, null]],
        834 => [[['_route' => 'api_create_service_request_config', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestConfigController::createRequestConfig'], [], ['POST' => 0], null, false, false, null]],
        848 => [[['_route' => 'api_update_service_request_config', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestConfigController::updateRequestConfig'], [], ['POST' => 0], null, false, false, null]],
        862 => [[['_route' => 'api_delete_service_request_config', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestConfigController::deleteRequestConfig'], [], ['POST' => 0], null, false, false, null]],
        879 => [[['_route' => 'api_get_service_request', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestController::getServiceRequest'], ['id'], ['GET' => 0], null, false, true, null]],
        893 => [[['_route' => 'api_create_service_request', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestController::createServiceRequest'], [], ['POST' => 0], null, false, false, null]],
        907 => [[['_route' => 'api_update_service_request', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestController::updateServiceRequest'], [], ['POST' => 0], null, false, false, null]],
        927 => [[['_route' => 'api_duplicate_service_request', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestController::duplicateServiceRequest'], [], ['POST' => 0], null, false, false, null]],
        940 => [[['_route' => 'api_delete_service_request', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestController::deleteServiceRequest'], [], ['POST' => 0], null, false, false, null]],
        970 => [[['_route' => 'api_get_service_request_parameter', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestParameterController::getServiceRequestParameter'], ['id'], ['GET' => 0], null, false, true, null]],
        984 => [[['_route' => 'api_create_service_request_parameter', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestParameterController::createServiceRequestParameter'], [], ['POST' => 0], null, false, false, null]],
        998 => [[['_route' => 'api_update_service_request_parameter', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestParameterController::updateServiceRequestParameter'], [], ['POST' => 0], null, false, false, null]],
        1012 => [[['_route' => 'api_delete_service_request_parameter', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestParameterController::deleteServiceRequestParameter'], [], ['POST' => 0], null, false, false, null]],
        1051 => [[['_route' => 'api_get_request_response_key_list', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestResponseKeyController::getRequestResponseKeyList'], ['id'], ['GET' => 0], null, false, false, null]],
        1068 => [[['_route' => 'api_get_request_response_key', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceRequestResponseKeyController::getRequestResponseKey'], ['id', 'response_key_id'], ['GET' => 0], null, false, true, null]],
        1101 => [[['_route' => 'api_get_service_response_key', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceResponseKeyController::getServiceResponseKey'], ['id'], ['GET' => 0], null, false, true, null]],
        1116 => [[['_route' => 'api_create_service_response_key', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceResponseKeyController::createServiceResponseKey'], [], ['POST' => 0], null, false, false, null]],
        1131 => [[['_route' => 'api_update_service_response_key', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceResponseKeyController::updateServiceResponseKey'], [], ['POST' => 0], null, false, false, null]],
        1146 => [[['_route' => 'api_delete_service_response_key', '_controller' => 'App\\Controller\\Api\\Backend\\Services\\ServiceResponseKeyController::deleteServiceResponseKey'], [], ['POST' => 0], null, false, false, null]],
        1176 => [
            [['_route' => 'api_request_operation', '_controller' => 'App\\Controller\\Api\\Frontend\\OperationsController::searchOperation'], ['service_request_name'], ['GET' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
