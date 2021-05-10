<?php

namespace SEOPressPro\JsonSchemas;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Helpers\RichSnippetType;
use SEOPress\JsonSchemas\Image;
use SEOPress\JsonSchemas\Organization;
use SEOPress\Models\GetJsonData;
use SEOPressPro\Models\JsonSchemaValue;

class Video extends JsonSchemaValue implements GetJsonData {
    const NAME = 'video';

    const ALIAS = ['videos'];

    protected function getName() {
        return self::NAME;
    }

    /**
     * @since 4.6.0
     *
     * @return array
     *
     * @param array $schemaManual
     */
    protected function getVariablesForManualSnippet($schemaManual) {
        $keys = [
            'type'                      => '_seopress_pro_rich_snippets_type',
            'name'                      => '_seopress_pro_rich_snippets_videos_name',
            'description'               => '_seopress_pro_rich_snippets_videos_description',
            'thumbnailUrl'              => '_seopress_pro_rich_snippets_videos_img',
            'imgWidth'                  => '_seopress_pro_rich_snippets_videos_img_width',
            'imgHeight'                 => '_seopress_pro_rich_snippets_videos_img_height',
            'duration'                  => '_seopress_pro_rich_snippets_videos_duration',
            'url'                       => '_seopress_pro_rich_snippets_videos_url',
        ];
        $variables = [];

        foreach ($keys as $key => $value) {
            $variables[$key] = isset($schemaManual[$value]) ? $schemaManual[$value] : '';
        }

        return $variables;
    }

    /**
     * @since 4.6.0
     *
     * @param array $context
     *
     * @return array
     */
    public function getJsonData($context = null) {
        $data = $this->getArrayJson();

        $typeSchema = isset($context['type']) ? $context['type'] : RichSnippetType::MANUAL;

        $variables = [];

        switch ($typeSchema) {
            case RichSnippetType::MANUAL:
                $schemaManual = $this->getCurrentSchemaManual($context);

                if (null === $schemaManual) {
                    return $data;
                }

                $variables = $this->getVariablesForManualSnippet($schemaManual);
                break;
        }

        if (isset($context['post']->ID)) {
            $variables['uploadDate'] = get_the_date('c', $context['post']->ID);
        }

        if (isset($variables['duration']) && ! empty($variables['duration'])) {
            $time   = explode(':', $variables['duration']);
            $sec 	  = isset($time[2]) ? $time[2] : 00;
            $min 	  = isset($time[0]) && isset($time[1]) ? $time[0] * 60.0 + $time[1] * 1.0 : $_seopress_pro_rich_snippets_videos_duration;

            $variables['duration'] = sprintf('PT%sM%sS', $min, $sec);
        }

        if (isset($variables['url'])) {
            $variables['contentUrl'] = $variables['embedUrl'] = $variables['url'];
        }

        $publisher  = seopress_get_service('SocialOption')->getSocialKnowledgeName();

        if ( ! empty($publisher)) {
            $variablesSchema = [
                'type'    => 'Organization',
                'name'    => $publisher,
            ];
            $contextWithVariables              = $context;
            $contextWithVariables['variables'] = $variablesSchema;
            $contextWithVariables['type']      = RichSnippetType::SUB_TYPE;
            $schema                            = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(Organization::NAME, $contextWithVariables, ['remove_empty'=> true]);
            if (count($schema) > 1) {
                $data['publisher']                 = $schema;
                $contextWithVariables['variables'] = [
                    'url'    => seopress_get_service('SocialOption')->getSocialKnowledgeImage(),
                ];
                $schema                            = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(Image::NAME, $contextWithVariables, ['remove_empty'=> true]);
                if (count($schema) > 1) {
                    $data['publisher']['logo'] = $schema;
                }
            }
        }

        $data = seopress_get_service('VariablesToString')->replaceDataToString($data, $variables);

        return apply_filters('seopress_pro_get_json_data_video', $data, $context);
    }
}
