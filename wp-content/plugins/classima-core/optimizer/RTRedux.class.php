<?php 

// Security check
defined('ABSPATH') || die();

class RTRedux extends RTOptimizerHooks implements RTOptionFramework{

    public $config;
    protected $redux_opt_name;

    public function __construct($config){

        $this->config = $config;
        
        add_action( 'rt_after_redux_options_loaded', [&$this, 'register'] );

    }

    public function get_option($id){
        
        global $classima;
        
        $options = &$classima;

        if( isset($options[$id]) && !empty($options[$id]) )
            return $options[$id];
        else return '';

    }

    public function register($opt_name){

        $this->redux_opt_name = $opt_name; 

        // Section
        foreach($this->config['sections'] as $section){
            
            Redux::setSection( $this->redux_opt_name, [
                'id' => $section['id'],
                'title' => __($section['title'], $this->config['TextDomain']),
                'icon' => $section['icon'] ?? 'el el-cogs',
            ] );

            // Sub Section
            foreach($section['sub_sections'] as $sub_section){

                Redux::setSection( $this->redux_opt_name, [
                    'id' => $sub_section['id'],
                    'title' => __($sub_section['title'], $this->config['TextDomain']),
                    'icon' => $sub_section['icon'] ?? 'el el-cog',
                    'subsection' => true,
                    'fields' => $sub_section['fields']
                ] );

            }

        }

    }

}