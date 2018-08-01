# wordpress-cpt-generator-class
A PHP class that helps in creating custom post types and their taxonomies. Simply include the library in your theme or plugin, initiate a new class to BNE_CPT

### Quick Usage
1. Include the library in your application
2. Setup the Custom Post Type (basic usage). For example, let's register a custom post type for books.

```php
$cpt = new BNE_CPT( 'my_books_cpt_slug', array(
	'singular'	=>	__( 'Book', 'bne-testimonials' ),
	'plural'	=> 	__( 'Books', 'bne-testimonials' ),
	'slug'		=>	'books',
	'args'		=>	array(
		'labels'	=>	array(
			'menu_name'	=>	__( 'Books', 'text-domain' ),
		),
		'supports'	=>	array( 'title', 'editor', 'thumbnail' ),
		'menu_icon'	=>	'dashicons-book-alt',
		'show_ui'	=>	true,
		'show_in_menu'	=>	true,
	)
));
```

3. Setup a custom Taxomony (basic usage). Going with our Books post type, we'll register a taxonomy for Genres.
```php
$cpt->register_taxonomy( 'my_custom_taxonomy_name', array(
	'singular'	=>	__( 'Genre', 'text-domain' ),
	'plural'	=>	__( 'Genres', 'text-domain' ),
	'slug'		=>	'categories',
	'args'		=>	array(
		'labels'	=>	array(
			'menu_name'	=>	__( 'Genres', 'text-domain' ),
		),
		'show_admin_column'	=>	true,
		'hierarchical'		=>	true,
		'public'		=>	false,
		'show_ui'		=>	true,
		'show_tagcloud'		=>	false,
		'show_in_nav_menus'	=>	false,
		'show_admin_column'	=>	true,
		'rewrite'		=>	false,
	)
));
```

4. If needed, assign your textdomain for translation.
```php
$cpt->set_textdomain( 'text-domain' );
```

### What Options are available?
All of the arguments used for ```register_post_type``` and ```register_taxonomy``` can be set. Any options omitted in the above examples will use their default settings defined by WordPress.
- [View Register Post Type Options](https://codex.wordpress.org/Function_Reference/register_post_type)
- [View Register Taxonomy Options](https://codex.wordpress.org/Function_Reference/register_taxonomy)
