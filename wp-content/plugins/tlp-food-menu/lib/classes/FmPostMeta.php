<?php
if ( ! class_exists( 'FmPostMeta' ) ):

	/**
	 *
	 */
	class FmPostMeta {

		function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_shortcode' ) );
			add_action( 'add_meta_boxes', array( $this, 'food_menu_meta_boxs' ) );
			add_action( 'save_post', array( $this, 'save_food_meta_data' ), 10, 3 );
			add_action( 'edit_form_after_title', array( $this, 'food_menu_after_title' ) );
			add_action( 'quick_edit_custom_box', array( $this, 'food_menu_add_to_bulk_quick_edit_custom_box' ), 10, 2 );
			add_action( 'save_post', array( $this, 'food_menu_quick_edit_save' ) );
			add_action( 'admin_print_scripts-edit.php', array( $this, 'food_menu_enqueue_edit_scripts' ) );
		}

		function food_menu_enqueue_edit_scripts() {
			wp_enqueue_script( 'food-menu-admin-edit', TLPFoodMenu()->getAssetsUrl() . 'js/quick_edit.js', array(
				'jquery',
				'inline-edit-post'
			), '', true );
		}

		function food_menu_quick_edit_save( $post_id ) {
			$post = get_post( $post_id );
			// Criteria for not saving: Auto-saves, not post_type_characters, can't edit
			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || TLPFoodMenu()->post_type != $post->post_type ) {
				return $post_id;
			}

			// RoleType
			if ( $post->post_type != 'revision' ) {
				$price = ( isset( $_POST['price'] ) ? sprintf( "%.2f",
					floatval( sanitize_text_field( esc_attr( $_POST['price'] ) ) ) ) : null );;
				update_post_meta( $post_id, 'price', $price );
			}

			// Sexuality went here

			// Gender went here
		}

		function food_menu_add_to_bulk_quick_edit_custom_box( $column_name, $post_type ) {
			switch ( $post_type ) {
				case TLPFoodMenu()->post_type:

					switch ( $column_name ) {
						case 'price':
							global $post;
							//$pid = get_the_ID();
							$price = get_post_meta( $post->ID, 'price', true );
							?>
                            <fieldset class="inline-edit-col-right">
                            <div class="inline-edit-group">
                                <label>
                                    <span class="title">Price</span>
                                    <span class="input-text-wrap">
                                            <input type="text" name="price" class="inline-edit-menu-order-input"
                                                   value="<?php echo $price; ?>"/>
                                        </span>
                                </label>
                            </div>
                            </fieldset><?php
							break;
					}
					break;

			}
		}

		function admin_enqueue_scripts() {
			global $pagenow, $typenow;
			// validate page
			if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php', 'edit.php' ) ) ) {
				return;
			}

			if ( $typenow != TLPFoodMenu()->post_type ) {
				return;
			}

			wp_enqueue_style( array( 'wp-color-picker', 'fm-select2', 'fm-admin' ) );
			wp_enqueue_script( array( 'wp-color-picker', 'fm-select2', 'fm-admin' ) );
			$nonce = wp_create_nonce( TLPFoodMenu()->nonceText() );
			wp_localize_script( 'fm-admin', 'tpl_fm_var', array( 'tlp_fm_nonce' => $nonce ) );
		}

		function admin_enqueue_scripts_shortcode() {
			global $pagenow, $typenow;
			// validate page
			if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php', 'edit.php' ) ) ) {
				return;
			}

			if ( $typenow != TLPFoodMenu()->getShortCodePT() ) {
				return;
			}

			wp_enqueue_style( array( 'wp-color-picker', 'fm-select2', 'fm-admin', 'fm-frontend' ) );
			wp_enqueue_script( array( 'wp-color-picker', 'fm-select2', 'fm-admin' ) );
			$nonce = wp_create_nonce( TLPFoodMenu()->nonceText() );
			wp_localize_script( 'fm-admin', 'tpl_fm_var', array( 'tlp_fm_nonce' => $nonce ) );
		}

		function food_menu_after_title( $post ) {
			if ( TLPFoodMenu()->post_type !== $post->post_type ) {
				return;
			}
			$html = null;
			$html .= '<div class="postbox" style="margin-bottom: 0;"><div class="inside">';
			$html .= '<p style="text-align: center;"><a style="color: red; text-decoration: none; font-size: 14px;" href="https://www.radiustheme.com/food-menu-pro-wordpress/" target="_blank">Please check the pro features</a></p>';
			$html .= '</div></div>';

			echo $html;
		}

		function food_menu_meta_boxs() {
			add_meta_box( 'tlp_food_menu_meta_details', __( 'Food Details', 'tlp-food-menu' ),
				array( $this, 'food_menu_meta_option' ), TLPFoodMenu()->post_type, 'normal', 'high' );
		}

		function food_menu_meta_option( $post ) {
			wp_nonce_field( TLPFoodMenu()->nonceText(), 'tlp_fm_nonce' );
			$meta = get_post_meta( $post->ID );
			$price = ! isset( $meta['price'][0] ) ? '' : $meta['price'][0];

			?>
            <table class="form-table">

                <tr>
                    <td class="team_meta_box_td" colspan="2">
                        <label for="price"><?php _e( 'Price', 'tlp-food-menu' ); ?></label>
                    </td>
                    <td colspan="4">
                        <input min="0" step="0.01" type="number" name="price" id="price" class="tlpfield"
                               value="<?php echo sprintf( "%.2f", $price ); ?>">
                        <p class="description"><?php _e( 'Insert the price, leave blank if it is free',
								'tlp-food-menu' ); ?></p>
                    </td>
                </tr>
            </table>
			<?php
		}


		function save_food_meta_data( $post_id, $post, $update ) {

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}


			if ( ! TLPFoodMenu()->verifyNonce() ) {
				return;
			}

			// Check permissions

			if ( TLPFoodMenu()->post_type != $post->post_type ) {
				return;
			}

			$meta['price'] = ( isset( $_POST['price'] ) ? sprintf( "%.2f",
				floatval( sanitize_text_field( esc_attr( $_POST['price'] ) ) ) ) : null );

			foreach ( $meta as $key => $value ) {
				update_post_meta( $post->ID, $key, $value );
			}
		}
	}
endif;
