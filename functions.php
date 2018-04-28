<?php
/**
 * Seismic Dark functions and definitions
 *
 */

//Initialize the update checker.
require_once( get_template_directory() .'/inc/theme-update-checker.php' );
$my_theme = wp_get_theme();
$example_update_checker = new ThemeUpdateChecker(
	'standard', 
	'http://version.seismicthemes.com/check.php?theme='.$my_theme->get( 'TextDomain' ).'&domain='.domain(home_url())
);

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640;

/** Tell WordPress to run seismicdark_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'seismicdark_setup' );

if ( ! function_exists( 'seismicdark_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 */
function seismicdark_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Post Format support. You can also use the legacy "gallery" or "asides" (note the plural) categories.
	add_theme_support( 'post-formats', array( 'aside', 'gallery' ) );

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Let WordPress handle the page titles
	add_theme_support( 'title-tag' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'seismic-dark', get_template_directory() . '/languages' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'seismic-dark' ),
	) );

	// This theme allows users to set a custom background.
	add_theme_support( 'custom-background', array(
		
	) );
}
endif;

/**
 * Use Google's jquery api
 */
function pretty_javascript() {
    wp_enqueue_script( 'jquery' );
    wp_register_script( 'menu', get_template_directory_uri() . '/js/menu.js');
    wp_enqueue_script( 'menu' );
    wp_register_script( 'media_queries', get_template_directory_uri() . '/js/css3-mediaqueries.js');
    wp_enqueue_script( 'media_queries' );
}
add_action('wp_enqueue_scripts', 'pretty_javascript');

//wp_enqueue_script('menu', get_template_directory() . '/js/menu.js' );

if ( ! function_exists( 'seismicdark_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 */
function seismicdark_admin_header_style() {
?>
<style type="text/css">
/* Shows the same border as on front end */
#headimg {
	
}
/* If header-text was supported, you would style the text with these selectors:
	#headimg #name { }
	#headimg #desc { }
*/
</style>
<?php
}
endif;

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 */
function seismicdark_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'seismicdark_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 *
 */
function seismicdark_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'seismicdark_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 */
function seismicdark_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'seismic-dark' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and seismicdark_continue_reading_link().
 *
 */
function seismicdark_auto_excerpt_more( $more ) {
	return ' &hellip;' . seismicdark_continue_reading_link();
}
add_filter( 'excerpt_more', 'seismicdark_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 */
function seismicdark_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= seismicdark_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'seismicdark_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 */
add_filter( 'use_default_gallery_style', '__return_false' );

/**
 * Deprecated way to remove inline styles printed when the gallery shortcode is used.
 *
 */
function seismicdark_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
// Backwards compatibility with WordPress 3.0.
if ( version_compare( $GLOBALS['wp_version'], '3.1', '<' ) )
	add_filter( 'gallery_style', 'seismicdark_remove_gallery_css' );

if ( ! function_exists( 'seismicdark_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 */
function seismicdark_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'seismic-dark' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'seismic-dark' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'seismic-dark' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'seismic-dark' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'seismic-dark' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'seismic-dark' ), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 */
function seismicdark_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'seismic-dark' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'seismic-dark' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Secondary Widget Area', 'seismic-dark' ),
		'id' => 'secondary-widget-area',
		'description' => __( 'The secondary widget area', 'seismic-dark' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 3, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'First Footer Widget Area', 'seismic-dark' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'The first footer widget area', 'seismic-dark' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 4, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Second Footer Widget Area', 'seismic-dark' ),
		'id' => 'second-footer-widget-area',
		'description' => __( 'The second footer widget area', 'seismic-dark' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 5, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Third Footer Widget Area', 'seismic-dark' ),
		'id' => 'third-footer-widget-area',
		'description' => __( 'The third footer widget area', 'seismic-dark' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 6, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Fourth Footer Widget Area', 'seismic-dark' ),
		'id' => 'fourth-footer-widget-area',
		'description' => __( 'The fourth footer widget area', 'seismic-dark' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
/** Register sidebars by running seismicdark_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'seismicdark_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 */
function seismicdark_remove_recent_comments_style() {
	add_filter( 'show_recent_comments_widget_style', '__return_false' );
}
add_action( 'widgets_init', 'seismicdark_remove_recent_comments_style' );

if ( ! function_exists( 'seismicdark_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 */
function seismicdark_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'seismic-dark' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'seismic-dark' ), get_the_author() ) ),
			get_the_author()
		)
	);
}
endif;

if ( ! function_exists( 'seismicdark_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 */
function seismicdark_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'seismic-dark' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'seismic-dark' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'seismic-dark' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;

function seismicdark_add_styles() {
	wp_enqueue_style('seismicdark-style', get_stylesheet_uri(), false, '0.5');
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action('wp_enqueue_scripts', 'seismicdark_add_styles');

/**
 * Tell WordPress to customize the page title.
 */
function seismicdark_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'seismic-dark' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'seismicdark_wp_title', 10, 2 );