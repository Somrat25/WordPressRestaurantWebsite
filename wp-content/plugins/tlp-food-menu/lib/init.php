<?php

if (!class_exists('TLPFoodMenu')) {

    class TLPFoodMenu
    {

        public $post_type;
        public $taxonomies;
        public $options;
        private $shortCodePT;
        private $templatesPath;
        private $incPath;
        private $functionsPath;
        private $classesPath;
        private $post_type_slug;
        private $widgetsPath;
        private $viewsPath;
        private $assetsUrl;
        private $modelsPath;

        protected static $_instance;

        public static function instance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        function __construct() {
            $this->options = array(
                'settings'          => 'tpl_food_menu_settings',
                'version'           => TLP_FOOD_MENU_VERSION,
                'title'             => 'Food Menu',
                'slug'              => 'tlp-food-menu',
                'installed_version' => 'tlp-food-menu-installed-version'
            );

            $settings = get_option($this->options['settings']);
            $this->post_type = "food-menu";
            $this->shortCodePT = "fmsc";
            $this->post_type_slug = isset($settings['general']['slug']) ? ($settings['general']['slug'] ? sanitize_title_with_dashes($settings['general']['slug']) : 'food-menu') : 'food-menu';
            $this->taxonomies = array('category' => $this->post_type . '-category');

            $this->incPath = dirname(__FILE__);
            $this->functionsPath = $this->incPath . '/functions/';
            $this->classesPath = $this->incPath . '/classes/';
            $this->widgetsPath = $this->incPath . '/widgets/';
            $this->modelsPath = $this->incPath . '/models/';
            $this->viewsPath = $this->incPath . '/views/';
            $this->templatesPath = $this->incPath . '/templates/';
            $this->assetsUrl = TLP_FOOD_MENU_PLUGIN_URL . '/assets/';
            $this->fmpLoadModel($this->modelsPath);
            $this->TPLloadFunctions($this->functionsPath);
            $this->TPLloadClass($this->classesPath);

        }

        function verifyNonce() {
            $nonce = isset($_REQUEST[$this->nonceId()]) && !empty($_REQUEST[$this->nonceId()]) ? $_REQUEST[$this->nonceId()] : null;
            if (!wp_verify_nonce($nonce, $this->nonceText())) {
                return false;
            }

            return true;
        }

        function nonceId() {
            return "tlp_fm_nonce";
        }

        function nonceText() {
            return "tlp_food_menu_nonce";
        }

        function TPLloadClass($dir) {
            if (!file_exists($dir)) {
                return;
            }

            $classes = array();

            foreach (scandir($dir) as $item) {
                if (preg_match("/.php$/i", $item)) {
                    require_once($dir . $item);
                    $className = str_replace(".php", "", $item);
                    $classes[] = new $className;
                }
            }

            if ($classes) {
                foreach ($classes as $class) {
                    $this->objects[] = $class;
                }
            }
        }


        /**
         * Load Model class
         *
         * @param $dir
         */
        function fmpLoadModel($dir) {
            if (!file_exists($dir)) {
                return;
            }

            foreach (scandir($dir) as $item) {
                if (preg_match("/.php$/i", $item)) {
                    require_once($dir . $item);
                }
            }
        }

        function loadWidget($dir) {
            if (!file_exists($dir)) {
                return;
            }
            foreach (scandir($dir) as $item) {
                if (preg_match("/.php$/i", $item)) {
                    require_once($dir . $item);
                    $class = str_replace(".php", "", $item);

                    if (method_exists($class, 'register_widget')) {
                        $caller = new $class;
                        $caller->register_widget();
                    } else {
                        register_widget($class);
                    }
                }
            }
        }

        function TPLloadFunctions($dir) {
            if (!file_exists($dir)) {
                return;
            }

            foreach (scandir($dir) as $item) {
                if (preg_match("/.php$/i", $item)) {
                    require_once($dir . $item);
                }
            }

        }

        /**
         * @param       $viewName
         * @param array $args
         * @param bool  $return
         *
         * @return string|void
         */
        function render($viewName, $args = array(), $return = false) {

            $path = str_replace(".", "/", $viewName);
            if ($args) {
                extract($args);
            }
            $template = array(
                "tlp-food-menu/{$path}.php",
                $path . ".php"
            );

            if (!$template_file = locate_template($template)) {
                $template_file = $this->templatesPath . $viewName . '.php';
            }
            if (!file_exists($template_file)) {
                return;
            }
            if ($return) {
                ob_start();
                include $template_file;

                return ob_get_clean();
            } else {
                include $template_file;
            }
        }


        /**
         * @param       $viewName
         * @param array $args
         * @param bool  $return
         *
         * @return string|void
         */
        function view($viewName, $args = array(), $return = false) {
            $path = str_replace(".", "/", $viewName);
            $viewPath = $this->viewsPath . $path . '.php';
            if (!file_exists($viewPath)) {
                return;
            }
            if ($args) {
                extract($args);
            }
            if ($return) {
                ob_start();
                include $viewPath;

                return ob_get_clean();
            }
            include $viewPath;
        }

        /**
         * Dynamicaly call any  method from models class
         * by pluginFramework instance
         */
        function __call($name, $args) {
            if (!is_array($this->objects)) {
                return;
            }
            foreach ($this->objects as $object) {
                if (method_exists($object, $name)) {
                    $count = count($args);
                    if ($count == 0) {
                        return $object->$name();
                    } elseif ($count == 1) {
                        return $object->$name($args[0]);
                    } elseif ($count == 2) {
                        return $object->$name($args[0], $args[1]);
                    } elseif ($count == 3) {
                        return $object->$name($args[0], $args[1], $args[2]);
                    } elseif ($count == 4) {
                        return $object->$name($args[0], $args[1], $args[2], $args[3]);
                    } elseif ($count == 5) {
                        return $object->$name($args[0], $args[1], $args[2], $args[3], $args[4]);
                    } elseif ($count == 6) {
                        return $object->$name($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
                    }
                }
            }
        }


        /**
         * @return string
         */
        public function getShortCodePT() {
            return $this->shortCodePT;
        }

        /**
         * @return string
         */
        public function getPostTypeSlug() {
            return $this->post_type_slug;
        }

        /**
         * @return string
         */
        public function getAssetsUrl() {
            return $this->assetsUrl;
        }

        /**
         * @return string
         */
        public function getTemplatesPath() {
            return $this->templatesPath;
        }

        /**
         * @return string
         */
        public function getWidgetsPath() {
            return $this->widgetsPath;
        }

        /**
         * @return string
         */
        public function getIncPath() {
            return $this->incPath;
        }
    }

    function TLPFoodMenu() {
        return TLPFoodMenu::instance();
    }

    TLPFoodMenu();
}
