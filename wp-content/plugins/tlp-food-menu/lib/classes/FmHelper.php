<?php
if ( ! class_exists( 'FmHelper' ) ):

	class FmHelper {
		function placeholder_img_src() {
			return TLPFoodMenu()->getAssetsUrl() . 'images/placeholder.png';
		}


		/**
		 * Generate MetaField Name list for shortCode Page
		 * @return array
		 */
		function fmpScMetaFields() {
			return array_merge(
				TLPFoodMenu()->scLayoutMetaFields(),
				TLPFoodMenu()->scFilterMetaFields(),
				TLPFoodMenu()->scItemFields(),
				TLPFoodMenu()->scStyleFields() );
		}

		function the_excerpt_max_charlength( $charLength ) {
			$excerpt = get_the_excerpt();
			$charLength ++;
			$html = null;
			if ( mb_strlen( $excerpt ) > $charLength ) {
				$subex   = mb_substr( $excerpt, 0, $charLength - 5 );
				$exwords = explode( ' ', $subex );
				$excut   = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
				if ( $excut < 0 ) {
					$html .= mb_substr( $subex, 0, $excut );
				} else {
					$html .= $subex;
				}
			} else {
				$html .= $excerpt;
			}

			return $html;
		}

		function t( $text ) {
			return __( $text, 'tlp-food-menu' );
		}


		function string_limit_words( $string, $word_limit ) {
			$words = explode( ' ', $string );

			return implode( ' ', array_slice( $words, 0, $word_limit ) );
		}

		function rtFieldGenerator( $fields = array() ) {
			$html = null;
			if ( is_array( $fields ) && ! empty( $fields ) ) {
				$fmField = new FmpField();
				foreach ( $fields as $fieldKey => $field ) {
					$html .= $fmField->Field( $fieldKey, $field );
				}
			}

			return $html;
		}

		function sanitize( $field = array(), $value = null ) {
			$newValue = null;
			if ( is_array( $field ) ) {
				$type = ( ! empty( $field['type'] ) ? $field['type'] : 'text' );
				if ( empty( $field['multiple'] ) ) {
					if ( $type == 'text' || $type == 'number' || $type == 'select' || $type == 'checkbox' || $type == 'radio' ) {
						$newValue = sanitize_text_field( $value );
					} else if ( $type == 'price' ) {
						$newValue = ( '' === $value ) ? '' : FMP()->format_decimal( $value );
					} else if ( $type == 'url' ) {
						$newValue = esc_url( $value );
					} else if ( $type == 'slug' ) {
						$newValue = sanitize_title_with_dashes( $value );
					} else if ( $type == 'textarea' ) {
						$newValue = wp_kses_post( $value );
					} else if ( $type == 'custom_css' ) {
						$newValue = esc_attr( $value );
					} else if ( $type == 'colorpicker' ) {
						$newValue = $this->sanitize_hex_color( $value );
					} else if ( $type == 'image_size' ) {
						$newValue = array();
						foreach ( $value as $k => $v ) {
							$newValue[ $k ] = esc_attr( $v );
						}
					} else if ( $type == 'style' ) {
						$newValue = array();
						foreach ( $value as $k => $v ) {
							if ( $k == 'color' ) {
								$newValue[ $k ] = $this->sanitize_hex_color( $v );
							} else {
								$newValue[ $k ] = $this->sanitize( array( 'type' => 'text' ), $v );
							}
						}
					} else {
						$newValue = sanitize_text_field( $value );
					}

				} else {
					$newValue = array();
					if ( ! empty( $value ) ) {
						if ( is_array( $value ) ) {
							foreach ( $value as $key => $val ) {
								if ( $type == 'style' && $key == 0 ) {
									if ( function_exists( 'sanitize_hex_color' ) ) {
										$newValue = sanitize_hex_color( $val );
									} else {
										$newValue[] = $this->sanitize_hex_color( $val );
									}
								} else {
									$newValue[] = sanitize_text_field( $val );
								}
							}
						} else {
							$newValue[] = sanitize_text_field( $value );
						}
					}
				}
			}

			return $newValue;
		}

		function sanitize_hex_color( $color ) {
			if ( function_exists( 'sanitize_hex_color' ) ) {
				return sanitize_hex_color( $color );
			} else {
				if ( '' === $color ) {
					return '';
				}

				// 3 or 6 hex digits, or the empty string.
				if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
					return $color;
				}
			}
		}

		/* Convert hexdec color string to rgb(a) string */
		function rtHex2rgba( $color, $opacity = .5 ) {

			$default = 'rgb(0,0,0)';

			//Return default if no color provided
			if ( empty( $color ) ) {
				return $default;
			}

			//Sanitize $color if "#" is provided
			if ( $color[0] == '#' ) {
				$color = substr( $color, 1 );
			}

			//Check if color has 6 or 3 characters and get values
			if ( strlen( $color ) == 6 ) {
				$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
			} elseif ( strlen( $color ) == 3 ) {
				$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
			} else {
				return $default;
			}

			//Convert hexadec to rgb
			$rgb = array_map( 'hexdec', $hex );

			//Check if opacity is set(rgba or rgb)
			if ( $opacity ) {
				if ( abs( $opacity ) > 1 ) {
					$opacity = 1.0;
				}
				$output = 'rgba(' . implode( ",", $rgb ) . ',' . $opacity . ')';
			} else {
				$output = 'rgb(' . implode( ",", $rgb ) . ')';
			}

			//Return rgb(a) color string
			return $output;
		}

		function getAllFmpCategoryList() {
			$terms    = array();
			$termList = get_terms( array( TLPFoodMenu()->taxonomies['category'] ), array( 'hide_empty' => 0 ) );
			if ( is_array( $termList ) && ! empty( $termList ) && empty( $termList['errors'] ) ) {
				foreach ( $termList as $term ) {
					$terms[ $term->term_id ] = $term->name;
				}
			}

			return $terms;
		}


		function get_image_sizes() {
			global $_wp_additional_image_sizes;

			$sizes = array();
			foreach ( get_intermediate_image_sizes() as $_size ) {
				if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
					$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
					$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
					$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
				} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
					$sizes[ $_size ] = array(
						'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
						'height' => $_wp_additional_image_sizes[ $_size ]['height'],
						'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
					);
				}
			}

			$imgSize = array();
			foreach ( $sizes as $key => $img ) {
				$imgSize[ $key ] = ucfirst( $key ) . " ({$img['width']}*{$img['height']})";
			}

			return $imgSize;
		}

		function get_product_list_html( $products = array() ) {
			$html = null;
			if ( ! empty( $products ) ) {
				foreach ( $products as $type => $list ) {
					if ( ! empty( $list ) ) {
						$htmlProducts = null;
						foreach ( $list as $product ) {
							$image_url       = isset( $product['image_url'] ) ? $product['image_url'] : null;
							$image_thumb_url = isset( $product['image_thumb_url'] ) ? $product['image_thumb_url'] : null;
							$image_thumb_url = $image_thumb_url ? $image_thumb_url : $image_url;
							$price           = isset( $product['price'] ) ? $product['price'] : null;
							$title           = isset( $product['title'] ) ? $product['title'] : null;
							$url             = isset( $product['url'] ) ? $product['url'] : null;
							$buy_url         = isset( $product['buy_url'] ) ? $product['buy_url'] : null;
							$buy_url         = $buy_url ? $buy_url : $url;
							$doc_url         = isset( $product['doc_url'] ) ? $product['doc_url'] : null;
							$demo_url        = isset( $product['demo_url'] ) ? $product['demo_url'] : null;
							$feature_list    = null;
							$info_html       = sprintf( '<div class="rt-product-info">%s%s%s</div>',
								$title ? sprintf( "<h3 class='rt-product-title'><a href='%s' target='_blank'>%s%s</a></h3>", esc_url( $url ), $title, $price ? " ($" . $price . ")" : null ) : null,
								$feature_list,
								$buy_url || $demo_url || $doc_url ?
									sprintf(
										'<div class="rt-product-action">%s%s%s</div>',
										$buy_url ? sprintf( '<a class="rt-buy button button-primary" href="%s" target="_blank">%s</a>', esc_url( $buy_url ), esc_html__( 'Buy', 'food-menu-pro' ) ) : null,
										$demo_url ? sprintf( '<a class="rt-demo button" href="%s" target="_blank">%s</a>', esc_url( $demo_url ), esc_html__( 'Demo', 'food-menu-pro' ) ) : null,
										$doc_url ? sprintf( '<a class="rt-doc button" href="%s" target="_blank">%s</a>', esc_url( $doc_url ), esc_html__( 'Documentation', 'food-menu-pro' ) ) : null
									)
									: null
							);

							$htmlProducts .= sprintf(
								'<div class="rt-product">%s%s</div>',
								$image_thumb_url ? sprintf(
									'<div class="rt-media"><img src="%s" alt="%s" /></div>',
									esc_url( $image_thumb_url ),
									esc_html( $title )
								) : null,
								$info_html
							);

						}
						$html .= sprintf( '<div class="rt-product-list">%s</div>', $htmlProducts );

					}
				}
			}

			return $html;
		}

		function pagination( $pages = '', $range = 4, $ajax = false, $scID = '' ) {

			$html      = null;
			$showitems = ( $range * 2 ) + 1;
			global $paged;
			if ( empty( $paged ) ) {
				$paged = 1;
			}
			if ( $pages == '' ) {
				global $wp_query;
				$pages = $wp_query->max_num_pages;
				if ( ! $pages ) {
					$pages = 1;
				}
			}
			$ajaxClass = null;
			$dataAttr  = null;

			if ( $ajax ) {
				$ajaxClass = ' fmp-ajax';
				$dataAttr  = "data-sc-id='{$scID}' data-paged='1'";
			}

			if ( 1 != $pages ) {

				$html .= '<div class="fmp-pagination' . $ajaxClass . '" ' . $dataAttr . '>';
				$html .= '<ul class="pagination-list">';
				if ( $paged > 2 && $paged > $range + 1 && $showitems < $pages ) {
					$html .= "<li><a data-paged='1' href='" . get_pagenum_link( 1 ) . "' aria-label='First'>&laquo;</a></li>";
				}

				if ( $paged > 1 && $showitems < $pages ) {
					$p    = $paged - 1;
					$html .= "<li><a data-paged='{$p}' href='" . get_pagenum_link( $p ) . "' aria-label='Previous'>&lsaquo;</a></li>";
				}


				for ( $i = 1; $i <= $pages; $i ++ ) {
					if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
						$html .= ( $paged == $i ) ? "<li class=\"active\"><span>" . $i . "</span>

    </li>" : "<li><a data-paged='{$i}' href='" . get_pagenum_link( $i ) . "'>" . $i . "</a></li>";

					}

				}

				if ( $paged < $pages && $showitems < $pages ) {
					$p    = $paged + 1;
					$html .= "<li><a data-paged='{$p}' href=\"" . get_pagenum_link( $paged + 1 ) . "\"  aria-label='Next'>&rsaquo;</a></li>";
				}

				if ( $paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages ) {
					$html .= "<li><a data-paged='{$pages}' href='" . get_pagenum_link( $pages ) . "' aria-label='Last'>&raquo;</a></li>";
				}

				$html .= "</ul>";
				$html .= "</div>";
			}

			return $html;

		}

		function strip_tags_content( $text, $limit = 0, $tags = '', $invert = false ) {

			preg_match_all( '/<(.+?)[\s]*\/?[\s]*>/si', trim( $tags ), $tags );
			$tags = array_unique( $tags[1] );

			if ( is_array( $tags ) AND count( $tags ) > 0 ) {
				if ( $invert == false ) {
					$text = preg_replace( '@<(?!(?:' . implode( '|', $tags ) . ')\b)(\w+)\b.*?>.*?</\1>@si', '',
						$text );
				} else {
					$text = preg_replace( '@<(' . implode( '|', $tags ) . ')\b.*?>.*?</\1>@si', '', $text );
				}
			} else if ( $invert == false ) {
				$text = preg_replace( '@<(\w+)\b.*?>.*?</\1>@si', '', $text );
			}
			if ( $limit > 0 && strlen( $text ) > $limit ) {
				$text = substr( $text, 0, $limit );
			}

			return $text;
		}


		/**
		 * Call the Image resize model for resize function
		 *
		 * @param            $url
		 * @param null $width
		 * @param null $height
		 * @param null $crop
		 * @param bool|true $single
		 * @param bool|false $upscale
		 *
		 * @return array|bool|string
		 * @throws FmpException
		 */
		function rtImageReSize( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
			$rtResize = new FmReSizer();

			return $rtResize->process( $url, $width, $height, $crop, $single, $upscale );
		}

		function getFeatureImage( $post_id, $fImgSize = 'medium' ) {
			global $post;
			$img_class = "fmp-feature-img";
			$image     = null;
			$post_id   = $post_id ? absint( $post_id ) : $post->ID;
			$alt       = esc_url( get_the_title( $post_id ) );
			$thumb_id  = get_post_thumbnail_id( $post_id );
			if ( $thumb_id ) {
				$image = wp_get_attachment_image( $thumb_id, $fImgSize, '', array( "class" => $img_class ) );
			} else {
				$image = sprintf( '<img alt="%s" class="%s" src="%s" />', $alt, $img_class, esc_url( TLPFoodMenu()->placeholder_img_src() ) );
			}

			return $image;
		}

		function get_shortCode_list() {

			$scList = null;
			$scQ    = get_posts( array(
				'post_type'      => TLPFoodMenu()->getShortCodePT(),
				'order_by'       => 'title',
				'order'          => 'ASC',
				'post_status'    => 'publish',
				'posts_per_page' => - 1
			) );
			if ( ! empty( $scQ ) ) {
				foreach ( $scQ as $sc ) {
					$scList[ $sc->ID ] = $sc->post_title;
				}
			}

			return $scList;
		}
	}

endif;