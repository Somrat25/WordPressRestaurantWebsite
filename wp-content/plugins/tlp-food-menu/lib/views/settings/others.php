<?php
$css = ( isset( $others['css'] ) ? ( $others['css'] ? $others['css'] : null ) : null );
?>
<div class="tab-content">
    <div class='rt-field-wrapper'>
        <div class="rt-label">
            <label for=""><?php esc_html_e( 'Custom CSS', 'tlp-food-menu' ); ?></label>
        </div>
        <div class="rt-field">
            <textarea name="others[css]" cols="40" rows="10"><?php echo $css; ?></textarea>
            <p class="description" style="color: red">Please use default customizer to add your css. This option is deprecated.</p>
        </div>
    </div>

</div>
