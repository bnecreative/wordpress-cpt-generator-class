# wordpress-cpt-generator-class
A PHP class that helps in creating custom post types and their taxonomies.

### Quick Usage
1. Include the library in your application
2. Setup the Custom Post Type (basic usage)

```
$cpt = new BNE_CPT( 'your_post_type_name, array(
	'singular'	=>	__( 'Book', 'bne-testimonials' ),
	'plural'	=> 	__( 'Books', 'bne-testimonials' ),
	'slug'		=>	'books',
	'args'		=>	array(
		'labels'	=>	array(
			'menu_name'	=>	__( 'Books', 'text-domain' ),
		),
		'supports'				=>	array( 'title', 'editor', 'thumbnail' ),
		'menu_icon'			    =>	'dashicons-id-alt',
		'public'				=>	false,
		'publicly_queryable'	=>	false,
		'exclude_from_search'	=>	true,
		'show_ui'				=>	true,
		'show_in_menu'			=>	true,
		'show_in_nav_menus'		=>	false,
		'show_in_admin_bar'		=>	false,
		'hierarchical'			=>	false,
		'has_archive'			=>	false,
	),
));
```

3. Setup a custom Taxomony (basic usage)
```
$cpt->register_taxonomy( 'my_custom_taxonomy_name', array(
	'singular'	=>	__( 'Category', 'text-domain' ),
	'plural'	=>	__( 'Categories', 'text-domain' ),
	'slug'		=>	'categories',
	'args'		=>	array(
		'labels'	=>	array(
			'menu_name'		=>	__( 'Categories', 'text-domain' ),
		),
		'show_admin_column'		=>	true,
		'hierarchical'			=>	true,
		'public'				=>	false,
		'show_ui'				=>	true,
		'show_tagcloud'			=>	false,
		'show_in_nav_menus'		=>	false,
		'show_admin_column'		=>	true,
		'rewrite'				=>	false,
	)
));
```

4. Assign your textdomain for translation
```
$cpt->set_textdomain( 'text-domain' );
```

### What Options are available?
All of the arguments used ```register_post_type``` and ```register_taxonomy``` can be set.
