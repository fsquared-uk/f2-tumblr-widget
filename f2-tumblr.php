<?php
/**
 * F2 Tumblr Widget
 *
 * A widget to display recent posts from a tumblr blog
 *
 * @package   F2 Tumblr Widget
 * @author    Pete Favelle <pete@fsquared.co.uk>
 * @license   GPL-2.0+
 * @link      http://www.fsquared.co.uk
 * @copyright 2014-2015 fsquared
 *
 * @wordpress-plugin
 * Plugin Name:       F2 Tumblr Widget
 * Plugin URI:        http://www.fsquared.co.uk/software/f2-tumblr/
 * Description:       Widget to display recent posts from a tumblr blog
 * Version:           0.2.8
 * Author:            fsquared limited
 * Author URI:        http://www.fsquared.co.uk
 * Text Domain:       f2-tumblr-widget
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

class F2_Tumblr_Widget extends WP_Widget {

    /**
     * Unique identifier for the widget.
     */
    protected $widget_slug = 'f2-tumblr-widget';

    /**
     * Default values for widget parameters.
     */
    protected $default_settings = array(
        'title'         => 'Tumblr',
        'tumblr'        => '',
        'posts'         => 3,
        'cache_period'  => 10,
        'post_type'     => 'all',
        'media_width'   => '100',
        'media_align'   => 'aligncenter',
        'post_tag'      => '',
        'content_type'  => 'excerpt',
        'excerpt_size'  => '50',
        'display_type'  => 'list',
        'slide_speed'   => '10',
        'title_size'    => '',
        'text_size'     => '',
        'line_spacing'  => '',
        'media_padding' => '',
        'audio_width'   => 1,
        'clean_quotes'  => 1
    );

    protected $allowed_post_types = array();
    protected $allowed_media_widths = array();
    protected $allowed_display_types = array();
    protected $allowed_content_types = array();
    protected $allowed_media_alignments = array();

    protected $allowed_css_units = array(
        'em', 'ex', 'ch', 'rem', 'vw', 'vh', 'vmin', 'vmax', 
        'cm', 'mm', 'in', 'px', 'pt', 'pc', '%',
    );

    /*--------------------------------------------------*/
    /* Constructor
    /*--------------------------------------------------*/

    /**
     * Specifies the classname and description, instantiates the widget,
     * loads localization files, and includes necessary stylesheets 
     * and JavaScript.
     */
    public function __construct() {

        // load plugin text domain
        add_action( 'init', array( $this, 'widget_textdomain' ) );

        parent::__construct(
            $this->get_widget_slug(),
            __( 'F2 Tumblr Widget', $this->get_widget_slug() ),
            array(
                'classname'  => $this->get_widget_slug().'-class',
                'description' => __( 'Widget to display recent posts from a tumblr blog.', $this->get_widget_slug() )
            )
        );

        // Register admin styles and scripts
        add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

        // Register site styles and scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'ajax_enqueue_style' ) );

        // Add in an AJAX handler to allow dynamically generated CSS
        add_action( 'wp_ajax_f2_tumblr_dynamic_css', array( $this, 'ajax_dynamic_css' ) );
        add_action( 'wp_ajax_nopriv_f2_tumblr_dynamic_css', array( $this, 'ajax_dynamic_css' ) );

        // Refreshing the widget's cached output with each new post
        add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
        add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
        add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

        // Initialise the allowed values for fields; has to be done here
        // so that we can load the correct language data
        $this->allowed_post_types['all'] = __( 'All Posts', $this->get_widget_slug() );
        $this->allowed_post_types['text'] = __( 'Text', $this->get_widget_slug() );
        $this->allowed_post_types['photo'] = __( 'Photo', $this->get_widget_slug() );
        $this->allowed_post_types['quote'] = __( 'Quote', $this->get_widget_slug() );
        $this->allowed_post_types['link'] = __( 'Link', $this->get_widget_slug() );
        $this->allowed_post_types['chat'] = __( 'Chat', $this->get_widget_slug() );
        $this->allowed_post_types['audio'] = __( 'Audio', $this->get_widget_slug() );
        $this->allowed_post_types['video'] = __( 'Video', $this->get_widget_slug() );

        $this->allowed_media_widths['75'] = __( '75px', $this->get_widget_slug() );
        $this->allowed_media_widths['100'] = __( '100px', $this->get_widget_slug() );
        $this->allowed_media_widths['250'] = __( '250px', $this->get_widget_slug() );
        $this->allowed_media_widths['400'] = __( '400px', $this->get_widget_slug() );
        $this->allowed_media_widths['500'] = __( '500px', $this->get_widget_slug() );
        $this->allowed_media_widths['1280'] = __( '1280px', $this->get_widget_slug() );

        $this->allowed_content_types['none'] = __( 'Title Only', $this->get_widget_slug() );
        $this->allowed_content_types['bare'] = __( 'Media Only', $this->get_widget_slug() );
        $this->allowed_content_types['media'] = __( 'Title And Media', $this->get_widget_slug() );
        $this->allowed_content_types['excerpt'] = __( 'Post Excerpt', $this->get_widget_slug() );
        $this->allowed_content_types['full'] = __( 'Whole Post', $this->get_widget_slug() );

        $this->allowed_media_alignments['alignleft'] = __( 'Left', $this->get_widget_slug() );
        $this->allowed_media_alignments['aligncenter'] = __( 'Centered', $this->get_widget_slug() );
        $this->allowed_media_alignments['alignright'] = __( 'Right', $this->get_widget_slug() );

        $this->allowed_display_types['list'] = __( 'Vertical List', $this->get_widget_slug() );
        $this->allowed_display_types['hlist'] = __( 'Horizontal List', $this->get_widget_slug() );
        $this->allowed_display_types['slide'] = __( 'Slideshow', $this->get_widget_slug() );
    } // end constructor


    /**
     * Return the widget slug.
     *
     * @return    Plugin slug variable.
     */
    public function get_widget_slug() {
        return $this->widget_slug;
    }

    /*--------------------------------------------------*/
    /* Widget API Functions
    /*--------------------------------------------------*/

    /**
     * Outputs the content of the widget.
     *
     * @param array args  The array of form elements
     * @param array instance The current instance of the widget
     */
    public function widget( $args, $instance ) {

        // Ensure that we have the widget ID handy
        if ( ! isset ( $args['widget_id'] ) )
            $args['widget_id'] = $this->id;

        // Check to see if we need to refresh the Tumblr feed
        $local_params = wp_parse_args( $instance, $this->default_settings );
        $tumblr_data = get_transient( $this->get_widget_slug() . $args['widget_id'] );

        if ( false === $tumblr_data ) {

            // So, fetch the data from Tumblr
            $tumblr_url = $local_params['tumblr'] 
                        . '/api/read?num=' . $local_params['posts'];
            if ( 'all' != $local_params['post_type'] ) {
                $tumblr_url .= '&type=' . $local_params['post_type'];
            }
            if ( !empty( $local_params['post_tag'] ) ) {
                $tumblr_url .= '&tagged=' . urlencode( $local_params['post_tag'] );
            }
            $tumblr_data = wp_remote_retrieve_body( 
                wp_remote_get( $tumblr_url ) 
            );

            // Save this transient data
            set_transient( 
                $this->get_widget_slug() . $args['widget_id'],
                $tumblr_data,
                60 * $local_params['cache_period']
            );

            // And clear any cache of the widget
            $this->flush_widget_cache();
        }

        // Check if there is a cached output
        $cache = wp_cache_get( $this->get_widget_slug(), 'widget' );
        if ( !is_array( $cache ) ) {
            $cache = array();
        }

        // If there *is*, send that rather then doing the same work over
        if ( isset ( $cache[ $args['widget_id'] ] ) ) {
            return print $cache[ $args['widget_id'] ];
        }

        // So, looks like we have to do some work after all
        $widget_string = $args['before_widget'];

        // Render any title we're provided with
        $title = apply_filters( 'widget_title', $local_params['title'] );
        if ( !empty($title) ) {
            $widget_string .= $args['before_title'];
            $widget_string .= $title;
            $widget_string .= $args['after_title'];
        }

        // And then fetch the main widget view
        ob_start();
        include( plugin_dir_path( __FILE__ ) . 'views/widget.php' );
        $widget_string .= ob_get_clean();
        $widget_string .= $args['after_widget'];

        // Save the rendered output in the cache
        $cache[ $args['widget_id'] ] = $widget_string;
        wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );

        print $widget_string;

    } // end widget
    
    /**
     * Clears widget data from the cache
     */
    public function flush_widget_cache() 
    {
        wp_cache_delete( $this->get_widget_slug(), 'widget' );
    }


    /**
     * Processes the widget's options to be saved.
     *
     * @param array new_instance The new instance of values to be generated.
     * @param array old_instance The previous instance of values.
     */
    public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        // Clean up user text inputs
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['post_tag'] = strip_tags( $new_instance['post_tag'] );

        // Numeric ones
        $instance['posts'] = intval( $new_instance['posts'] );
        $instance['cache_period'] = intval( $new_instance['cache_period'] );
        $instance['audio_width'] = intval( $new_instance['audio_width'] );
        $instance['clean_quotes'] = intval( $new_instance['clean_quotes'] );
        if ( !empty( $new_instance['excerpt_size'] ) ) {
            $instance['excerpt_size'] = intval( $new_instance['excerpt_size'] );
        }
        if ( !empty( $new_instance['slide_speed'] ) ) {
            $instance['slide_speed'] = intval( $new_instance['slide_speed'] );
        }

        // The provided URL needs to be cleaned up a little
        if ( 'http' == mb_substr( $new_instance['tumblr'], 0, 4 ) ) {
            $entered_url = $new_instance['tumblr'];
        } else {
            $entered_url = 'http://' . $new_instance['tumblr'];
        }
        $instance['tumblr'] = parse_url( $entered_url, PHP_URL_SCHEME ) 
                            . '://' . parse_url( $entered_url, PHP_URL_HOST );

        // And selections
        if ( array_key_exists( $new_instance['post_type'], $this->allowed_post_types ) ) {
            $instance['post_type'] = $new_instance['post_type'];
        }
        if ( array_key_exists( $new_instance['media_width'], $this->allowed_media_widths ) ) {
            $instance['media_width'] = $new_instance['media_width'];
        }
        if ( array_key_exists( $new_instance['media_align'], $this->allowed_media_alignments ) ) {
            $instance['media_align'] = $new_instance['media_align'];
        }
        if ( array_key_exists( $new_instance['display_type'], $this->allowed_display_types ) ) {
            $instance['display_type'] = $new_instance['display_type'];
        }
        if ( array_key_exists( $new_instance['content_type'], $this->allowed_content_types ) ) {
            $instance['content_type'] = $new_instance['content_type'];
        }

        // And lastly, CSS dimension rules...
        if ( empty( $new_instance['title_size'] )
          || !in_array( $new_instance['title_size_units'], $this->allowed_css_units ) ) {
            $instance['title_size'] = '';
        } else {
            $instance['title_size'] = floatval( $new_instance['title_size'] )
                                    . $new_instance['title_size_units'];
        }
        if ( empty( $new_instance['text_size'] )
          || !in_array( $new_instance['text_size_units'], $this->allowed_css_units ) ) {
            $instance['text_size'] = '';
        } else {
            $instance['text_size'] = floatval( $new_instance['text_size'] )
                                   . $new_instance['text_size_units'];
        }
        if ( empty( $new_instance['line_spacing'] )
          || !in_array( $new_instance['line_spacing_units'], $this->allowed_css_units ) ) {
            $instance['line_spacing'] = '';
        } else {
            $instance['line_spacing'] = floatval( $new_instance['line_spacing'] )
                                   . $new_instance['line_spacing_units'];
        }
        if ( empty( $new_instance['media_padding'] )
          || !in_array( $new_instance['media_padding_units'], $this->allowed_css_units ) ) {
            $instance['media_padding'] = '';
        } else {
            $instance['media_padding'] = floatval( $new_instance['media_padding'] )
                                   . $new_instance['media_padding_units'];
        }

        // Clear the transient data, to force a refrsh of Tumblr data.
        delete_transient( $this->get_widget_slug() . $this->id );

        return $instance;

    } // end widget

    /**
     * Generates the administration form for the widget.
     *
     * @param array instance The array of keys and values for the widget.
     */
    public function form( $instance ) {

        $local_params = wp_parse_args( $instance, $this->default_settings );

        // Display the admin form
        include( plugin_dir_path(__FILE__) . 'views/admin.php' );

    } // end form

    /**
     * Sanitised version of wp_trim_words, which doesn't strip out 
     * paragraphs.
     *
     * @param string $text Text to trim.
     * @param int $num_words Number of words.
     * @param string $more  What to append if $text needs to be trimmed.
     * @return string Trimmed text.
     */
    public function trim_words( $text, $num_words, $more ) {
        $text = strip_tags( $text, '<p>' );
        /* translators: If your word count is based on single characters (East Asian characters),
           enter 'characters'. Otherwise, enter 'words'. Do not translate into your own language. */
        if ( 'characters' == _x( 'words', 'word count: words or characters?' ) && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
            $text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
            preg_match_all( '/./u', $text, $words_array );
            $words_array = array_slice( $words_array[0], 0, $num_words + 1 );
            $sep = '';
        } else {
            $words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
            $sep = ' ';
        }
        if ( count( $words_array ) > $num_words ) {
            array_pop( $words_array );
            $text = implode( $sep, $words_array );
            $text = $text . $more;
        } else {
            $text = implode( $sep, $words_array );
        }

        return $text;
    }

    /**
     * Handles the callback for custom CSS
     */
    public function ajax_dynamic_css() {

        // Check that we've been called right

        // Need to set the content type right
        header('Content-type: text/css');

        // So, work through all instances and put out any required CSS
        foreach( get_option( $this->option_name ) as $key => $instance ) {

            // Actual instances will be numeric keys
            if ( !is_numeric( $key ) ) {
                continue;
            }

            // So process it, taking into account defaults
            $local_params = wp_parse_args( $instance, $this->default_settings );
            if ( strlen( $local_params['title_size'] ) > 0 ) {
                echo '#' . $this->id_base . '-' . $key
                   . ' div.f2-tumblr-post h3 { '
                   . 'font-size: ' . $local_params['title_size'] . ';'
                   . '}';
            }
            if ( strlen( $local_params['text_size'] ) 
              || strlen( $local_params['line_spacing'] ) > 0 ) {
                echo '#' . $this->id_base . '-' . $key
                   . ' div.f2-tumblr-post p { ';
                if ( strlen( $local_params['text_size'] ) > 0 ) {
                    echo 'font-size: ' . $local_params['text_size'] . ';';
                }
                if ( strlen( $local_params['line_spacing'] ) > 0 ) {
                    echo 'line-height: ' . $local_params['line_spacing'] . ';';
                }
                echo '}';
            }
            if ( strlen( $local_params['media_padding'] ) > 0 ) {
                echo '#' . $this->id_base . '-' . $key
                   . ' div.f2-tumblr-media img { ';
                if ( 'alignleft' == $local_params['media_align' ] ) {
                    echo 'margin-right: ' . $local_params['media_padding'] . ';';
                    echo '} #' . $this->id_base . '-' . $key
                       . ' div.f2-tumblr-media { ' 
                       . 'margin-right: 0px;';

                } else if ( 'alignright' == $local_params['media_align' ] ) {
                    echo 'margin-left: ' . $local_params['media_padding'] . ';';
                    echo '} #' . $this->id_base . '-' . $key
                       . ' div.f2-tumblr-media { ' 
                       . 'margin-left: 0px;';
                }
                echo '}';
            }
        }

        // All done, so die
        die();
    }

    /**
     * Enqueues the widget-specific CSS callback
     */
    public function ajax_enqueue_style() {
        wp_enqueue_style( 
            $this->get_widget_slug() . '-custom-style-' . $this->id,
            admin_url( 'admin-ajax.php?action=f2_tumblr_dynamic_css' )
        );
    }

    /*--------------------------------------------------*/
    /* Public Functions
    /*--------------------------------------------------*/

    /**
     * Loads the Widget's text domain for localization and translation.
     */
    public function widget_textdomain() {

        load_plugin_textdomain( $this->get_widget_slug(), false, 
                                plugin_dir_path( __FILE__ ) . 'lang/' );

    } // end widget_textdomain

    /**
     * Registers and enqueues admin-specific styles.
     */
    public function register_admin_styles() {

        wp_enqueue_style( $this->get_widget_slug().'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ) );

    } // end register_admin_styles

    /**
     * Registers and enqueues admin-specific JavaScript.
     */
    public function register_admin_scripts() {

        wp_enqueue_script( $this->get_widget_slug().'-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array('jquery') );

    } // end register_admin_scripts

    /**
     * Registers and enqueues widget-specific styles.
     */
    public function register_widget_styles() {

        wp_enqueue_style( $this->get_widget_slug().'-widget-styles', plugins_url( 'css/widget.css', __FILE__ ) );

    } // end register_widget_styles

    /**
     * Registers and enqueues widget-specific scripts.
     */
    public function register_widget_scripts() {

        wp_enqueue_script( $this->get_widget_slug().'-script', plugins_url( 'js/widget.js', __FILE__ ), array('jquery'), false, true );

    } // end register_widget_scripts

    /**
     * Cleans up smart quotes and other less-than-portable characters
     */
    private function clean_encoding( $p_text ) {

        // First, strip offending UTF-8 characters
        $p_text = str_replace
            (
                array( "\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", 
                       "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", 
                       "\xe2\x80\xa6" ),
                array( "'", "'", '"', '"', '-', '--', '...' ),
                $p_text
            );

        // And then the same for Windows 1252 ones
        $p_text = str_replace
            (
                array( chr(145), chr(146), chr(147), chr(148),
                       chr(150), chr(151), chr(133) ),
                array( "'", "'", '"', '"', '-', '--', '...' ),
                $p_text
            );

        return $p_text;
    }

} // end class

add_action( 'widgets_init', create_function( '', 'register_widget("F2_Tumblr_Widget");' ) );
