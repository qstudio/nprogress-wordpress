<?php

/* 
 * Plugin Name:     NProgress
 * Description:     Include NProgress in WordPress: http://ricostacruz.com/nprogress/
 * Version:         0.1
 * Author:          Q Studio
 * Author URI:      http://www.qstudio.us
 * Class:           Q_NProgress
 * Text-Domain      q-nprogress
*/

/**
 * NProgress: 
 * 
 * http://ricostacruz.com/nprogress/
 * https://github.com/rstacruz/nprogress
 */


// quick check :) ##
defined( 'ABSPATH' ) OR exit;

/* Check for Class */
if ( ! class_exists( 'Q_NProgress' ) ) 
{

    // instatiate plugin via WP hook - not too early, not too late ##
    add_action( 'after_setup_theme', array ( 'Q_NProgress', 'get_instance' ), 0 );
    
    // define constants ##
    define( 'Q_NPROGRESS_VERSION', '0.1' );

    // on plugin activate ##
    register_activation_hook( __FILE__, array( 'Q_NProgress', 'activation_hook' ) );

    // on plugin deactive ##
    register_deactivation_hook( __FILE__, array( 'Q_NProgress', 'deactivation_hook' ) );

    // on plugin uninstall ##
    #register_uninstall_hook( __FILE__, array( 'Q_NProgress', 'uninstall_hook' ) );
    
    // Hour Tracking Class ##
    class Q_NProgress 
    {
        
        // Refers to a single instance of this class. ##
        private static $instance = null;
        
        // properties
        static $text_domain = "q-nprogress";
        
        /**
         * Creates or returns an instance of this class.
         *
         * @return  Foo     A single instance of this class.
         */
        public static function get_instance() {

            if ( null == self::$instance ) {
                self::$instance = new self;
            }

            return self::$instance;

        }

        /**
         * Instatiate Class
         * 
         * @since       0.1
         * @return      void
         */
        public function __construct() 
        {
            
            // set text domain
            #add_action( 'init', array( $this, 'load_plugin_textdomain' ), 1 );
            
            // front-end options ##
            if ( ! is_admin() ) {

                // add scripts ##
                add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 10 );
                
                // add call to JS to wp_footer() action hook ##
                add_action( 'wp_footer', array( $this, 'wp_footer' ), 1 );
                
            }
            
        }


        /**
         * Plugin Activation
         * 
         * @since       0.1
         * @return      void
         */
        public static function activation_hook()
        {
            
            // save plugin version ##
            $option["version"] = Q_NPROGRESS_VERSION; 
            
            // add flag that plugin is configured - set to autoload ##
            $option["configured"] = true;

            // save install settings - auto loaded option ##
            add_option( 'q_nprogress', $option, '', 'yes' );

        }


        /**
         * Plugin Deactivation
         * 
         * @since       0.1
         * @return      void
         */
        public static function deactivation_hook()
        {

            // remove plugin options ##
            delete_option( 'q_nprogress' );

        }


        /**
         * Plugin Uninstalled
         * 
         * @since       0.1
         * @return      void
         */
        public static function uninstall_hook()
        {

            // remove plugin options ##
            delete_option( 'q_nprogress' );
            
        }


        /**
         * Load Text Domain for translations
         * 
         * @since       0.1
         * @link        http://geertdedeckere.be/article/loading-wordpress-language-files-the-right-way
         */
        public function load_plugin_textdomain() 
        {

            load_plugin_textdomain(self::$text_domain, FALSE, dirname(plugin_basename(__FILE__)).'/languages/' );

        }
        
         
        /**
         * Enqueue Plugin Scripts & Styles
         * 
         * @since       0.5
         */
        public function wp_enqueue_scripts() 
        {
            
            // add CSS ##
            wp_register_style( 'nprogress-css', self::get_plugin_url( 'css/nprogress.css' ) );
            wp_enqueue_style( 'nprogress-css' );
            
            // add JS ##
            wp_enqueue_script( 'nprogress-js', self::get_plugin_url( 'javascript/nprogress.js' ), array( "jquery" ), Q_NPROGRESS_VERSION, false );
            
        }
        
        
        public function wp_footer()
        {
            
?>
        <script>
            // Show the progress bar 
            NProgress.start();

            // Increase randomly
            var interval = setInterval(function() { NProgress.inc(); }, 1000);        

            // Trigger finish when page fully loaded
            jQuery(window).load(function () {
                clearInterval(interval);
                NProgress.done();
            });

            // Trigger bar when exiting the page
            window.onbeforeunload = function() {
                console.log("triggered");
                NProgress.start();
            };
        </script>    
<?php
            
        }
        
        
        
        /**
         * Get Plugin URL
         * 
         * @since       0.1
         * @param       string $path Path to plugin directory
         * @return      string  Absoulte URL to plugin directory
         */
        public static function get_plugin_url( $path = '' ) 
        {

            return plugins_url( ltrim( $path, '/' ), __FILE__ );

        }
        
        
    }
    
}
