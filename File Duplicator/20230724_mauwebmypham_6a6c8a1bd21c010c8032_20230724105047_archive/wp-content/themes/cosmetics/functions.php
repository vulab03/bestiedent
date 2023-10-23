<?php

// xoá thông báo Flatsome issues
add_action( 'init', 'hide_notice' );
function hide_notice() {
remove_action( 'admin_notices', 'flatsome_maintenance_admin_notice' );
}

// Add custom Theme Functions here
/*
* Hiển thị phần rating xuống dưới phần giá trong loop
* Author: giuseart.com
*/
add_action('woocommerce_after_shop_loop_item_title','giuseart_change_loop_ratings_location', 2 );
function giuseart_change_loop_ratings_location(){
    remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_rating', 5 );
    add_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_rating', 15 );
}
//Thêm thương hiệu vào loop
add_action('woocommerce_before_shop_loop_item_title', function(){
  echo do_shortcode('[pwb-brand product_id="'.get_the_ID().'" as_link="true"]');
});
/*
* Chèn box quà tặng phía dưới add to cart form
* Author: giuseart.com
*/
add_action( 'woocommerce_after_add_to_cart_form', 'giuseart_after_add_to_cart_form' );

function giuseart_after_add_to_cart_form(){
global $product;?>
<?php $qua_tang=get_field('qua_tang');
	$label_qua_tang_1 = get_sub_field('label_qua_tang_1');
			$label_qua_tang_2 = get_sub_field('label_qua_tang_2');
			$label_qua_tang_3 = get_sub_field('label_qua_tang_3');
			$label_qua_tang_4 = get_sub_field('label_qua_tang_4');
			$noi_dung_qua_tang_1 = get_sub_field('noi_dung_qua_tang_1');
			$noi_dung_qua_tang_2 = get_sub_field('noi_dung_qua_tang_2');
			$noi_dung_qua_tang_3 = get_sub_field('noi_dung_qua_tang_3');
			$noi_dung_qua_tang_4 = get_sub_field('noi_dung_qua_tang_4');
			?>
<?php if($qua_tang){?>
			<div class="qua-tang-box">
				<div class="row-gift">
					<span class="label"><?php echo $qua_tang['label_qua_tang_1'];?></span><span class="value"><?php echo $qua_tang['noi_dung_qua_tang_1'];?></span>
				</div>
				<?php if($qua_tang['label_qua_tang_2']){?>
				<div class="row-gift">
					<span class="label"><?php echo $qua_tang['label_qua_tang_2'];?></span><span class="value"><?php echo $qua_tang['noi_dung_qua_tang_2'];?></span>
				</div>
				<?php }?>
				<?php if($qua_tang['label_qua_tang_3']){?>
				<div class="row-gift">
					<span class="label"><?php echo $qua_tang['label_qua_tang_3'];?></span><span class="value"><?php echo $qua_tang['noi_dung_qua_tang_3'];?></span>
				</div>
				<?php }?>
				<?php if($qua_tang['label_qua_tang_4']){?>
				<div class="row-gift">
					<span class="label"><?php echo $qua_tang['label_qua_tang_4'];?></span><span class="value"><?php echo $qua_tang['noi_dung_qua_tang_4'];?></span>
				</div>
				<?php }?>
			</div>
<?php }?>

<?php }
	
//THêm % giảm giá bên cạnh giá sản phẩm
add_action( 'woocommerce_after_shop_loop_item_title', 'giuseart_woocommerce_template_loop_price' );
 
function giuseart_woocommerce_template_loop_price() {	
	global $product;
	if( $product->is_on_sale()){
		$regular_price = (float) $product->get_regular_price(); // Regular price
        $sale_price = (float) $product->get_price(); // Active price (the "Sale price" when on-sale)
        // "Saving Percentage" calculation and formatting
        $precision = 1; // Max number of decimals
        $saving_percentage = round( 100 - ( $sale_price / $regular_price * 100 ), 1 ) . '%';
		echo '<span class="phan-tram-km">';
			echo $saving_percentage;
		echo '</span>';
	}
 }
//THêm % giảm giá bên cạnh giá sản phẩm trong trang chi tiết
add_action( 'woocommerce_single_product_summary', 'giuseart_woocommerce_template_single_price' );
 
function giuseart_woocommerce_template_single_price() {	
	global $product;
	if( $product->is_on_sale()){
		$regular_price = (float) $product->get_regular_price(); // Regular price
        $sale_price = (float) $product->get_price(); // Active price (the "Sale price" when on-sale)
        // "Saving Percentage" calculation and formatting
        $precision = 1; // Max number of decimals
        $saving_percentage = round( 100 - ( $sale_price / $regular_price * 100 ), 1 ) . '%';
		echo '<span class="phan-tram-km">';
			echo $saving_percentage;
		echo '</span>';
	}
 }

/**
 * Attributes shortcode callback.
 */
function giuseart_attributes_shortcode( $atts ) {

    global $product;

    if( ! is_object( $product ) || ! $product->has_attributes() ){
        return;
    }

    // parse the shortcode attributes
    $args = shortcode_atts( array(
        'attributes' => array_keys( $product->get_attributes() ), // by default show all attributes
    ), $atts );

    // is pass an attributes param, turn into array
    if( is_string( $args['attributes'] ) ){
        $args['attributes'] = array_map( 'trim', explode( '|' , $args['attributes'] ) );
    }

    // start with a null string because shortcodes need to return not echo a value
    $html = '';

    if( ! empty( $args['attributes'] ) ){

        foreach ( $args['attributes'] as $attribute ) {

            // get the WC-standard attribute taxonomy name
            $taxonomy = strpos( $attribute, 'pa_' ) === false ? wc_attribute_taxonomy_name( $attribute ) : $attribute;

            if( taxonomy_is_product_attribute( $taxonomy ) ){

                // Get the attribute label.
                $attribute_label = wc_attribute_label( $taxonomy );

                // Build the html string with the label followed by a clickable list of terms.
                // Updated for WC3.0 to use getters instead of directly accessing property.
                $html .= get_the_term_list( $product->get_id(), $taxonomy, '<span class="thuoc-tinh-con">' . $attribute_label . ': ' , ', ', '</span>' ); 
            }

        }

        // if we have anything to display, wrap it in a <ul> for proper markup
        // OR: delete these lines if you only wish to return the <li> elements
        if( $html ){
            $html = '<span class="thuoc-tinh">' . $html . '</span>';
        }

    }

    return $html;
}
add_shortcode( 'display_attributes', 'giuseart_attributes_shortcode' );
//Thêm nút mua ngay sau nút add to cart
/*
* Add quick buy button go to checkout after click
* Author: giuseart.com
*/
add_action('woocommerce_after_add_to_cart_button','devvn_quickbuy_after_addtocart_button');
function devvn_quickbuy_after_addtocart_button(){
    global $product;
    ?>
    <style>
        .devvn-quickbuy button.single_add_to_cart_button.loading:after {
            display: none;
        }
        .devvn-quickbuy button.single_add_to_cart_button.button.alt.loading {
            color: #fff;
            pointer-events: none !important;
        }
        .devvn-quickbuy button.buy_now_button {
            position: relative;
            color: rgba(255,255,255,0.05);
        }
        .devvn-quickbuy button.buy_now_button:after {
            animation: spin 500ms infinite linear;
            border: 2px solid #fff;
            border-radius: 32px;
            border-right-color: transparent !important;
            border-top-color: transparent !important;
            content: "";
            display: block;
            height: 16px;
            top: 50%;
            margin-top: -8px;
            left: 50%;
            margin-left: -8px;
            position: absolute;
            width: 16px;
        }
    </style>
    <button type="button" class="button buy_now_button">
        <?php _e('Mua ngay', 'devvn'); ?>
    </button>
    <input type="hidden" name="is_buy_now" class="is_buy_now" value="0" autocomplete="off"/>
    <script>
        jQuery(document).ready(function(){
            jQuery('body').on('click', '.buy_now_button', function(e){
                e.preventDefault();
                var thisParent = jQuery(this).parents('form.cart');
                if(jQuery('.single_add_to_cart_button', thisParent).hasClass('disabled')) {
                    jQuery('.single_add_to_cart_button', thisParent).trigger('click');
                    return false;
                }
                thisParent.addClass('devvn-quickbuy');
                jQuery('.is_buy_now', thisParent).val('1');
                jQuery('.single_add_to_cart_button', thisParent).trigger('click');
            });
        });
    </script>
    <?php
}
add_filter('woocommerce_add_to_cart_redirect', 'redirect_to_checkout');
function redirect_to_checkout($redirect_url) {
    if (isset($_REQUEST['is_buy_now']) && $_REQUEST['is_buy_now']) {
        $redirect_url = wc_get_checkout_url(); //or wc_get_cart_url()
    }
    return $redirect_url;
}
/**
 * Rename product data tabs
 */
add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );
function woo_rename_tabs( $tabs ) {
	$tabs['description']['title'] = __( 'Thông tin sản phẩm' );		// Thay đổi tên tab description
	return $tabs;
}
/*
* Author: Le Van Toan - https://levantoan.com
* Đoạn code thu gọn nội dung bao gồm cả nút xem thêm và thu gọn lại sau khi đã click vào xem thêm
*/
add_action('wp_footer','devvn_readmore_flatsome');
function devvn_readmore_flatsome(){
    ?>
    <style>
        .single-product .panel.entry-content {
            overflow: hidden;
            position: relative;
            padding-bottom: 25px;
        }
        .fix_height{
            max-height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .devvn_readmore_flatsome {padding-top:20px;
            text-align: left;
            cursor: pointer;
            position: absolute;
            z-index: 10;
            bottom: 0;
            width: 100%;padding-bottom:10px;
            background: #fff;
        }
        .devvn_readmore_flatsome:before {
            height: 20px;
            margin-top: -20px;
            content: "";
            background: -moz-linear-gradient(top, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);
            background: -webkit-linear-gradient(top, rgba(255,255,255,0) 0%,rgba(255,255,255,1) 100%);
            background: linear-gradient(to bottom, rgba(255,255,255,0) 0%,rgba(255,255,255,1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff00', endColorstr='#ffffff',GradientType=0 );
            display: block;
        }
        .devvn_readmore_flatsome a {text-decoration:underline;
           color: #e67e22;
    font-weight: 500;
        }
        .devvn_readmore_flatsome a:after {
content: '';
    width: 17px;
    height: 15px;
    display: block;
    background-image: url(/wp-content/uploads/2023/03/chevrons-right-svgrepo-com.svg);
    background-repeat: no-repeat;
    background-size: 19px;
    display: inline-block;
        }
        .devvn_readmore_flatsome_less a:after {
            display:none;
        }
        .devvn_readmore_flatsome_less:before {
           content: '';
    width: 17px;
    height: 15px;
    display: block;
    background-image: url(/wp-content/uploads/2023/03/chevrons-left-svgrepo-com.svg);
    background-repeat: no-repeat;
    background-size: 19px;
    display: inline-block; margin-right:5px;
        }
    </style>
    <script>
        (function($){
            $(document).ready(function(){
                $(window).on('load', function(){
                    if($('.single-product .panel.entry-content').length > 0){
                        let wrap = $('.single-product .panel.entry-content');
                        let current_height = wrap.height();
                        let your_height = 200;
                        if(current_height > your_height){
                            wrap.addClass('fix_height');
                            wrap.append(function(){
                                return '<div class="devvn_readmore_flatsome devvn_readmore_flatsome_more"><a title="Xem thêm nội dung" href="javascript:void(0);">Xem thêm nội dung</a></div>';
                            });
                            wrap.append(function(){
                                return '<div class="devvn_readmore_flatsome devvn_readmore_flatsome_less" style="display: none;"><a title="Thu-gọn-nội-dung" href="javascript:void(0);">Thu gọn nội dung</a></div>';
                            });
                            $('body').on('click','.devvn_readmore_flatsome_more', function(){
                                wrap.removeClass('fix_height');
                                $('body .devvn_readmore_flatsome_more').hide();
                                $('body .devvn_readmore_flatsome_less').show();
                            });
                            $('body').on('click','.devvn_readmore_flatsome_less', function(){
                                wrap.addClass('fix_height');
                                $('body .devvn_readmore_flatsome_less').hide();
                                $('body .devvn_readmore_flatsome_more').show();
                            });
                        }
                    }
                });
            });
        })(jQuery);
    </script>
    <?php
}
//Xóa tab thông tin bổ sung
function kenthan_remove_product_tabs( $tabs ) {
unset( $tabs['additional_information'] );
return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'kenthan_remove_product_tabs', 98 );

//Tạo thêm sidebar
register_sidebar(array(
    'name' => 'Sidebar 2',
    'id' => 'sidebar-2',
    'description' => 'Khu vực sidebar hiển thị dưới mỗi bài viết',
    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    'after_widget' => '</aside>',
    'before_title' => '<span class="widget-title">',
    'after_title' => '</span>'
));
