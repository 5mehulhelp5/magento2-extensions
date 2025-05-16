<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Magento\Store\Model\ScopeInterface;

/**
 * Scope config Provider model
 */
class ConfigProvider extends ConfigProviderAbstract
{
    /**
     * @var string
     */
    protected $pathPrefix = 'amastyfaq/';
    public const PATH_PREFIX = 'amastyfaq/';

    public const ENABLED = 'general/enabled';
    public const URL_KEY_PATH = 'general/url_key';
    public const ADD_TO_TOOLBAR = 'general/add_to_toolbar_menu';
    public const ADD_TO_FOOTER = 'general/add_to_footer_menu';
    public const LIMIT_SHORT_ANSWER = 'faq_page/limit_short_answer';
    public const USER_NOTIFY = 'user_email/user_notify';
    public const ALLOW_UNREGISTERED_CUSTOMER_ASK  = 'general/unregistered_customers_questions';
    public const USER_NOTIFY_SENDER = 'user_email/sender';
    public const USER_NOTIFY_EMAIL_TEMPLATE = 'user_email/template';
    public const ADMIN_NOTIFY = 'admin_email/enable_notify';
    public const ADMIN_NOTIFY_EMAIL = 'admin_email/send_to';
    public const ADMIN_NOTIFY_EMAIL_TEMPLATE = 'admin_email/template';
    public const TRANS_IDENT_GENERAL_EMAIL = 'trans_email/ident_general/email';
    public const TRANS_IDENT_GENERAL_NAME = 'trans_email/ident_general/name';
    public const TRANS_IDENT_EMAIL = 'trans_email/ident_%s/email';
    public const TRANS_IDENT_NAME = 'trans_email/ident_%s/name';
    public const CATEGORIES_SORT = 'faq_page/category_sort';
    public const QUESTIONS_SORT = 'faq_page/question_sort';
    public const IS_SHOW_SEARCH = 'faq_page/show_search';
    public const SHOW_TAB_ON_PRODUCT_PAGE = 'product_page/show_tab';
    public const SHOW_ASK_QUESTION_FORM_ON_PRODUCT_PAGE = 'product_page/show_link';
    public const SHOW_ASK_QUESTION_FORM_ON_ANSWER_PAGE = 'faq_page/show_ask';
    public const SHOW_BREADCRUMBS = 'faq_page/show_breadcrumbs';
    public const LABEL = 'general/label';
    public const LABEL_NO_RESULT = 'faq_page/no_result';
    public const ADD_TO_MAIN_MENU = 'general/add_to_category_menu';
    public const IS_RATING_ENABLED = 'rating/enabled';
    public const RATING_TEMPLATE = 'rating/type';
    public const IS_SITEMAP_ENABLED = 'seo/xml/sitemap';
    public const IS_HREFLANG_ENABLED = 'seo/xml/hreflang';
    public const HREFLANG_LANGUAGE = 'seo/xml/language';
    public const HREFLANG_COUNTRY = 'seo/xml/country';
    public const CHANGE_FREQUENCY = 'seo/xml/changefreq';
    public const SITEMAP_PRIORITY = 'seo/xml/sitemap_priority';
    public const RATING_HIDE_EMPTY_TOTAL = 'rating/avg_hide_empty_total';
    public const RATING_ALLOW_GUESTS = 'rating/allow_guests';
    public const ADD_URL_SUFFIX = 'seo/add_url_suffix';
    public const REMOVE_TRAILING_SLASH = 'seo/remove_trailing_slash';
    public const REMOVE_TRAILING_SLASH_HOME = 'seo/remove_trailing_slash_home';
    public const URL_SUFFIX = 'seo/url_suffix';
    public const CANONICAL_URL = 'seo/canonical_url';
    public const ADD_STRUCTUREDDATA = 'seo/add_structureddata';
    public const ADD_RICHDATA_BREADCRUMBS = 'seo/add_richdata_breadcrumbs';
    public const ADD_RICHDATA_ORGANIZATION = 'seo/add_richdata_organization';
    public const RICHDATA_ORGANIZATION_WEBSITE_URL = 'seo/organization_website_url';
    public const RICHDATA_ORGANIZATION_LOGO_URL = 'seo/organization_logo_url';
    public const RICHDATA_ORGANIZATION_NAME = 'seo/organization_name';
    public const ADD_RICHDATA_CONTACT = 'seo/add_richdata_contact';
    public const RICHDATA_ORGANIZATION_CONTACT_TYPE = 'seo/organization_contact_type';
    public const RICHDATA_ORGANIZATION_TELEPHONE = 'seo/organization_telephone';
    public const CATEGORY_SEARCH = 'faq_page/category_in_search';
    public const LIMIT_CATEGORY_SEARCH = 'faq_page/limit_category_search';
    public const SEARCH_PAGE_SIZE = 'faq_page/limit_question_search';
    public const CATEGORY_PAGE_SIZE = 'faq_page/limit_question_category';
    public const PRODUCT_PAGE_SIZE = 'product_page/limit_question_product';
    public const SOCIAL_ENABLED = 'social/enabled';
    public const SOCIAL_ACTIVE_BUTTONS = 'social/buttons';
    public const PAGE_LAYOUT = 'faq_home_page/layout';
    public const FAQ_PAGE_SHORT_ANSWER_BEHAVIOR = 'faq_page/short_answer_behavior';
    public const PRODUCT_PAGE_SHORT_ANSWER_BEHAVIOR = 'product_page/short_answer_behavior';
    public const FAQ_CMS_HOME_PAGE = 'faq_home_page/cmspages_faq_home_page';
    public const USE_FAQ_CMS_HOME_PAGE = 'faq_home_page/use_faq_home_page';
    public const GDPR_ENABLED = 'gdpr/enabled';
    public const GDPR_TEXT = 'gdpr/text';
    public const TAG_MENU_LIMIT = 'faq_page/tag_menu_limit';
    public const TAB_NAME = 'product_page/tab_name';
    public const TAB_POSITION = 'product_page/tab_position';

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isAllowUnregisteredCustomersAsk($storeId = null)
    {
        return (bool)$this->getValue(self::ALLOW_UNREGISTERED_CUSTOMER_ASK, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return (bool)$this->getValue(self::ENABLED, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getUrlKey($storeId = null)
    {
        return $this->getValue(self::URL_KEY_PATH, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     */
    public function getLimitShortAnswer($storeId = null)
    {
        return (int)$this->getValue(self::LIMIT_SHORT_ANSWER, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isNotifyUser($storeId = null)
    {
        return (bool)$this->getValue(self::USER_NOTIFY, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getNotifySender($storeId = null)
    {
        return $this->getValue(self::USER_NOTIFY_SENDER, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isNotifyAdmin($storeId = null)
    {
        return (bool)$this->getValue(self::ADMIN_NOTIFY, $storeId);
    }

    public function notifyAdminEmail(?int $storeId = null): string
    {
        return (string)$this->getValue(self::ADMIN_NOTIFY_EMAIL, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getCategoriesSort($storeId = null)
    {
        return $this->getValue(self::CATEGORIES_SORT, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getQuestionsSort($storeId = null)
    {
        return $this->getValue(self::QUESTIONS_SORT, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isShowSearchBox($storeId = null)
    {
        return (bool)$this->getValue(self::IS_SHOW_SEARCH, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isShowAskQuestionOnAnswerPage($storeId = null)
    {
        return (bool)$this->getValue(self::SHOW_ASK_QUESTION_FORM_ON_ANSWER_PAGE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isShowAskQuestionOnProductPage($storeId = null)
    {
        return (bool)$this->getValue(self::SHOW_ASK_QUESTION_FORM_ON_PRODUCT_PAGE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isShowBreadcrumbs($storeId = null)
    {
        return (bool)$this->getValue(self::SHOW_BREADCRUMBS);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getLabel($storeId = null)
    {
        return $this->getValue(self::LABEL, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getNoItemsLabel($storeId = null)
    {
        return $this->getValue(self::LABEL_NO_RESULT, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isAddToFooter($storeId = null)
    {
        return (bool)$this->getValue(self::ADD_TO_FOOTER, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isAddToToolbar($storeId = null)
    {
        return (bool)$this->getValue(self::ADD_TO_TOOLBAR, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isAddToMainMenu($storeId = null)
    {
        return (bool)$this->getValue(self::ADD_TO_MAIN_MENU, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isRatingEnabled($storeId = null)
    {
        return (bool)$this->getValue(self::IS_RATING_ENABLED, $storeId);
    }

    public function getVotingBehavior($storeId = null): string
    {
        switch ($this->getValue(self::RATING_TEMPLATE, $storeId)) {
            case \Amasty\Faq\Model\OptionSource\Question\RatingType::VOTING:
                $behavior = 'voting';
                break;
            case \Amasty\Faq\Model\OptionSource\Question\RatingType::AVERAGE:
                $behavior = 'average';
                break;
            case \Amasty\Faq\Model\OptionSource\Question\RatingType::YESNO:
            default:
                $behavior = 'yesno';
                break;
        }

        return $behavior;
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getRatingTemplateName($storeId = null)
    {
        return 'Amasty_Faq/rating/' . $this->getVotingBehavior($storeId);
    }

    public function isHideZeroRatingTotal($storeId = null): bool
    {
        return  (bool)$this->getValue(self::RATING_HIDE_EMPTY_TOTAL, $storeId);
    }

    public function isGuestRatingAllowed($storeId = null): bool
    {
        return  (bool)$this->getValue(self::RATING_ALLOW_GUESTS, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isSiteMapEnabled($storeId = null)
    {
        return (bool)$this->getValue(self::IS_SITEMAP_ENABLED, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isHreflangEnabled($storeId = null)
    {
        return (bool)$this->getValue(self::IS_HREFLANG_ENABLED, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getHreflangLanguage($storeId = null)
    {
        return $this->getValue(self::HREFLANG_LANGUAGE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getHreflangCountry($storeId = null)
    {
        return $this->getValue(self::HREFLANG_COUNTRY, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getFrequency($storeId = null)
    {
        return $this->getValue(self::CHANGE_FREQUENCY, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getSitemapPriority($storeId = null)
    {
        return $this->getValue(self::SITEMAP_PRIORITY, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isAddUrlSuffix($storeId = null)
    {
        return $this->isSetFlag(self::ADD_URL_SUFFIX, $storeId);
    }

    /**
     * @return bool
     */
    public function isRemoveTrailingSlash()
    {
        return $this->isSetFlag(self::REMOVE_TRAILING_SLASH);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getUrlSuffix($storeId = null)
    {
        return $this->getValue(self::URL_SUFFIX, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isCanonicalUrlEnabled($storeId = null)
    {
        return (bool)$this->getValue(self::CANONICAL_URL, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isSocialButtonsEnabled($storeId = null)
    {
        return $this->isSetFlag(self::SOCIAL_ENABLED, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return array
     */
    public function getSocialActiveButtons($storeId = null)
    {
        return explode(',', $this->getValue(self::SOCIAL_ACTIVE_BUTTONS, $storeId));
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isAddStructuredData($storeId = null)
    {
        return (bool)$this->getValue(self::ADD_STRUCTUREDDATA, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isAddRichDataBreadcrumbs($storeId = null)
    {
        return (bool)$this->getValue(self::ADD_RICHDATA_BREADCRUMBS, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isAddRichDataOrganization($storeId = null)
    {
        return (bool)$this->getValue(self::ADD_RICHDATA_ORGANIZATION, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getRichDataWebsiteUrl($storeId = null)
    {
        return $this->getValue(self::RICHDATA_ORGANIZATION_WEBSITE_URL, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getRichDataLogoUrl($storeId = null)
    {
        return $this->getValue(self::RICHDATA_ORGANIZATION_LOGO_URL, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getRichDataOrganizationName($storeId = null)
    {
        return $this->getValue(self::RICHDATA_ORGANIZATION_NAME, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isAddRichDataContact($storeId = null)
    {
        return (bool)$this->getValue(self::ADD_RICHDATA_CONTACT, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getRichDataContactType($storeId = null)
    {
        return $this->getValue(self::RICHDATA_ORGANIZATION_CONTACT_TYPE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getRichDataTelephone($storeId = null)
    {
        return $this->getValue(self::RICHDATA_ORGANIZATION_TELEPHONE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     */
    public function getProductPageSize($storeId = null)
    {
        return (int)$this->getValue(self::PRODUCT_PAGE_SIZE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     */
    public function getCategoryPageSize($storeId = null)
    {
        return (int)$this->getValue(self::CATEGORY_PAGE_SIZE, $storeId);
    }

    /**
     * @return bool
     */
    public function isShowCategoryInSearch()
    {
        return (bool)$this->getValue(self::CATEGORY_SEARCH);
    }

    /**
     * @return int
     */
    public function getLimitCategoryInSearch()
    {
        return (int)$this->getValue(self::LIMIT_CATEGORY_SEARCH);
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     */
    public function getSearchPageSize($storeId = null)
    {
        return (int)$this->getValue(self::SEARCH_PAGE_SIZE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     */
    public function getFaqPageShortAnswerBehavior($storeId = null)
    {
        return (int)$this->getValue(self::FAQ_PAGE_SHORT_ANSWER_BEHAVIOR, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     */
    public function getProductPageShortAnswerBehavior($storeId = null)
    {
        return (int)$this->getValue(self::PRODUCT_PAGE_SHORT_ANSWER_BEHAVIOR, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getPageLayout($storeId = null)
    {
        return $this->getValue(self::PAGE_LAYOUT, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isUseFaqCmsHomePage($storeId = null)
    {
        return (bool)$this->getValue(self::USE_FAQ_CMS_HOME_PAGE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getFaqCmsHomePage($storeId = null)
    {
        return $this->getValue(self::FAQ_CMS_HOME_PAGE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isGDPREnabled($storeId = null)
    {
        return (bool)$this->getValue(self::GDPR_ENABLED, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getGDPRText($storeId = null)
    {
        return $this->getValue(self::GDPR_TEXT, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     */
    public function getTagMenuLimit($storeId = null)
    {
        return (int)$this->getValue(self::TAG_MENU_LIMIT, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getTabName($storeId = null)
    {
        return $this->getValue(self::TAB_NAME, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     */
    public function getTabPosition($storeId = null)
    {
        return (int)$this->getValue(self::TAB_POSITION, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isShowTab($storeId = null)
    {
        return (bool)$this->getValue(self::SHOW_TAB_ON_PRODUCT_PAGE, $storeId);
    }

    public function getTransIdentGeneralEmail(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::TRANS_IDENT_GENERAL_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getTransIdentGeneralName(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::TRANS_IDENT_GENERAL_NAME,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getTransIdentEmail(string $emailTo, ?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            sprintf(self::TRANS_IDENT_EMAIL, $emailTo),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getTransIdentName(string $emailTo, ?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            sprintf(self::TRANS_IDENT_NAME, $emailTo),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getTemplateIdentifier(string $templateConfigPath, ?int $storeId = null): string
    {
        return (string)$this->getValue($templateConfigPath, $storeId);
    }

    public function isRemoveTrailingSlashHome(): bool
    {
        return $this->isSetFlag(self::REMOVE_TRAILING_SLASH_HOME);
    }
}
