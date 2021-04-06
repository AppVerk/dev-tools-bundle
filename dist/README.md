### Base
1. ```composer require sensio/framework-extra-bundle sentry/sentry-symfony```

2. ```composer require --dev friendsofphp/php-cs-fixer phpstan/phpstan-symfony phpstan/phpstan-phpunit symfony/test-pack symfony/profiler-pack```

3. Add scripts to composer.json
```
    "setup": [
        "bin/console doctrine:database:create --if-not-exists",
        "bin/console doctrine:schema:create",
        "bin/console messenger:setup-transports"
    ],
    "test": "bin/phpunit -d memory_limit=2G",
    "analyse": [
        "php-cs-fixer fix --diff --diff-format=udiff --verbose --show-progress=estimating --dry-run",
        "phpstan analyse src tests -l 7"
    ],
    "cs-fix": "php-cs-fixer fix --diff --diff-format=udiff --verbose --show-progress=estimating"
```

4. Copy/merge files from vendor/app-verk/dev-tools-bundle/dist/base with project root directory

### Jwt auth

1. ```composer require security jwt-auth cors gesdinet/jwt-refresh-token-bundle```

2. Copy/merge files from vendor/app-verk/dev-tools-bundle/dist/jwt-auth with project root directory

### Enums

1. ```composer require myclabs/php-enum acelaya/doctrine-enum-type```

2. ```composer require --dev timeweb/phpstan-enum```

3. Copy/merge files from vendor/app-verk/dev-tools-bundle/dist/enums with project root directory

### OAuth2

1. ```composer require security cors trikoder/oauth2-bundle nyholm/psr7```

2. Copy/merge files from vendor/app-verk/dev-tools-bundle/dist/oauth2 with project root directory