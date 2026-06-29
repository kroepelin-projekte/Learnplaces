# Learnplaces - ILIAS Plugin

**Table of Contents**

- [Introduction](#introduction)
- [Compatibility](#compatibility)
- [Installation](#installation)
- [Activation](#activation)

## Introduction

This plugin can be used to create learnplaces.
Each learnplaces object has a position on the map and can store information about this location
such as formatted text, images, videos, ILIAS links or accordions.


## Compatibility
| Plugin Version | ILIAS Versions | PHP Versions |
|----------------|----------------|--------------|
| v1.X           | 5.2 - 5.3      | 7.0          |
| v2.X           | 5.3 - 5.4      | 7.0 - 7.2    |
| v3.X           | 5.4 - 6        | 7.0 - 7.4    |
| v4.X           | 6 - 7          | 7.2 - 7.4    |
| v5.X           | 8 - 9          | 7.4 - 8.2    |
| v6.X           | 10             | 8.2 - 8.3    |


## Installation

1. Launch a terminal instance running `bash` from the project's root directory.
2. Enter the following commands to proceed with the plugin installation.

**Create directories**
```bash
mkdir -p Customizing/global/plugins/Services/Repository/RepositoryObject
cd Customizing/global/plugins/Services/Repository/RepositoryObject
```

## Apache Config
In ILIAS 10, the `public/.htaccess` file is generated automatically and may be overwritten during updates or maintenance tasks.  
Therefore, we recommend configuring custom rewrite rules on the server level (Apache vhost / directory configuration) instead of editing `.htaccess`.

ILIAS should always be served from the `public/` directory. Depending on the setup, this is typically done in one of the following ways.

### Variant A: `public/` as DocumentRoot (recommended)
```apacheconf
<IfModule mod_ssl.c>
  <VirtualHost *:443>
    ServerName example.tld
    DocumentRoot /var/www/html/ilias/public
    
    <Directory /var/www/html/ilias/public>
      Require all granted
      AllowOverride All
      Options -Indexes +FollowSymLinks +MultiViews
    
      <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php/php8.2-fpm.sock|fcgi://localhost"
      </FilesMatch>
    </Directory>
    
    RewriteEngine On
    
    # Preserve the Authorization header (required for some PHP/FastCGI setups)
    RewriteCond %{HTTP:Authorization} ^(.+)
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%1]
    
    # Learnplaces API endpoint (root install)
    RewriteRule ^/?api/learnplaceapp(/.*)?$ /Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/classes/api/connector.php [END,PT]
    
    # ...
  </VirtualHost>
</IfModule>
```

### Variant B: ILIAS under a path using `Alias` (e.g. `/ilias`)
```apacheconf
<IfModule mod_ssl.c>
  <VirtualHost *:443>
    ServerName example.tld
    DocumentRoot /var/www/html/ilias/public
    
    Alias /ilias /var/www/html/ilias/public
    
    <Directory /var/www/html/ilias/public>
      Require all granted
      AllowOverride All
      Options -Indexes +FollowSymLinks +MultiViews
    
      <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php/php8.2-fpm.sock|fcgi://localhost"
      </FilesMatch>
    </Directory>
    
    RewriteEngine On
    
    # Preserve the Authorization header (required for some PHP/FastCGI setups)
    RewriteCond %{HTTP:Authorization} ^(.+)
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%1]
    
    # Learnplaces API endpoint (alias install)
    RewriteRule ^/?ilias/api/learnplaceapp(/.*)?$ /ilias/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/classes/api/connector.php [END,PT]
       
    # ...
  </VirtualHost>
</IfModule>
```

## Nginx Config
```nginx
# Learnplaces
location ~ ^/api/learnplaceapp/(.*)$ {
    rewrite ^/api/learnplaceapp/(.*)$ /Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/classes/api/connector.php last;
}
```


**Clone Project**
```bash
git clone https://github.com/kroepelin-projekte/Learnplaces.git Learnplaces
```

**Switch to branch**
```bash
cd Learnplaces
git switch release_x
```

**Install dependencies**
```bash
composer install
```

## Activation

1. Sign in to ILIAS with Administrator privileges.
2. Proceed to `Administration » Extending ILIAS » Plugins`
3. Locate the desired plugin, then select `Actions » Install`, and subsequently, `Actions » Activate`.
