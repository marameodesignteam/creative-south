<?php

namespace SEOPressPro\Models;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\JsonSchemaValue as JsonSchemaValueBase;

/**
 * @abstract
 */
abstract class JsonSchemaValue extends JsonSchemaValueBase {
    /**
     * @since 4.5.0
     *
     * @param string $file
     * @param mixed  $name
     *
     * @return string
     */
    public function getJson() {
        $file = apply_filters('seopress_get_json_from_file', sprintf('%s/%s.json', SEOPRESS_PRO_TEMPLATE_JSON_SCHEMAS, $this->getName(), '.json'));

        if ( ! file_exists($file)) {
            return '';
        }

        $json = file_get_contents($file);

        return $json;
    }

    /**
     * @since 4.6.0
     *
     * @param array $context
     *
     * @return array|null
     */
    public function getCurrentSchemaManual($context) {
        if ( ! seopress_get_service('CheckContextPage')->hasSchemaManualValues($context)) {
            return null;
        }

        return $context['schemas_manual'][$context['key_get_json_schema']];
    }
}
