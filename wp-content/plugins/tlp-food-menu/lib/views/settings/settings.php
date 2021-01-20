<?php
$settings = get_option(TLPFoodMenu()->options['settings']);
?>
<div class="wrap">
    <h2><?php esc_html_e('TLP Food Menu Settings', 'tlp-food-menu'); ?></h2>
    <div class="rt-settings-container">
        <div class="rt-setting-title">
            <h3><?php esc_html_e('General settings', "testimonial-slider-showcase-pro") ?></h3>
        </div>
        <div class="rt-setting-content">
            <form id="fmp-settings-form">
                <div class="rt-tab-container">
                    <ul class="rt-tab-nav">
                        <li><a href="#general"><i class="dashicons dashicons-admin-settings"></i><?php esc_html_e('General', 'tlp-food-menu'); ?>
                            </a></li>
                        <li><a href="#others"><i class="dashicons dashicons-admin-appearance"></i><?php esc_html_e('Others', 'tlp-food-menu'); ?>
                            </a></li>
                        <li><a href="#promotions"><i class="dashicons dashicons-megaphone"></i><?php esc_html_e('Theme & Plugins (Pro)', 'tlp-food-menu'); ?>
                            </a>
                        </li>
                    </ul>
                    <div id="general" class="rt-tab-content">
                        <?php
                        TLPFoodMenu()->view('settings.general', array('general' => isset($settings['general']) ? ($settings['general'] ? $settings['general'] : array()) : array()));
                        ?>
                    </div>
                    <div id="others" class="rt-tab-content">
                        <?php
                        TLPFoodMenu()->view('settings.others', array('others' => isset($settings['others']) ? ($settings['others'] ? $settings['others'] : array()) : array()));
                        ?>
                    </div>
                    <div id="promotions" class="rt-tab-content">
                        <?php TLPFoodMenu()->view('settings.promotions'); ?>
                    </div>
                </div>
                <p class="submit"><input type="submit" name="submit" id="tlpSaveButton"
                                         class="rt-admin-btn button button-primary"
                                         value="<?php esc_html_e('Save Changes', 'tlp-food-menu'); ?>"></p>

                <?php wp_nonce_field(TLPFoodMenu()->nonceText(), TLPFoodMenu()->nonceId()); ?>
            </form>
            <div class="rt-response"></div>
        </div>
        <div class="rt-pro-feature-content">
            <?php TLPFoodMenu()->rt_plugin_sc_pro_information('settings'); ?>
        </div>
    </div>
</div>