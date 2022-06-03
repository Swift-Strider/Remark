# Command Guards
A `Guard` prevents a HandlerMethod from being ran when a requirement isn't satisfied. I. e. the command sender not having permission.

## permission
Requires that the `CommandSender` has all of the permissions passed in.
```php
string $permission,
string ...$otherPermissions,
```
* permission - One required permission
* otherPermissions - Other required permissions
