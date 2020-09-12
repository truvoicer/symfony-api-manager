# A Symfony API Management System REST API
A simple api management system made using the symfony framework.

## Core Functionalities
- External api provider management
- Api requests management
- External api response data management
- Merging external api response data by using the api response keys manager
- Making oath requests to external api providers

## Basic Usage
1. Providers    
A provider is an external api provider, like facebook-api.com
The system allows you to store providers through the (/api/provider) endpoints.

2. Properties    
A property is relating to a common property which differ based on the external api provider.    
The system allows for the addition of properties for each provider through the (/api/provider/property) endpoint.    
Examples of a property are:
    - api_authentication_type
    - api_request_method    

3. Services    
A service allows for the grouping of api requests.

4. Service Requests    


## Required/Reserved Data
- Properties
    - api_authentication_type
    - api_request_authentication_type
    - api_request_method
    - oauth_access_token_grant_type
    - oauth_grant_type_field_name
    - oauth_grant_type_field_value
    - oauth_scope_field_name
    - oauth_scope_field_value
    - oauth_token_url
