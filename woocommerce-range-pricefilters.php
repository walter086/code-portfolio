<?php
/**
 * Plugin Name: Woocommerce range pricefilter
 * Description: Price filter widget for shop category page
 * Version: 1.0
 * Author: Walter Oostland
*/

add_action('widgets_init', 'registerPriceFilterWidget');
function registerPriceFilterWidget() {
    register_widget('PriceRangeFilterWidget');
}



Class PriceRangeFilterWidget extends WP_Widget {

	public function __construct() {
        $widget_ops = array('classname' => 'PriceFilterWidget', 'description' => 'Prijs Range filter.' );
        parent::__construct('PrijsFilterWidget', 'Prijs range Filter', $widget_ops);

	}

	// back-end
    public function form($instance) {
        $title = '';
        if (isset($instance['title'])) $title = $instance['title'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /><br /><br />

            <?php
            $fields = isset($instance['filter_option_min']) ? $instance['filter_option_min'] : array();
            $field_num = count($fields);
            $fields[$field_num + 1] = '';
            $fields_counter = 0;

            echo 'Prijsfilters:<br /><table><tr><th>Min</th><th>Max</th></tr>';
            foreach ( $fields as $name => $value ) {
                echo '<tr>';
                echo sprintf(
                    '<td><input type="text" name="%1$s[%3$s]" value="%4$s" class="widefat"></td>' .
                    '<td><input type="text" name="%2$s[%3$s]" value="%5$s" class="widefat"></td>',
                    $this->get_field_name('filter_option_min'),
                    $this->get_field_name('filter_option_max'),
                    $fields_counter,
                    esc_attr($value),
                    (!empty($instance['filter_option_max'][$name]) ? esc_attr($instance['filter_option_max'][$name]) : '')
                );
                $fields_counter += 1;
                echo '</tr>';
            }
            echo '</tr></table>';
            ?>


        </p>
        <?php
    }

    //front-end
    public function widget($args, $instance) {
        $title = '';
        $filter_option_min = [];
        $filter_option_max = [];
        if (isset($instance['title'])) $title = $instance['title'];
        if (isset($instance['filter_option_min'])) {
            $filter_option_min = $instance['filter_option_min'];
            if (!is_array($filter_option_min)) $filter_option_min[] = $filter_option_min;
        }
        if (isset($instance['filter_option_max'])) {
            $filter_option_max = $instance['filter_option_max'];
            if (!is_array($filter_option_max)) $filter_option_max[] = $filter_option_max;
        }

        if (!empty($filter_option_min) && !empty($filter_option_max)) {
            ?>
            <aside class="widget">
                <span class="widget-title shop-sidebar"><?php echo $title ?></span>
                <ul>
                   <?php
                    // TODO: KORTER en versimpelen:
                   $current_link = strtok($_SERVER["REQUEST_URI"],'?');
                   $current_link2 = $_SERVER["REQUEST_URI"];
                   $query= parse_url ($_SERVER["REQUEST_URI"], PHP_URL_QUERY);
                   $current_link_query = $current_link . $query;

                   if ($current_link == $current_link_query ) { echo '<a href="'. $current_link . '" class="range-active">Alle prijsklassen</a>'; }
                   else {  echo '<a href="'. $current_link . '" class="range">Alle prijsklassen</a>'; }
                    foreach ($filter_option_min as $key => $val) {

                        $filterargs = [];
                        $filterargs['min_price'] = $val;
                        if (!empty($filter_option_max[$key])) $filterargs['max_price'] = $filter_option_max[$key];

                        $link = add_query_arg($filterargs, remove_query_arg(['min_price', 'max_price'], $_SERVER['REQUEST_URI']));
                        if ($link == $current_link2)  $class = "range-active"; else $class = "range";

                        echo '<li><a href="' . $link . '" class="'. $class . '">' . number_format($val, 2, ',', '.') . (!empty($filter_option_max[$key]) ? ' - ' . $filter_option_max[$key] : ' en hoger') . '</a></li>';
                        if (!empty($filter_option_max[$key])) $filterargs['max_price'] = $filter_option_max[$key];
                    }

                    ?>
                </ul>
            </aside>
            <?php
        }
	}

    public function update( $new_instance, $old_instance )
    {
        $instance          = $old_instance;
        $instance['title'] = esc_html( $new_instance['title'] );

        $instance['filter_option_min'] = [];
        if ( isset ( $new_instance['filter_option_min'] ) ) {
            foreach ( $new_instance['filter_option_min'] as $value ) {
                if ( '' !== trim( $value ) ) $instance['filter_option_min'][] = $value;
            }
        }

        $instance['filter_option_max'] = [];
        if ( isset ( $new_instance['filter_option_max'] ) ) {
            foreach ( $new_instance['filter_option_max'] as $value ) {
                if ( '' !== trim( $value ) ) $instance['filter_option_max'][] = $value;
            }
        }

        return $instance;
    }

}

