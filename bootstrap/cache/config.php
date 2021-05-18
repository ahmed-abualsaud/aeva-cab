<?php return array (
  'app' => 
  array (
    'name' => 'Qruz',
    'env' => 'local',
    'debug' => true,
    'url' => 'http://localhost:3000',
    'asset_url' => NULL,
    'timezone' => 'Africa/Cairo',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => 'base64:/G6LecQkAcHIzlVZqXUWcSUObBCbj3NVOt2P9CFNP+o=',
    'cipher' => 'AES-256-CBC',
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Cookie\\CookieServiceProvider',
      6 => 'Illuminate\\Database\\DatabaseServiceProvider',
      7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      10 => 'Illuminate\\Hashing\\HashServiceProvider',
      11 => 'Illuminate\\Mail\\MailServiceProvider',
      12 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      13 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      14 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      15 => 'Illuminate\\Queue\\QueueServiceProvider',
      16 => 'Illuminate\\Redis\\RedisServiceProvider',
      17 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      18 => 'Illuminate\\Session\\SessionServiceProvider',
      19 => 'Illuminate\\Translation\\TranslationServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'App\\Providers\\AppServiceProvider',
      23 => 'App\\Providers\\AuthServiceProvider',
      24 => 'App\\Providers\\BroadcastServiceProvider',
      25 => 'App\\Providers\\EventServiceProvider',
      26 => 'App\\Providers\\HorizonServiceProvider',
      27 => 'App\\Providers\\RouteServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Arr' => 'Illuminate\\Support\\Arr',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Redis' => 'Illuminate\\Support\\Facades\\Redis',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'Str' => 'Illuminate\\Support\\Str',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'admin',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'admin' => 
      array (
        'driver' => 'jwt',
        'provider' => 'cached-admins',
      ),
      'partner' => 
      array (
        'driver' => 'jwt',
        'provider' => 'cached-partners',
      ),
      'user' => 
      array (
        'driver' => 'jwt',
        'provider' => 'cached-users',
      ),
      'driver' => 
      array (
        'driver' => 'jwt',
        'provider' => 'cached-drivers',
      ),
    ),
    'providers' => 
    array (
      'admins' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Admin',
      ),
      'partners' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Partner',
      ),
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\User',
      ),
      'drivers' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Driver',
      ),
      'cached-admins' => 
      array (
        'driver' => 'cached-admin',
        'model' => 'App\\Admin',
      ),
      'cached-partners' => 
      array (
        'driver' => 'cached-partner',
        'model' => 'App\\Partner',
      ),
      'cached-users' => 
      array (
        'driver' => 'cached-user',
        'model' => 'App\\User',
      ),
      'cached-drivers' => 
      array (
        'driver' => 'cached-driver',
        'model' => 'App\\Driver',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
        'throttle' => 60,
      ),
      'drivers' => 
      array (
        'provider' => 'drivers',
        'table' => 'password_resets',
        'expire' => 60,
        'throttle' => 60,
      ),
      'partners' => 
      array (
        'provider' => 'partners',
        'table' => 'password_resets',
        'expire' => 60,
        'throttle' => 60,
      ),
      'admins' => 
      array (
        'provider' => 'admins',
        'table' => 'password_resets',
        'expire' => 60,
        'throttle' => 60,
      ),
    ),
    'password_timeout' => 10800,
  ),
  'broadcasting' => 
  array (
    'default' => 'pusher',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => '48477c9c2419bd65e74f',
        'secret' => 'd55c31550b394ca0040a',
        'app_id' => '971168',
        'options' => 
        array (
          'cluster' => 'eu',
          'useTLS' => true,
          'host' => '127.0.0.1',
          'port' => 6001,
          'scheme' => 'http',
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'redis',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => '/Users/ahmadghallab/Documents/Projects/PHP/code/qruz/storage/framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'cache',
      ),
      'dynamodb' => 
      array (
        'driver' => 'dynamodb',
        'key' => 'AKIA45TGTSRPFETTZ2B6',
        'secret' => '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',
        'region' => 'us-east-2',
        'table' => 'cache',
        'endpoint' => NULL,
      ),
    ),
    'prefix' => 'qruz_cache',
  ),
  'cors' => 
  array (
    'supportsCredentials' => false,
    'allowedOrigins' => 
    array (
      0 => '*',
    ),
    'allowedOriginsPatterns' => 
    array (
    ),
    'allowedHeaders' => 
    array (
      0 => '*',
    ),
    'allowedMethods' => 
    array (
      0 => '*',
    ),
    'exposedHeaders' => 
    array (
    ),
    'maxAge' => 0,
    'paths' => 
    array (
    ),
    'allowed_methods' => 
    array (
      0 => '*',
    ),
    'allowed_origins' => 
    array (
      0 => '*',
    ),
    'allowed_origins_patterns' => 
    array (
    ),
    'allowed_headers' => 
    array (
      0 => '*',
    ),
    'exposed_headers' => 
    array (
    ),
    'max_age' => 0,
    'supports_credentials' => false,
  ),
  'custom' => 
  array (
    'app_url' => 'http://localhost:3000',
    'mail_to_address' => 'help.ahmadghallab@gmail.com',
    'google_map_key' => 'AIzaSyA0a4_5jHzBStit_c4_ZM7TPTCO-uNLfoM',
    'azure_storage_url' => 'https://qruz.blob.core.windows.net/uploads',
    'firebase_access_key' => 'AAAAWe3pgOY:APA91bGvj5Sg0KWBWh0dnh9zBU860LkMUtsOW72g9iRCejlfpNsrk5A0oXhvwgS6BUWgsND9DU4lK468kvbuTaexSdA0C9dCRh83ughg8Pa_m6Ka1JaopVpGxrsU_r_f65BMTjPpceGp',
    'otp_username' => '5NFU1SZf',
    'otp_password' => '3iZFppXQxD',
    'otp_sender_id' => 'Qruz',
    'valulus_app_id' => '3b0d7231-7586-49e3-944b-27f63b50f569',
    'valulus_password' => 'Ah3210779',
    'valulus_hash_secret' => 'b7b8909732336565353931302d666165',
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'url' => NULL,
        'database' => 'qruz',
        'prefix' => '',
        'foreign_key_constraints' => true,
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'qruz',
        'username' => 'root',
        'password' => 'secret',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'qruz',
        'username' => 'root',
        'password' => 'secret',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'schema' => 'public',
        'sslmode' => 'prefer',
      ),
      'sqlsrv' => 
      array (
        'driver' => 'sqlsrv',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'qruz',
        'username' => 'root',
        'password' => 'secret',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'predis',
      'options' => 
      array (
        'cluster' => 'redis',
        'prefix' => 'qruz_database_',
      ),
      'default' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => '0',
      ),
      'cache' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => '1',
      ),
      'horizon' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => '0',
        'options' => 
        array (
          'prefix' => 'horizon:',
        ),
      ),
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'cloud' => 's3',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => '/Users/ahmadghallab/Documents/Projects/PHP/code/qruz/storage/app',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => '/Users/ahmadghallab/Documents/Projects/PHP/code/qruz/storage/app/public',
        'url' => 'http://localhost:3000/storage',
        'visibility' => 'public',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => 'AKIA45TGTSRPFETTZ2B6',
        'secret' => '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',
        'region' => 'us-east-2',
        'bucket' => '',
        'url' => NULL,
      ),
      'azure' => 
      array (
        'driver' => 'azure',
        'name' => 'qruz',
        'key' => 'eUrtEplqMsFjlY0H/X43fo1YL9OONpx68QQ+NJZI9LeOx/x1hdOn6+5s5XjTAKrm4VD+VyajsL9PSwITTjC5dg==',
        'container' => 'uploads',
        'url' => 'https://qruz.blob.core.windows.net/uploads',
        'prefix' => NULL,
      ),
    ),
  ),
  'hashids' => 
  array (
    'default' => 'main',
    'connections' => 
    array (
      'main' => 
      array (
        'salt' => 'EWrDBcZ2ES6Lmc5zCO09azd4WbWBfQdM7EXstsii',
        'length' => '6',
      ),
      'alternative' => 
      array (
        'salt' => 'your-salt-string',
        'length' => 'your-length-integer',
      ),
    ),
  ),
  'hashing' => 
  array (
    'driver' => 'bcrypt',
    'bcrypt' => 
    array (
      'rounds' => 10,
    ),
    'argon' => 
    array (
      'memory' => 1024,
      'threads' => 2,
      'time' => 2,
    ),
  ),
  'horizon' => 
  array (
    'domain' => NULL,
    'path' => 'horizon',
    'use' => 'default',
    'prefix' => 'horizon:',
    'middleware' => 
    array (
      0 => 'web',
    ),
    'waits' => 
    array (
      'redis:default' => 60,
    ),
    'trim' => 
    array (
      'recent' => 60,
      'completed' => 60,
      'recent_failed' => 10080,
      'failed' => 10080,
      'monitored' => 10080,
    ),
    'fast_termination' => false,
    'memory_limit' => 64,
    'environments' => 
    array (
      'production' => 
      array (
        'supervisor-1' => 
        array (
          'connection' => 'redis',
          'queue' => 
          array (
            0 => 'high',
            1 => 'low',
          ),
          'balance' => 'simple',
          'processes' => 10,
          'tries' => 1,
        ),
      ),
      'local' => 
      array (
        'supervisor-1' => 
        array (
          'connection' => 'redis',
          'queue' => 
          array (
            0 => 'high',
            1 => 'low',
          ),
          'balance' => 'simple',
          'processes' => 3,
          'tries' => 1,
        ),
      ),
    ),
  ),
  'jwt' => 
  array (
    'secret' => 'snKz1GcCSZwWoXzV5OpbxtaJfz6Cq0KhU3gE5jjNnxAG7UwCkILrRF7xlZUiw6mP',
    'keys' => 
    array (
      'public' => NULL,
      'private' => NULL,
      'passphrase' => NULL,
    ),
    'ttl' => NULL,
    'refresh_ttl' => 20160,
    'algo' => 'HS256',
    'required_claims' => 
    array (
      0 => 'iss',
      1 => 'iat',
      2 => 'nbf',
      3 => 'sub',
      4 => 'jti',
    ),
    'persistent_claims' => 
    array (
    ),
    'lock_subject' => true,
    'leeway' => 0,
    'blacklist_enabled' => true,
    'blacklist_grace_period' => 0,
    'decrypt_cookies' => false,
    'providers' => 
    array (
      'jwt' => 'Tymon\\JWTAuth\\Providers\\JWT\\Lcobucci',
      'auth' => 'Tymon\\JWTAuth\\Providers\\Auth\\Illuminate',
      'storage' => 'Tymon\\JWTAuth\\Providers\\Storage\\Illuminate',
    ),
  ),
  'lighthouse' => 
  array (
    'route' => 
    array (
      'uri' => '/graphql',
      'name' => 'graphql',
      'middleware' => 
      array (
        0 => 'Nuwave\\Lighthouse\\Support\\Http\\Middleware\\AcceptJson',
        1 => 'Nuwave\\Lighthouse\\Support\\Http\\Middleware\\AttemptAuthentication',
      ),
    ),
    'guard' => NULL,
    'schema' => 
    array (
      'register' => '/Users/ahmadghallab/Documents/Projects/PHP/code/qruz/graphql/schema.graphql',
    ),
    'cache' => 
    array (
      'enable' => false,
      'key' => 'lighthouse-schema',
      'store' => NULL,
      'ttl' => NULL,
    ),
    'namespaces' => 
    array (
      'models' => 
      array (
        0 => 'App',
        1 => 'App\\Models',
      ),
      'queries' => 'App\\GraphQL\\Queries',
      'mutations' => 'App\\GraphQL\\Mutations',
      'subscriptions' => 'App\\GraphQL\\Subscriptions',
      'interfaces' => 'App\\GraphQL\\Interfaces',
      'unions' => 'App\\GraphQL\\Unions',
      'scalars' => 'App\\GraphQL\\Scalars',
      'directives' => 
      array (
        0 => 'App\\GraphQL\\Directives',
      ),
      'validators' => 
      array (
        0 => 'App\\GraphQL\\Validators',
      ),
    ),
    'security' => 
    array (
      'max_query_complexity' => 0,
      'max_query_depth' => 0,
      'disable_introspection' => 0,
    ),
    'pagination' => 
    array (
      'default_count' => NULL,
      'max_count' => NULL,
    ),
    'debug' => 3,
    'error_handlers' => 
    array (
      0 => 'Nuwave\\Lighthouse\\Execution\\ExtensionErrorHandler',
      1 => 'Nuwave\\Lighthouse\\Execution\\ReportingErrorHandler',
    ),
    'field_middleware' => 
    array (
      0 => 'Nuwave\\Lighthouse\\Schema\\Directives\\TrimDirective',
      1 => 'Nuwave\\Lighthouse\\Schema\\Directives\\SanitizeDirective',
      2 => 'Nuwave\\Lighthouse\\Validation\\ValidateDirective',
      3 => 'Nuwave\\Lighthouse\\Schema\\Directives\\TransformArgsDirective',
      4 => 'Nuwave\\Lighthouse\\Schema\\Directives\\SpreadDirective',
      5 => 'Nuwave\\Lighthouse\\Schema\\Directives\\RenameArgsDirective',
    ),
    'global_id_field' => 'id',
    'batched_queries' => true,
    'transactional_mutations' => true,
    'force_fill' => true,
    'batchload_relations' => true,
    'subscriptions' => 
    array (
      'queue_broadcasts' => true,
      'broadcasts_queue_name' => NULL,
      'storage' => 'redis',
      'storage_ttl' => NULL,
      'broadcaster' => 'pusher',
      'broadcasters' => 
      array (
        'log' => 
        array (
          'driver' => 'log',
        ),
        'pusher' => 
        array (
          'driver' => 'pusher',
          'routes' => 'Nuwave\\Lighthouse\\Subscriptions\\SubscriptionRouter@pusher',
          'connection' => 'pusher',
        ),
        'echo' => 
        array (
          'driver' => 'echo',
          'connection' => 'default',
          'routes' => 'Nuwave\\Lighthouse\\Subscriptions\\SubscriptionRouter@echoRoutes',
        ),
      ),
      'version' => 1,
      'exclude_empty' => false,
    ),
    'defer' => 
    array (
      'max_nested_fields' => 0,
      'max_execution_ms' => 0,
    ),
    'federation' => 
    array (
      'entities_resolver_namespace' => 'App\\GraphQL\\Entities',
    ),
  ),
  'logging' => 
  array (
    'default' => 'stack',
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'single',
        ),
        'ignore_exceptions' => false,
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => '/Users/ahmadghallab/Documents/Projects/PHP/code/qruz/storage/logs/laravel.log',
        'level' => 'debug',
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => '/Users/ahmadghallab/Documents/Projects/PHP/code/qruz/storage/logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'critical',
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'formatter' => NULL,
        'with' => 
        array (
          'stream' => 'php://stderr',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
      ),
      'null' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\NullHandler',
      ),
      'emergency' => 
      array (
        'path' => '/Users/ahmadghallab/Documents/Projects/PHP/code/qruz/storage/logs/laravel.log',
      ),
    ),
  ),
  'mail' => 
  array (
    'driver' => 'smtp',
    'host' => 'smtp.gmail.com',
    'port' => '587',
    'from' => 
    array (
      'address' => 'qruz.solution@gmail.com',
      'name' => 'Qruz',
    ),
    'encryption' => 'tls',
    'username' => 'qruz.solution@gmail.com',
    'password' => 'Ah3210779',
    'sendmail' => '/usr/sbin/sendmail -bs',
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => '/Users/ahmadghallab/Documents/Projects/PHP/code/qruz/resources/views/vendor/mail',
      ),
    ),
    'log_channel' => NULL,
  ),
  'queue' => 
  array (
    'default' => 'redis',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => 0,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => 'AKIA45TGTSRPFETTZ2B6',
        'secret' => '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',
        'prefix' => 'https://sqs.us-east-2.amazonaws.com/888199681118',
        'queue' => 'default',
        'region' => 'us-east-2',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'low',
        'retry_after' => 90,
        'block_for' => NULL,
      ),
      'azure' => 
      array (
        'driver' => 'azure',
        'protocol' => 'https',
        'accountname' => 'qruz',
        'key' => 'eUrtEplqMsFjlY0H/X43fo1YL9OONpx68QQ+NJZI9LeOx/x1hdOn6+5s5XjTAKrm4VD+VyajsL9PSwITTjC5dg==',
        'queue' => 'default',
        'timeout' => 90,
      ),
    ),
    'failed' => 
    array (
      'driver' => 'database',
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'services' => 
  array (
    'sign_in_with_apple' => 
    array (
      'login' => NULL,
      'redirect' => NULL,
      'client_id' => NULL,
      'client_secret' => NULL,
    ),
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
      'endpoint' => 'api.mailgun.net',
    ),
    'postmark' => 
    array (
      'token' => NULL,
    ),
    'ses' => 
    array (
      'key' => 'AKIA45TGTSRPFETTZ2B6',
      'secret' => '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',
      'region' => 'us-east-2',
    ),
    'facebook' => 
    array (
      'client_id' => '777428832782014',
      'client_secret' => '0c519c024aa6e438250556b22bba619f',
      'redirect' => NULL,
    ),
    'google' => 
    array (
      'client_id' => '384562568834-10k8skn9s8h42jt2ajbr5iuoeadkuu6k.apps.googleusercontent.com',
      'client_secret' => 'jBi3GbGCsURXUQQvwo9vhN_Y',
      'redirect' => NULL,
    ),
    'apple' => 
    array (
      'client_id' => 'com.VolaAgency.qruz',
      'client_secret' => 'eyJraWQiOiJITkNMVjNWNExXIiwiYWxnIjoiRVMyNTYifQ.eyJpc3MiOiJGOVlNNjUyQTU3IiwiaWF0IjoxNTk4NzE4NjUxLCJleHAiOjE2MTQyNzA2NTEsImF1ZCI6Imh0dHBzOi8vYXBwbGVpZC5hcHBsZS5jb20iLCJzdWIiOiJjb20uVm9sYUFnZW5jeS5xcnV6In0.iG1tjqwig0zNlQl0UMjP1WmEJMWHvkF4Za8Rhx1fr4Erc_WkfcIZoaqjmZwZc_xIgSZMf-FpQPA8xU7o06gZiw',
      'redirect' => NULL,
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => '120',
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => '/Users/ahmadghallab/Documents/Projects/PHP/code/qruz/storage/framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'qruz_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => false,
    'http_only' => true,
    'same_site' => NULL,
  ),
  'telescope' => 
  array (
    'domain' => NULL,
    'path' => 'telescope',
    'driver' => 'database',
    'storage' => 
    array (
      'database' => 
      array (
        'connection' => 'mysql',
        'chunk' => 1000,
      ),
    ),
    'enabled' => true,
    'middleware' => 
    array (
      0 => 'web',
      1 => 'Laravel\\Telescope\\Http\\Middleware\\Authorize',
    ),
    'ignore_paths' => 
    array (
      0 => 'nova-api*',
    ),
    'ignore_commands' => 
    array (
    ),
    'watchers' => 
    array (
      'Laravel\\Telescope\\Watchers\\CacheWatcher' => true,
      'Laravel\\Telescope\\Watchers\\CommandWatcher' => 
      array (
        'enabled' => true,
        'ignore' => 
        array (
        ),
      ),
      'Laravel\\Telescope\\Watchers\\DumpWatcher' => true,
      'Laravel\\Telescope\\Watchers\\EventWatcher' => 
      array (
        'enabled' => true,
        'ignore' => 
        array (
        ),
      ),
      'Laravel\\Telescope\\Watchers\\ExceptionWatcher' => true,
      'Laravel\\Telescope\\Watchers\\JobWatcher' => true,
      'Laravel\\Telescope\\Watchers\\LogWatcher' => true,
      'Laravel\\Telescope\\Watchers\\MailWatcher' => true,
      'Laravel\\Telescope\\Watchers\\ModelWatcher' => 
      array (
        'enabled' => true,
        'events' => 
        array (
          0 => 'eloquent.*',
        ),
      ),
      'Laravel\\Telescope\\Watchers\\NotificationWatcher' => true,
      'Laravel\\Telescope\\Watchers\\QueryWatcher' => 
      array (
        'enabled' => true,
        'ignore_packages' => true,
        'slow' => 100,
      ),
      'Laravel\\Telescope\\Watchers\\RedisWatcher' => true,
      'Laravel\\Telescope\\Watchers\\RequestWatcher' => 
      array (
        'enabled' => true,
        'size_limit' => 64,
      ),
      'Laravel\\Telescope\\Watchers\\GateWatcher' => 
      array (
        'enabled' => true,
        'ignore_abilities' => 
        array (
        ),
        'ignore_packages' => true,
      ),
      'Laravel\\Telescope\\Watchers\\ScheduleWatcher' => true,
      'Laravel\\Telescope\\Watchers\\ViewWatcher' => true,
    ),
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => '/Users/ahmadghallab/Documents/Projects/PHP/code/qruz/resources/views',
    ),
    'compiled' => '/Users/ahmadghallab/Documents/Projects/PHP/code/qruz/storage/framework/views',
  ),
  'websockets' => 
  array (
    'dashboard' => 
    array (
      'port' => 6001,
    ),
    'apps' => 
    array (
      0 => 
      array (
        'id' => '971168',
        'name' => 'Qruz',
        'key' => '48477c9c2419bd65e74f',
        'secret' => 'd55c31550b394ca0040a',
        'path' => NULL,
        'capacity' => NULL,
        'enable_client_messages' => true,
        'enable_statistics' => false,
      ),
    ),
    'app_provider' => 'BeyondCode\\LaravelWebSockets\\Apps\\ConfigAppProvider',
    'allowed_origins' => 
    array (
    ),
    'max_request_size_in_kb' => 250,
    'path' => 'websockets',
    'middleware' => 
    array (
      0 => 'web',
      1 => 'BeyondCode\\LaravelWebSockets\\Dashboard\\Http\\Middleware\\Authorize',
    ),
    'statistics' => 
    array (
      'model' => 'BeyondCode\\LaravelWebSockets\\Statistics\\Models\\WebSocketsStatisticsEntry',
      'interval_in_seconds' => 60,
      'delete_statistics_older_than_days' => 60,
      'perform_dns_lookup' => false,
    ),
    'ssl' => 
    array (
      'local_cert' => NULL,
      'local_pk' => NULL,
      'passphrase' => NULL,
    ),
    'channel_manager' => 'BeyondCode\\LaravelWebSockets\\WebSockets\\Channels\\ChannelManagers\\ArrayChannelManager',
  ),
  'flare' => 
  array (
    'key' => NULL,
    'reporting' => 
    array (
      'anonymize_ips' => true,
      'collect_git_information' => true,
      'report_queries' => true,
      'maximum_number_of_collected_queries' => 200,
      'report_query_bindings' => true,
      'report_view_data' => true,
      'grouping_type' => NULL,
    ),
    'send_logs_as_events' => true,
  ),
  'ignition' => 
  array (
    'editor' => 'phpstorm',
    'theme' => 'light',
    'enable_share_button' => true,
    'register_commands' => false,
    'ignored_solution_providers' => 
    array (
      0 => 'Facade\\Ignition\\SolutionProviders\\MissingPackageSolutionProvider',
    ),
    'enable_runnable_solutions' => NULL,
    'remote_sites_path' => '',
    'local_sites_path' => '',
    'housekeeping_endpoint_prefix' => '_ignition',
  ),
  'vapor' => 
  array (
    'redirect_to_root' => true,
    'redirect_robots_txt' => true,
    'serve_assets' => 
    array (
    ),
  ),
  'trustedproxy' => 
  array (
    'proxies' => 
    array (
      0 => '0.0.0.0/0',
      1 => '2000:0:0:0:0:0:0:0/3',
    ),
    'headers' => 30,
  ),
  'graphql-playground' => 
  array (
    'route' => 
    array (
      'uri' => '/graphql-playground',
      'name' => 'graphql-playground',
    ),
    'endpoint' => '/graphql',
    'enabled' => true,
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'alias' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
);
