<?php 
/**
 * PLugin Name: Custom Portfolio
 * Plugin Uri: www.google.com
 * Author: Nikita
 * Version: 1.0.0
 * Description: This plugin will add custom portfolio in your website.
 */



    // Register Custom Post Type

    add_action('init', 'activate_myplugin');
    function activate_myplugin() 
    {

        $labels = array(
            'name'                  => _x( 'Portfolio', 'Portfolio General Name', 'text_domain' ),
            'singular_name'         => _x( 'Portfolio', 'Portfolio Singular Name', 'text_domain' ),
            'menu_name'             => __( 'Portfolios', 'text_domain' ),
            'name_admin_bar'        => __( 'Portfolio', 'text_domain' ),
            'archives'              => __( 'Portfolio Archives', 'text_domain' ),
            'attributes'            => __( 'Portfolio Attributes', 'text_domain' ),
            'parent_item_colon'     => __( 'Parent Portfolio:', 'text_domain' ),
            'all_items'             => __( 'All Portfolios', 'text_domain' ),
            'add_new_item'          => __( 'Add New Portfolio', 'text_domain' ),
            'add_new'               => __( 'Add New', 'text_domain' ),
            'new_item'              => __( 'New Portfolio', 'text_domain' ),
            'edit_item'             => __( 'Edit Portfolio', 'text_domain' ),
            'update_item'           => __( 'Update Portfolio', 'text_domain' ),
            'view_item'             => __( 'View Portfolio', 'text_domain' ),
            'view_items'            => __( 'View Portfolios', 'text_domain' ),
            'search_items'          => __( 'Search Portfolio', 'text_domain' ),
            'not_found'             => __( 'Not found', 'text_domain' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
            'featured_image'        => __( 'Featured Image', 'text_domain' ),
            'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
            'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
            'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
            'insert_into_item'      => __( 'Insert into Portfolio', 'text_domain' ),
            'uploaded_to_this_item' => __( 'Uploaded to this Portfolio', 'text_domain' ),
            'items_list'            => __( 'Portfolios list', 'text_domain' ),
            'items_list_navigation' => __( 'Portfolios list navigation', 'text_domain' ),
            'filter_items_list'     => __( 'Filter Portfolios list', 'text_domain' ),
        );

        $args = array(
            'label'                 => __( 'Portfolio', 'text_domain' ),
            'description'           => __( 'Portfolio Description', 'text_domain' ),
            'rewrite'               => array('slug' => 'portfolio'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'excerpt','author'),
            'hierarchical'          => true,
            'public'                => true,
            'menu_position'         => 10,
            'has_archive'           => true,
            'capability_type'       => 'post',
            'menu_icon'             => 'dashicons-images-alt2',
        );

	    register_post_type( 'portfolio', $args );

    }
    
    // Plugin activation
    function myplugin_activate()
    {
            activate_myplugin();
            flush_rewrite_rules();
    
    }
    register_activation_hook( __FILE__, 'myplugin_activate' );

    // Plugin deactivation
    function myplugin_deactivate()
    {
            unregister_post_type( 'portfolio' );
            flush_rewrite_rules();
    
    }
    register_deactivation_hook( __FILE__, 'myplugin_deactivate' );

    //Register Custom Taxonomy

    function portfolio_register_category_taxonomy() {
        $args = array(
            'labels'       => array(
                'name'          => 'Project Category',
                'singular_name' => 'Project Category',
                'edit_item'     => 'Edit Project Category',
                'update_item'   => 'Update Project Category',
                'add_new_item'  => 'Add New Project Category',
                'new_item_name' => 'New Project Category Name',
                'menu_name'     => 'Project Category',
            ),
            'hierarchical' => true,
            'rewrite'      => array( 'slug' => 'project_category' ),
            'show_in_rest'           => true,
        );
    
        register_taxonomy( 'project_category', 'portfolio', $args );
    }

    add_action( 'init', 'portfolio_register_category_taxonomy' );
    
    function disply_portfolio(){


    // Get a list of categories

    // Run a query for fetch latest 3 Portfolios

    $p_args = array(
        'post_type' => 'portfolio', 
        'posts_per_page' => '3',
        'order_by' => 'date', 
        //'order' => 'ASC', 
        //'cat' => $term->ID
    );
    $new_query = new WP_Query ($p_args);
    if ($new_query->have_posts()) {
        while($new_query->have_posts()){
            $new_query->the_post();?>
            <div class="all">
                <div class="single-p">
                    <div class="thumb-p">
                            <?php // Post's featured image
                            echo '<a href="<?php echo the_permalink(); ?>"'.the_post_thumbnail('thumbnail').'</a>'?>
                    </div>
                    <div class="name-p">        
                            <?php // Post's title
                           echo '<a href="<?php echo the_permalink(); ?>"'.the_title().'</a>';
                           $id = get_the_ID();
                           $date = get_post_meta($id ,'_wpse_value', true);

                           echo "<h4> Date: ". $date ."</h4>";
                            ?>
                    </div>
                </div>
            </div>
            

            
        <?php }
    }
    wp_reset_postdata();       
    } 

    // Short code to display portfolios in the home page
    add_shortcode('display_portfolio','disply_portfolio');


    //Meta box date

    //making the meta box (Note: meta box != custom meta field)

        function meta_box_date_callback()
        {
            global $post;

            // Use nonce for verification to secure data sending
            wp_nonce_field( basename( __FILE__ ), 'wpse_our_nonce' );

            $value = get_post_meta( $post->ID, '_wpse_value', true );

            ?>

            <!-- my custom value input -->
            <input type="date" name="wpse_value" id="wpse_value" value="<?php echo $value?>">

            <?php
        }
        

        function add_custom_meta_box_date() {
            add_meta_box(
                'meta_box_date',       // $id
                'Date',                  // $title
                'meta_box_date_callback',  // $callback
                'portfolio',                 // $page
                'normal',                  // $context
                'high'                     // $priority
            );
        }
        add_action('add_meta_boxes', 'add_custom_meta_box_date');

        
        //now we are saving the data
        function wpse_save_meta_fields( $post_id ) {

        // verify nonce
        if (!isset($_POST['wpse_our_nonce']) || !wp_verify_nonce($_POST['wpse_our_nonce'], basename(__FILE__)))
            return 'nonce not verified';

        // check autosave
        if ( wp_is_post_autosave( $post_id ) )
            return 'autosave';

        //check post revision
        if ( wp_is_post_revision( $post_id ) )
            return 'revision';

        // check permissions
        if ( 'portfolio' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) )
                return 'cannot edit page';
            } elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
                return 'cannot edit post';
        }

        //so our basic checking is done, now we can grab what we've passed from our newly created form
        $wpse_value = $_POST['wpse_value'];

        //simply we have to save the data now
        global $wpdb;

//         $table = $wpdb->base_prefix . '_posts';

//   $wpdb->insert(
//             $table,
//             array(
//                 'col_post_id' => $post_id, //as we are having it by default with this function
//                 'col_value'   =>  $wpse_value  //assuming we are passing numerical value
//               ),
//             array(
//                 '%d', //%s - string, %d - integer, %f - float
//                 '%d', //%s - string, %d - integer, %f - float
//               )
//           );


        // Sanitize user input.
    $my_data = sanitize_text_field( $_POST['wpse_value'] );

    // Update the meta field in the database.
    update_post_meta( $post_id, '_wpse_value', $my_data );

        }


        add_action( 'save_post', 'wpse_save_meta_fields' );
        add_action( 'new_to_publish', 'wpse_save_meta_fields' );
        
   

    ?>