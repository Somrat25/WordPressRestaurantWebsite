<?php
extract($arg);
$gridQuery = new WP_Query($args);
$html = "<h2 class='fmp-category-title'>{$term->name}</h2>";
while ($gridQuery->have_posts()) : $gridQuery->the_post();
    $html .= "<div class='{$grid} {$class}'>";
    $html .= "<div class='fmp-food-item'>";
    if (in_array('image', $items)) {
        $html .= '<div class="fmp-image-wrap">';
        $image = TLPFoodMenu()->getFeatureImage(get_the_ID(), $imgSize);
        if (!$link) {
            $html .= $image;
        } else {
            $html .= '<a href="' . get_permalink() . '" title="' . get_the_title() . '">' . $image . '</a>';
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
    $gTotal = TLPFoodMenu()->getPriceWithLabel();
    if (in_array('price', $items)) {
        $html .= '<span class="price">' . $gTotal . '</span>';
    }
    $html .= "</div>";
    if (in_array('excerpt', $items)) {
        $html .= '<p>' . TLPFoodMenu()->strip_tags_content(get_the_excerpt(), $excerpt_limit) . '</p>';
    }
    $html .= '</div>';
    $html .= '</div>';
    $html .= "</div>";
endwhile;
wp_reset_postdata();
echo $html;