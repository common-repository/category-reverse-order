<?php
/**
* Plugin Name: Category Reverse Order
* Plugin URI: https://www.janbpunkt.de/catrevorder
* Description: Shows posts of a sepcific category in reverse order (oldest to newest) OR shows only a specific category in reverse order (oldest to newest) on the home page
* Version: 0.1
* Author: Jan B-Punkt
* Author URI: https://www.janbpunkt.de/
**/


// create plugin settings menu
add_action('admin_menu', 'catrevorder_menu');

function catrevorder_menu() {
	//create new top-level menu
	add_menu_page('Category Reverse Order', 'Category Reverse Order', 'administrator', __FILE__, 'catrevorder_settings_page');

	//call register settings function
	add_action('admin_init', 'catrevorder_settings');
}


function catrevorder_settings() {
	//register our settings
    register_setting( 'catrevorder-settings', 'catrevorder-cat');
    register_setting( 'catrevorder-settings', 'catrevorder-main');
}

function catrevorder_settings_page() {
?>
<div class="wrap">
<h1>Category Reverse Order</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'catrevorder-settings' ); ?>
    <?php do_settings_sections( 'catrevorder-settings' ); ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row" style="width:250px;">
                <label for="catrevorder-cat">Category that should be reversed</label>
            </th>
            <td>
                <Select name="catrevorder-cat" id="catrevorder-cat">
                
                <?php 
                $catsaved = get_option('catrevorder-cat');
                $categories = get_categories(array('orderby' => 'name','order' => 'ASC'));
                
                foreach( $categories as $category ) {
                    $catname = $category->name;
                    $catid = $category->term_id;

                    if ($catid == $catsaved) {
                        $selected = "selected";
                    } else $selected ="";

                    echo '<option value="'.$catid.'" '.$selected.'>'.$catname.'</option>';                 
                }
            ?>
            </select>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label for="catrevorder-main">Show only selected category on home page?</label>
            </th>
            <td>
                <input type="checkbox" name="catrevorder-main" id="catrevorder-main" value="true" <?php if (get_option('catrevorder-main')=="true") echo "checked"; ?>>
            </td>
        </tr>
    </table>
    <?php submit_button(); ?>
</form>
</div>
<?php 
}
?>


<?php
function category_reverse_order($query) { 

    if (get_option('catrevorder-main') == "true") {
        //only show the selected category in reversed order on the main page
        if (!is_admin() && ($query->is_home() && $query->is_main_query() || $query->is_category(get_option('catrevorder-cat')))) {
            $query->set( 'cat',get_option('catrevorder-cat'));
            $query->set( 'orderby', 'date' );
            $query->set( 'order', 'ASC' );
            $query->set( 'suppress_filters', 'true' );
        }
    } else {
        //if we show the category do reverse the order of the posts
        if (!is_admin() && $query->is_category(get_option('catrevorder-cat'))) {
            $query->set( 'orderby', 'date' );
            $query->set( 'order', 'ASC' );
            $query->set( 'suppress_filters', 'true' );
        }
    }
 } 
 
 add_action( 'pre_get_posts', 'category_reverse_order' );
?>