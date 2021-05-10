<?php

namespace SEOPressPro\JsonSchemas;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Helpers\RichSnippetType;
use SEOPress\Models\GetJsonData;
use SEOPressPro\Models\JsonSchemaValue;

class LocalBusiness extends JsonSchemaValue implements GetJsonData {
    const NAME = 'local-business';

    const ALIAS = ['localbusiness'];

    protected function getName() {
        return self::NAME;
    }

    protected function getDayByKey($key) {
        switch ($key) {
            case 0:
                return 'Monday';
            case 1:
                return 'Tuesday';
            case 2:
                return 'Wednesday';
            case 3:
                return 'Thursday';
            case 4:
                return 'Friday';
            case 5:
                return 'Saturday';
            case 6:
                return 'Sunday';
        }
    }

    /**
     * @since 4.6.0
     *
     * @return array
     */
    protected function getVariablesForOptionLocalBusiness() {
        return [
           'type'           => '%%local_business_type%%',
           'image'          => '%%social_knowledge_image%%',
           'id'             => '%%siteurl%%',
           'name'           => '%%social_knowledge_name%%',
           'url'            => '%%local_business_url%%',
           'telephone'      => '%%local_business_phone%%',
           'priceRange'     => '%%local_business_price_range%%',
           'servesCuisines' => '%%local_business_cuisine%%',
        ];
    }

    /**
     * @since 4.6.0
     *
     * @return array
     *
     * @param array $schemaManual
     */
    protected function getVariablesForManualSnippet($schemaManual) {
        $servesCuisine = [
            'FoodEstablishment',
            'Bakery',
            'BarOrPub',
            'Brewery',
            'CafeOrCoffeeShop',
            'FastFoodRestaurant',
            'IceCreamShop',
            'Restaurant',
            'Winery',
        ];

        $type = isset($schemaManual['_seopress_pro_rich_snippets_lb_type']) ? $schemaManual['_seopress_pro_rich_snippets_lb_type'] : '';

        $variables = [
           'type'                 => $type,
           'image'                => isset($schemaManual['_seopress_pro_rich_snippets_lb_img']) ? $schemaManual['_seopress_pro_rich_snippets_lb_img'] : '',
           'url'                  => isset($schemaManual['_seopress_pro_rich_snippets_lb_website']) ? $schemaManual['_seopress_pro_rich_snippets_lb_website'] : '',
           'telephone'            => isset($schemaManual['_seopress_pro_rich_snippets_lb_tel']) ? $schemaManual['_seopress_pro_rich_snippets_lb_tel'] : '',
           'priceRange'           => isset($schemaManual['_seopress_pro_rich_snippets_lb_price']) ? $schemaManual['_seopress_pro_rich_snippets_lb_price'] : '',
           'name'                 => isset($schemaManual['_seopress_pro_rich_snippets_lb_name']) ? $schemaManual['_seopress_pro_rich_snippets_lb_name'] : '%%sitetitle%%',
           'id'                   => '%%schema_article_canonical%%',
        ];

        if (in_array($type, $servesCuisine)) {
            $variables['servesCuisine'] = isset($schemaManual['_seopress_pro_rich_snippets_lb_cuisine']) ? $schemaManual['_seopress_pro_rich_snippets_lb_cuisine'] : '';
        }

        return $variables;
    }

    /**
     * @since 4.5.0
     *
     * @param array $context
     *
     * @return array
     */
    public function getJsonData($context = null) {
        $data = $this->getArrayJson();

        $typeSchema = isset($context['type']) ? $context['type'] : RichSnippetType::OPTION_LOCAL_BUSINESS;

        $variables    = [];
        $openingHours = [];
        switch ($typeSchema) {
            case RichSnippetType::OPTION_LOCAL_BUSINESS:
            default:
                $variables    = $this->getVariablesForOptionLocalBusiness();
                $openingHours = seopress_pro_get_service('OptionPro')->getLocalBusinessOpeningHours();
                break;
            case RichSnippetType::MANUAL:
                $schemaManual = $this->getCurrentSchemaManual($context);

                if (null === $schemaManual) {
                    return $data;
                }
                $variables    = $this->getVariablesForManualSnippet($schemaManual);
                $openingHours = isset($schemaManual['_seopress_pro_rich_snippets_lb_opening_hours']['seopress_local_business_opening_hours']) ? $schemaManual['_seopress_pro_rich_snippets_lb_opening_hours']['seopress_local_business_opening_hours'] : [];
                break;
            case RichSnippetType::SUB_TYPE:
                $variables = isset($context['variables']) ? $context['variables'] : [];
                break;
        }

        $data = seopress_get_service('VariablesToString')->replaceDataToString($data, $variables);

        $schema = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(PostalAddress::NAME, $context, ['remove_empty'=> true]);

        if (count($schema) > 1) {
            $data['address'] = $schema;
        }

        $schema = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(Geo::NAME, $context, ['remove_empty'=> true]);

        if (count($schema) > 1) {
            $data['geo'] = $schema;
        }

        if ( ! empty($openingHours)) {
            foreach ($openingHours as $key => $day) {
                if (isset($day['open']) && '1' === $day['open']) { // bad name => reality is closed
                    continue;
                }

                foreach ($day as $keyHalfDay => $halfDay) {
                    if ( ! isset($halfDay['open']) || '1' !== $halfDay['open']) {
                        continue;
                    }

                    $variables = [
                        'dayOfWeek' => $this->getDayByKey($key),
                        'opens'     => \sprintf('%s:%s:00', $halfDay['start']['hours'], $halfDay['start']['mins']),
                        'closes'    => \sprintf('%s:%s:00', $halfDay['end']['hours'], $halfDay['end']['mins']),
                    ];

                    $schema = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(OpeningHours::NAME, ['variables' => $variables], ['remove_empty'=> true]);
                    if (count($schema) > 1) {
                        $data['openingHoursSpecification'][] = $schema;
                    }
                }
            }
        }

        return apply_filters('seopress_pro_get_json_data_local_business', $data, $context);
    }
}
