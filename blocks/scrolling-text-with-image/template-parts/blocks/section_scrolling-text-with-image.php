<?php

/**

 * Scrolling Text with Image Block Template.

 * @param   array $block The block settings and attributes.

 * @param   string $content The block inner HTML (empty).

 * @param   bool $is_preview True during AJAX preview.

 * @param   (int|string) $post_id The post ID this block is saved to.

 */



$className = '';

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



$title_tag = get_field('title_tag');

$title_fontsize = get_field('title_fontsize');

$animation_counter=100;



?>



<div  <?php echo $anchor; ?> class="section_scroll_text_box wp-block-acf <?php echo esc_attr($className); ?> fadeInUp delay-<?php echo $animation_counter; ?>ms" <?php echo $bg_style; ?>>
	

	<?php if( have_rows('content_boxes') ){ 
		
		$total_rows = count( get_field('content_boxes') );?>
		<div class="content_boxes animated fadeInUp delay-<?php echo $animation_counter; ?>ms">

		

			<?php  
			$side_images_html="";
			while( have_rows('content_boxes') ) : the_row();
				$row_num=get_row_index();
				$is_last = ( $row_num === $total_rows );
				$is_first = ( $row_num === 1 );
				

				$title = get_sub_field('title');
				$title = str_replace('|', '<br>', $title);

				$content = get_sub_field('content');

				$side_image = get_sub_field('side_image');
				if(!empty($side_image)){
					$side_images_html .= '<span class="box_img' . ($is_first ? ' active first' : '') . '" data-image-id="num-' . $row_num . '">' . wp_get_attachment_image($side_image, 'full') . '</span>';
				}

				

				?>

				<div data-content-id="num-<?php echo $row_num;?>" class="content_item  <?php echo $is_first ? ' active first' : ''; ?> <?php echo $is_last ? ' last' : ''; ?>  ">
					<div class="content_box">
				
						<?php if (!empty($title)) {?>

							<<?php echo $title_tag?> class="content_title fontsize-<?php echo $title_fontsize;?>">
								<?php echo $title;?>
							</<?php echo $title_tag?>>

						<?php } ?>

						<?php if (!empty($content)) {?>
							<div class="description">
							<?php echo $content;?>
							</div>
							
						<?php  } ?>

					</div>

					<?php if(!empty($side_image)){?>
						<div class="side_image ">
							<?php echo wp_get_attachment_image($side_image, 'full'); ?>
						</div>
					<?php }?>
			
				</div>

				            

			<?php endwhile; ?>
			 <?php $animation_counter=$animation_counter+100;  ?>
		
		</div> 
		
		<div class="side_image_col animated fadeInLeft delay-<?php echo $animation_counter; ?>ms">
			<?php echo $side_images_html;?>
		</div>
	

	<?php  } ?>
			
</div>

