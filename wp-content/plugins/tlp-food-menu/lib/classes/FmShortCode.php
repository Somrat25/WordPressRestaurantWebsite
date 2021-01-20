<?php

if ( ! class_exists( 'FmShortCode' ) ):

	/**
	 *
	 */
	class FmShortCode {

		function __construct() {
			add_shortcode( 'foodmenu', array( $this, 'foodmenu_shortcode' ) );
            add_shortcode( 'rt-foodmenu', array( $this, 'foodmenu_shortcode' ) );
			add_shortcode( 'foodmenu-single', array( $this, 'foodmenu_single' ) );
			add_action( 'wp_ajax_fmpPreviewAjaxCall', array( $this, 'foodmenu_shortcode' ) );
		}

		function foodmenu_shortcode( $atts, $content = "" ) {

			$error = true;
			$html  = $msg = null;


			$preview = isset( $_REQUEST['sc_id'] ) ? absint( $_REQUEST['sc_id'] ) : 0;
			$scID    = isset( $atts['id'] ) ? absint( $atts['id'] ) : 0;
			if ( $scID || $preview ) {
				$post = get_post( $scID );
				if ( ( ! $preview && ! is_null( $post ) && $post->post_type === TLPFoodMenu()->getShortCodePT() ) || ( $preview && TLPFoodMenu()->verifyNonce() ) ) {
					$rand       = mt_rand();
					$layoutID   = "fmp-container-" . $rand;
					$html       = null;
					$arg        = array();
					$query_args = array(
						'post_type'   => TLPFoodMenu()->post_type,
						'post_status' => 'publish',
					);

					if ( $preview ) {

						$error            = false;
						$scMeta           = $_REQUEST;
						$layout           = isset( $scMeta['fmp_layout'] ) ? $scMeta['fmp_layout'] : 'layout1';
						$dCol             = isset( $scMeta['fmp_desktop_column'] ) ? absint( $scMeta['fmp_desktop_column'] ) : 3;
						$tCol             = isset( $scMeta['fmp_tab_column'] ) ? absint( $scMeta['fmp_tab_column'] ) : 2;
						$mCol             = isset( $scMeta['fmp_mobile_column'] ) ? absint( $scMeta['fmp_mobile_column'] ) : 1;
						$imgSize          = isset( $scMeta['fmp_image_size'] ) ? $scMeta['fmp_image_size'] : "medium";
						$excerpt_limit    = isset( $scMeta['fmp_excerpt_limit'] ) ? absint( $scMeta['fmp_excerpt_limit'] ) : 0;
						$post__in         = isset( $scMeta['fmp_post__in'] ) ? $scMeta['fmp_post__in'] : null;
						$post__not_in     = isset( $scMeta['fmp_post__not_in'] ) ? $scMeta['fmp_post__not_in'] : null;
						$limit            = ( empty( $scMeta['fmp_limit'] ) || $scMeta['fmp_limit'] === '-1' ) ? 10000000 : (int) $scMeta['fmp_limit'][0];
						$cats             = isset( $scMeta['fmp_categories'] ) ? array_filter( $scMeta['fmp_categories'] ) : array();
						$order_by         = isset( $scMeta['fmp_order_by'] ) ? $scMeta['fmp_order_by'] : null;
						$order            = isset( $scMeta['fmp_order'] ) ? $scMeta['fmp_order'] : null;
						$gridType         = isset( $scMeta['fmp_grid_style'] ) ? $scMeta['fmp_grid_style'] : 'even';
						$arg['read_more'] = isset( $scMeta['fmp_read_more_button_text'] ) ? esc_attr( $scMeta['fmp_read_more_button_text'] ) : null;
						$arg['items']     = isset( $scMeta['fmp_item_fields'] ) ? $scMeta['fmp_item_fields'] : array();
						$link             = isset( $scMeta['fmp_detail_page_link'] ) ? true : false;
						$parentClass      = isset( $scMeta['fmp_parent_class'] ) ? trim( $scMeta['fmp_parent_class'] ) : null;

						// Common filter
					} else {
						$scMeta           = get_post_meta( $scID );
						$layout           = ( ! empty( $scMeta['fmp_layout'][0] ) ? $scMeta['fmp_layout'][0] : 'layout1' );
						$dCol             = isset( $scMeta['fmp_desktop_column'][0] ) ? absint( $scMeta['fmp_desktop_column'][0] ) : 3;
						$tCol             = isset( $scMeta['fmp_tab_column'][0] ) ? absint( $scMeta['fmp_tab_column'][0] ) : 2;
						$mCol             = isset( $scMeta['fmp_mobile_column'][0] ) ? absint( $scMeta['fmp_mobile_column'][0] ) : 1;
						$imgSize          = ( ! empty( $scMeta['fmp_image_size'][0] ) ? $scMeta['fmp_image_size'][0] : "medium" );
						$excerpt_limit    = ( ! empty( $scMeta['fmp_excerpt_limit'][0] ) ? absint( $scMeta['fmp_excerpt_limit'][0] ) : 0 );
						$post__in         = ( isset( $scMeta['fmp_post__in'][0] ) ? $scMeta['fmp_post__in'][0] : null );
						$post__not_in     = ( isset( $scMeta['fmp_post__not_in'][0] ) ? $scMeta['fmp_post__not_in'][0] : null );
						$limit            = ( ( empty( $scMeta['fmp_limit'][0] ) || $scMeta['fmp_limit'][0] === '-1' ) ? 10000000 : (int) $scMeta['fmp_limit'][0] );
						$cats             = ( isset( $scMeta['fmp_categories'] ) ? array_filter( $scMeta['fmp_categories'] ) : array() );
						$order_by         = ( isset( $scMeta['fmp_order_by'][0] ) ? $scMeta['fmp_order_by'][0] : null );
						$order            = ( isset( $scMeta['fmp_order'][0] ) ? $scMeta['fmp_order'][0] : null );
						$gridType         = ! empty( $scMeta['fmp_grid_style'][0] ) ? $scMeta['fmp_grid_style'][0] : 'even';
						$arg['read_more'] = ! empty( $scMeta['fmp_read_more_button_text'][0] ) ? esc_attr( $scMeta['fmp_read_more_button_text'][0] ) : null;
						$arg['items']     = ! empty( $scMeta['fmp_item_fields'] ) ? $scMeta['fmp_item_fields'] : array();
						$link             = ! empty( $scMeta['fmp_detail_page_link'][0] ) ? true : false;
						$parentClass      = ( ! empty( $scMeta['fmp_parent_class'][0] ) ? trim( $scMeta['fmp_parent_class'][0] ) : null );
					}

					if ( ! in_array( $layout, array_keys( TLPFoodMenu()->scLayout() ) ) ) {
						$layout = 'layout-free';
					}
					$isCat = preg_match( '/grid-by-cat/', $layout );

					if ( ! in_array( $dCol, array_keys( TLPFoodMenu()->scColumns() ) ) ) {
						$dCol = 3;
					}
					if ( ! in_array( $tCol, array_keys( TLPFoodMenu()->scColumns() ) ) ) {
						$tCol = 2;
					}
					if ( ! in_array( $dCol, array_keys( TLPFoodMenu()->scColumns() ) ) ) {
						$mCol = 1;
					}

					/* post__in */
					if ( $post__in ) {
						$post__in               = explode( ',', $post__in );
						$query_args['post__in'] = $post__in;
					}
					/* post__not_in */
					if ( $post__not_in ) {
						$post__not_in               = explode( ',', $post__not_in );
						$query_args['post__not_in'] = $post__not_in;
					}

					/* LIMIT */
					$query_args['posts_per_page'] = $limit;


					// Taxonomy
					$taxQ = array();
					if ( is_array( $cats ) && ! empty( $cats ) ) {
						$taxQ[]                  = array(
							'taxonomy' => TLPFoodMenu()->taxonomies['category'],
							'field'    => 'term_id',
							'terms'    => $cats,
						);
						$query_args['tax_query'] = $taxQ;
					}

					// Order
					if ( $order ) {
						$query_args['order'] = $order;
					}
					if ( $order_by ) {
						$query_args['orderby'] = $order_by;
					}


					// Validation
					$containerDataAttr = "data-sc-id='{$scID}' data-layout='{$layout}' data-desktop-col='{$dCol}'  data-tab-col='{$tCol}'  data-mobile-col='{$mCol}'";
					$dCol              = round( 12 / $dCol );
					$tCol              = round( 12 / $tCol );
					$mCol              = round( 12 / $mCol );
					$gridExtra         = null;

					$arg['grid']          = "fmp-col-lg-{$dCol} fmp-col-md-{$dCol} fmp-col-sm-{$tCol} fmp-col-xs-{$mCol}";
					$arg['class']         = $gridType . "-grid-item";
					$arg['class']         .= " fmp-item";
					$arg['link']          = $link;

					// Start layout
					$html .= $this->layoutStyle( $layoutID, $scMeta, $preview );
					$html .= "<div class='fmp-container-fluid fmp-wrapper fmp {$parentClass}' id='{$layoutID}' {$containerDataAttr}>";
					$html .= "<div class='fmp-row fmp-{$layout}'>";
					if ( $isCat ) {

						$terms = get_terms( [
							'taxonomy'   => TLPFoodMenu()->taxonomies['category'],
							'hide_empty' => false
						] );
						if ( is_array( $terms ) && ! empty( $terms ) && empty( $terms['errors'] ) ) {
							foreach ( $terms as $term ) {
								if ( ! empty( $cats ) && is_array( $cats ) && ! in_array( $term->term_id, $cats ) ) {
									continue;
								}
								$taxQ                    = array();
								$taxQ[]                  = array(
									'taxonomy' => TLPFoodMenu()->taxonomies['category'],
									'field'    => 'term_id',
									'terms'    => array( $term->term_id ),
								);
								$query_args['tax_query'] = $taxQ;
								$data['args']            = $query_args;

								$data['taxonomy']       = TLPFoodMenu()->taxonomies['category'];
								$data['excerpt_limit']  = $excerpt_limit;
								$data['imgSize']        = $imgSize;
								$data['term']           = $term;
								$data['catId']          = $term->term_id;
								$data['catName']        = $term->name;
								$data['catDescription'] = $term->description;
								$data['arg']            = $arg;
								$html                   .= TLPFoodMenu()->render( 'layouts/' . $layout, $data, true );
							}
						} else {
							$html .= "<p>" . __( 'No category found', 'food-menu-pro' ) . "</p>";
						}
					} else {
						$fmpQuery = new WP_Query( $query_args );
						if ( $fmpQuery->have_posts() ) {
							while ( $fmpQuery->have_posts() ) : $fmpQuery->the_post();
								$pID            = get_the_ID();
								$arg['pID']     = $pID;
								$arg['title']   = get_the_title();
								$arg['pLink']   = get_permalink();
								$excerpt        = get_the_excerpt();
								$arg['excerpt'] = TLPFoodMenu()->strip_tags_content( $excerpt, $excerpt_limit );
								$arg['img']     = TLPFoodMenu()->getFeatureImage( $pID, $imgSize );
								$html           .= TLPFoodMenu()->render( 'layouts/' . $layout, $arg, true );
							endwhile;
						} else {
							$html .= "<p>" . __( 'No post found...', 'food-menu-pro' ) . "</p>";
						}
					}
					$html .= "</div>"; // End row

					$html .= "</div>"; // container fmp-fmp
					wp_reset_postdata();

				} else {
					if ( $preview ) {
						$msg = __( 'Session Error !!', 'tlp-portfolio' );
					} else {
						$html .= "<p>" . __( "No shortCode found", 'tlp-portfolio' ) . "</p>";
					}
				}

				if ( $preview ) {
					wp_send_json( array(
						'error' => $error,
						'msg'   => $msg,
						'data'  => $html
					) );
				} else {
					return $html;
				}
			} else {
				return $this->get_old_layout( $atts );
			}
		}


		function foodmenu_single( $atts, $content = "" ) {
			/**
			 * Shortcode attribute desctiption
			 *
			 * @var [type]
			 */

			$html = null;

			$atts = shortcode_atts( array(
				'id' => null,
			), $atts, 'foodmenu-single' );

			return $html;
		}

		function styleGenerator( $title_color ) {
			$html = null;
			if ( ! empty( $title_color ) ) {
				$html .= "<style type='text/css'>";
				$html .= ".fmp-wrapper h3,.fmp-wrapper h3 a{ color:{$title_color}; }";
				$html .= "</style>";
			}

			return $html;
		}

		private function get_old_layout( $atts ) {

			wp_enqueue_script( 'fm-frontend' );

			$atts = shortcode_atts( array(
				'col'          => 2,
				'orderby'      => 'date',
				'order'        => 'DESC',
				'cat'          => 'all',
				'hide-img'     => false,
				'disable-link' => false,
				'title-color'  => null,
				'class'        => null,
			), $atts, 'foodmenu' );

			@$rawCat = ( $atts['cat'] == 'all' ? null : $atts['cat'] );
			$settings   = get_option( TLPFoodMenu()->options['settings'] );
			$charLength = ( isset( $settings['general']['character_limit'] ) ? ( $settings['general']['character_limit'] ? intval( $settings['general']['character_limit'] ) : 150 ) : 150 );
			$col        = in_array( $atts['col'], array( 1, 2, 3, 4 ) ) ? $atts['col'] : 2;
			$grid       = 12 / $col;

			$bss = "tlp-col-md-{$grid} tlp-col-lg-{$grid} tlp-col-sm-12 fmp-item";

			$cat = array();
			if ( isset( $rawCat ) ) {
				$rca = explode( ",", $rawCat );
				if ( ! empty( $rca ) ) {
					foreach ( $rca as $c ) {
						$cat[] = $c;
					}
				}
			}
			$html  = null;
			$class = array(
				'fmp-container-fluid',
				'fmp-wrapper'
			);
			if ( ! empty( $atts['class'] ) ) {
				$class[] = $atts['class'];
			}
			$class = implode( ' ', $class );
			$html  .= '<div class="' . esc_attr( $class ) . '">';
			if ( ! empty( $cat ) && is_array( $cat ) ) {
				foreach ( $cat as $c ) {
					$args = array(
						'post_type'      => TLPFoodMenu()->post_type,
						'post_status'    => 'publish',
						'posts_per_page' => - 1,
						'orderby'        => $atts['orderby'],
						'order'          => $atts['order'],
						'tax_query'      => array(
							array(
								'taxonomy' => TLPFoodMenu()->taxonomies['category'],
								'field'    => 'term_id',
								'terms'    => array( $c ),
								'operator' => 'IN',
							),
						)
					);

					$foodQuery = new WP_Query( $args );
					$term      = get_term_by( 'id', $c, TLPFoodMenu()->taxonomies['category'] );
					if ( $foodQuery->have_posts() ) {
						$html .= $this->styleGenerator( $atts['title-color'] );
						$html .= "<h2 class='category-title'>{$term->name}</h2>";
						$html .= '<div class="fmp-row">';
						while ( $foodQuery->have_posts() ) : $foodQuery->the_post();
							$html .= "<div class='{$bss}'>";
							$html .= "<div class='tlp-equal-height fmp-food-item'>";
							if ( ! $atts['hide-img'] ) {
								$html .= '<div class="fmp-image-wrap">';
								if ( has_post_thumbnail() ) {
									$img = get_the_post_thumbnail( get_the_ID(), 'medium' );
								} else {
									$img = "<img src='" . TLPFoodMenu()->getAssetsUrl() . 'images/demo-55x55.png' . "' alt='" . get_the_title() . "' />";
								}
								if ( $atts['disable-link'] ) {
									$html .= $img;
								} else {
									$html .= '<a href="' . get_permalink() . '" title="' . get_the_title() . '">' . $img . '</a>';
								}
								$html .= '</div>';
							}
							$html .= '<div class="fmp-content-wrap">';
							$html .= "<div class='fmp-title'>";
							if ( $atts['disable-link'] ) {
								$html .= '<h3>' . get_the_title() . '</h3>';
							} else {
								$html .= '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
							}
							$gTotal = TLPFoodMenu()->getPriceWithLabel();
							$html   .= '<span class="price">' . $gTotal . '</span>';
							$html   .= "</div>";
							$html   .= '<p>' . TLPFoodMenu()->the_excerpt_max_charlength( $charLength ) . '</p>';
							$html   .= '</div>';
							$html   .= '</div>';
							$html   .= "</div>";
						endwhile;
						wp_reset_postdata();
						$html .= '</div>';
					}
				}
			} else {
				$html      .= '<div class="fmp-row">';
				$args      = array(
					'post_type'      => TLPFoodMenu()->post_type,
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'orderby'        => $atts['orderby'],
					'order'          => $atts['order']
				);
				$foodQuery = new WP_Query( $args );
				if ( $foodQuery->have_posts() ) {
					$html .= TLPFoodMenu()->styleGenerator( $atts['title-color'] );
					while ( $foodQuery->have_posts() ) : $foodQuery->the_post();
						$html .= "<div class='{$bss}'>";
						$html .= "<div class='tlp-equal-height fmp-food-item'>";
						if ( ! $atts['hide-img'] ) {
							$html .= '<div class="fmp-image-wrap">';
							if ( has_post_thumbnail() ) {
								$img = get_the_post_thumbnail( get_the_ID(), 'medium' );
							} else {
								$img = "<img src='" . TLPFoodMenu()->getAssetsUrl() . 'images/demo-55x55.png' . "' alt='" . get_the_title() . "' />";
							}
							if ( $atts['disable-link'] ) {
								$html .= $img;
							} else {
								$html .= '<a href="' . get_permalink() . '" title="' . get_the_title() . '">' . $img . '</a>';
							}
							$html .= '</div>';
						}
						$html .= '<div class="fmp-content-wrap">';
						$html .= "<div class='fmp-title'>";
						if ( $atts['disable-link'] ) {
							$html .= '<h3>' . get_the_title() . '</h3>';
						} else {
							$html .= '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
						}
						$gTotal = TLPFoodMenu()->getPriceWithLabel();
						$html   .= '<span class="price">' . $gTotal . '</span>';
						$html   .= '</div>';
						$html   .= '<p>' . TLPFoodMenu()->the_excerpt_max_charlength( $charLength ) . '</p>';
						$html   .= '</div>';
						$html   .= '</div>';
						$html   .= "</div>";
					endwhile;
					wp_reset_postdata();

				} else {
					$html .= "<p>" . __( 'No food found.', 'tlp-food-menu' ) . "</p>";
				}
				$html .= '</div>';
			}

			$html .= '</div>';

			return $html;
		}

		private function layoutStyle( $ID, $scMeta, $preview = false ) {

			$css = null;
			// Title
			$title = ( ! empty( $scMeta['fmp_title_style'] ) ? $scMeta['fmp_title_style'] : array() );
			if ( ! empty( $title ) ) {
				$css             .= "<style type='text/css' media='all'>";
				$title_color     = ( ! empty( $title['color'] ) ? $title['color'] : null );
				$title_size      = ( ! empty( $title['size'] ) ? absint( $title['size'] ) : null );
				$title_weight    = ( ! empty( $title['weight'] ) ? $title['weight'] : null );
				$title_alignment = ( ! empty( $title['align'] ) ? $title['align'] : null );
				$css             .= "#{$ID} .fmp-title h3, ";
				$css             .= "#{$ID} .fmp-title h3 a{ ";
				if ( $title_color ) {
					$css .= "color:" . $title_color . ";";
				}
				if ( $title_size ) {
					$css .= "font-size:" . $title_size . "px;";
				}
				if ( $title_weight ) {
					$css .= "font-weight:" . $title_weight . ";";
				}
				if ( $title_alignment ) {
					$css .= "text-align:" . $title_alignment . ";";
				}
				$css .= "}";
				$css .= "</style>";

			}


			return $css;
		}

	}


endif;
