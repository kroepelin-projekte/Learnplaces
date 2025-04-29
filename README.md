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
| v6.X           | 10             | 8.2          |


## Installation

1. Launch a terminal instance running `bash` from the project's root directory.
2. Enter the following commands to proceed with the plugin installation.

**Create directories**
```bash
mkdir -p Customizing/global/plugins/Services/Repository/RepositoryObject
cd Customizing/global/plugins/Services/Repository/RepositoryObject
```
**Apache Config**
```apacheconf
<Directory /var/www/html>
  AllowOverride All
  Require all granted
</Directory>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-l
RewriteRule "^/api/*" "/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/classes/api/connector.php" [L]
SetEnvIf Authorization .+ HTTP_AUTHORIZATION=$0
RewriteCond %{HTTP:Authorization} ^(.)
RewriteRule . - [E=HTTP_AUTHORIZATION:%1]
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
