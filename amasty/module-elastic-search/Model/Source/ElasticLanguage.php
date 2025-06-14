<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Elastic Search Base for Magento 2
 */

namespace Amasty\ElasticSearch\Model\Source;

class ElasticLanguage implements \Magento\Framework\Option\ArrayInterface
{
    public const ANY = '0';
    public const ALL = '1';

    /**
     * @var array
     */
    private $laguages = [
        'arabic' => 'Arabic (General)',
        'armenian' => 'Armenian',
        'basque' => 'Basque (Spain)',
        'bengali' => 'Bengali (Bangladesh)',
        'brazilian' => 'Portuguese (Brasil)',
        'bulgarian' => 'Bulgarian',
        'catalan' => 'Catalan (Spain)',
        'czech' => 'Czech',
        'danish' => 'Danish',
        'dutch' => 'Dutch (Gemeral)',
        'english' => 'English',
        'finnish' => 'Finnish',
        'french' => 'French (General)',
        'galician' => 'Galician (Spain)',
        'german' => 'German',
        'greek' => 'Greek',
        'hindi' => 'Hindi',
        'hungarian' => 'Hungarian',
        'indonesian' => 'Indonesian',
        'irish' => 'English (Ireland)',
        'italian' => 'Italian',
        'latvian' => 'Latvian',
        'norwegian' => 'Norwegian (General)',
        'persian' => 'Persian',
        'portuguese' => 'Portuguese (General)',
        'romanian' => 'Romanian',
        'russian' => 'Russian',
        'sorani' => 'Sorani',
        'spanish' => 'Spanish (General)',
        'swedish' => 'Swedish',
        'thai' => 'Thai',
        'turkish' => 'Turkish'
    ];

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->laguages as $key => $label) {
            $options[] = [
                'value' => $key,
                'label' => __($label),
            ];
        }

        return $options;
    }
}
