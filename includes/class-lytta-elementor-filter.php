<?php
namespace Lytta_Filter;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Main Lytta Elementor Filter Class
 */
final class Lytta_Elementor_Filter
{

    /**
     * Plugin Version
     */
    const VERSION = '1.0.0';

    /**
     * Minimum Elementor Version
     */
    const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

    /**
     * Minimum PHP Version
     */
    const MINIMUM_PHP_VERSION = '7.3';

    /**
     * Instance
     */
    private static $_instance = null;

    /**
     * Instance
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('init', [$this, 'i18n']);
        $this->init();
    }

    /**
     * Load Textdomain
     */
    public function i18n()
    {
        load_plugin_textdomain('lytta-filter', false, dirname(plugin_basename(__DIR__)) . '/languages');
    }

    /**
     * Initialize the plugin
     */
    public function init()
    {
        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return;
        }

        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return;
        }


        // We need to hook into 'elementor/query/{query_id}' but since we don't know the query ID
        // in advance, we'll hook into 'pre_get_posts' or we can add a wildcard hook if possible.
        // Elementor provides action: "elementor/query/{$query_id}". 
        // A better approach is to use the general hook 'elementor/query/query_results' or similar, 
        // but 'pre_get_posts' is usually safer for global adjustments if we check for our parameters.
        // Actually, Elementor recommends using the specific query ID. We can add a generic hook 
        // that checks ALL Elementor queries, or we can instruct the user to use a specific query ID, e.g., 'lytta_filter'.
        // Let's hook into `pre_get_posts` globally but only apply if Elementor is doing a query AND our GET params are present,
        // OR better yet, let's use the specific action 'elementor/query/lytta_filter' as a default, and allow custom ones via filter.
        // For maximum compatibility, let's intercept 'pre_get_posts' and check if we are on frontend and have our query parameters.
        add_action('pre_get_posts', [$this, 'modify_elementor_query']);

        // Register scripts & styles
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_styles']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'register_scripts']);

        // Register Widget
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
    }

    /**
     * Admin notice
     * Warning when the site doesn't have a minimum required Elementor version.
     */
    public function admin_notice_minimum_elementor_version()
    {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'lytta-filter'),
            '<strong>' . esc_html__('Lytta Elementor Advanced Filter', 'lytta-filter') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'lytta-filter') . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice
     * Warning when the site doesn't have a minimum required PHP version.
     */
    public function admin_notice_minimum_php_version()
    {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'lytta-filter'),
            '<strong>' . esc_html__('Lytta Elementor Advanced Filter', 'lytta-filter') . '</strong>',
            '<strong>' . esc_html__('PHP', 'lytta-filter') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Enqueue Frontend Styles
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            'lytta-filter-css',
            LYTTA_FILTER_URL . 'assets/css/lytta-filter.css',
        [],
            self::VERSION
        );
    }

    /**
     * Register Frontend Scripts
     */
    public function register_scripts()
    {
        wp_register_script(
            'lytta-filter-js',
            LYTTA_FILTER_URL . 'assets/js/lytta-filter.js',
        ['jquery'],
            self::VERSION,
            true
        );

        // Localize script
        // For the current URL, simpler to use a generic JS reload, or just home_url() and append.
        wp_localize_script('lytta-filter-js', 'lyttaFilter', [
            'ajaxUrl' => admin_url('admin-ajax.php')
        ]);
    }

    /**
     * Register Widgets
     */
    public function register_widgets($widgets_manager)
    {
        require_once LYTTA_FILTER_DIR . 'includes/widgets/class-lytta-filter-widget.php';
        $widgets_manager->register(new Widgets\Lytta_Filter_Widget());
    }

    /**
     * Modify Elementor Query based on URL parameters.
     * We hook into pre_get_posts to catch Elementor's queries.
     */
    public function modify_elementor_query($query)
    {
        // Don't modify in the admin
        if (is_admin() && !wp_doing_ajax()) {
            return;
        }

        // Only modify if it's the main query OR if it's an Elementor widget query
        // Elementor uses 'pre_get_posts' when running its Loop Grid.
        // To be safe, we only apply filters if our GET parameters are present
        // and we ensure we don't break menus or other minor queries.

        $has_lytta_params = false;
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'lytta_') === 0 && !empty($value)) {
                $has_lytta_params = true;
                break;
            }
        }

        if (!$has_lytta_params) {
            return;
        }

        // Try to target only relevant queries (Posts, Loop Grid)
        // Elementor usually sets 'suppress_filters' to false for these.
        if ($query->get('suppress_filters')) {
            return;
        }

        // Additional safety check: only modify if it's a post, page or custom post type query
        // not attachments, nav menus, etc.
        $post_type = $query->get('post_type');
        if ($post_type === 'nav_menu_item' || $post_type === 'attachment') {
            return;
        }

        $tax_query = $query->get('tax_query') ?: [];
        $meta_query = $query->get('meta_query') ?: [];
        $search_query = $query->get('s') ?: '';

        foreach ($_GET as $name => $value) {
            if (empty($value))
                continue;

            $value = sanitize_text_field($value);

            if ($name === 'lytta_search') {
                $query->set('s', $value);
            }
            elseif (strpos($name, 'lytta_tax_') === 0) {
                $tax_slug = str_replace('lytta_tax_', '', $name);
                $tax_query[] = [
                    'taxonomy' => $tax_slug,
                    'field' => 'slug',
                    'terms' => $value,
                ];
            }
            elseif (strpos($name, 'lytta_acf_') === 0) {
                if (strpos($name, 'lytta_acf_min_') === 0) {
                    $meta_key = str_replace('lytta_acf_min_', '', $name);
                    $meta_query[] = [
                        'key' => $meta_key,
                        'value' => floatval($value),
                        'compare' => '>=',
                        'type' => 'NUMERIC'
                    ];
                }
                elseif (strpos($name, 'lytta_acf_max_') === 0) {
                    $meta_key = str_replace('lytta_acf_max_', '', $name);
                    $meta_query[] = [
                        'key' => $meta_key,
                        'value' => floatval($value),
                        'compare' => '<=',
                        'type' => 'NUMERIC'
                    ];
                }
                else {
                    $meta_key = str_replace('lytta_acf_', '', $name);
                    $meta_query[] = [
                        'key' => $meta_key,
                        'value' => $value,
                        'compare' => 'LIKE'
                    ];
                }
            }
        }

        if (!empty($tax_query)) {
            $tax_query['relation'] = 'AND';
            $query->set('tax_query', $tax_query);
        }

        if (!empty($meta_query)) {
            $meta_query['relation'] = 'AND';
            $query->set('meta_query', $meta_query);
        }
    }
}
