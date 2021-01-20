<div class="<?php echo esc_attr($grid . " " . $class); ?>">
    <div class='fmp-food-item'>
        <?php
        if (in_array('image', $items)) {

            $html .= '<div class="fmp-image-wrap">';
            if (!$link) {
                $html .= $img;
            } else {
                $html .= '<a href="' . get_permalink() . '" title="' . get_the_title() . '">' . $img . '</a>';
            }
            $html .= '</div>';
        }
        $html .= '<div class="fmp-content-wrap">';
        $html .= "<div class='fmp-title'>";
        if (in_array('title', $items)) {
            if (!$link) {
                $html .= '<h3>' . get_the_title() . '</h3>';
            } else {
                $html .= '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
            }
        }
        if (in_array('price', $items)) {
            $html .= '<span class="price">' . TLPFoodMenu()->getPriceWithLabel() . '</span>';
        }
        $html .= '</div>';
        if (in_array('excerpt', $items)) {
            $html .= '<p>' . $excerpt . '</p>';
        }
        $html .= '</div>';
        echo $html;
        ?>
    </div>
</div>