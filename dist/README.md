### Base
1. ```composer require sensio/framework-extra-bundle sentry/sentry-symfony```

2. ```composer require --dev friendsofphp/php-cs-fixer phpstan/phpstan-symfony phpstan/phpstan-phpunit symfony/test-pack symfony/profiler-pack```

3. Add scripts to composer.json
    ```json
    {
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
    }
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

### FosRest

1. ```composer require friendsofsymfony/rest-bundle```

2. Copy/merge files from vendor/app-verk/dev-tools-bundle/dist/fos-rest with project root directory

3. Enable FosRest integration in DevTools bundle
    ```yaml
    dev_tools:
        api:
            fos_rest: true
    ```

### Notification (email, sms, etc.)

1. ```composer require symfony/notifier symfony/mailer symfony/twig-pack twig/cssinliner-extra twig/inky-extra```

2. Copy/merge files from vendor/app-verk/dev-tools-bundle/dist/notification with project root directory

### Documentation

1. ```composer require nelmio/api-doc-bundle symfony/asset```

2. Copy/merge files from vendor/app-verk/dev-tools-bundle/dist/documentation with project root directory

### Tests

1. ```composer require coduo/php-matcher hautelook/alice-bundle```

2. Copy/merge files from vendor/app-verk/dev-tools-bundle/dist/tests with project root directory
