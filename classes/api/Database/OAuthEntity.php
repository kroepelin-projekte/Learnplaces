<?php

namespace KPG\Learnplaces\api\Database;

use ActiveRecord;

class OAuthEntity extends ActiveRecord
{
    public const TABLE_NAME = 'xsrl_oauth';

    public static function returnDbTableName(): string
    {
        return self::TABLE_NAME;
    }

    /**
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @con_is_notnull true
     * @con_is_primary true
     * @con_is_unique  true
     * @con_sequence   true
     */
    protected ?int $id = null;

    /**
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     2000
     * @con_is_notnull true
     */
    protected ?string $state = null;

    /**
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     2000
     * @con_is_notnull true
     */
    protected ?string $code_challenge = null;

    /**
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     2000
     * @con_is_notnull true
     */
    protected ?string $redirect_uri = null;

    /**
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     2000
     * @con_is_notnull false
     */
    protected ?string $code = null;

    /**
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @con_is_notnull true
     */
    protected ?string $expire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): OAuthEntity
    {
        $this->id = $id;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): OAuthEntity
    {
        $this->state = $state;
        return $this;
    }

    public function getCodeChallenge(): ?string
    {
        return $this->code_challenge;
    }

    public function setCodeChallenge(?string $code_challenge): OAuthEntity
    {
        $this->code_challenge = $code_challenge;
        return $this;
    }

    public function getRedirectUri(): ?string
    {
        return $this->redirect_uri;
    }

    public function setRedirectUri(?string $redirect_uri): OAuthEntity
    {
        $this->redirect_uri = $redirect_uri;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): OAuthEntity
    {
        $this->code = $code;
        return $this;
    }

    public function getExpire(): ?string
    {
        return $this->expire;
    }

    public function setExpire(?string $expire): OAuthEntity
    {
        $this->expire = $expire;
        return $this;
    }
}
