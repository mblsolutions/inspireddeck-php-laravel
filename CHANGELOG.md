## V3.1.0

+ AB#135116 2FA login steps and logout
+ login() takes optional otp or recovery_code; pullRecoveryCodes() returns new recovery codes once
+ login() destroys the old session record
+ logout() also ends the API token
+ A 401 from the API sends the user to the login page
+ Require mblsolutions/inspireddeck-php ^2.1.0

## V3.0.1

+ Bump mblsolutions/inspireddeck-php version

## V3.0.1

+ AB#113985 Update for php8.4 compliance

## V3.0.0

+ AB#113985 Update composer.json dependencies for Laravel v11, v12

## V2.2.0

+ Update composer.json dependencies for Laravel v10

## V2.1.0

+ Update outdated json error response

## V2.0.0

+ Upgrade to Laravel 9 and update related dependencies