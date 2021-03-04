<?php

namespace WS\Site\Service;

use WS\Core\Library\Setting\SettingDefinitionInterface;
use WS\Core\Library\Setting\Definition\Group;
use WS\Core\Library\Setting\Definition\Section;
use WS\Core\Library\Setting\Definition\Setting;

class SettingDefinitionService implements SettingDefinitionInterface
{
    public function getSettingsDefinition(): array
    {
        $siteSection = new Section(
            'site',
            'settings.section.name',
            [
                'description' => 'settings.section.description',
                'translation_domain' => 'ws_cms_site',
                'icon' => 'fa-sliders-h',
                'role'  => 'ROLE_WS_SITE_SETTING',
                'order'  => 10
            ]
        );

        $siteGroup = new Group(
            'general',
            'general.settings.group.general.name',
            [
                'description' => 'settings.section.description',
                'translation_domain' => 'ws_cms_site',
                'order' => 100,
            ]
        );
        $siteSection->addGroup($siteGroup);

        $httpsSetting = new Setting(
            'site_general_force_https',
            'general.settings.setting.site_general_force_https.name',
            SettingDefinitionInterface::SETTING_BOOLEAN,
            [
                'description' => 'general.settings.setting.site_general_force_https.description',
                'translation_domain' => 'ws_cms_site',
                'required' => false,
                'default' => false,
            ]
        );

        $maintenanceSetting = new Setting(
            'site_general_in_maintenance',
            'general.settings.setting.site_general_in_maintenance.name',
            SettingDefinitionInterface::SETTING_BOOLEAN,
            [
                'description' => 'general.settings.setting.site_general_in_maintenance.description',
                'translation_domain' => 'ws_cms_site',
                'required' => false,
                'default' => false,
            ]
        );
        $siteGroup->addSetting($httpsSetting);
        $siteGroup->addSetting($maintenanceSetting);

        $seoSection = new Section(
            'seo',
            'settings.section.name',
            [
                'description' => 'settings.section.description',
                'translation_domain' => 'ws_cms_site_seo',
                'icon' => 'fa-share-alt',
                'role' => 'ROLE_WS_SITE_SETTING',
                'order'  => 50
            ]
        );

        $facebookGroup = new Group(
            'facebook',
            'social.settings.group.facebook.name',
            [
                'translation_domain' => 'ws_cms_site_seo',
                'description' => 'social.settings.group.facebook.description',
                'order' => 1,
            ]
        );
        $seoSection->addGroup($facebookGroup);
        $twitterGroup = new Group(
            'twitter',
            'social.settings.group.twitter.name',
            [
                'translation_domain' => 'ws_cms_site_seo',
                'description' => 'social.settings.group.twitter.description',
                'order' => 2,
            ]
        );
        $seoSection->addGroup($twitterGroup);
        $youtubeGroup = new Group(
            'youtube',
            'social.settings.group.youtube.name',
            [
                'translation_domain' => 'ws_cms_site_seo',
                'description' => 'social.settings.group.youtube.description',
                'order' => 3,
            ]
        );
        $seoSection->addGroup($youtubeGroup);
        $linkedinGroup = new Group(
            'linkedin',
            'social.settings.group.linkedin.name',
            [
                'translation_domain' => 'ws_cms_site_seo',
                'description' => 'social.settings.group.linkedin.description',
                'order' => 4,
            ]
        );
        $seoSection->addGroup($linkedinGroup);

        $facebookSetting = new Setting(
            'social_facebook_page',
            'social.settings.setting.social_facebook_page.name',
            SettingDefinitionInterface::SETTING_TEXT,
            [
                'translation_domain' => 'ws_cms_site_seo',
                'required' => false,
                'description' => 'social.settings.setting.social_facebook_page.description',
                'default' => '',
                'placeholder' => 'social.settings.setting.social_facebook_page.placeholder'
            ]
        );
        $facebookGroup->addSetting($facebookSetting);

        $twitterSetting = new Setting(
            'social_twitter_profile',
            'social.settings.setting.social_twitter_profile.name',
            SettingDefinitionInterface::SETTING_TEXT,
            [
                'translation_domain' => 'ws_cms_site_seo',
                'required' => false,
                'description' => 'social.settings.setting.social_twitter_profile.description',
                'default' => '',
                'placeholder' => 'social.settings.setting.social_twitter_profile.placeholder',
            ]
        );
        $twitterGroup->addSetting($twitterSetting);

        $youtubeSetting = new Setting(
            'social_youtube_page',
            'social.settings.setting.social_youtube_page.name',
            SettingDefinitionInterface::SETTING_TEXT,
            [
                'translation_domain' => 'ws_cms_site_seo',
                'required' => false,
                'description' => 'social.settings.setting.social_youtube_page.description',
                'default' => '',
                'placeholder' => 'social.settings.setting.social_youtube_page.placeholder',
            ]
        );
        $youtubeGroup->addSetting($youtubeSetting);

        $linkedinSetting = new Setting(
            'social_linkedin_page',
            'social.settings.setting.social_linkedin_page.name',
            SettingDefinitionInterface::SETTING_TEXT,
            [
                'translation_domain' => 'ws_cms_site_seo',
                'required' => false,
                'description' => 'social.settings.setting.social_linkedin_page.description',
                'default' => '',
                'placeholder' => 'social.settings.setting.social_linkedin_page.placeholder',
            ]
        );
        $linkedinGroup->addSetting($linkedinSetting);

        $robotsSection = new Section(
            'seo',
            'settings.section.name',
            [
                'description' => 'settings.section.description',
                'translation_domain' => 'ws_cms_site_seo',
                'icon' => 'fa-share-alt',
                'role' => 'ROLE_WS_SITE_SETTING',
                'order'  => 50
            ]
        );

        $robotsGroup = new Group(
            'robots',
            'robots.settings.group.robots.name',
            [
                'translation_domain' => 'ws_cms_site_seo',
                'description' => 'robots.settings.group.robots.description',
                'order' => 100,
            ]
        );
        $robotsSection->addGroup($robotsGroup);

        $humansGroup = new Group(
            'humans',
            'robots.settings.group.humans.name',
            [
                'translation_domain' => 'ws_cms_site_seo',
                'description' => 'robots.settings.group.humans.description',
                'order' => 101,
            ]
        );
        $robotsSection->addGroup($humansGroup);

        $robotsCustomRulesSetting = new Setting(
            'robots_custom_rules',
            'robots.settings.setting.robots_custom_rules.name',
            SettingDefinitionInterface::SETTING_TEXTAREA,
            [
                'translation_domain' => 'ws_cms_site_seo',
                'required' => false,
                'description' => 'robots.settings.setting.robots_custom_rules.description',
                'default' => '',
            ]
        );
        $robotsGroup->addSetting($robotsCustomRulesSetting);

        $humansContactNameSetting = new Setting(
            'humans_contact_name',
            'robots.settings.setting.humans_contact_name.name',
            SettingDefinitionInterface::SETTING_TEXT,
            [
                'translation_domain' => 'ws_cms_site_seo',
                'required' => true,
                'description' => 'robots.settings.setting.humans_contact_name.description',
                'default' => '',
            ]
        );
        $humansGroup->addSetting($humansContactNameSetting);

        $humansContactFormSetting = new Setting(
            'humans_contact_form',
            'robots.settings.setting.humans_contact_form.name',
            SettingDefinitionInterface::SETTING_TEXT,
            [
                'translation_domain' => 'ws_cms_site_seo',
                'required' => true,
                'description' => 'robots.settings.setting.humans_contact_form.description',
                'placeholder' => 'robots.settings.setting.humans_contact_form.placeholder',
                'default' => '',
            ]
        );
        $humansGroup->addSetting($humansContactFormSetting);

        $humansLocationSetting = new Setting(
            'humans_location',
            'robots.settings.setting.humans_location.name',
            SettingDefinitionInterface::SETTING_TEXT,
            [
                'translation_domain' => 'ws_cms_site_seo',
                'required' => false,
                'description' => 'robots.settings.setting.humans_location.description',
                'default' => '',
            ]
        );
        $humansGroup->addSetting($humansLocationSetting);

        return [
            $siteSection, $seoSection, $robotsSection
        ];
    }
}
