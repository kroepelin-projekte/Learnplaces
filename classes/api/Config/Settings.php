<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Config;

class Settings implements constConfig
{

    public static function setCookieExpire(int $expire): void
    {
        $settings = new \ilSetting(self::SETTING_MODULE_ID);
        $settings->set('cookie_expire', $expire);
    }

    public static function getCookieExpire(): int
    {
        $settings = new \ilSetting(self::SETTING_MODULE_ID);
        return $settings->get("cookie_expire", 8);
    }

    public static function setPermissionRoles(string $roles): void
    {
        $settings = new \ilSetting(self::SETTING_MODULE_ID);
        $settings->set('roles', $roles);
    }

    public static function getPermissionRoles(): string
    {
        $settings = new \ilSetting(self::SETTING_MODULE_ID);
        return $settings->get("roles", "");
    }
    public static function uninstall(): void
    {
        $settings = new \ilSetting(self::SETTING_MODULE_ID);
        $settings->delete(self::SETTING_MODULE_ID);
    }
    public static function setClientURL(string $url): void
    {
        $settings = new \ilSetting(self::SETTING_MODULE_ID);
        $settings->set('client_url', $url);
    }

    public static function getClientURL(): string
    {
        $settings = new \ilSetting(self::SETTING_MODULE_ID);
        return $settings->get("client_url", '');
    }

    public static function setSecret(string $secret): void
    {
        $settings = new \ilSetting(self::SETTING_MODULE_ID);
        $settings->set('secret', $secret);
    }

    public static function getSecret(): string
    {
        $settings = new \ilSetting(self::SETTING_MODULE_ID);
        return $settings->get("secret", '');
    }

}