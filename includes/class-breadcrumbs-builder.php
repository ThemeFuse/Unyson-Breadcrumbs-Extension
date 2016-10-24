<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class Breadcrumbs_Builder {

	public function __construct( $settings = array() ) {
		$this->settings['labels'] = array(
			'homepage-title' => __( 'Homepage', 'fw' ),
			'blogpage-title' => __( 'Blog', 'fw' ),
			'404-title'      => __( '404 Not found', 'fw' ),
		);

		if ( isset( $settings['labels'] ) ) {
			$this->settings['labels'] = array_merge( $this->settings['labels'], $settings['labels'] );
		}
	}

	/**
	 * Determine if the page has parents and in case it has, adds all page parents hierarchy
	 *
	 * @param $id , page id
	 *
	 * @return array
	 */
	private function get_page_hierarchy( $id ) {
		$page = get_post( $id );

		if ( empty( $page ) || is_wp_error( $page ) ) {
			return array();
		}

		$return   = array();
		$page_obj = array();

		$page_obj['type']      = 'post';
		$page_obj['post_type'] = $page->post_type;
		$page_obj['name']      = $page->post_title;
		$page_obj['id']        = $id;
		$page_obj['url']       = get_permalink( $id );

		$return[] = $page_obj;
		if ( $page->post_parent > 0 ) {
			$return = array_merge( $return, $this->get_page_hierarchy( $page->post_parent ) );
		}

		return $return;
	}

	/**
	 * Determine if the term has parents and in case it has, adds all term parents hierarchy
	 *
	 * @param $id , term id
	 * @param $taxonomy , term taxonomy name
	 *
	 * @return array
	 */
	private function get_term_hierarchy( $id, $taxonomy ) {
		$term = get_term( $id, $taxonomy );

		if ( empty( $term ) || is_wp_error( $term ) ) {
			return array();
		}

		$return   = array();
		$term_obj = array();

		$term_obj['type']     = 'taxonomy';
		$term_obj['name']     = $term->name;
		$term_obj['id']       = $id;
		$term_obj['url']      = get_term_link( $id, $taxonomy );
		$term_obj['taxonomy'] = $taxonomy;

		$return[] = $term_obj;

		if ( $term->parent > 0 ) {
			$return = array_merge( $return, $this->get_term_hierarchy( $term->parent, $taxonomy ) );
		}

		return $return;
	}

	/**
	 * Determine the current frontend page location, in creates the breadcrumbs array
	 * @return array
	 */
	private function build_breadcrumbs() {
		if ( is_admin() ) {
			return array();
		}

		if ( did_action( 'wp' ) == 0 ) {
			return array();
		}

		$return = array(
			0 => array(
				'name' => $this->settings['labels']['homepage-title'],
				'url'  => esc_url( home_url('/') ),
				'type' => 'front_page'
			),
		);

		$custom_page = apply_filters( 'fw_ext_breadcrumbs_current_page', array() );

		if ( is_array( $custom_page ) && ! empty( $custom_page ) ) {
			$return[] = $custom_page;
			$return = apply_filters( 'fw_ext_breadcrumbs_build', $return );
			return $return;
		}

		if ( is_404() ) {
			$page = array();

			$page['type'] = '404';
			$page['name'] = $this->settings['labels']['404-title'];
			$page['url']  = fw_current_url();

			$return[] = $page;
		} elseif ( is_search() ) {
			$search = array();

			$search['type'] = 'search';
			$search['name'] = __( 'Searching for:', 'fw' ) . ' ' . get_search_query();
			$s              = '?s=' . apply_filters( 'fw_ext_breadcrumbs_search_query', get_search_query() );
			$search['url']  = home_url( '/' ) . $s;

			$return[] = $search;
		} elseif ( is_front_page() ) {
		} elseif ( is_home() ) {

			$blog = array(
				'name' => $this->settings['labels']['blogpage-title'],
				'url'  => fw_current_url(),
				'type' => 'front_page'
			);

			$return[] = $blog;
		} elseif ( is_page() ) {
			global $post;
			$return = array_merge( $return, array_reverse( $this->get_page_hierarchy( $post->ID ) ) );
		} elseif ( is_single() ) {
			global $post;

			$taxonomies = get_object_taxonomies( $post->post_type, 'objects' );
			$slugs      = array();
			if ( ! empty( $taxonomies ) ) {
				foreach ( $taxonomies as $key => $tax ) {
					if ( $tax->show_ui === true && $tax->public === true && $tax->hierarchical !== false ) {
						array_push( $slugs, $tax->name );
					}
				}

				$terms = wp_get_post_terms( $post->ID, $slugs );

				if ( ! empty( $terms ) ) {
					$lowest_term = $this->get_lowest_taxonomy_terms($terms);
					$term = $lowest_term[0];
					$return = array_merge( $return,
						array_reverse( $this->get_term_hierarchy( $term->term_id, $term->taxonomy ))
					);
				}
			}

			$return = array_merge( $return, array_reverse( $this->get_page_hierarchy( $post->ID ) ) );

		} elseif ( is_category() ) {
			$term_id = get_query_var( 'cat' );
			$return  = array_merge( $return, array_reverse( $this->get_term_hierarchy( $term_id, 'category' ) ) );
		} elseif ( is_tag() ) {
			$term_id = get_query_var( 'tag' );
			$term    = get_term_by( 'slug', $term_id, 'post_tag' );

			if ( empty( $term ) || is_wp_error( $term ) ) {
				return array();
			}

			$tag = array();

			$tag['type']     = 'taxonomy';
			$tag['name']     = $term->name;
			$tag['url']      = get_term_link( $term_id, 'post_tag' );
			$tag['taxonomy'] = 'post_tag';
			$return[]        = $tag;
		} elseif ( is_tax() ) {
			$term_id  = get_queried_object()->term_id;
			$taxonomy = get_query_var( 'taxonomy' );
			$return   = array_merge( $return, array_reverse( $this->get_term_hierarchy( $term_id, $taxonomy ) ) );
		} elseif ( is_author() ) {
			$author = array();

			$author['name'] = get_queried_object()->data->display_name;
			$author['id']   = get_queried_object()->data->ID;
			$author['url']  = get_author_posts_url( $author['id'], get_queried_object()->data->user_nicename );
			$author['type'] = 'author';

			$return[] = $author;
		} elseif ( is_date() ) {
			$date  = array();

			if ( get_option( 'permalink_structure' ) ) {
				$day   = get_query_var( 'day' );
				$month = get_query_var( 'monthnum' );
				$year  = get_query_var( 'year' );
			} else {
				$m = get_query_var('m');
				$year = substr( $m, 0, 4 );
				$month = substr( $m, 4, 2 );
				$day = substr( $m, 6, 2 );
			}

			if ( is_day() ) {
				$date['name']      = mysql2date( apply_filters( 'fw_ext_breadcrumbs_date_day_format', 'd F Y' ),
					$day . '-' . $month . '-' . $year );
				$date['url']       = get_day_link( $year, $month, $day );
				$date['date_type'] = 'daily';
				$date['day']       = $day;
				$date['month']     = $month;
				$date['year']      = $year;
			} elseif ( is_month() ) {
				$date['name']      = mysql2date( apply_filters( 'fw_ext_breadcrumbs_date_month_format', 'F Y' ),
					'01.' . $month . '.' . $year );
				$date['url']       = get_month_link( $year, $month );
				$date['date_type'] = 'monthly';
				$date['month']     = $month;
				$date['year']      = $year;
			} else {
				$date['name']      = mysql2date( apply_filters( 'fw_ext_breadcrumbs_date_year_format', 'Y' ),
					'01.01.' . $year );
				$date['url']       = get_year_link( $year );
				$date['date_type'] = 'yearly';
				$date['year']      = $year;
			}

			$return[] = $date;
		} elseif ( is_archive() ) {
			$post_type = get_query_var( 'post_type' );
			if ( $post_type ) {
				$post_type_obj   = get_post_type_object( $post_type );
				$archive         = array();
				$archive['name'] = $post_type_obj->labels->name;
				$archive['url']  = get_post_type_archive_link( $post_type );
				$return[]        = $archive;
			}
		}

		foreach ( $return as $key => $item ) {
			if ( empty( $item['name'] ) ) {
				$return[ $key ]['name'] = __( 'No title', 'fw' );
			}
		}


		/* Reserved for pagination
		 * global $wp_query;
		$paged = array();
		$paged['name'] = get_query_var('paged');
		$paged['max_pages'] = $wp_query->max_num_pages;
		if( intval( $paged['name'] ) > 0 ){
			$paged['name'] = __('Page', 'fw') . ' ' . $paged['name'];
			$return[] = $paged;
		}*/

		$return = apply_filters( 'fw_ext_breadcrumbs_build', $return );

		return $return;
	}

	/**
	 * Returns the lowest hierarchical term
	 * @return array
	 */
	private function get_lowest_taxonomy_terms( $terms ) {
		// if terms is not array or its empty don't proceed
		if ( ! is_array( $terms ) || empty( $terms ) ) {
			return false;
		}

		return $this->filter_terms($terms);
	}

	private function filter_terms($terms) {
		$return_terms = array();
		$term_ids = array();

		foreach ($terms as $t) {
			$term_ids[] = $t->term_id;
		}

		foreach ( $terms as $t ) {
			if ( $t->parent == false || !in_array($t->parent,$term_ids) )  {
				// remove this term
			} else {
				$return_terms[] = $t;
			}
		}

		if ( count($return_terms) ) {
			return $this->filter_terms($return_terms);
		} else {
			return $terms;
		}
	}

	/**
	 * Returns the breadcrumbs array
	 * @return string
	 */
	public function get_breadcrumbs() {
		return $this->build_breadcrumbs();
	}
}