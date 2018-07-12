<?php
/*
 * 	BNE Custom Post Type Class
 *
 * 	Used to help create custom post types for WordPress.
 *
 * 	@author  	Kerry Kline, BNE Creative
 * 	@link    	http://www.bnecreative.com
 * 	@version 	1.0.1
 * 	License: 	GPL2

    Copyright 2015 BNE Creative

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;



if( !class_exists('BNE_CPT') ) {

	
	class BNE_CPT {
		

		
		/*
		 *	Post type name.
		 *
		 *	@var string $post_type_name Holds the name of the post type.
		 *
		*/
		public $post_type_name;
		

		
		/*
		 *	Post type slug. This is a robot friendly name, all lowercase and uses
		 * 	hyphens assigned on __construct().
		 *
		 *	@var string $slug Holds the post type slug name.
		 *
		*/
		public $slug;
		

		
		/*
		 *	Holds the singular name of the post type. This is a human friendly
		 * 	name, capitalized with spaces assigned on __construct().
		 *
		 *	@var string $singular Post type singular name.
		 *
		*/
		public $singular;
		

		
		/*
		 *	Holds the plural name of the post type. This is a human friendly
		 * 	name, capitalized with spaces assigned on __construct().
		 *
		 *	@var string $plural Singular post type name.
		 *
		*/
		public $plural;
		

		
		/*
		 *	User submitted options assigned on __construct().
		 *
		 *	@var array $options Holds the user submitted post type options.
		 *
		*/
		public $options;
	

	
		/*
		 *	Textdomain used for translation. Use the set_textdomain() method to set a custom textdomain.
		 *
		 *	@var string $textdomain Used for internationalising. Defaults to "cpt" without quotes.
		 *
		*/
		public $textdomain;
		



		/*
		 *	Taxonomy slug. Use the register_taxonomy() method to add a new custom taxonomy.
		 *
		 *	@var string $taxonomy_name Holds our taxonomy slug and registers it.
		 *
		*/
		public $taxonomy_name;
	    
	    
	    
	    /*
	     * 	Constructor
	     *
	     * 	Register a custom post type.
	     *
	     * 	@param mixed $post_type_name The name of the post type.
	     * 	@param array $options User submitted options.
	     *
	    */
		function __construct( $post_type_name, $options = array() ) {
			
			// Set the post type name
			$this->post_type_name = $post_type_name;
			
			// Set the slug name
			$this->slug = $this->get_slug( isset( $options['slug'] ) ? $options['slug'] : $this->post_type_name );
			
			// Set the plural name label
			$this->singular = isset( $options['singular'] ) ? $options['singular'] : $this->get_singular();
			
			// Set the singular name label
			$this->plural = isset( $options['plural'] ) ? $options['plural'] : $this->get_plural();

			// Set the cpt arguments
			$this->args = isset( $options['args'] ) ? $options['args'] : null;

			// Register the post type.
			add_action( 'init', array( $this, 'register_post_type' ) );
			
			// rewrite post update messages
			add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
			add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_updated_messages' ), 10, 2 );
			
		}
		

		
		/*
		 * 	Set textdomain
		 *
		 * 	@param string $textdomain Textdomain used for translation.
		 *
		*/
		function set_textdomain( $textdomain ) {
			$this->textdomain = $textdomain;
		}
		

		
		/*
		 * 	Get slug
		 *
		 * 	Creates an url friendly slug.
		 *
		 * 	@param  string $name Name to slugify.
		 * 	@return string $name Returns the slug.
		*/
		function get_slug( $name = null ) {
		
			// If no name set use the post type name.
			if( ! isset( $name ) ) {
				$name = $this->post_type_name;
			}
			
			// Name to lower case.
			$name = strtolower( $name );
			
			// Replace spaces with hyphen.
			$name = str_replace( " ", "-", $name );
			
			// Replace underscore with hyphen.
			$name = str_replace( "_", "-", $name );
			
			return $name;
			
		}
		

		
		/*
		 * 	Get plural
		 *
		 * 	Returns the friendly plural name.
		 *
		 *	ucwords			capitalize words
		 *  strtolower		makes string lowercase before capitalizing
		 *  str_replace		replace all instances of _ to space
		 *
		 * 	@param  string $name The slug name you want to pluralize.
		 * 	@return string the friendly pluralized name.
		 *
		*/
		function get_plural( $name = null ) {
			
			// If no name is passed the post_type_name is used.
			if( ! isset( $name ) ) {
				$name = $this->post_type_name;
			}
			
			// Return the plural name. Add 's' to the end.
			return $this->get_human_friendly( $name ) . 's';
		
		}
		

		
		/*
		 * 	Get singular
		 *
		 * 	Returns the friendly singular name.
		 *
		 *	ucwords			capitalize words
		 *	strtolower		makes string lowercase before capitalizing
		 *	str_replace		replace all instances of _ to space
		 *
		 * 	@param string $name The slug name you want to unpluralize.
		 * 	@return string The friendly singular name.
		*/
		function get_singular( $name = null ) {
			
			// If no name is passed the post_type_name is used.
			if ( ! isset( $name ) ) {
				$name = $this->post_type_name;
			}
			
			// Return the string.
			return $this->get_human_friendly( $name );
		
		}
		

		
		/*
		 * 	Get human friendly
		 *
		 * 	Returns the human friendly name.
		 *
		 *	ucwords      	capitalize words
		 *	strtolower   	makes string lowercase before capitalizing
		 *	str_replace  	replace all instances of hyphens and underscores to spaces
		 *
		 * 	@param string $name The name you want to make friendly.
		 * 	@return string The human friendly name.
		*/
		function get_human_friendly( $name = null ) {
			
			// If no name is passed the post_type_name is used.
			if ( ! isset( $name ) ) {
				$name = $this->post_type_name;
			}
			
			// Return human friendly name.
			return ucwords( strtolower( str_replace( "-", " ", str_replace( "_", " ", $name ) ) ) );
		
		}
		

		
	    /*
	     * 	Register Post Type
	     *
	     * 	@see http://codex.wordpress.org/Function_Reference/register_post_type
	     *
	    */
	    function register_post_type() {
	       
	        // Friendly post type names.
	        $slug     = $this->slug;
	        $plural   = $this->plural;
	        $singular = $this->singular;
	        
	        
	        // Default labels.
	        $labels = array(
	            'name'               	=> 	sprintf( esc_html__( '%s', $this->textdomain ), $plural ),
	            'singular_name'      	=> 	sprintf( esc_html__( '%s', $this->textdomain ), $singular ),
	            'menu_name'          	=> 	sprintf( esc_html__( '%s', $this->textdomain ), $plural ),
	            'all_items'          	=> 	sprintf( esc_html__( 'All %s', $this->textdomain ), $plural ),
	            'add_new'            	=> 	esc_html__( 'Add New', $this->textdomain ),
	            'add_new_item'       	=> 	sprintf( esc_html__( 'Add New %s', $this->textdomain ), $singular ),
	            'edit_item'          	=> 	sprintf( esc_html__( 'Edit %s', $this->textdomain ), $singular ),
	            'new_item'           	=> 	sprintf( esc_html__( 'New %s', $this->textdomain ), $singular ),
	            'view_item'          	=> 	sprintf( esc_html__( 'View %s', $this->textdomain ), $singular ),
	            'search_items'       	=> 	sprintf( esc_html__( 'Search %s', $this->textdomain ), $plural ),
	            'not_found'          	=> 	sprintf( esc_html__( 'No %s found', $this->textdomain ), $plural ),
	            'not_found_in_trash' 	=> 	sprintf( esc_html__( 'No %s found in Trash', $this->textdomain ), $plural ),
	            'parent_item_colon'  	=> 	sprintf( esc_html__( 'Parent %s:', $this->textdomain ), $singular ),
			    'featured_image' 		=> 	sprintf( esc_html__( '%s Thumbnail', $this->textdomain ), $singular ),
			    'set_featured_image'	=>	esc_html__( 'Set Image', $this->textdomain ),
			    'remove_featured_image'	=>	esc_html__( 'Remove Image', $this->textdomain ),
	        );


	        // Default options.
	        $defaults = array(
	            'labels' 	=> 	$labels,
	            'public' 	=> 	true,
	            'rewrite' 	=> 	array(
	                'slug' 		=> 	$slug,
	            )
	        );
	        
	        
	        // Overide and merge user defined arguments.
	        if( $this->args ) {
		        
		        // PHP 5.3
				if( function_exists( 'array_replace_recursive' ) ) { 
					$args = array_replace_recursive( $defaults, $this->args ); 
				
				// PHP 5.2 fallback
				} else {
					$args = $this->array_replace_recursive_fallback( $defaults, $this->args );
				}
	        
	        } else {
		        $args = $defaults;
	        }

	                	        
	        // Check that the post type doesn't already exist.
	        if( !post_type_exists( $this->post_type_name ) ) {
	            // Register the post type.
	            register_post_type( $this->post_type_name, $args );
	        }
	   

	    }	
	
	

		/*
		 * 	Register taxonomy
		 *
		 * 	@see http://codex.wordpress.org/Function_Reference/register_taxonomy
		 *
		 * 	@param string $taxonomy_name The slug for the taxonomy.
		 * 	@param array  $options Taxonomy options.
		 *
		*/
		function register_taxonomy( $taxonomy_name, $options = array() ) {

			// Create user friendly names.
			$singular 	= isset( $options['singular'] ) ? $options['singular'] : $this->get_singular( $taxonomy_name );
			$plural 	= isset( $options['plural'] ) ? $options['plural'] : $this->get_plural( $taxonomy_name );
			$slug 		= isset( $options['slug'] ) ? $options['slug'] : $this->get_slug( $taxonomy_name );

			// Set the taxonomy arguments
			$options = isset( $options['args'] ) ? $options['args'] : $options;


			// Default labels.
			$labels = array(
				'name'                       => sprintf( esc_html__( '%s', $this->textdomain ), $plural ),
				'singular_name'              => sprintf( esc_html__( '%s', $this->textdomain ), $singular ),
				'menu_name'                  => sprintf( esc_html__( '%s', $this->textdomain ), $plural ),
				'all_items'                  => sprintf( esc_html__( 'All %s', $this->textdomain ), $plural ),
				'edit_item'                  => sprintf( esc_html__( 'Edit %s', $this->textdomain ), $singular ),
				'view_item'                  => sprintf( esc_html__( 'View %s', $this->textdomain ), $singular ),
				'update_item'                => sprintf( esc_html__( 'Update %s', $this->textdomain ), $singular ),
				'add_new_item'               => sprintf( esc_html__( 'Add New %s', $this->textdomain ), $singular ),
				'new_item_name'              => sprintf( esc_html__( 'New %s Name', $this->textdomain ), $singular ),
				'parent_item'                => sprintf( esc_html__( 'Parent %s', $this->textdomain ), $plural ),
				'parent_item_colon'          => sprintf( esc_html__( 'Parent %s:', $this->textdomain ), $plural ),
				'search_items'               => sprintf( esc_html__( 'Search %s', $this->textdomain ), $plural ),
				'popular_items'              => sprintf( esc_html__( 'Popular %s', $this->textdomain ), $plural ),
				'separate_items_with_commas' => sprintf( esc_html__( 'Separate %s with commas', $this->textdomain ), $plural ),
				'add_or_remove_items'        => sprintf( esc_html__( 'Add or remove %s', $this->textdomain ), $plural ),
				'choose_from_most_used'      => sprintf( esc_html__( 'Choose from most used %s', $this->textdomain ), $plural ),
				'not_found'                  =>	sprintf( esc_html__( 'No %s found', $this->textdomain ), $plural ),
			);

			// Default options.
			$defaults = array(
				'labels' 		=>	$labels,
				'hierarchical' 	=> 	true,
				'rewrite' 		=> 	array(
					'slug' 			=> 	$this->slug.'/'.$slug,
					'with_front'	=>	false,
				),
				'show_admin_column' => 	true,
			);


			// Overide and merge user defined arguments.
			if( $options ) {
			
				// PHP 5.3
				if( function_exists( 'array_replace_recursive' ) ) { 
					$args = array_replace_recursive( $defaults, $options ); 
			
				// PHP 5.2 fallback
				} else {
					$args = $this->array_replace_recursive_fallback( $defaults, $options );
				}
			
			} else {
				$args = $defaults;
			}
			
	        // Check that the taxonomy doesn't already exist.
			if( !taxonomy_exists( $taxonomy_name ) ) {
	            // Register the taxonomy.
				register_taxonomy( $taxonomy_name, $this->post_type_name, $args );
			}
			
		
		}




		
		
		/*
		 * 	Updated messages
		 *
		 * 	Internal function that modifies the post type names in updated messages
		 *
		 * 	@param array $messages an array of post updated messages
		 *
		*/
		function updated_messages( $messages ) {
			$post = get_post();
			$singular = $this->singular;
			$messages[$this->post_type_name] = array(
				0 => '',
				1 => sprintf( esc_html__( '%s updated.', $this->textdomain ), $singular ),
				2 => esc_html__( 'Custom field updated.', $this->textdomain ),
				3 => esc_html__( 'Custom field deleted.', $this->textdomain ),
				4 => sprintf( esc_html__( '%s updated.', $this->textdomain ), $singular ),
				5 => isset( $_GET['revision'] ) ? sprintf( esc_html__( '%2$s restored to revision from %1$s', $this->textdomain ), wp_post_revision_title( (int) $_GET['revision'], false ), $singular ) : false,
				6 => sprintf( esc_html__( '%s updated.', $this->textdomain ), $singular ),
				7 => sprintf( esc_html__( '%s saved.', $this->textdomain ), $singular ),
				8 => sprintf( esc_html__( '%s submitted.', $this->textdomain ), $singular ),
				9 => sprintf(
						__( '%2$s scheduled for: <strong>%1$s</strong>.', $this->textdomain ),
						date_i18n( esc_html__( 'M j, Y @ G:i', $this->textdomain ), strtotime( $post->post_date ) ),
						$singular
					),
				10 => sprintf( esc_html__( '%s draft updated.', $this->textdomain ), $singular ),
			);
			
			return $messages;
		
		}
		
		
		
		/*
		 * 	Bulk updated messages
		 *
		 * 	Internal function that modifies the post type names in bulk updated messages
		 *
		 * 	@param array $messages an array of bulk updated messages
		 *
		*/
		function bulk_updated_messages( $bulk_messages, $bulk_counts ) {
			$singular = $this->singular;
			$plural = $this->plural;
			$bulk_messages[ $this->post_type_name ] = array(
				'updated'   => _n( '%s '.$singular.' updated.', '%s '.$plural.' updated.', $bulk_counts['updated'] ),
				'locked'    => _n( '%s '.$singular.' not updated, somebody is editing it.', '%s '.$plural.' not updated, somebody is editing them.', $bulk_counts['locked'] ),
				'deleted'   => _n( '%s '.$singular.' permanently deleted.', '%s '.$plural.' permanently deleted.', $bulk_counts['deleted'] ),
				'trashed'   => _n( '%s '.$singular.' moved to the Trash.', '%s '.$plural.' moved to the Trash.', $bulk_counts['trashed'] ),
				'untrashed' => _n( '%s '.$singular.' restored from the Trash.', '%s '.$plural.' restored from the Trash.', $bulk_counts['untrashed'] ),
			);
		
			return $bulk_messages;
		
		}
		
	
	
		/*
		 * 	Helper function for array_replace_recursive
		 *
		 * 	array_replace_recursive is a PHP 5.3 function, so let's add
		 *	backwards compadibility for PHP 5.2 users.
		 *
		 * 	@param array $base 				the origional array.
		 *	@param array $replacements		new elements to add to the $base array.
		 *
		 *	http://php.net/manual/en/function.array-replace-recursive.php
		 *
		*/
		function array_replace_recursive_fallback( $base, $replacements ) {
		    foreach( array_slice( func_get_args(), 1) as $replacements ) {
				$bref_stack = array( &$base );
				$head_stack = array( $replacements );
		
				do {
					end( $bref_stack );
		
					$bref = &$bref_stack[key( $bref_stack )];
					$head = array_pop( $head_stack );
		
					unset( $bref_stack[key( $bref_stack )] );
		
					foreach(array_keys( $head ) as $key ) {
						if( isset( $bref[$key] ) && is_array( $bref[$key] ) && is_array( $head[$key] ) ) {
							$bref_stack[] = &$bref[$key];
							$head_stack[] = $head[$key];
						} else {
							$bref[$key] = $head[$key];
						}
					}
				}
				while( count( $head_stack ) );
			}
		
			return $base;
		}	
	
	} // End class

} // End class_exists
