UPGRADE FROM 5.x to 6.0
=======================

DashboardBundle
-----------

 * [BC] Rewrite of the whole DashboardBundle has been completed. The old google-api-custom repository will not be used anymore and 
 we switched to the official google-api repo (https://github.com/google/google-api-php-client). Most of the functionality is still the same but some classes
 were given a new name. The access token for google will still be saved, together with an refresh token. When the access token expires, the refresh token will be used
 to gain a new access token.
