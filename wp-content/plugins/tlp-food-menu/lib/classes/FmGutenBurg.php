<?php
if (!defined('WPINC')) {
    die;
}

if (!class_exists('FmGutenBurg')):

    class FmGutenBurg
    {
        protected $version;

        function __construct() {
            $this->version = (defined('WP_DEBUG') && WP_DEBUG) || (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? time() : TLP_FOOD_MENU_VERSION;
            add_action('enqueue_block_assets', array($this, 'block_assets'));
            add_action('enqueue_block_editor_assets', array($this, 'block_editor_assets'));
            if (function_exists('register_block_type')) {
                register_block_type('radiustheme/tlp-food-menu', array(
                    'render_callback' => array($this, 'render_shortcode_old'),
                ));
                register_block_type('rttpg/food-menu-pro', array(
                    'render_callback' => array($this, 'render_shortcode'),
                ));
            }
        }

        static function render_shortcode_old($atts) {
            $shortcode = '[foodmenu';
            if (isset($atts['column']) && !empty($atts['column']) && $col = absint($atts['column'])) {
                $shortcode .= ' col="' . $col . '"';
            }
            if (isset($atts['orderby']) && !empty($atts['orderby'])) {
                $shortcode .= ' orderby="' . $atts['orderby'] . '"';
            }
            if (isset($atts['order']) && !empty($atts['order'])) {
                $shortcode .= ' order="' . $atts['order'] . '"';
            }
            if (isset($atts['cats']) && !empty($atts['cats']) && is_array($atts['cats'])) {
                $cats = array_filter($atts['cats']);
                if (!empty($cats)) {
                    $shortcode .= ' cat="' . implode(',', $cats) . '"';
                }
            }
            if (isset($atts['isImageHide']) && !empty($atts['isImageHide'])) {
                $shortcode .= ' hide-img="1"';
            }
            if (isset($atts['isLinkDisabled']) && !empty($atts['isLinkDisabled'])) {
                $shortcode .= ' disable-link="1"';
            }
            if (isset($atts['titleColor']) && !empty($atts['titleColor'])) {
                $shortcode .= ' title-color="' . $atts['titleColor'] . '"';
            }
            if (isset($atts['wrapperClass']) && !empty($atts['wrapperClass'])) {
                $shortcode .= ' class="' . $atts['wrapperClass'] . '"';
            }
            $shortcode .= ']';

            return do_shortcode($shortcode);
        }

        static function render_shortcode($atts) {
            if (!empty($atts['gridId']) && $id = absint($atts['gridId'])) {
                return do_shortcode('[foodmenu id="' . $id . '"]');
            }
        }


        function block_assets() {
            wp_enqueue_style('wp-blocks');
        }

        function block_editor_assets() {
            wp_enqueue_script(
                'rt-tlp-food-menu-gb-block-js',
                TLPFoodMenu()->getAssetsUrl() . "js/tlp-food-menu-blocks.min.js",
                array('wp-blocks', 'wp-i18n', 'wp-element'),
                $this->version,
                true
            );

            // Enqueue block editor styles
            wp_enqueue_style(
                'rt-tlp-food-menu-gb-block-editor',
                TLPFoodMenu()->getAssetsUrl() . 'css/tlp-food-menu-blocks-editor.min.css',
                ['wp-edit-blocks'],
                $this->version
            );

            wp_localize_script('rt-tlp-food-menu-gb-block-js', 'tlpFoodMenu', array(
                'column'      => TLPFoodMenu()->scColumns(),
                'orderby'     => TLPFoodMenu()->scOrderBy(),
                'order'       => TLPFoodMenu()->scOrder(),
                'cats'        => TLPFoodMenu()->getAllFmpCategoryList(),
                'icon'        => TLPFoodMenu()->getAssetsUrl() . 'images/icon-20x20.png',
                'short_codes' => TLPFoodMenu()->get_shortCode_list()
            ));
        }
    }

endif;