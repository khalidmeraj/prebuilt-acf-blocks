<?php

/**

 * Accordion List Block Template.

 * @param   array $block The block settings and attributes.

 * @param   string $content The block inner HTML (empty).

 * @param   bool $is_preview True during AJAX preview.

 * @param   (int|string) $post_id The post ID this block is saved to.

 */



$className = 'global-block-ptb';

if (!empty($block['className'])) {

	$className .= ' ' . $block['className'];

}

if ( ! empty($block['align']) ) {

  $className .= ' align' . $block['align'];

}

if ( ! empty($block['backgroundColor']) ) {

  $className .= ' bgcolor-' . $block['backgroundColor'];

   $className .= ' has_bg_color';

}

if ( ! empty($block['gradient']) ) {

  $className .= ' bgcolor-' . $block['gradient'];

   $className .= ' has_bg_gradient';

}





if ( ! empty($block['fontSize']) ) {

  $className .= ' fontsize-' . $block['fontSize'];

}

if ( ! empty($block['textColor']) ) {

  $className .= ' textcolor-' . $block['textColor'];

}

if ( ! empty($block['align_text']) ) {

  $className .= ' text-align-' . $block['align_text'];

}



           

$anchor = !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';

// Gutenberg background color and Image support

  $bg_color = $block['style']['color']['background'] ?? '';

  $bg_image = get_field('background_image');

  $bg_url = $bg_image ? wp_get_attachment_image_url($bg_image, 'full') : '';

  $bg_style = '';

  if ($bg_color) {

    $bg_style .= 'background-color:' . esc_attr($bg_color) . ';';

    $className .= ' has_bg_color';

  }

  if ($bg_url) {

    $bg_style .= 'background-image: url(' . esc_url($bg_url) . ');';

    $className .= ' has_bg';

  }

  $bg_style = $bg_style ? 'style="' . $bg_style . '"' : '';

// End : Gutenberg background color and Image support



$section_title = get_field('section_title');

$section_title_tag = get_field('section_title_tag');

$section_title_fontsize = get_field('section_title_fontsize');

$section_description = get_field('section_description');

$section_description_fontsize = get_field('section_description_fontsize');

$inner_container_width = get_field('inner_container_width');

$animation_counter=100;

?>



<div  <?php echo $anchor; ?> class="section_accordion wp-block-acf <?php echo esc_attr($className); ?>" <?php echo $bg_style; ?>>

    <div class="container <?php echo $inner_container_width;?>">
      <div  class="accordion_inner">
        <?php if (!(empty($section_title) && empty($section_description))) {?>
          <div class="section_head">

            <?php if (!empty($section_title)) {?>

            <<?php echo $section_title_tag?> class="section_title fontsize-<?php echo $section_title_fontsize;?> animated fadeInUp delay-<?php echo $animation_counter; ?>ms">

                <?php echo wp_kses_post($section_title);?>

            </<?php echo $section_title_tag?>>

            <?php $animation_counter=$animation_counter+100;  ?>

            <?php  } ?>

                

            <?php if (!empty($section_description)) {?>

                <div class="section_description fontsize-<?php echo $section_description_fontsize;?>  animated fadeInUp delay-<?php echo $animation_counter; ?>ms"><?php echo $section_description;?></div>

                <?php $animation_counter=$animation_counter+100;  ?>

            <?php  } ?> 
          </div>
        <?php  } ?>
      
        <?php if( have_rows('accordion') ){    $block_id = $block['id'] ?? uniqid( 'faq_' );?>
          <div class="faq_accordion animated fadeInUp delay-<?php echo $animation_counter; ?>ms">
            <?php while (have_rows('accordion')) : the_row();
              
                $row_num=get_row_index();
                $row_num_padded = sprintf('%02d', $row_num);
                $unique_id = $block_id . '-' . get_row_index();
                $question = get_sub_field('question');
                $answer_content = get_sub_field('answer_content');
              ?>
              <?php if (!empty($question)) {?>
              <div class="faq_accordion_item">
                <h3 class="accordion_title font-family-primary textcolor-black fontsize-small-20 ftw-medium">
                    <button type="button"
                            class="accordion_title_head"
                            id="faq-trigger-<?php echo esc_attr( $unique_id ); ?>"
                            aria-expanded="false"
                            aria-controls="faq-panel-<?php echo esc_attr( $unique_id ); ?>">
                            <span class="row_count"> <?php echo $row_num_padded; ?></span>
                        <?php echo esc_html( $question ); ?>
                    </button>
                </h3>

                <div class="accordion_content textcolor-black"
                    id="faq-panel-<?php echo esc_attr( $unique_id ); ?>"
                    role="region"
                    aria-labelledby="faq-trigger-<?php echo esc_attr( $unique_id ); ?>"
                    hidden>
                    <div class="answer_item">
                        <div class="answer_content">
                            <?php echo $answer_content; ?>
                        </div>
                    </div>
                </div>

              </div>
              <?php  } ?>
              
            <?php endwhile; ?>
          </div>
        <?php  } ?>
      </div> 
  </div>  
</div>