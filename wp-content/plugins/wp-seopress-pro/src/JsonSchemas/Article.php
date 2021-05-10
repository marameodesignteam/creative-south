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

class Article extends JsonSchemaValue implements GetJsonData {
    const NAME = 'article';

    const ALIAS = ['articles'];

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
            'type'              => '_seopress_pro_rich_snippets_type',
            'articleType'       => '_seopress_pro_rich_snippets_article_type',
            'title'             => '_seopress_pro_rich_snippets_article_title',
            'image'             => '_seopress_pro_rich_snippets_article_img',
            'imageWidth'        => '_seopress_pro_rich_snippets_article_img_width',
            'imageHeight'       => '_seopress_pro_rich_snippets_article_img_height',
            'coverageStartDate' => '_seopress_pro_rich_snippets_article_coverage_start_date',
            'coverageStartTime' => '_seopress_pro_rich_snippets_article_coverage_start_time',
            'coverageEndDate'   => '_seopress_pro_rich_snippets_article_coverage_end_date',
            'coverageEndTime'   => '_seopress_pro_rich_snippets_article_coverage_end_time',
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

        if (isset($variables['image']) && ! empty($variables['image'])) {
            $variablesContext = [
                'url'    => $variables['image'],
                'width'  => isset($variables['imageWidth']) ? $variables['imageWidth'] : '',
                'height' => isset($variables['imageHeight']) ? $variables['imageHeight'] : '',
            ];

            $contextWithVariables              = $context;
            $contextWithVariables['variables'] = $variablesContext;
            $schema                            = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(Image::NAME, $contextWithVariables, ['remove_empty'=> true]);
            if (count($schema) > 1) {
                $data['image'] = $schema;
            }
        } else {
            $variablesContext = [
                'url'    => '%%post_thumbnail_url%%',
                'width'  => '%%post_thumbnail_url_width%%',
                'height' => '%%post_thumbnail_url_height%%',
            ];

            $contextWithVariables              = $context;
            $contextWithVariables['variables'] = $variablesContext;
            $schema                            = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(Image::NAME, $contextWithVariables, ['remove_empty'=> true]);
            if (count($schema) > 1) {
                $data['image'] = $schema;
            }
        }

        $schema = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(MainEntityOfPage::NAME, $context, ['remove_empty'=> true]);

        if (count($schema) > 1) {
            $data['mainEntityOfPage'] = $schema;
        }

        $schema = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(Author::NAME, $context, ['remove_empty'=> true]);

        if (count($schema) > 1) {
            $data['author'] = $schema;
        }

        $schema = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(Organization::NAME, $context, ['remove_empty'=> true]);
        if (count($schema) > 1 && isset($schema['@type']) && 'none' !== $schema['@type']) {
            $data['publisher'] = $schema;

            $logo = seopress_pro_get_service('OptionPro')->getArticlesPublisherLogo();
            if ( ! empty($logo)) {
                $variablesContext = [
                    'url'    => $logo,
                    'width'  => seopress_pro_get_service('OptionPro')->getArticlesPublisherLogoWidth(),
                    'height' => seopress_pro_get_service('OptionPro')->getArticlesPublisherLogoHeight(),
                ];
                $contextWithVariables              = $context;
                $contextWithVariables['variables'] = $variablesContext;

                $schema                            = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(Image::NAME, $contextWithVariables, ['remove_empty'=> true]);
                if (count($schema) > 1) {
                    $data['publisher']['logo'] = $schema;
                }
            }
        }

        if (isset($variables['articleType']) && 'LiveBlogPosting' === $variables['articleType']) {
            if (
                isset($variables['coverageStartDate']) && ! empty($variables['coverageStartDate']) &&
                isset($variables['coverageStartTime']) && ! empty($variables['coverageStartTime'])
            ) {
                $data['coverageStartTime'] = sprintf('%sT%s', $variables['coverageStartDate'], $variables['coverageStartTime']);
            }
            if (
                isset($variables['coverageEndDate']) && ! empty($variables['coverageEndDate']) &&
                isset($variables['coverageEndTime']) && ! empty($variables['coverageEndTime'])
            ) {
                $data['coverageEndTime'] = sprintf('%sT%s', $variables['coverageEndDate'], $variables['coverageEndTime']);
            }
        } elseif (isset($variables['articleType']) && 'ReviewNewsArticle' === $variables['articleType']) {
            $schema                            = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(Thing::NAME, $context, ['remove_empty'=> true]);
            if (count($schema) > 1) {
                $data['itemReviewed']= $schema;
            }
        }

        return apply_filters('seopress_pro_get_json_data_article', $data, $context);
    }

    /**
     * @since 4.5.0
     *
     * @param  $data
     *
     * @return array
     */
    public function cleanValues($data) {
        if (isset($data['publisher'])) {
            $unsetKeys = ['sameAs', 'logo', 'url', 'contactPoint'];

            foreach ($unsetKeys as $key => $value) {
                if (isset($data[$value])) {
                    unset($data[$value]);
                }
            }
        }

        if (isset($data['publisher']['@context'])) {
            unset($data['publisher']['@context']);
        }

        return parent::cleanValues($data);
    }
}
