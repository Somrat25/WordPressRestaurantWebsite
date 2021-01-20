<?php

if (!class_exists('FmSCMeta')):
    /**
     *
     */
    class FmSCMeta
    {

        function __construct() {
            add_action('add_meta_boxes', array($this, 'fm_sc_meta_boxes'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            add_action('save_post', array($this, 'save_post'), 10, 2);
            add_action('edit_form_after_title', array($this, 'fmp_sc_after_title'));
            add_action('admin_init', array($this, 'fm_pro_remove_all_meta_box'));
            add_filter('manage_edit-fmsc_columns', array($this, 'arrange_fm_sc_columns'));
            add_action('manage_fmsc_posts_custom_column', array($this, 'manage_fm_sc_columns'), 10, 2);
        }

        public function manage_fm_sc_columns($column) {
            switch ($column) {
                case 'fm_short_code':
                    echo '<input type="text" onfocus="this.select();" readonly="readonly" value="[foodmenu id=&quot;' . get_the_ID() . '&quot; title=&quot;' . get_the_title() . '&quot;]" class="large-text code rt-code-sc">';
                    break;
                default:
                    break;
            }
        }

        public function arrange_fm_sc_columns($columns) {
            $shortcode = array('fm_short_code' => __('Shortcode', 'food-menu-pro'));

            return array_slice($columns, 0, 2, true) + $shortcode + array_slice($columns, 1, null, true);
        }

        /**
         * This will add input text field for shortCode
         *
         * @param $post
         */
        function fmp_sc_after_title($post) {
            if (TLPFoodMenu()->getShortCodePT() !== $post->post_type) {
                return;
            }

            $html = null;
            $html .= '<div class="postbox" style="margin-bottom: 0;"><div class="inside">';
            $html .= '<p><input type="text" onfocus="this.select();" readonly="readonly" value="[foodmenu id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]" class="large-text code rt-code-sc">
            <input type="text" onfocus="this.select();" readonly="readonly" value="&#60;&#63;php echo do_shortcode( &#39;[foodmenu id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]&#39; ); &#63;&#62;" class="large-text code rt-code-sc">
            </p>';
            $html .= '</div></div>';
            echo $html;
        }

        function fm_pro_remove_all_meta_box() {
            if (is_admin()) {
                add_filter("get_user_option_meta-box-order_" . TLPFoodMenu()->getShortCodePT(),
                    array($this, 'remove_all_meta_boxes_fmp_sc'));
            }
        }


        /**
         * Add only custom meta box
         *
         * @return array
         */
        function remove_all_meta_boxes_fmp_sc() {
            global $wp_meta_boxes;
            $publishBox = $wp_meta_boxes[TLPFoodMenu()->getShortCodePT()]['side']['core']['submitdiv'];
            $scBox = $wp_meta_boxes[TLPFoodMenu()->getShortCodePT()]['normal']['high'][TLPFoodMenu()->getShortCodePT() . '_sc_settings_meta'];
            $previewBox = $wp_meta_boxes[TLPFoodMenu()->getShortCodePT()]['normal']['high'][TLPFoodMenu()->getShortCodePT() . '_sc_preview_meta'];
            $docBox = $wp_meta_boxes[TLPFoodMenu()->getShortCodePT()]['side']['low']['rt_plugin_sc_pro_information'];
            $wp_meta_boxes[TLPFoodMenu()->getShortCodePT()] = array(
                'side'   => array(
                    'core'    => array('submitdiv' => $publishBox),
                    'default' => [
                        'rt_plugin_sc_pro_information' => $docBox
                    ]
                ),
                'normal' => array(
                    'high' => array(
                        TLPFoodMenu()->getShortCodePT() . '_sc_settings_meta' => $scBox,
                        TLPFoodMenu()->getShortCodePT() . '_sc_preview_meta'  => $previewBox
                    )
                )
            );

            return array();
        }

        function admin_enqueue_scripts() {

            global $pagenow, $typenow;
            // validate page
            if (!in_array($pagenow, array('post.php', 'post-new.php', 'edit.php'))) {
                return;
            }
            if ($typenow != TLPFoodMenu()->getShortCodePT()) {
                return;
            }

            wp_enqueue_media();
            // scripts
            wp_enqueue_script(array(
                'jquery',
                'wp-color-picker',
                'fm-select2',
                'fm-frontend',
                'fm-admin'
            ));

            // styles
            wp_enqueue_style(array(
                'wp-color-picker',
                'fm-select2',
                'fm-frontend',
                'fm-admin'
            ));

            $nonce = wp_create_nonce(TLPFoodMenu()->nonceText());
            wp_localize_script('fm-admin', 'fm',
                array(
                    'nonceId' => TLPFoodMenu()->nonceId(),
                    'nonce'   => $nonce,
                    'ajaxurl' => admin_url('admin-ajax.php')
                ));


        }

        function fm_sc_meta_boxes() {
            add_meta_box(
                TLPFoodMenu()->getShortCodePT() . '_sc_settings_meta',
                __('Short Code Generator', 'tlp-food-menu'),
                array($this, 'fm_sc_settings_selection'),
                TLPFoodMenu()->getShortCodePT(),
                'normal',
                'high');
            add_meta_box(
                TLPFoodMenu()->getShortCodePT() . '_sc_preview_meta',
                __('Layout Preview', 'tlp-food-menu'),
                array($this, 'fm_sc_preview_selection'),
                TLPFoodMenu()->getShortCodePT(),
                'normal',
                'high');
            add_meta_box(
                'rt_plugin_sc_pro_information',
                __('Documentation', 'tlp-food-menu'),
                array($this, 'rt_plugin_sc_pro_information'),
                TLPFoodMenu()->getShortCodePT(),
                'side',
                'low'
            );
        }

        function rt_plugin_sc_pro_information($post) {

            $html = '';
            if ($post === 'settings') {
                $html .= '<div class="rt-document-box rt-update-pro-btn-wrap">
                <a href="https://www.radiustheme.com/downloads/food-menu-pro-wordpress/" target="_blank" class="rt-update-pro-btn">Update Pro To Get More Features</a>
            </div>';
            } else {
                $html .= sprintf('<div class="rt-document-box"><div class="rt-box-icon"><i class="dashicons dashicons-megaphone"></i></div><div class="rt-box-content"><h3 class="rt-box-title">Pro Features</h3>%s</div></div>', TLPFoodMenu()->get_pro_feature_list());
            }
            $html .= sprintf('<div class="rt-document-box">
							<div class="rt-box-icon"><i class="dashicons dashicons-media-document"></i></div>
							<div class="rt-box-content">
                    			<h3 class="rt-box-title">%1$s</h3>
                    				<p>%2$s</p>
                        			<a href="https://radiustheme.com/how-to-setup-and-configure-tlp-food-menu-free-version-for-wordpress/" target="_blank" class="rt-admin-btn">%1$s</a>
                			</div>
						</div>',
                __("Documentation", 'tlp-food-menu'),
                __("Get started by spending some time with the documentation we included step by step process with screenshots with video.", 'tlp-food-menu')
            );

            $html .= '<div class="rt-document-box">
							<div class="rt-box-icon"><i class="dashicons dashicons-sos"></i></div>
							<div class="rt-box-content">
                    			<h3 class="rt-box-title">Need Help?</h3>
                    				<p>Stuck with something? Please create a 
                        <a href="https://www.radiustheme.com/contact/">ticket here</a> or post on <a href="https://www.facebook.com/groups/234799147426640/">facebook group</a>. For emergency case join our <a href="https://www.radiustheme.com/">live chat</a>.</p>
                        			<a href="https://www.radiustheme.com/contact/" target="_blank" class="rt-admin-btn">Get Support</a>
                			</div>
						</div>';

            echo $html;
        }

        /**
         *  Preview section
         */
        function fm_sc_preview_selection() {
            $html = null;
            $html .= "<div class='fmp-response'><span class='spinner'></span></div>";
            $html .= "<div id='fmp-preview-container'>";
            $html .= "</div>";

            echo $html;

        }

        /**
         * Setting Sections
         *
         * @param $post
         */
        function fm_sc_settings_selection($post) {
            wp_nonce_field(TLPFoodMenu()->nonceText(), TLPFoodMenu()->nonceId());
            $html = null;
            $html .= '<div class="rt-tab-container">';
            $html .= '<ul class="rt-tab-nav">
	                            <li><a href="#sc-fm-layout"><i class="dashicons dashicons-layout"></i>' . __('Layout', 'food-menu-pro') . '</a></li>
	                            <li><a href="#sc-fm-filter"><i class="dashicons dashicons-filter"></i>' . __('Filtering', 'food-menu-pro') . '</a></li>
	                            <li><a href="#sc-fm-field-selection"><i class="dashicons dashicons-editor-table"></i>' . __('Field selection', 'food-menu-pro') . '</a></li>
	                            <li><a href="#sc-fm-style"><i class="dashicons dashicons-admin-customizer"></i>' . __('Styling', 'food-menu-pro') . '</a></li>
	                          </ul>';
            $html .= sprintf('<div id="sc-fm-layout" class="rt-tab-content">%s</div>', TLPFoodMenu()->rtFieldGenerator(TLPFoodMenu()->scLayoutMetaFields()));
            $html .= sprintf('<div id="sc-fm-filter" class="rt-tab-content">%s</div>', TLPFoodMenu()->rtFieldGenerator(TLPFoodMenu()->scFilterMetaFields()));
            $html .= sprintf('<div id="sc-fm-field-selection" class="rt-tab-content">%s</div>', TLPFoodMenu()->rtFieldGenerator(TLPFoodMenu()->scItemFields()));
            $html .= sprintf('<div id="sc-fm-style" class="rt-tab-content">%s</div>', TLPFoodMenu()->rtFieldGenerator(TLPFoodMenu()->scStyleFields()));
            $html .= '</div>';

            echo $html;
        }


        function save_post($post_id, $post) {


            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }
            if (!TLPFoodMenu()->verifyNonce()) {
                return $post_id;
            }
            if (TLPFoodMenu()->getShortCodePT() != $post->post_type) {
                return $post_id;
            }
            $mates = TLPFoodMenu()->fmpScMetaFields();
            foreach ($mates as $metaKey => $field) {
                $rValue = !empty($_REQUEST[$metaKey]) ? $_REQUEST[$metaKey] : null;
                $value = TLPFoodMenu()->sanitize($field, $rValue);
                if (empty($field['multiple'])) {
                    update_post_meta($post_id, $metaKey, $value);
                } else {
                    delete_post_meta($post_id, $metaKey);
                    if (is_array($value) && !empty($value)) {
                        foreach ($value as $item) {
                            add_post_meta($post_id, $metaKey, $item);
                        }
                    } else {
                        update_post_meta($post_id, $metaKey, "");
                    }
                }
            }

        }
    }
endif;