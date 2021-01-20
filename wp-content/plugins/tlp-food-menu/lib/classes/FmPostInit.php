<?php

if (!class_exists('FmPostInit')):

    class FmPostInit
    {

        protected $version;

        public function __construct() {
            $this->version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : TLP_FOOD_MENU_VERSION;
            add_action('init', array($this, 'register'));
            add_action('admin_enqueue_scripts', array($this, 'register_frond_end_script'), 1);
            add_action('wp_enqueue_scripts', array($this, 'register_frond_end_script'), 1);
            add_action('wp_enqueue_scripts', array($this, 'load_frond_end_script'), 999);
            add_action('admin_init', array($this, 'register_admin_script'), 1);
            register_activation_hook(TLP_FOOD_MENU_PLUGIN_ACTIVE_FILE_NAME, array($this, 'activate'));
            register_deactivation_hook(TLP_FOOD_MENU_PLUGIN_ACTIVE_FILE_NAME, array($this, 'deactivate'));
        }

        public function register() {
            $this->register_post_type();
            $this->register_taxonomy_category();
            $this->register_shortcode_post_type();
        }

        public function activate() {
            flush_rewrite_rules();
            $this->dataInsert();
        }

        /**
         * Fired for each blog when the plugin is deactivated.
         *
         * @since 0.1.0
         */
        public function deactivate() {
            flush_rewrite_rules();
        }

        protected function register_post_type() {
            $labels = array(
                'name'               => __('Food Menu', 'tlp-food-menu'),
                'singular_name'      => __('Food Menu', 'tlp-food-menu'),
                'all_items'          => __('All Foods', 'tlp-food-menu'),
                'add_new'            => __('Add Food', 'tlp-food-menu'),
                'add_new_item'       => __('Add Food', 'tlp-food-menu'),
                'edit_item'          => __('Edit Food', 'tlp-food-menu'),
                'new_item'           => __('New Food', 'tlp-food-menu'),
                'view_item'          => __('View Food', 'tlp-food-menu'),
                'search_items'       => __('Search Food', 'tlp-food-menu'),
                'not_found'          => __('No Food found', 'tlp-food-menu'),
                'not_found_in_trash' => __('No Food in the trash', 'tlp-food-menu'),
            );
            $supports = array(
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'page-attributes'
            );
            $args = array(
                'labels'          => $labels,
                'supports'        => $supports,
                'public'          => true,
                'capability_type' => 'post',
                'rewrite'         => array('slug' => TLPFoodMenu()->getPostTypeSlug()),
                'menu_position'   => 20,
                'menu_icon'       => TLPFoodMenu()->getAssetsUrl() . 'images/icon-16x16.png',
            );
            register_post_type(TLPFoodMenu()->post_type, $args);

        }

        /**
         * Register a taxonomy for Team Categories.
         *
         * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
         */
        protected function register_taxonomy_category() {
            $labels = array(
                'name'                       => __('Food Categories', 'tlp-food-menu'),
                'singular_name'              => __('Food Category', 'tlp-food-menu'),
                'menu_name'                  => __('Categories', 'tlp-food-menu'),
                'edit_item'                  => __('Edit Category', 'tlp-food-menu'),
                'update_item'                => __('Update Category', 'tlp-food-menu'),
                'add_new_item'               => __('Add New Category', 'tlp-food-menu'),
                'new_item_name'              => __('New Category', 'tlp-food-menu'),
                'parent_item'                => __('Parent Category', 'tlp-food-menu'),
                'parent_item_colon'          => __('Parent Category:', 'tlp-food-menu'),
                'all_items'                  => __('All Categories', 'tlp-food-menu'),
                'search_items'               => __('Search Categories', 'tlp-food-menu'),
                'popular_items'              => __('Popular Categories', 'tlp-food-menu'),
                'separate_items_with_commas' => __('Separate categories with commas', 'tlp-food-menu'),
                'add_or_remove_items'        => __('Add or remove categories', 'tlp-food-menu'),
                'choose_from_most_used'      => __('Choose from the most used  categories', 'tlp-food-menu'),
                'not_found'                  => __('No categories found.', 'tlp-food-menu'),
            );
            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_in_nav_menus' => true,
                'show_ui'           => true,
                'show_tagcloud'     => true,
                'hierarchical'      => true,
                'rewrite'           => array('slug' => TLPFoodMenu()->taxonomies['category']),
                'show_admin_column' => true,
                'query_var'         => true,
            );
            register_taxonomy(TLPFoodMenu()->taxonomies['category'], TLPFoodMenu()->post_type, $args);
        }

        private function dataInsert() {
            update_option(TLPFoodMenu()->options['installed_version'], TLPFoodMenu()->options['version']);
        }

        private function register_shortcode_post_type() {

            $sc_args = array(
                'label'               => __('ShortCode', 'tlp-food-menu'),
                'description'         => __('Food ShortCode', 'tlp-food-menu'),
                'labels'              => array(
                    'all_items'          => __('ShortCode', 'tlp-food-menu'),
                    'menu_name'          => __('ShortCode', 'tlp-food-menu'),
                    'singular_name'      => __('ShortCode', 'tlp-food-menu'),
                    'edit_item'          => __('Edit ShortCode', 'tlp-food-menu'),
                    'new_item'           => __('New ShortCode', 'tlp-food-menu'),
                    'view_item'          => __('View ShortCode', 'tlp-food-menu'),
                    'search_items'       => __('ShortCode Locations', 'tlp-food-menu'),
                    'not_found'          => __('No ShortCode found.', 'tlp-food-menu'),
                    'not_found_in_trash' => __('No ShortCode found in trash.', 'tlp-food-menu')
                ),
                'supports'            => array('title'),
                'public'              => false,
                'rewrite'             => false,
                'show_ui'             => true,
                'show_in_menu'        => 'edit.php?post_type=' . TLPFoodMenu()->post_type,
                'show_in_admin_bar'   => true,
                'show_in_nav_menus'   => true,
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => false,
                'publicly_queryable'  => false,
                'capability_type'     => 'page',
            );
            register_post_type(TLPFoodMenu()->getShortCodePT(), apply_filters('rtfm-register-sc-args', $sc_args));

        }

        public function register_frond_end_script() {
            wp_register_script('fm-frontend', TLPFoodMenu()->getAssetsUrl() . 'js/tlpfoodmenu.js', array('jquery'), $this->version, true);
            wp_register_style('fm-frontend', TLPFoodMenu()->getAssetsUrl() . 'css/tlpfoodmenu.css', false, $this->version);
        }

        public function load_frond_end_script() {
            wp_enqueue_style('fm-frontend');
        }

        public function register_admin_script() {
            $this->register_frond_end_script();
            $scripts = array();
            $styles = array();
            $scripts['fm-select2'] = array(
                'src'    => TLPFoodMenu()->getAssetsUrl() . "vendor/select2/select2.min.js",
                'deps'   => array('jquery'),
                'footer' => false
            );
            $scripts['fm-admin'] = array(
                'src'    => TLPFoodMenu()->getAssetsUrl() . "js/settings.js",
                'deps'   => array('jquery'),
                'footer' => true
            );
            $styles['fm-select2'] = TLPFoodMenu()->getAssetsUrl() . 'vendor/select2/select2.min.css';
            $styles['fm-admin'] = TLPFoodMenu()->getAssetsUrl() . 'css/settings.css';


            foreach ($scripts as $handle => $script) {
                wp_register_script($handle, $script['src'], $script['deps'], $this->version, $script['footer']);
            }


            foreach ($styles as $k => $v) {
                wp_register_style($k, $v, false, $this->version);
            }
        }
    }

endif;
