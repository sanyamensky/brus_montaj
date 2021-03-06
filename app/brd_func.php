<?php   
function get_hansel_and_gretel_breadcrumbs()
{
    // Set variables for later use
    $here_text        = __( 'You are currently here!' );
    $home_link        = home_url('/');
    $home_text        = __( 'Home' );
    $link_before      = '<span typeof="v:Breadcrumb">';
    $link_after       = '</span>';
    $link_attr        = ' rel="v:url" property="v:title"';
    $link             = $link_before . '<a' . $link_attr . ' href="%1$s">%2$s</a>' . $link_after;
    $delimiter        = ' // ';              // Delimiter between crumbs
    $before           = '<span class="current">'; // Tag before the current crumb
    $after            = '</span>';                // Tag after the current crumb
    $page_addon       = '';                       // Adds the page number if the query is paged
    $breadcrumb_trail = '';
    $category_links   = '';

    /** 
     * Set our own $wp_the_query variable. Do not use the global variable version due to 
     * reliability
     */
    $wp_the_query   = $GLOBALS['wp_the_query'];
    $queried_object = $wp_the_query->get_queried_object();
    // Handle single post requests which includes single pages, posts and attatchments

        global $post;
    if ( is_singular('project') ) 
    {
        $term_objects        = wp_get_post_terms( $post->ID, 'project_cat');
        if(!empty($term_objects)){
            $term_object         = $term_objects[get_youngest($term_objects)];
            $taxonomy           = $term_object->taxonomy;
            $term_id            = $term_object->term_id;
            $term_name          = $term_object->name;
            $term_parent        = $term_object->parent;
            $taxonomy_object    = get_taxonomy( $taxonomy );
            $current_term_link  = sprintf( $link, esc_url( get_term_link( $term_object ) ), $term_object->name );
            $parent_term_string = '';
            if ( 0 !== $term_parent )
            {
                // Get all the current term ancestors
                $parent_term_links = [];
                while ( $term_parent ) {
                    $term = get_term( $term_parent, $taxonomy );
                    $parent_term_links[] = sprintf( $link, esc_url( get_term_link( $term ) ), $term->name );

                    $term_parent = $term->parent;
                }

                $parent_term_links  = array_reverse( $parent_term_links );
                $parent_term_string = implode( $delimiter, $parent_term_links );
            }


            if(isset($post)){
                $post_type        = $post->post_type;
                $post_type_object = get_post_type_object( $post_type );
                $breadcrumb_trail2 = $before . $post_type_object->labels->name . $after;
                $archive_link = get_post_type_archive_link( $post_type ); 
                $brd = sprintf( $link, $archive_link, $post_type_object->labels->name );
                if($parent_term_string){
                    $breadcrumb_trail =  $brd . $delimiter . $parent_term_string . $delimiter . $current_term_link . $delimiter . get_the_title();
                } else {
                    $breadcrumb_trail =  $brd . $delimiter . $current_term_link . $delimiter . get_the_title();
                }
            }
        } else {
            $post_type        = $post->post_type;
            $post_type_object = get_post_type_object( $post_type );
            $archive_link = get_post_type_archive_link( $post_type ); 
            $brd = sprintf( $link, $archive_link, $post_type_object->labels->name );
            $breadcrumb_trail =  $brd . $delimiter . get_the_title();
        }
    } else if ( is_singular('portfolio') ){
        $term_objects        = wp_get_post_terms( $post->ID, 'portfolio_cat');
        if(!empty($term_objects)){
            $term_object         = $term_objects[get_youngest($term_objects)];
            $taxonomy           = $term_object->taxonomy;
            $term_id            = $term_object->term_id;
            $term_name          = $term_object->name;
            $term_parent        = $term_object->parent;
            $taxonomy_object    = get_taxonomy( $taxonomy );
            $current_term_link  = sprintf( $link, esc_url( get_term_link( $term_object ) ), $term_object->name );
            $parent_term_string = '';
            if ( 0 !== $term_parent )
            {
                // Get all the current term ancestors
                $parent_term_links = [];
                while ( $term_parent ) {
                    $term = get_term( $term_parent, $taxonomy );
                    $parent_term_links[] = sprintf( $link, esc_url( get_term_link( $term ) ), $term->name );

                    $term_parent = $term->parent;
                }

                $parent_term_links  = array_reverse( $parent_term_links );
                $parent_term_string = implode( $delimiter, $parent_term_links );
            }


            if(isset($post)){
                $post_type        = $post->post_type;
                $post_type_object = get_post_type_object( $post_type );
                $breadcrumb_trail2 = $before . $post_type_object->labels->name . $after;
                $archive_link = get_post_type_archive_link( $post_type ); 
                $brd = sprintf( $link, $archive_link, $post_type_object->labels->name );

                $breadcrumb_trail =  $brd . $delimiter . $parent_term_string . $delimiter . $current_term_link . $delimiter . get_the_title();
            }
        } else {
            $post_type        = $post->post_type;
            $post_type_object = get_post_type_object( $post_type );
            $archive_link = get_post_type_archive_link( $post_type ); 
            $brd = sprintf( $link, $archive_link, $post_type_object->labels->name );
            $breadcrumb_trail =  $brd . $delimiter . get_the_title();
        }
    } else if(is_singular()){
    /** 
          * Set our own $post variable. Do not use the global variable version due to 
          * reliability. We will set $post_object variable to $GLOBALS['wp_the_query']
          */
         $post_object = sanitize_post( $queried_object );

         // Set variables 
         $title          = apply_filters( 'the_title', $post_object->post_title );
         $parent         = $post_object->post_parent;
         $post_type      = $post_object->post_type;
         $post_id        = $post_object->ID;
         $post_link      = $before . $title . $after;
         $parent_string  = '';
         $post_type_link = '';

         if ( 'post' === $post_type ) 
         {
             // Get the post categories
             $categories = get_the_category( $post_id );
             if ( $categories ) {
                 // Lets grab the first category
                 $category  = $categories[0];

                 $category_links = get_category_parents( $category, true, $delimiter );
                 $category_links = str_replace( '<a',   $link_before . '<a' . $link_attr, $category_links );
                 $category_links = str_replace( '</a>', '</a>' . $link_after,             $category_links );
             }
         }

         if ( !in_array( $post_type, ['post', 'page', 'attachment'] ) )
         {
             $post_type_object = get_post_type_object( $post_type );
             $archive_link     = esc_url( get_post_type_archive_link( $post_type ) );

             $post_type_link   = sprintf( $link, $archive_link, $post_type_object->labels->singular_name );
         }

         // Get post parents if $parent !== 0
         if ( 0 !== $parent ) 
         {
             $parent_links = [];
             while ( $parent ) {
                 $post_parent = get_post( $parent );

                 $parent_links[] = sprintf( $link, esc_url( get_permalink( $post_parent->ID ) ), get_the_title( $post_parent->ID ) );

                 $parent = $post_parent->post_parent;
             }

             $parent_links = array_reverse( $parent_links );

             $parent_string = implode( $delimiter, $parent_links );
         }

         // Lets build the breadcrumb trail
         if ( $parent_string ) {
             $breadcrumb_trail = $parent_string . $delimiter . $post_link;
         } else {
             $breadcrumb_trail = $post_link;
         }

         if ( $post_type_link )
             $breadcrumb_trail = $post_type_link . $delimiter . $breadcrumb_trail;

         if ( $category_links )
             $breadcrumb_trail = $category_links . $breadcrumb_trail;
    }

    // Handle archives which includes category-, tag-, taxonomy-, date-, custom post type archives and author archives
    if( is_archive() )  {
        if (is_tax()) {
            // Set the variables for this section
            $term_object        = get_term( $queried_object );
            $taxonomy           = $term_object->taxonomy;
            $term_id            = $term_object->term_id;
            $term_name          = $term_object->name;
            $term_parent        = $term_object->parent;
            $taxonomy_object    = get_taxonomy( $taxonomy );
            $current_term_link  = $before .  $term_name . $after;
            $parent_term_string = '';

            if ( 0 !== $term_parent )
            {
                // Get all the current term ancestors
                $parent_term_links = [];
                while ( $term_parent ) {
                    $term = get_term( $term_parent, $taxonomy );

                    $parent_term_links[] = sprintf( $link, esc_url( get_term_link( $term ) ), $term->name );

                    $term_parent = $term->parent;
                }

                $parent_term_links  = array_reverse( $parent_term_links );
                $parent_term_string = implode( $delimiter, $parent_term_links );
            }


            global $post;
            if(isset($post)){
                $post_type        = $post->post_type;
                $post_type_object = get_post_type_object( $post_type );
                $breadcrumb_trail2 = $before . $post_type_object->labels->name . $after;
                $archive_link = get_post_type_archive_link( $post_type ); 
                $brd = sprintf( $link, $archive_link, $post_type_object->labels->name );   
                if ( $parent_term_string ) {
                    $breadcrumb_trail =  $brd . $delimiter . $parent_term_string . $delimiter . $current_term_link;
                } else {
                    $breadcrumb_trail =  $brd .  $delimiter . $current_term_link;
                }
            }

        } elseif ( is_post_type_archive() ) {

            $post_type        = $wp_the_query->query_vars['post_type'];
            $post_type_object = get_post_type_object( $post_type );

            $breadcrumb_trail = $before . $post_type_object->labels->name . $after;

        }
    }   

    // Handle the search page
    if ( is_search() ) {
        $breadcrumb_trail = __( 'Search query for: ' ) . $before . get_search_query() . $after;
    }

    // Handle 404's
    if ( is_404() ) {
        $breadcrumb_trail = $before . __( 'Error 404' ) . $after;
    }

    // Handle paged pages
    if ( is_paged() ) {
        $current_page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' );
        $page_addon   = $before . sprintf( __( ' ( Page %s )' ), number_format_i18n( $current_page ) ) . $after;
    }

    $breadcrumb_output_link  = '';
    $breadcrumb_output_link .= '<div class="breadcrumb">';
    $breadcrumb_output_link .= $breadcrumb_trail;
    $breadcrumb_output_link .= $page_addon;
    $breadcrumb_output_link .= '</div><!-- .breadcrumbs -->';

    return $breadcrumb_output_link;
}
function get_youngest($terms){
    $maxnum = 0;
    $cur = 0;
    for ($i = 0,$len = count($terms); $i < $len; $i++) {
        $obj = $terms[$i];
        $parent = $obj->parent;
        $cnt = 0;
        for ($o = 0,$len = count($terms); $o < $len; $o++) {
            $obj2 = $terms[$o];
            if($parent == $obj2->term_id){
                $parent = $obj2->parent;
                $cnt++;
            }
        }
        if($maxnum < $cnt){
            $cur = $i;
        }
    }
    return $cur;
}


?>