<?
/*
Plugin Name: Technoscore WC Category Carousel
Plugin URI: http://nddw.com/demo3/sws-res-slider/
Description: This plugin adds showcase for woocommerce products categories images in a nice sliding manner.
Version:  1.0.1
Author: Technoscore
Author URI: http://www.technoscore.com/
Text Domain: techno_
*/

add_action('admin_menu', 'techno_wc_cat');

function techno_wc_cat() {

	//create new top-level menu
	add_menu_page('WC Category Carousel', 'WC Category Carousel', 'administrator', __FILE__, 'techno_wc_cat_page');
	
	//call register settings function
	add_action( 'admin_init', 'techno_wc_register_settings' );
	
}


function techno_wc_register_settings() {
	//register our settings
	register_setting( 'techno-settings-group', 'techno_wc_navigation' );
	register_setting( 'techno-settings-group', 'techno_wc_loop' );
	register_setting( 'techno-settings-group', 'techno_wc_images' );
	
}


function techno_wc_cat_page() {
?>
<div class="wrap">
<h1>WC Category Carousel Settings</h1>
<form method="post" action="options.php">
    <?php settings_fields( 'techno-settings-group' ); ?>
    <?php do_settings_sections( 'techno-settings-group' ); ?>
    <table class="form-table">
	
		  <tr valign="top">
        <th scope="row">Carousel Images To Show</th>
        <td>
			<select name="techno_wc_images">
				<option value="1" <?php selected(get_option('techno_wc_images'), "1"); ?>>1</option>
				<option value="2" <?php selected(get_option('techno_wc_images'), "2"); ?>>2</option>
				<option value="3" <?php selected(get_option('techno_wc_images'), "3"); ?>>3</option>
			</select>
		</td>
        </tr>  
		
		
        <tr valign="top">
        <th scope="row">Carousel Navigation</th>
        <td>
			<select name="techno_wc_navigation">
				<option value="true" <?php selected(get_option('techno_wc_navigation'), "true"); ?>>true</option>
				<option value="false" <?php selected(get_option('techno_wc_navigation'), "false"); ?>>false</option>
			</select>
		</td>
        </tr>  
		
		<tr valign="top">
        <th scope="row">Carousel Loop</th>
        <td>
			<select name="techno_wc_loop">
				<option value="true" <?php selected(get_option('techno_wc_loop'), "true"); ?>>true</option>
				<option value="false" <?php selected(get_option('techno_wc_loop'), "false"); ?>>false</option>
			</select>
		
		</td>
        </tr> 

		<tr valign="top">
        <th scope="row">WC Category Carousel Shortcode Integration</th>
        <td><label>[techno_wc_list_init  carousel_class ='add-class-name'  ids='add-wc-cat-ids']</label>
		<br/><br/>
		ex:- 
		<br/>
		[techno_wc_list_init carousel_class ='add-class-name'  ids='4335,4336,4337,4334']
		<br/> It display images of woocommerce product categories having ids '4335,4336,4337,4334'.
		<br/><br/>
			 [techno_wc_list_init carousel_class ='add-class-name'  ids='']	 <br/>or<br/>
			 [techno_wc_list_init  carousel_class ='add-class-name'  ids='add-wc-cat-ids']
			 <br/> It display images of all woocommerce product categories.
		</td>
        </tr> 


		
    </table>
    <?php submit_button(); ?>
</form>
</div>

<?php } 


function techno_wc_list($atts) {
$atts = shortcode_atts(
		array(
			'carousel_class' => '',
			'ids' => '',
		), $atts, 'bartag' );
		
if($atts['carousel_class'] =='' || $atts['carousel_class'] == 'add-class-name'){
	$carousel_class =  'techno';
}else{
	$carousel_class =  $atts['carousel_class'];
}
			
if($atts['ids'] =='' || $atts['ids'] == 'add-wc-cat-ids'){
	$taxonomy     = 'product_cat';
	$hierarchical = 1;      // 1 for yes, 0 for no  
	$empty        = 0;
	$args = array(
			 'taxonomy'     => $taxonomy,
			 'hierarchical' => $hierarchical,
			 'hide_empty'   => $empty
	  );
	 $all_categories = get_categories( $args );	
	foreach($all_categories as $all_categories_val){
		$all_categories_ids[] = $all_categories_val->term_id;
	}
}else{
	$all_categories_ids = array_values(explode(',',$atts['ids']));
}		

foreach($all_categories_ids as $all_categories_ids_val){
	$thumbnail_id = get_woocommerce_term_meta( $all_categories_ids_val, 'thumbnail_id', true );
	if(!empty($thumbnail_id) && $thumbnail_id !=0)
		{ 
			 $all_categories_ids_new[$all_categories_ids_val]['thumbnail_id'] = $thumbnail_id;
		}
  }


$techno_wc_navigation = get_option('techno_wc_navigation');
$techno_wc_loop = get_option('techno_wc_loop');
$techno_wc_images = get_option('techno_wc_images');
	
wp_enqueue_script( 'jquery');		
wp_enqueue_style( 'techno_carousel_custom_css', plugin_dir_url( __FILE__ ) . 'assets/css/techno_style.css' );
wp_enqueue_style( 'techno_carousel_owl_css', plugin_dir_url( __FILE__ ) . 'assets/css/owl.carousel.min.css' );
wp_enqueue_style( 'techno_carousel_theme_css', plugin_dir_url( __FILE__ ) . 'assets/css/owl.theme.default.min.css' );
wp_enqueue_script( 'techno_carousel_js', plugin_dir_url( __FILE__ ) . 'assets/js/owl.carousel.min.js' );
?>
<div class="owl-carousel owl-theme  <? echo $carousel_class; ?>">
<? foreach($all_categories_ids_new as $all_categories_ids_new_key => $all_categories_ids_new_val){ ?>
            <div class="item" cat-id="<? echo $all_categories_ids_new_key; ?>" thumbnail-id="<? echo $all_categories_ids_new_val['thumbnail_id']; ?>">
			<? echo $image = wp_get_attachment_image( $all_categories_ids_new_val['thumbnail_id'], 'medium', false, array('class'=>'techno_wc_cat_thumb'));  /* wp_get_attachment_url(thumb_id) */?>
              <h4><? echo get_the_category_by_ID($all_categories_ids_new_key); ?></h4>
            </div>
<? } ?>
          </div>	  
  <script type="text/javascript">
        jQuery(document).ready(function ($) { 
               $(document).ready(function() {
              var owl = $('.owl-carousel');
              owl.owlCarousel({
                margin: 10,
				autoHeight:false,
                nav: <? echo (empty($techno_wc_navigation) ? 'true' : $techno_wc_navigation); ?>,
                loop: <? echo (empty($techno_wc_loop) ? 'true' : $techno_wc_loop); ?>,
                responsive: {
                  0: {
                    items: 1
                  },
                  600: {
                    items:2
                  },
                  1000: {
                    items: <? echo (empty($techno_wc_images) ? '3' : $techno_wc_images); ?>
                  }
                }
              })
            })	
        });
  </script>
			<?php
 }
add_shortcode( 'techno_wc_list_init', 'techno_wc_list' );


function techno_wc_shortcode_button_script() 
{
    if(wp_script_is("quicktags"))
    {
        ?>
            <script type="text/javascript">
                
                //this function is used to retrieve the selected text from the text editor
                function getSel()
                {
                    var txtarea = document.getElementById("content");
                    var start = txtarea.selectionStart;
                    var finish = txtarea.selectionEnd;
                    return txtarea.value.substring(start, finish);
                }

                QTags.addButton( 
                    "code_shortcode", 
                    "Add WC Product Category Carousel", 
                    callback
                );

                function callback()
                {
                    /* var selected_text = getSel(); */
                    var selected_text = 'techno_wc_list_init';
                    QTags.insertContent("[" +  selected_text + " carousel_class ='add-class-name'  ids='add-wc-cat-ids']");
                }
            </script>
        <?php
    }
}

add_action("admin_print_footer_scripts", "techno_wc_shortcode_button_script");