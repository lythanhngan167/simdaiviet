<?php 
// Security check
defined('ABSPATH') || die();

if (!class_exists('RTOptimize')):

    class RTOptimize
    {

        /**
         * @description detecting option framework at the begining to decide the flow.
         * @values: 'Customizer' || 'Redux'
         * */
        public $option_framework = '';

        /**
         * @description Optimization configuration will go here.
        */
        public $config;

        /**
         * @description Options register for Optimization settings.
        */
        public $options;

        public function __construct()
        {
            $this->option_framework = defined('REDUX_PLUGIN_FILE') ? 'Redux' : 'Customizer';

            $this->config = RTOptimizeConfig::get_config();

            add_action( 'after_setup_theme', [&$this, 'add_options'], 9999999999999999999999999999999999 );
            
            $this->add_options();

        }

        public function add_options(){

            if($this->option_framework == 'Redux')
                $this->options = new RTRedux( $this->config );
            else
                $this->options = new RTCustomizer( $this->config );

        }

        
    }

    global $rt_optimize;

    $rt_optimize = new RTOptimize();

endif;
