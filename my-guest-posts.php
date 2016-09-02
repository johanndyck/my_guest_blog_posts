<?php
/*
Plugin Name: My Guest Posts
Description: Create links to posts an author has written for other blogs.
Author: Johann Dyck
Author URI: http://johanndyck.com
Version: 1.0
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// required plugin class
require_once dirname(__FILE__) . '/lib/class-tgm-plugin-activation.php';

class My_Guest_Posts {
  private static $instance;

    const FIELD_PREFIX = 'jdmgp_';
    const TEXT_DOMAIN = 'jd-my-guest-posts';

  public static function getInstance() {
    if (self::$instance == NULL) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  private function __construct() {
	add_action( 'init', 'My_Guest_Posts::register_post_type' );

    add_action( 'tgmpa_register', array( $this, 'check_required_plugins' ) );
    add_filter( 'rwmb_meta_boxes', array( $this, 'metabox_custom_fields' ) );

  }

  static function register_post_type() {
	  register_post_type('guest_posts', array(
		  'labels' => array(
			  'name' => __('My Guest Posts'),
			  'singular_name' => __('Guest Post'),
		  ),
		  'description' => 'Guest posts on other blogs can easily be added to this site.',
		  'supports' => array(
			  'title', 'editor', 'author', 'revisions', 'thumbnail', 'custom-fields',
		  ),
		  'public' => TRUE,
          'menu_icon' => 'dashicons-edit',
          'menu_position' => 4,
          'show_in_nav_menus' => FALSE,
	  ));
  }

  static function activate() {
      self::register_post_type();
      flush_rewrite_rules(  );
  }


  function check_required_plugins(){
      $plugins = array(
          array(
              'name' => 'Meta Box',
              'slug' => 'meta-box',
              'required' => true,
              'force_activation' => false,
              'force_deactivation' => false,
          ),
      );

    $config  = array(
        'domain'           => 'jd_my_guest_posts',
        'default_path'     => '',
        'parent_slug'      => 'plugins.php',
        'capability'       => 'update_plugins',
        'menu'             => 'install-required-plugins',
        'has_notices'      => true,
        'is_automatic'     => false,
        'message'          => '',
        'strings'          => array(
            'page_title'                      => __( 'Install Required Plugins', 'jd-my-guest-posts' ),
            'menu_title'                      => __( 'Install Plugins', 'jd-my-guest-posts' ),
            'installing'                      => __( 'Installing Plugin: %s', 'jd-my-guest-posts' ),
            'oops'                            => __( 'Something went wrong with the plugin API.', 'jd-my-guest-posts' ),
            'notice_can_install_required'     => _n_noop( 'The My Guest Posts plugin depends on the following plugin: %1$s.', 'The My Guest Posts plugin depends on the following plugins: %1$s.' ),
            'notice_can_install_recommended'  => _n_noop( 'The My Guest Posts plugin recommends the following plugin: %1$s.', 'The My Guest Posts plugin recommends the following plugins: %1$s.' ),
            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ),
            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ),
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ),
            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ),
            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ),
            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ),
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
            'activate_link'                   => _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
            'return'                          => __( 'Return to Required Plugins Installer', 'jd-my-guest-posts' ),
            'plugin_activated'                => __( 'Plugin activated successfully.', 'jd-my-guest-posts' ),
            'complete'                        => __( 'All plugins installed and activated successfully. %s', 'jd-my-guest-posts' ),
            'nag_type'                        => 'updated',
    )
    );
    tgmpa( $plugins, $config );
  }

  function metabox_custom_fields() {
      $meta_boxes[] = array(
          'id'      => 'external_post_data',
          'title'   => 'Additional Information',
          'post_types'   => array( 'guest_posts' ),
          'context' => 'normal',
          'priority' => 'high',
          'fields' => array(
              array(
                  'name' => 'Blog Name',
                  'desc' => 'The name of the blog where this post appears',
                  'id' => 'jd_ext_blog_name',
                  'type' => 'text',
                  'std' => '',
              ),
              array(
                  'name' => 'Post Link',
                  'desc' => 'Link for this post on the external blog',
                  'id' => 'jd_ext_blog_url',
                  'type' => 'url',
                  'std' => '',                
              ),
          )
      );

      return $meta_boxes;
  }

}

My_Guest_Posts::getInstance();

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'My_Guest_Posts::activate' );

