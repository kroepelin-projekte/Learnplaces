<?php
namespace Repository\RepositoryObject\Learnplaces\classes\api\Config;

interface constConfig {
    // Settings
    public const SETTING_MODULE_ID = "learnplaces_api_settings";
    // TAB ID
    public const TAB_ID_API_SETTINGS = "tab_id_api_settings";
    public const TAB_SUB_ID_API_SETTINGS = "tab_sub_id_api_settings";
    public const TAB_SUB_ID_PERMISSION_SETTINGS = "tab_sub_id_permission_settings";
    // LANG
    public const LANG_TAB_API_SETTINGS = "lang_tab_api_settings";
    public const LANG_TAB_SUB_API_SETTINGS = "lang_tab_sub_api_settings";
    public const LANG_TAB_SUB_PERMISSION_SETTINGS = "lang_tab_sub_permission_settings";
    public const LANG_INPUT_TEXT_BASE_URL = "lang_input_text_base_url";
    public const LANG_INPUT_TEXT_BASE_URL_BYLINE = "lang_input_text_base_url_byline";
    public const LANG_INPUT_TEXT_COOKIE_EXPIRE = "lang_input_text_cookie_expire";
    public const LANG_INPUT_TEXT_COOKIE_EXPIRE_BYLINE = "lang_input_text_cookie_expire_byline";
    public const LANG_SETTINGS = "lang_settings";
    public const LANG_ERROR_REQUIRED_FIELD = "lang_error_required_field";
    public const LANG_SUCCESS_SETTINGS = "lang_success_settings";
    public const LANG_INPUT_TEXT_ROLES = "lang_input_text_roles";
    public const LANG_INPUT_TEXT_ROLES_BYLINE = "lang_input_text_roles_byline";
    // CMD
    public const CMD_SHOW_PERMISSION_SETTINGS = "cmd_show_permission_settings";
    public const CMD_SAVE_PERMISSION_SETTINGS = "cmd_save_permission_settings";
    public const CMD_SHOW_API_SETTINGS = "cmd_show_api_settings";
    public const CMD_SAVE_API_SETTINGS = "cmd_save_api_settings";

}