<?php
/**
 * Course carousel template
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="<?php tutor_container_classes(); ?> tutor-divi-carousel-main-wrap">

<!--loading course init-->
<?php

//available categories
$available_cat      = tutor_divi_course_categories();
//sort be key asc order
ksort($available_cat);

//user's selected category
$category_includes  = $args['category_includes'];
$category_includes  = explode('|', $category_includes);

$category_terms     = tutor_divi_get_user_selected_terms( $available_cat, $category_includes );

$available_author   = tutor_divi_course_authors();
ksort($available_author);

$author_includes        = $args['author_includes'];
$author_includes        = explode('|', $author_includes);
$selected_author_ids    = tutor_divi_get_user_selected_authors($available_author, $author_includes); 

$order_by           = sanitize_text_field( $args['order_by'] );
$order              = sanitize_text_field( $args['order'] );
$limit              = sanitize_text_field( $args['limit'] );

//carousel visibility settings
$skin               = $args['skin'];
$slide_to_show      = $args['slides_to_show'];
$hover_animation    = $args['hover_animation'];
$show_image         = $args['show_image'];    
$image_size         = $args['image_size'];    
$meta_data          = $args['meta_data'];    
$rating             = $args['rating'];    
$avatar             = $args['avatar'];    
$difficulty_label   = $args['difficulty_label'];    
$wish_list          = $args['wish_list'];    
$category           = $args['category'];    
$footer             = $args['footer']; 

?>
<input type="hidden" id="cart_button_font_icon" value="">
<?php
/*
* query arguements
*/
$query_args = [
    'post_type'         => tutor()->course_post_type,
    'post_status'       => 'publish',
    'posts_per_page'    => $limit,
    'order_by'          => $order_by,
    'order'             => $order
];

if( count($selected_author_ids) > 0) {
    $query_args['author__in']    = $selected_author_ids;
}

if( count($category_terms) > 0 ) {
       $query_args['tax_query'] = array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'course-category',
            'field'    => 'term_id',
            'terms'    => $category_terms,
            'operator' => 'IN',
        ),
    );    
}

$the_query = new WP_Query($query_args);

if ( $the_query->have_posts()) : ?>

    <!-- loop start -->
    <?php
    $shortcode_arg = isset($GLOBALS['tutor_shortcode_arg']) ? $GLOBALS['tutor_shortcode_arg']['column_per_row'] : null;
    $courseCols = $shortcode_arg === null ? tutor_utils()->get_option('courses_col_per_row', 4) : $shortcode_arg;
    ?>
    <!-- loop start -->

    <div class="tutor-divi-slick-responsive tutor-divi-carousel-loop-wrap tutor-courses tutor-courses-loop-wrap tutor-courses-layout-<?php esc_attr_e( $courseCols ); ?> tutor-divi-carousel-<?php esc_attr_e( $skin );?> <?php echo $show_image === 'off' ? esc_attr( 'hide-thumbnail' ) : ''  ;?>" id="tutor-divi-slick-responsive">

        <?php while ($the_query->have_posts()) : $the_query->the_post();
        ?>
            <!-- slick-slider-main-wrapper -->

            <div class="<?php tutor_course_loop_col_classes(); ?>">
            <?php
                $image_size = $image_size;
                $image_url = get_tutor_course_thumbnail($image_size, $url = true);
                
            ?>
            <div class="tutor-divi-card <?php echo $hover_animation == 'on' ? esc_attr( 'hover-animation' ) : '';?>">

                    <!-- header -->
                    
                    <div class="tutor-course-header ">
                        <?php if("on" == $show_image):?>
                        <a href="<?php echo esc_url( the_permalink() );?>">
                            <img src="<?php echo esc_url( $image_url );?>" alt="">
                        </a> 
                        <?php endif;?>                              
                        <div class="tutor-course-loop-header-meta">
                            <?php
                            $course_id = get_the_ID();
                            $is_wishlisted = tutor_utils()->is_wishlisted($course_id);
                            $has_wish_list = '';
                            if ($is_wishlisted) {
                                $has_wish_list = 'has-wish-listed';
                            }

                            $action_class = '';
                            if (is_user_logged_in()) {
                                $action_class = apply_filters('tutor_wishlist_btn_class', 'tutor-course-wishlist-btn');
                            } else {
                                $action_class = apply_filters('tutor_popup_login_class', 'cart-required-login');
                            }
                            if ("on" === $difficulty_label) {
                                echo '<span class="tutor-course-loop-level">' . esc_html( get_tutor_course_level() ) . '</span>';
                            }
                            if ("on" === $wish_list) {
                                echo '<span class="tutor-course-wishlist"><a href="javascript:;" class="tutor-icon-fav-line ' . esc_attr( $action_class ) . ' ' . esc_attr( $has_wish_list ) . ' " data-course-id="' . esc_attr( $course_id ) . '"></a> </span>';
                            }


                            ?>
                        </div> 

                    </div>
                  
                    <!--header end-->
                    <!-- start loop content wrap -->
                    <div class="tutor-divi-carousel-course-container">
                        <div class="tutor-loop-course-container">

                            <!-- loop rating -->
                            <?php if ("on" === $rating) : ?>
                                <div class="tutor-loop-rating-wrap">
                                    <?php
                                    $course_rating = tutor_utils()->get_course_rating();
                                    tutor_utils()->star_rating_generator($course_rating->rating_avg);
                                    ?>
                                    <span class="tutor-rating-count">
                                        <?php
                                        if ($course_rating->rating_avg > 0) {
                                            echo  apply_filters('tutor_course_rating_average', esc_html( $course_rating->rating_avg ));
                                        }
                                        ?>
                                        <i> (<?php echo apply_filters('tutor_course_rating_count', esc_html( $course_rating->rating_count ));?>)</i>
    
                                    </span>
                                </div>
                            <?php endif; ?>
                            <!-- loop title -->
                            <div class="tutor-course-loop-title">
                                <h2><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php esc_html( the_title() ); ?></a></h2>
                            </div>

                            <!-- loop meta -->
                            <?php
                            /**
                             * @package TutorLMS/Templates
                             * @version 1.4.3
                             */

                            global $post, $authordata;

                            $profile_url = tutor_utils()->profile_url($authordata->ID);
                            ?>


                            <?php if ("on" === $meta_data) : ?>
                                <div class="tutor-course-loop-meta">
                                    <?php
                                    $course_duration = get_tutor_course_duration_context();
                                    $course_students = tutor_utils()->count_enrolled_users_by_course();
                                    ?>
                                    <div class="tutor-single-loop-meta">
                                        <i class='tutor-icon-user'></i><span><?php esc_html_e( $course_students ); ?></span>
                                    </div>
                                    <?php
                                    if (!empty($course_duration)) { ?>
                                        <div class="tutor-single-loop-meta">
                                            <i class='tutor-icon-clock'></i> <span><?php esc_html_e( $course_duration ); ?></span>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php endif; ?>

                            <div class="tutor-loop-author">
                                <div class="tutor-single-course-avatar">
                                    <?php if ("on" === $avatar) : ?>
                                        <a href="<?php echo esc_url( $profile_url ); ?>"> 
                                            <?php echo wp_kses_post( tutor_utils()->get_tutor_avatar($post->post_author) ); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="tutor-single-course-author-name">
                                    <span><?php _e('by', 'tutor-lms-divi-modules'); ?></span>
                                    <a href="<?php echo esc_url( $profile_url );?>">
                                        <?php esc_html_e( get_the_author() ); ?>
                                    </a>
                                </div>

                                <div class="tutor-course-lising-category">
                                    <?php
                                    if ("on" === $category) {

                                        $course_categories = get_tutor_course_categories();
                                        if (!empty($course_categories) && is_array($course_categories) && count($course_categories)) {
                                    ?>
                                            <span><?php esc_html_e('In', 'tutor-lms-divi-modules') ?></span>
                                    <?php
                                            foreach ($course_categories as $course_category) {
                                                $category_name = $course_category->name;
                                                $category_link = get_term_link($course_category->term_id);
                                                echo "<a href='".esc_url( $category_link )."'> ".esc_html( $category_name )." </a>";
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- end content wrap -->
                        </div>

                        <!-- loop footer -->
                        <?php if ("on" === $footer) : ?>
                            <div class="tutor-loop-course-footer tutor-divi-carousel-footer">
                                <?php
                                tutor_course_loop_price()
                                ?>
                            </div>
                        <?php endif; ?>    
                    </div> <!-- tutor-divi-course-container -->
                    


                </div><!--card-end-->
            </div>

            <!-- slick-slider-main-wrapper -->

        <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </div>


    <!-- loop end -->
<?php

else :

    /**
     * No course found
     */
    tutor_load_template('course-none');

endif;

do_action('tutor_course/archive/after_loop');
?>
<!--loading course init-->

<!-- handle elementor settings -->
<?php
//carousel settings
$slides_to_show     = $args['slides_to_show'];
$arrows             = $args['arrows'];
$dots               = $args['dots'];
$transition         = $args['transition'];
$center_slides      = $args['center_slides'];
$smooth_scrolling   = $args['smooth_scrolling'];
$autoplay           = $args['autoplay'];
$autoplay_speed     = $args['autoplay_speed'];
$infinite_loop      = $args['infinite_loop'];
$pause_on_hover     = $args['paush_on_hover'];


?>
<div id="tutor_divi_carousel_settings" slides_to_show="<?php esc_attr_e( $slides_to_show);?>" arrows="<?php esc_attr_e( $arrows );?>" dots="<?php esc_attr_e( $dots );?>" transition="<?php esc_attr_e( $transition );?>" center_slides="<?php esc_attr_e( $center_slides );?>" smooth_scrolling="<?php esc_attr_e( $smooth_scrolling );?>" carousel_autoplay="<?php esc_attr_e( $autoplay ) ?>" autoplay_speed="<?php esc_attr_e( $autoplay_speed );?>" infinite_loop="<?php esc_attr_e( $infinite_loop );?>" pause_on_hover="<?php esc_attr_e( $pause_on_hover );?>">

</div>

</div>
