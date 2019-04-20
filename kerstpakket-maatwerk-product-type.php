<?php
/**
 * Plugin Name: Product type kerstpakket Maatwerk / zelf samenstellen
 * Description: Product type kerstpakket Maatwerk / zelf samenstellen
 * Version: 1.0
 * Author: Walter Oostland
 */

add_action('plugins_loaded', 'RegisterKerstpakketMaatwerkType');

function RegisterKerstpakketMaatwerkType()
{
    class WC_Product_Kerstpakket_Maatwerk_Product extends WC_Product

    {
        //add_action($tag, funtion_to_add, $priority = 10, $acceptet_args = 1);
        public $product_type = '';

        public function __construct($product)
        {
            $this->product_type = 'kerstpakket_maatwerk_product';
            remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
            add_action('woocommerce_shop_loop_item_title', array(__CLASS__, 'my_woocommerce_template_loop_product_title'), 10);
            add_action('woocommerce_kerstpakket_product_add_to_cart', array(__CLASS__, 'woocommerce_kerstpakket_product_add_to_cart'), 30);
            parent::__construct($product);
            remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
            add_action('woocommerce_after_single_product_summary', array(__CLASS__, 'woocommerce_kerstpakket_product_prefooter'),10);
        }

        public function my_init()
        {

            add_action('after_add_to_cart', array('__CLASS__', 'show_stock'));
        }

        public static function my_admin_init()
        {
            add_filter('product_type_selector', array(__CLASS__, 'AddKerstpakketMaatwerkType'));
            add_filter('woocommerce_product_data_tabs', array(__CLASS__, 'KerstpakketMaatwerkTab'));
            add_action('woocommerce_product_data_panels', array(__CLASS__, 'KerstpakketMaatwerkTabContent'));
            add_action('woocommerce_process_product_meta_kerstpakket_maatwerk_product', array(__CLASS__, 'SaveKerstpakketMaatwerkSettings'));
            add_action('admin_footer', array(__CLASS__, 'wh_variable_bulk_admin_custom_js_maatwerk'));
        }


//        public static function check_if_maatwerk()
//        {
//            // Checken of  het een Kerstpakket op Maat betreft
//            $category = wc_get_product_category_list($post->ID);
//            if(preg_match_all('(op.Maat)', $category) === 1) { echo "Ik wil kaas"; } else { echo 'ik wil GEEN kaas';}
//        }

        // Pre footer 
        public static function woocommerce_kerstpakket_product_prefooter() {
        }

        // stock en add to cart op pdp pagina
        public static function woocommerce_kerstpakket_product_add_to_cart() {
            $current_stock= get_post_meta( get_the_ID(), '_stock', true );
            if ($current_stock == 1) echo '<p class="last-in-stock">De laatste van dit product!</p>';
            elseif ($current_stock == 0) echo "";
            elseif ($current_stock >1 ) echo '<p class="custom-stock">De laatste '. $current_stock . ' van dit product!<br></p>';

        }

        // Productoverzicht titel weergave + enter na x aantal woorden
        public static function my_woocommerce_template_loop_product_title()
        {
            echo '<p class="name product-title"><a href="' . get_the_permalink() . '">' . WC_Product_Kerstpakket_Product::trimafterxwords(get_the_title(), get_post_meta(get_the_ID(), 'new_line_after_words_maatwerk', true)) . '</a></p>';
        }

        public static function trimafterxwords($text, $nrofwords)
        {
            if ($nrofwords <= 0) return $text;

            $newline = 0;
            $stop = 0;
            for ($i = 0; $i < strlen($text); $i++) {
                if ($text[$i] == " " || $text[$i] == "&nbsp;") $newline++;

                if ($newline == $nrofwords && $stop == 0) {
                    $newline = 0;
                    $text[$i] = "\n";
                    $stop = 1;
                }
            }
            return nl2br($text);
        }



        public static function KerstpakketMaatwerkDataTabs($tabs)
        {
            $tabs['attribute']['class'][] = 'hide_if_kerstpakket_maatwerk_product';
            return $tabs;
        }



        public static function KerstpakketMaatwerkTab($tabs)
        {
            $tabs['kerstpakket_maatwerk_product'] = array(
                'label' => 'Kerstpakket op Maat',
                'target' => 'kerstpakket_maatwerk_options',
                'class' => array('show_if_kerstpakket_maatwerk_product')
            );
            return $tabs;
        }

        public static function AddKerstpakketMaatwerkType($types)
        {
            $types['kerstpakket_maatwerk_product'] = 'Kerstpakket op Maat';
            return $types;

        }

        public static function KerstpakketMaatwerkTabContent()
        {
            //arrays text & number & text area
            $amount_maatwerk_maatwerk = array(
                'id' => 'amount_maatwerk',
                'label' => 'Aantal artikelen in het pakket',
                'type' => 'number',
            );
            $sku_maatwerk = array(
                'id' => 'sku_maatwerk',
                'label' => 'Leverancier artikelnummer',
                'type' => 'text',
            );
            $new_line_after_words_maatwerk = array(
                'id' => 'new_line_after_words_maatwerk',
                'label' => 'Nieuwe regel na x aantal woorden',
                'desc_tip' => 'true',
                'description' => 'Nieuwe regel na x aantal woorden',
                'type' => 'number',
            );
            $article_list_maatwerk = array(
                'id' => 'article_list_maatwerk',
                'label' => 'Artikel lijst',
                'type' => 'text',
                'style' => 'height:269px',
            );
            //arrays checkbox
            $product_above_price_range_maatwerk = array(
                'id' => 'product_above_price_range_maatwerk',
                'label' => 'Product boven <br /> in prijssegment',
                'placeholder' => '',
                'desc_tip' => 'true',
                'description' => 'Moet het product bovenaan prijssegment. TODO:Verder verduidelijken',
                'type' => 'checkbox',
            );
            ?>
            <div id="kerstpakket_maatwerk_options" class="panel woocommerce_options_panel">
                <div class="options_group"><?php
                    woocommerce_wp_text_input($amount_maatwerk_maatwerk);
                    woocommerce_wp_text_input($sku_maatwerk);
                    woocommerce_wp_text_input($new_line_after_words_maatwerk);
                    woocommerce_wp_checkbox($product_above_price_range_maatwerk);
                    woocommerce_wp_textarea_input($article_list_maatwerk);
                    ?></div>
            </div>
            <?php
        }

        public static function SaveKerstpakketMaatwerkSettings($post_id)
        {
            if (isset($_POST['amount_maatwerk_maatwerk'])) update_post_meta($post_id, 'amount_maatwerk_maatwerk', sanitize_text_field($_POST['amount_maatwerk_maatwerk']));
            if (isset($_POST['article_list_maatwerk'])) update_post_meta($post_id, 'article_list_maatwerk', esc_attr($_POST['article_list_maatwerk']));
            if (isset($_POST['sku_maatwerk'])) update_post_meta($post_id, 'sku_maatwerk', sanitize_text_field($_POST['sku_maatwerk']));
            if (isset($_POST['new_line_after_words_maatwerk'])) update_post_meta($post_id, 'new_line_after_words_maatwerk', sanitize_text_field($_POST['new_line_after_words_maatwerk']));
            if (isset($_POST['product_above_price_range_maatwerk'])) update_post_meta($post_id, 'product_above_price_range_maatwerk', sanitize_text_field($_POST['product_above_price_range_maatwerk']));
            else delete_post_meta($post_id, 'product_above_price_range_maatwerk');
        }

        public static function wh_variable_bulk_admin_custom_js_maatwerk()
        {
            if ('product' != get_post_type()) return;
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function () {
                    jQuery('.options_group.pricing').addClass('show_if_kerstpakket_product').show();
                    jQuery('.general_options.general_tab').addClass('show_if_simple show_if_variable show_if_external show_if_kerstpakket_product').show();
                    jQuery('.inventory_options.inventory_tab').addClass('show_if_kerstpakket_product').show();
                    jQuery('#postexcerpt').hide();
                    // TODO: alleen toepassen bij kerstpakket maatwerk product type:
                    jQuery('.form-field.woocommerce_product_min_max_qty_ignore_field')..addClass('show_if_kerstpakket_maatwerk_product').show();
                    jQuery('.form-field.woocommerce_product_min_max_qty_min_value_field').hide();
                    jQuery('.form-field.woocommerce_product_min_max_qty_max_value_field').hide();
                    jQuery('.form-field.woocommerce_product_min_max_qty_step_field').hide();
                });
            </script>
            <?php
        }

    }
 add_action('admin_init', array('WC_Product_Kerstpakket_Maatwerk_Product', 'my_admin_init'));
}


