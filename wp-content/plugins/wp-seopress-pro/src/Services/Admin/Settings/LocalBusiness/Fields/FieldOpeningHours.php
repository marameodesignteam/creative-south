<?php

namespace SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Helpers\OpeningHoursHelper;

trait FieldOpeningHours {
    /**
     * @since 4.5.0
     *
     * @return void
     */
    public function renderFieldOpeningHours() {
        $options = seopress_pro_get_service('OptionPro')->getLocalBusinessOpeningHours();

        $options = seopress_pro_get_service('TransformOldOpeningHours')->transform($options);

        $days  = OpeningHoursHelper::getDays();
        $hours = OpeningHoursHelper::getHours();
        $mins  = OpeningHoursHelper::getMinutes();

        $halfDay = ['am', 'pm']; ?>
        <ul class="wrap-opening-hours">
        <?php
        foreach ($days as $key => $day) {
            $closedAllDay = isset($options[$key]['open']) ? $options[$key]['open'] : 0; ?>
            <li style="margin-bottom:20px;">
                <div class="day" style="margin-bottom:10px; border-bottom:1px solid #ccd0d4; padding-bottom:5px;">
                    <strong><?php echo $day; ?></strong>
                </div>
                <input
                    id="seopress_local_business_opening_hours[<?php echo $key; ?>][open]"
                    name="seopress_local_business_opening_hours[<?php echo $key; ?>][open]"
                    type="checkbox"
                    <?php checked($closedAllDay, '1'); ?>
                     value="1"/>

                <label for="seopress_local_business_opening_hours[<?php echo $key; ?>][open]">
                    <?php echo __('Closed all the day?', 'wp-seopress-pro'); ?>
                </label>
                <?php foreach ($halfDay as $valueHalfDay) {
                $open = isset($options[$key][$valueHalfDay]['open']) ? $options[$key][$valueHalfDay]['open'] : 0;

                $startHours = isset($options[$key][$valueHalfDay]['start']['hours']) ? $options[$key][$valueHalfDay]['start']['hours'] : '00';
                $endHours   = isset($options[$key][$valueHalfDay]['end']['hours']) ? $options[$key][$valueHalfDay]['end']['hours'] : '00';
                $startMins  = isset($options[$key][$valueHalfDay]['start']['mins']) ? $options[$key][$valueHalfDay]['start']['mins'] : '00';
                $endMins    = isset($options[$key][$valueHalfDay]['end']['mins']) ? $options[$key][$valueHalfDay]['end']['mins'] : '00'; ?>
                    <div style="display:flex; align-items:center; margin-top: 10px;">
                        <input
                            id="seopress_local_business_opening_hours[<?php echo $key; ?>][<?php echo $valueHalfDay; ?>][open]"
                            name="seopress_local_business_opening_hours[<?php echo $key; ?>][<?php echo $valueHalfDay; ?>][open]"
                            type="checkbox"
                            <?php checked($open, '1'); ?>
                            value="1"
                        />

                        <label for="seopress_local_business_opening_hours[<?php echo $key; ?>][<?php echo $valueHalfDay; ?>][open]" style="margin-right:20px;">
                            <?php if ('am' === $valueHalfDay) { ?>

                                <?php echo __('Open in the morning?', 'wp-seopress-pro'); ?>
                            <?php } else { ?>
                                <?php echo __('Open in the afternoon?', 'wp-seopress-pro'); ?>
                            <?php } ?>
                        </label>
                        <select
                            id="seopress_local_business_opening_hours[<?php echo $key; ?>][<?php echo $valueHalfDay; ?>][start][hours]"
                            name="seopress_local_business_opening_hours[<?php echo $key; ?>][<?php echo $valueHalfDay; ?>][start][hours]"
                        >
                            <?php foreach ($hours as $hour) { ?>
                                <option <?php selected($hour, $startHours); ?>  value="<?php echo $hour; ?>">
                                    <?php echo $hour; ?>
                                </option>
                            <?php } ?>

                        </select>
                        <span style="margin-left:3px; margin-right:3px;">:</span>
                        <select
                            id="seopress_local_business_opening_hours[<?php echo $key; ?>][<?php echo $valueHalfDay; ?>][start][mins]"
                            name="seopress_local_business_opening_hours[<?php echo $key; ?>][<?php echo $valueHalfDay; ?>][start][mins]">

                            <?php foreach ($mins as $min) { ?>
                                <option <?php selected($min, $startMins); ?> value="<?php echo $min; ?>">
                                    <?php echo $min; ?>
                                </option>
                            <?php } ?>

                        </select>
                        <span style="margin-left:3px; margin-right:3px;">-</span>
                        <select
                            id="seopress_local_business_opening_hours[<?php echo $key; ?>][<?php echo $valueHalfDay; ?>][end][hours]"
                            name="seopress_local_business_opening_hours[<?php echo $key; ?>][<?php echo $valueHalfDay; ?>][end][hours]">

                        <?php foreach ($hours as $hour) { ?>
                            <option <?php selected($hour, $endHours); ?> value="<?php echo $hour; ?>">
                                <?php echo $hour; ?>
                            </option>
                        <?php } ?>

                        </select>
                        <span style="margin-left:3px; margin-right:3px;">:</span>
                        <select
                            id="seopress_local_business_opening_hours[<?php echo $key; ?>][<?php echo $valueHalfDay; ?>][end][mins]"
                            name="seopress_local_business_opening_hours[<?php echo $key; ?>][<?php echo $valueHalfDay; ?>][end][mins]">

                            <?php foreach ($mins as $min) { ?>
                                <option <?php selected($min, $endMins); ?> value="<?php echo $min; ?>">
                                    <?php echo $min; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                <?php
            } ?>

            </li>
        <?php
        } ?>
        </ul>

        <p class="description"><?php _e('<strong>Recommended</strong> property by Google.', 'wp-seopress-pro'); ?></p>

        <?php
    }
}
