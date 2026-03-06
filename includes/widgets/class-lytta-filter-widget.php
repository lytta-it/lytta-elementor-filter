<?php
namespace Lytta_Filter\Widgets;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Lytta Filter Elementor Widget.
 */
class Lytta_Filter_Widget extends Widget_Base
{

    /**
     * Get widget name.
     */
    public function get_name()
    {
        return 'lytta_filter';
    }

    /**
     * Get widget title.
     */
    public function get_title()
    {
        return esc_html__('Lytta Advanced Filter', 'lytta-filter');
    }

    /**
     * Get widget icon.
     */
    public function get_icon()
    {
        return 'eicon-search';
    }

    /**
     * Get widget categories.
     */
    public function get_categories()
    {
        return ['general']; // TODO: Add custom category 'lytta' later if needed
    }

    /**
     * Get widget keywords.
     */
    public function get_keywords()
    {
        return ['filter', 'search', 'post', 'cpt', 'acf', 'lytta'];
    }

    /**
     * Register widget controls.
     */
    protected function register_controls()
    {

        $this->start_controls_section(
            'content_section',
        [
            'label' => esc_html__('Query Settings', 'lytta-filter'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]
        );

        $this->add_control(
            'target_query_id',
        [
            'label' => esc_html__('Target Query ID', 'lytta-filter'),
            'type' => Controls_Manager::TEXT,
            'description' => esc_html__('Enter the Query ID set in your Loop Grid widget.', 'lytta-filter'),
            'default' => 'lytta_filter',
        ]
        );

        $this->end_controls_section();

        // Filters Section
        $this->start_controls_section(
            'filters_section',
        [
            'label' => esc_html__('Filters', 'lytta-filter'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'filter_type',
        [
            'label' => esc_html__('Filter Type', 'lytta-filter'),
            'type' => Controls_Manager::SELECT,
            'default' => 'taxonomy',
            'options' => [
                'taxonomy' => esc_html__('Taxonomy', 'lytta-filter'),
                'acf' => esc_html__('ACF Field', 'lytta-filter'),
                'search' => esc_html__('Search Bar', 'lytta-filter'),
            ],
        ]
        );

        $repeater->add_control(
            'taxonomy_name',
        [
            'label' => esc_html__('Taxonomy', 'lytta-filter'),
            'type' => Controls_Manager::SELECT,
            'options' => $this->get_available_taxonomies(),
            'default' => 'category',
            'condition' => [
                'filter_type' => 'taxonomy',
            ],
        ]
        );

        $repeater->add_control(
            'acf_field_name',
        [
            'label' => esc_html__('ACF Field', 'lytta-filter'),
            'type' => Controls_Manager::SELECT,
            'options' => $this->get_available_acf_fields(),
            'condition' => [
                'filter_type' => 'acf',
            ],
        ]
        );

        $repeater->add_control(
            'acf_field_type',
        [
            'label' => esc_html__('Field Display/Logic Type', 'lytta-filter'),
            'type' => Controls_Manager::SELECT,
            'default' => 'select',
            'options' => [
                'select' => esc_html__('Select Dropdown', 'lytta-filter'),
                'checkbox' => esc_html__('Checkboxes', 'lytta-filter'),
                'range' => esc_html__('Number Range (Min/Max)', 'lytta-filter'),
            ],
            'condition' => [
                'filter_type' => 'acf',
            ],
        ]
        );

        $repeater->add_control(
            'show_label',
        [
            'label' => esc_html__('Show External Label', 'lytta-filter'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'lytta-filter'),
            'label_off' => esc_html__('Hide', 'lytta-filter'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]
        );

        $repeater->add_control(
            'filter_label',
        [
            'label' => esc_html__('External Label', 'lytta-filter'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Filter by...', 'lytta-filter'),
            'condition' => [
                'show_label' => 'yes',
            ],
        ]
        );

        $repeater->add_control(
            'input_placeholder',
        [
            'label' => esc_html__('Internal Text (Placeholder)', 'lytta-filter'),
            'type' => Controls_Manager::TEXT,
            'description' => esc_html__('Text inside the search bar or default dropdown option (e.g. "Select a Category").', 'lytta-filter'),
            'default' => esc_html__('Select...', 'lytta-filter'),
        ]
        );

        $this->add_control(
            'lytta_filters',
        [
            'label' => esc_html__('Active Filters', 'lytta-filter'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                [
                    'filter_type' => 'search',
                    'filter_label' => esc_html__('Search', 'lytta-filter'),
                ],
            ],
            'title_field' => '{{{ filter_label }}} ({{{ filter_type }}})',
        ]
        );

        $this->add_control(
            'show_submit_button',
        [
            'label' => esc_html__('Show Submit Button', 'lytta-filter'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'lytta-filter'),
            'label_off' => esc_html__('Hide', 'lytta-filter'),
            'return_value' => 'yes',
            'default' => 'yes',
            'description' => esc_html__('If hidden, filters will apply automatically on change.', 'lytta-filter'),
        ]
        );

        $this->add_control(
            'submit_button_text',
        [
            'label' => esc_html__('Submit Button Text', 'lytta-filter'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Apply Filters', 'lytta-filter'),
            'condition' => [
                'show_submit_button' => 'yes',
            ],
        ]
        );

        $this->add_control(
            'show_reset_button',
        [
            'label' => esc_html__('Show Reset Button', 'lytta-filter'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'lytta-filter'),
            'label_off' => esc_html__('Hide', 'lytta-filter'),
            'return_value' => 'yes',
            'default' => 'yes',
            'separator' => 'before',
        ]
        );

        $this->add_control(
            'reset_button_text',
        [
            'label' => esc_html__('Reset Button Text', 'lytta-filter'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Clear All', 'lytta-filter'),
            'condition' => [
                'show_reset_button' => 'yes',
            ],
        ]
        );

        $this->add_control(
            'show_active_chips',
        [
            'label' => esc_html__('Show Active Filters (Chips)', 'lytta-filter'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'lytta-filter'),
            'label_off' => esc_html__('Hide', 'lytta-filter'),
            'return_value' => 'yes',
            'default' => 'yes',
            'description' => esc_html__('Displays pills above the grid for active filters with an "X" to remove them.', 'lytta-filter'),
            'separator' => 'before',
        ]
        );

        $this->end_controls_section();

        // Styling Sections will go here
        $this->register_style_controls();
    }

    /**
     * Helper to get available taxonomies
     */
    protected function get_available_taxonomies()
    {
        $options = [];
        $taxonomies = get_taxonomies(['public' => true], 'objects');
        if (!empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                // Skip core taxonomies that usually aren't filtered like this, if desired. 
                // We'll allow them all for maximum flexibility.
                $options[$taxonomy->name] = $taxonomy->label . ' (' . $taxonomy->name . ')';
            }
        }
        return $options;
    }

    /**
     * Helper to get available ACF Fields
     */
    protected function get_available_acf_fields()
    {
        $options = [];
        if (function_exists('acf_get_field_groups')) {
            $groups = acf_get_field_groups();
            if (!empty($groups)) {
                foreach ($groups as $group) {
                    $fields = acf_get_fields($group['key']);
                    if (!empty($fields)) {
                        foreach ($fields as $field) {
                            $options[$field['name']] = $field['label'] . ' (' . $field['name'] . ')';
                        }
                    }
                }
            }
        }

        if (empty($options)) {
            $options[''] = esc_html__('No ACF Fields Found', 'lytta-filter');
        }

        return $options;
    }

    /**
     * Register Style Controls
     */
    protected function register_style_controls()
    {
        // Container Background Section
        $this->start_controls_section(
            'style_container_section',
        [
            'label' => esc_html__('Container / Background', 'lytta-filter'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
        );

        $this->add_control(
            'container_bg_color',
        [
            'label' => esc_html__('Background Color', 'lytta-filter'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lytta-filters-container' => 'background-color: {{VALUE}};',
            ],
        ]
        );

        $this->add_responsive_control(
            'container_padding',
        [
            'label' => esc_html__('Padding', 'lytta-filter'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .lytta-filters-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
        [
            'name' => 'container_border',
            'label' => esc_html__('Border', 'lytta-filter'),
            'selector' => '{{WRAPPER}} .lytta-filters-container',
        ]
        );

        $this->add_control(
            'container_border_radius',
        [
            'label' => esc_html__('Border Radius', 'lytta-filter'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .lytta-filters-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
        [
            'name' => 'container_box_shadow',
            'label' => esc_html__('Box Shadow', 'lytta-filter'),
            'selector' => '{{WRAPPER}} .lytta-filters-container',
        ]
        );

        $this->end_controls_section();

        // Layout Style Section
        $this->start_controls_section(
            'style_layout_section',
        [
            'label' => esc_html__('Form Layout', 'lytta-filter'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
        );

        $this->add_responsive_control(
            'form_direction',
        [
            'label' => esc_html__('Direction', 'lytta-filter'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'row' => [
                    'title' => esc_html__('Row (Inline)', 'lytta-filter'),
                    'icon' => 'eicon-h-align-left',
                ],
                'column' => [
                    'title' => esc_html__('Column (Stacked)', 'lytta-filter'),
                    'icon' => 'eicon-v-align-top',
                ],
            ],
            'default' => 'row',
            'selectors' => [
                '{{WRAPPER}} .lytta-filter-form' => 'display: flex; flex-direction: {{VALUE}}; flex-wrap: wrap;',
            ],
        ]
        );

        $this->add_responsive_control(
            'form_gap',
        [
            'label' => esc_html__('Gap Between Filters', 'lytta-filter'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', 'rem'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 15,
            ],
            'selectors' => [
                '{{WRAPPER}} .lytta-filter-form' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]
        );

        $this->add_responsive_control(
            'form_alignment',
        [
            'label' => esc_html__('Alignment', 'lytta-filter'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [
                    'title' => esc_html__('Start', 'lytta-filter'),
                    'icon' => 'eicon-align-start-h',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'lytta-filter'),
                    'icon' => 'eicon-align-center-h',
                ],
                'flex-end' => [
                    'title' => esc_html__('End', 'lytta-filter'),
                    'icon' => 'eicon-align-end-h',
                ],
            ],
            'default' => 'flex-start',
            'selectors' => [
                '{{WRAPPER}} .lytta-filter-form' => 'justify-content: {{VALUE}};',
            ],
        ]
        );

        $this->end_controls_section();

        // Filter Items Style Section
        $this->start_controls_section(
            'style_item_section',
        [
            'label' => esc_html__('Filter Items', 'lytta-filter'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
        );

        $this->add_responsive_control(
            'item_width',
        [
            'label' => esc_html__('Item Width', 'lytta-filter'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'vw'],
            'range' => [
                'px' => [
                    'min' => 50,
                    'max' => 500,
                ],
                '%' => [
                    'min' => 10,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .lytta-filter-item' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]
        );

        $this->end_controls_section();

        // Labels Style Section
        $this->start_controls_section(
            'style_labels_section',
        [
            'label' => esc_html__('Labels', 'lytta-filter'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
        [
            'name' => 'label_typography',
            'label' => esc_html__('Typography', 'lytta-filter'),
            'selector' => '{{WRAPPER}} .lytta-filter-label',
        ]
        );

        $this->add_control(
            'label_color',
        [
            'label' => esc_html__('Color', 'lytta-filter'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lytta-filter-label' => 'color: {{VALUE}};',
            ],
        ]
        );

        $this->add_responsive_control(
            'label_spacing',
        [
            'label' => esc_html__('Spacing', 'lytta-filter'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em'],
            'range' => [
                'px' => ['min' => 0, 'max' => 50],
            ],
            'selectors' => [
                '{{WRAPPER}} .lytta-filter-label' => 'margin-bottom: {{SIZE}}{{UNIT}}; display: block;',
            ],
        ]
        );

        $this->end_controls_section();

        // Inputs & Selects Style Section
        $this->start_controls_section(
            'style_inputs_section',
        [
            'label' => esc_html__('Inputs & Dropdowns', 'lytta-filter'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
        [
            'name' => 'input_typography',
            'label' => esc_html__('Typography', 'lytta-filter'),
            'selector' => '{{WRAPPER}} .lytta-input-search, {{WRAPPER}} .lytta-input-select, {{WRAPPER}} .lytta-input-number',
        ]
        );

        $this->start_controls_tabs('tabs_input_style');

        // Normal Tab
        $this->start_controls_tab(
            'tab_input_normal',
        [
            'label' => esc_html__('Normal', 'lytta-filter'),
        ]
        );

        $this->add_control(
            'input_text_color',
        [
            'label' => esc_html__('Text Color', 'lytta-filter'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lytta-input-search, {{WRAPPER}} .lytta-input-select, {{WRAPPER}} .lytta-input-number' => 'color: {{VALUE}};',
            ],
        ]
        );

        $this->add_control(
            'input_bg_color',
        [
            'label' => esc_html__('Background Color', 'lytta-filter'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lytta-input-search, {{WRAPPER}} .lytta-input-select, {{WRAPPER}} .lytta-input-number' => 'background-color: {{VALUE}};',
            ],
        ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
        [
            'name' => 'input_border',
            'label' => esc_html__('Border', 'lytta-filter'),
            'selector' => '{{WRAPPER}} .lytta-input-search, {{WRAPPER}} .lytta-input-select, {{WRAPPER}} .lytta-input-number',
        ]
        );

        $this->end_controls_tab();

        // Focus Tab
        $this->start_controls_tab(
            'tab_input_focus',
        [
            'label' => esc_html__('Focus', 'lytta-filter'),
        ]
        );

        $this->add_control(
            'input_text_color_focus',
        [
            'label' => esc_html__('Text Color', 'lytta-filter'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lytta-input-search:focus, {{WRAPPER}} .lytta-input-select:focus, {{WRAPPER}} .lytta-input-number:focus' => 'color: {{VALUE}};',
            ],
        ]
        );

        $this->add_control(
            'input_bg_color_focus',
        [
            'label' => esc_html__('Background Color', 'lytta-filter'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lytta-input-search:focus, {{WRAPPER}} .lytta-input-select:focus, {{WRAPPER}} .lytta-input-number:focus' => 'background-color: {{VALUE}};',
            ],
        ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
        [
            'name' => 'input_border_focus',
            'label' => esc_html__('Border', 'lytta-filter'),
            'selector' => '{{WRAPPER}} .lytta-input-search:focus, {{WRAPPER}} .lytta-input-select:focus, {{WRAPPER}} .lytta-input-number:focus',
        ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'input_border_radius',
        [
            'label' => esc_html__('Border Radius', 'lytta-filter'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .lytta-input-search, {{WRAPPER}} .lytta-input-select, {{WRAPPER}} .lytta-input-number' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'separator' => 'before',
        ]
        );

        $this->add_responsive_control(
            'input_padding',
        [
            'label' => esc_html__('Padding', 'lytta-filter'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .lytta-input-search, {{WRAPPER}} .lytta-input-select, {{WRAPPER}} .lytta-input-number' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
        );

        $this->end_controls_section();

        // Submit Button Style Section
        $this->start_controls_section(
            'style_submit_section',
        [
            'label' => esc_html__('Submit Button', 'lytta-filter'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
        [
            'name' => 'submit_typography',
            'label' => esc_html__('Typography', 'lytta-filter'),
            'selector' => '{{WRAPPER}} .lytta-btn-submit',
        ]
        );

        $this->start_controls_tabs('tabs_submit_style');

        // Normal Tab
        $this->start_controls_tab(
            'tab_submit_normal',
        [
            'label' => esc_html__('Normal', 'lytta-filter'),
        ]
        );

        $this->add_control(
            'submit_text_color',
        [
            'label' => esc_html__('Text Color', 'lytta-filter'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lytta-btn-submit' => 'color: {{VALUE}};',
            ],
        ]
        );

        $this->add_control(
            'submit_bg_color',
        [
            'label' => esc_html__('Background Color', 'lytta-filter'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lytta-btn-submit' => 'background-color: {{VALUE}};',
            ],
        ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
        [
            'name' => 'submit_border',
            'label' => esc_html__('Border', 'lytta-filter'),
            'selector' => '{{WRAPPER}} .lytta-btn-submit',
        ]
        );

        $this->end_controls_tab();

        // Hover Tab
        $this->start_controls_tab(
            'tab_submit_hover',
        [
            'label' => esc_html__('Hover', 'lytta-filter'),
        ]
        );

        $this->add_control(
            'submit_text_color_hover',
        [
            'label' => esc_html__('Text Color', 'lytta-filter'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lytta-btn-submit:hover' => 'color: {{VALUE}};',
            ],
        ]
        );

        $this->add_control(
            'submit_bg_color_hover',
        [
            'label' => esc_html__('Background Color', 'lytta-filter'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lytta-btn-submit:hover' => 'background-color: {{VALUE}};',
            ],
        ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
        [
            'name' => 'submit_border_hover',
            'label' => esc_html__('Border', 'lytta-filter'),
            'selector' => '{{WRAPPER}} .lytta-btn-submit:hover',
        ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'submit_border_radius',
        [
            'label' => esc_html__('Border Radius', 'lytta-filter'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .lytta-btn-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'separator' => 'before',
        ]
        );

        $this->add_responsive_control(
            'submit_padding',
        [
            'label' => esc_html__('Padding', 'lytta-filter'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .lytta-btn-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
        );

        $this->add_responsive_control(
            'submit_margin',
        [
            'label' => esc_html__('Margin', 'lytta-filter'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .lytta-filter-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $target_query_id = $settings['target_query_id'];
        $filters = $settings['lytta_filters'];

        $show_submit = ($settings['show_submit_button'] === 'yes');
        $submit_text = !empty($settings['submit_button_text']) ? $settings['submit_button_text'] : __('Apply Filters', 'lytta-filter');
        $auto_submit = $show_submit ? 'no' : 'yes';

        // Generate a unique ID for the widget instance
        $widget_id = $this->get_id();

?>
		<div class="lytta-filter-wrapper" id="lytta-filter-<?php echo esc_attr($widget_id); ?>" data-target-query-id="<?php echo esc_attr($target_query_id); ?>" data-auto-submit="<?php echo esc_attr($auto_submit); ?>">
			
			<?php
        // Active Chips logic
        if ($settings['show_active_chips'] === 'yes'):
            $active_chips = [];

            // Re-iterate through filters to build chips for active URL params
            foreach ($filters as $filter) {
                if ($filter['filter_type'] === 'search' && !empty($_GET['lytta_search'])) {
                    $active_chips[] = [
                        'label' => !empty($filter['filter_label']) ? $filter['filter_label'] : __('Search', 'lytta-filter'),
                        'value' => sanitize_text_field($_GET['lytta_search']),
                        'param' => 'lytta_search',
                    ];
                }
                elseif ($filter['filter_type'] === 'taxonomy') {
                    $tax_name = $filter['taxonomy_name'];
                    if (!empty($_GET['lytta_tax_' . $tax_name])) {
                        $term_slug = sanitize_text_field($_GET['lytta_tax_' . $tax_name]);
                        $term = get_term_by('slug', $term_slug, $tax_name);
                        $active_chips[] = [
                            'label' => !empty($filter['filter_label']) ? $filter['filter_label'] : $tax_name,
                            'value' => $term ? $term->name : $term_slug,
                            'param' => 'lytta_tax_' . $tax_name,
                        ];
                    }
                }
                elseif ($filter['filter_type'] === 'acf') {
                    $acf_name = $filter['acf_field_name'];
                    if ($filter['acf_field_type'] === 'select' && !empty($_GET['lytta_acf_' . $acf_name])) {
                        $active_chips[] = [
                            'label' => !empty($filter['filter_label']) ? $filter['filter_label'] : $acf_name,
                            'value' => sanitize_text_field($_GET['lytta_acf_' . $acf_name]),
                            'param' => 'lytta_acf_' . $acf_name,
                        ];
                    }
                    elseif ($filter['acf_field_type'] === 'range') {
                        $min = isset($_GET['lytta_acf_min_' . $acf_name]) ? floatval($_GET['lytta_acf_min_' . $acf_name]) : '';
                        $max = isset($_GET['lytta_acf_max_' . $acf_name]) ? floatval($_GET['lytta_acf_max_' . $acf_name]) : '';
                        if ($min !== '' || $max !== '') {
                            $value_str = ($min !== '' ? "Min: $min " : "") . ($max !== '' ? "Max: $max" : "");
                            $active_chips[] = [
                                'label' => !empty($filter['filter_label']) ? $filter['filter_label'] : $acf_name,
                                'value' => trim($value_str),
                                'param' => 'lytta_acf_range_' . $acf_name, // Custom compound param for JS
                                'min_param' => 'lytta_acf_min_' . $acf_name,
                                'max_param' => 'lytta_acf_max_' . $acf_name,
                            ];
                        }
                    }
                }
            }

            if (!empty($active_chips)):
?>
				<div class="lytta-active-chips">
					<?php foreach ($active_chips as $chip): ?>
						<span class="lytta-chip" data-param="<?php echo esc_attr($chip['param']); ?>" 
                              <?php if (isset($chip['min_param'])) {
                        echo 'data-min-param="' . esc_attr($chip['min_param']) . '" data-max-param="' . esc_attr($chip['max_param']) . '"';
                    }?>>
							<strong><?php echo esc_html($chip['label']); ?>:</strong> <?php echo esc_html($chip['value']); ?>
							<span class="lytta-chip-remove" role="button" aria-label="<?php esc_attr_e('Remove filter', 'lytta-filter'); ?>">&times;</span>
						</span>
					<?php
                endforeach; ?>
				</div>
			<?php
            endif;
        endif; ?>

			<!-- Filters Container -->
			<div class="lytta-filters-container">
				<form class="lytta-filter-form" action="">
					<?php foreach ($filters as $filter): ?>
						<div class="lytta-filter-item lytta-filter-type-<?php echo esc_attr($filter['filter_type']); ?>">
							
							<?php if ($filter['show_label'] === 'yes' && !empty($filter['filter_label'])): ?>
								<label class="lytta-filter-label"><?php echo esc_html($filter['filter_label']); ?></label>
							<?php
            endif; ?>
							
							<?php
            // RENDER FILTER INPUTS
            $placeholder = !empty($filter['input_placeholder']) ? esc_html($filter['input_placeholder']) : '';

            switch ($filter['filter_type']) {
                case 'search':
                    $search_value = isset($_GET['lytta_search']) ? sanitize_text_field($_GET['lytta_search']) : '';
?>
									<input type="text" name="lytta_search" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_attr($search_value); ?>" class="lytta-input-search">
									<?php
                    break;

                case 'taxonomy':
                    $tax_name = $filter['taxonomy_name'];
                    $active_tax = isset($_GET['lytta_tax_' . $tax_name]) ? sanitize_text_field($_GET['lytta_tax_' . $tax_name]) : '';
                    $terms = get_terms([
                        'taxonomy' => $tax_name,
                        'hide_empty' => false,
                    ]);
?>
									<select name="lytta_tax_<?php echo esc_attr($tax_name); ?>" class="lytta-input-select" data-taxonomy="<?php echo esc_attr($tax_name); ?>">
										<option value=""><?php echo esc_html($placeholder); ?></option>
										<?php if (!is_wp_error($terms) && !empty($terms)): ?>
											<?php foreach ($terms as $term): ?>
												<option value="<?php echo esc_attr($term->slug); ?>" <?php selected($active_tax, $term->slug); ?>><?php echo esc_html($term->name); ?></option>
											<?php
                        endforeach; ?>
										<?php
                    endif; ?>
									</select>
									<?php
                    break;

                case 'acf':
                    // Render ACF filter based on field type
                    $acf_name = $filter['acf_field_name'];
                    $acf_type = $filter['acf_field_type'];

                    if ($acf_type === 'select') {
                        $active_acf = isset($_GET['lytta_acf_' . $acf_name]) ? sanitize_text_field($_GET['lytta_acf_' . $acf_name]) : '';
                        echo '<select name="lytta_acf_' . esc_attr($acf_name) . '" class="lytta-input-select" data-acf="' . esc_attr($acf_name) . '">';
                        echo '<option value="">' . esc_html($placeholder) . '</option>';

                        $choices = [];

                        // Try to get choices directly from ACF if it is a choice field
                        if (function_exists('acf_get_field')) {
                            $acf_field = acf_get_field($acf_name);
                            if ($acf_field && isset($acf_field['choices']) && !empty($acf_field['choices'])) {
                                $choices = $acf_field['choices'];
                            }
                        }

                        // If no choices in ACF settings, try to get existing metadata values from DB
                        if (empty($choices)) {
                            global $wpdb;
                            // Fetch distinct values for this meta key from published posts
                            $results = $wpdb->get_col($wpdb->prepare(
                                "SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
                                INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                                WHERE pm.meta_key = %s 
                                AND p.post_status = 'publish' 
                                AND pm.meta_value != ''",
                                $acf_name
                            ));

                            if ($results) {
                                foreach ($results as $val) {
                                    // Handle serialized arrays if ACF saved multiple values
                                    if (is_serialized($val)) {
                                        $unserialized = maybe_unserialize($val);
                                        if (is_array($unserialized)) {
                                            foreach ($unserialized as $u_val) {
                                                $choices[$u_val] = $u_val;
                                            }
                                        }
                                    }
                                    else {
                                        $choices[$val] = $val;
                                    }
                                }
                            }
                        }

                        if (!empty($choices)) {
                            foreach ($choices as $choice_value => $choice_label) {
                                $selected = selected($active_acf, $choice_value, false);
                                echo '<option value="' . esc_attr($choice_value) . '" ' . $selected . '>' . esc_html($choice_label) . '</option>';
                            }
                        }

                        echo '</select>';
                    }
                    elseif ($acf_type === 'range') {
                        $min_val = isset($_GET['lytta_acf_min_' . $acf_name]) ? floatval($_GET['lytta_acf_min_' . $acf_name]) : '';
                        $max_val = isset($_GET['lytta_acf_max_' . $acf_name]) ? floatval($_GET['lytta_acf_max_' . $acf_name]) : '';
                        echo '<div class="lytta-range-inputs">';
                        echo '<input type="number" name="lytta_acf_min_' . esc_attr($acf_name) . '" placeholder="Min" value="' . esc_attr($min_val) . '" class="lytta-input-number">';
                        echo '<input type="number" name="lytta_acf_max_' . esc_attr($acf_name) . '" placeholder="Max" value="' . esc_attr($max_val) . '" class="lytta-input-number">';
                        echo '</div>';
                    }
                    break;
            }
?>

						</div>
					<?php
        endforeach; ?>
                    
                    <?php if ($show_submit || $settings['show_reset_button'] === 'yes'): ?>
                    <div class="lytta-filter-submit">
                        <?php if ($show_submit): ?>
                            <button type="submit" class="lytta-btn-submit"><?php echo esc_html($submit_text); ?></button>
                        <?php
            endif; ?>
                        
                        <?php if ($settings['show_reset_button'] === 'yes'): ?>
                            <?php $reset_text = !empty($settings['reset_button_text']) ? $settings['reset_button_text'] : __('Clear All', 'lytta-filter'); ?>
                            <button type="button" class="lytta-btn-reset"><?php echo esc_html($reset_text); ?></button>
                        <?php
            endif; ?>
                    </div>
                    <?php
        endif; ?>
				</form>
			</div>

		</div>
		<?php
    }
}
