<h3>Feed Settings</h3>
<p>
  Define where and how posts are retrieved. Cache duration 
  specifies how often Tumblr is checked for new content.
</p>
<p>
  <label>
    <?php _e( 'Tumblr URL:', $this->get_widget_slug() ); ?>
    <input class="widefat" 
           id="<?php echo $this->get_field_id('tumblr'); ?>"
           name="<?php echo $this->get_field_name('tumblr'); ?>"
           type="text"
           value="<?php echo esc_attr( $local_params['tumblr'] ); ?>">
  </label>
</p>
<p>
  <label>
    <?php _e( 'Post Types:', $this->get_widget_slug() ); ?>
    <select class="widefat"
            id="<?php echo $this->get_field_id('post_type'); ?>"
            name="<?php echo $this->get_field_name('post_type'); ?>">
      <?php
      foreach( $this->allowed_post_types as $type_value => $type_text ) {
        echo '<option value="' . $type_value . '" ';
        selected( $local_params['post_type'], $type_value );
        echo '>' . $type_text . '</option>';
      }
      ?>
    </select>
  </label>
</p>
<p>
  <label>
    <?php _e( 'Tags (leave blank for all posts):', $this->get_widget_slug() ); ?>
    <input class="widefat" 
           id="<?php echo $this->get_field_id('post_tag'); ?>"
           name="<?php echo $this->get_field_name('post_tag'); ?>"
           type="text"
           value="<?php echo esc_attr( $local_params['post_tag'] ); ?>">
  </label>
</p>
<p>
  <label>
    <?php _e( 'Cache Duration:', $this->get_widget_slug() ); ?>
    <select class="widefat"
            id="<?php echo $this->get_field_id('cache_period'); ?>"
            name="<?php echo $this->get_field_name('cache_period'); ?>">
    <?php
    echo '<option value="1" ';
    selected( $local_params['cache_period'], 1 );
    echo '>' . __( '1 minute', $this->get_widget_slug() ) . '</option>';
    for( $index = 5; $index <= 60; $index += 5 ) {
      echo '<option value="' . $index . '" ';
      selected( $local_params['cache_period'], $index );
      echo '>' . $index . __( ' minutes', $this->get_widget_slug() ) . '</option>';
      if ( $index > 25 ) {
        $index += 10;
      }
    }
    ?>
    </select>
  </label>
</p>
<h3>Display Settings</h3>
<p>
  Define how Tumblr posts are displayed. Media refers to image content in 
  Photo and Video posts.
</p>
<p>
  <label>
    <?php _e( 'Widget Title:', $this->get_widget_slug() ); ?>
    <input class="widefat" 
           id="<?php echo $this->get_field_id('title'); ?>"
           name="<?php echo $this->get_field_name('title'); ?>"
           type="text"
           value="<?php echo esc_attr( $local_params['title'] ); ?>">
  </label>
</p>
<p>
  <label>
    <?php _e( 'Display Type:', $this->get_widget_slug() ); ?>
    <select class="widefat"
            id="<?php echo $this->get_field_id('display_type'); ?>"
            name="<?php echo $this->get_field_name('display_type'); ?>"
            onchange="f2_tumblr_switch_visibility('<?php echo $this->get_field_id('slide_speed'); ?>', this.value, 'slide');">
      <?php
      foreach( $this->allowed_display_types as $type_value => $type_text ) {
        echo '<option value="' . $type_value . '" ';
        selected( $local_params['display_type'], $type_value );
        echo '>' . $type_text . '</option>';
      }
      ?>
    </select>
  </label>
</p>
<p>
  <label>
    <?php _e( 'Slideshow Speed (seconds):', $this->get_widget_slug() ); ?>
    <input class="widefat" 
           id="<?php echo $this->get_field_id('slide_speed'); ?>"
           name="<?php echo $this->get_field_name('slide_speed'); ?>"
           type="text"
           value="<?php echo esc_attr( $local_params['slide_speed'] ); ?>"
           <?php 
           if ( 'slide' != $local_params['display_type']) { 
             echo 'disabled="disabled" ';
           }
           ?>>
  </label>
</p>
<p>
  <label>
    <?php _e( 'Number Of Posts:', $this->get_widget_slug() ); ?>
    <select class="widefat"
            id="<?php echo $this->get_field_id('posts'); ?>"
            name="<?php echo $this->get_field_name('posts'); ?>">
      <?php
      for( $index = 1; $index <= 20; $index++ ) {
        echo '<option value="' . $index . '" ';
        selected( $local_params['posts'], $index );
        echo '>' . $index . '</option>';
      }
      ?>
    </select>
  </label>
</p>
<p>
  <label>
    <?php _e( 'Maximum Media Width:', $this->get_widget_slug() ); ?>
    <select class="widefat"
            id="<?php echo $this->get_field_id('media_width'); ?>"
            name="<?php echo $this->get_field_name('media_width'); ?>">
      <?php
      foreach( $this->allowed_media_widths as $type_value => $type_text ) {
        echo '<option value="' . $type_value . '" ';
        selected( $local_params['media_width'], $type_value );
        echo '>' . $type_text . '</option>';
      }
      ?>
    </select>
  </label>
  <label>
    <?php _e( 'Apply to Audio Posts:', $this->get_widget_slug() ); ?>
    <input type="checkbox" 
           id="<?php echo $this->get_field_id('audio_width'); ?>"
           name="<?php echo $this->get_field_name('audio_width'); ?>"
           value="1"
           <?php checked( $local_params['audio_width'], 1 ); ?>>
  </label>
</p>
<p>
  <label>
    <?php _e( 'Media Alignment:', $this->get_widget_slug() ); ?>
    <select class="widefat"
            id="<?php echo $this->get_field_id('media_align'); ?>"
            name="<?php echo $this->get_field_name('media_align'); ?>">
      <?php
      foreach( $this->allowed_media_alignments as $type_value => $type_text ) {
        echo '<option value="' . $type_value . '" ';
        selected( $local_params['media_align'], $type_value );
        echo '>' . $type_text . '</option>';
      }
      ?>
    </select>
  </label>
</p>
<p>
  <label>
    <?php _e( 'Content Type:', $this->get_widget_slug() ); ?>
    <select class="widefat"
            id="<?php echo $this->get_field_id('content_type'); ?>"
            name="<?php echo $this->get_field_name('content_type'); ?>"
            onchange="f2_tumblr_switch_visibility('<?php echo $this->get_field_id('excerpt_size'); ?>', this.value, 'excerpt');">
      <?php
      foreach( $this->allowed_content_types as $type_value => $type_text ) {
        echo '<option value="' . $type_value . '" ';
        selected( $local_params['content_type'], $type_value );
        echo '>' . $type_text . '</option>';
      }
      ?>
    </select>
  </label>
</p>
<p>
  <label>
    <?php _e( 'Maximum Excerpt Size (words):', $this->get_widget_slug() ); ?>
    <input class="widefat" 
           id="<?php echo $this->get_field_id('excerpt_size'); ?>"
           name="<?php echo $this->get_field_name('excerpt_size'); ?>"
           type="text"
           value="<?php echo esc_attr( $local_params['excerpt_size'] ); ?>"
           <?php 
           if ( 'excerpt' != $local_params['content_type']) { 
             echo 'disabled="disabled" ';
           }
           ?>>
    </select>
  </label>
</p>
<p>
  <label>
    <?php _E( 'Replace "special" characters:', $this->get_widget_slug() ); ?>
    <input type="checkbox"
           id="<?php echo $this->get_field_id('clean_quotes'); ?>"
           name="<?php echo $this->get_field_name('clean_quotes'); ?>"
           value="1"
           <?php checked( $local_params['clean_quotes'], 1 ); ?>>
  </label>
</p>
<h3>Style Settings</h3>
<p>
  Control formatting without the need to update stylesheets.
  Any field left blank will default to your theme style.
</p>
<p>
  <label>
    <?php _e( 'Title text size:', $this->get_widget_slug() ); ?>
    <br>
    <?php
    // Extract any existing data
    if ( !preg_match(
      '/(?P<length>[0-9.]+)(?P<unit>[a-z%]+)/', 
      $local_params['title_size'], 
      $fields
    ) ) {
      // If it's unparseable, assume some (empty) defaults
      $fields['length'] = '';
      $fields['unit'] = 'px';
    }
    ?>
    <input id="<?php echo $this->get_field_id('title_size'); ?>"
           name="<?php echo $this->get_field_name('title_size'); ?>"
           type="text"
           value="<?php echo esc_attr( $fields['length'] ); ?>">
    <select id="<?php echo $this->get_field_id('title_size_units'); ?>"
            name="<?php echo $this->get_field_name('title_size_units'); ?>">
    <?php
    foreach( $this->allowed_css_units as $css_unit ) {
      echo '<option value="' . $css_unit . '" ';
      selected( $fields['unit'], $css_unit );
      echo '>' . $css_unit . '</option>';
    }
    ?>
    </select>    
  </label>
</p>
<p>
  <label>
    <?php _e( 'Excerpt text size:', $this->get_widget_slug() ); ?>
    <br>
    <?php
      // Extract any existing data
      if ( !preg_match(
        '/(?P<length>[0-9.]+)(?P<unit>[a-z%]+)/', 
        $local_params['text_size'], 
        $fields
      ) ) {
        // If it's unparseable, assume some (empty) defaults
        $fields['length'] = '';
        $fields['unit'] = 'px';
      }
    ?>
    <input id="<?php echo $this->get_field_id('text_size'); ?>"
           name="<?php echo $this->get_field_name('text_size'); ?>"
           type="text"
           value="<?php echo esc_attr( $fields['length'] ); ?>">
    <select id="<?php echo $this->get_field_id('text_size_units'); ?>"
            name="<?php echo $this->get_field_name('text_size_units'); ?>">
    <?php
    foreach( $this->allowed_css_units as $css_unit ) {
      echo '<option value="' . $css_unit . '" ';
      selected( $fields['unit'], $css_unit );
      echo '>' . $css_unit . '</option>';
    }
    ?>
    </select>    
  </label>
</p>
<p>
  <label>
    <?php _e( 'Excerpt line spacing:', $this->get_widget_slug() ); ?>
    <br>
    <?php
    // Extract any existing data
    if ( !preg_match(
      '/(?P<length>[0-9.]+)(?P<unit>[a-z%]+)/', 
      $local_params['line_spacing'], 
      $fields
    ) ) {
      // If it's unparseable, assume some (empty) defaults
      $fields['length'] = '';
      $fields['unit'] = 'px';
    }
    ?>
    <input id="<?php echo $this->get_field_id('line_spacing'); ?>"
           name="<?php echo $this->get_field_name('line_spacing'); ?>"
           type="text"
           value="<?php echo esc_attr( $fields['length'] ); ?>">
    <select id="<?php echo $this->get_field_id('line_spacing_units'); ?>"
            name="<?php echo $this->get_field_name('line_spacing_units'); ?>">
    <?php
    foreach( $this->allowed_css_units as $css_unit ) {
      echo '<option value="' . $css_unit . '" ';
      selected( $fields['unit'], $css_unit );
      echo '>' . $css_unit . '</option>';
    }
    ?>
    </select>    
  </label>
</p>
<p>
  <label>
    <?php _e( 'Image left/right padding:', $this->get_widget_slug() ); ?>
    <br>
    <?php
    // Extract any existing data
    if ( !preg_match(
      '/(?P<length>[0-9.]+)(?P<unit>[a-z%]+)/', 
      $local_params['media_padding'], 
      $fields
    ) ) {
      // If it's unparseable, assume some (empty) defaults
      $fields['length'] = '';
      $fields['unit'] = 'px';
    }
    ?>
    <input id="<?php echo $this->get_field_id('media_padding'); ?>"
           name="<?php echo $this->get_field_name('media_padding'); ?>"
           type="text"
           value="<?php echo esc_attr( $fields['length'] ); ?>">
    <select id="<?php echo $this->get_field_id('media_padding_units'); ?>"
            name="<?php echo $this->get_field_name('media_padding_units'); ?>">
    <?php
    foreach( $this->allowed_css_units as $css_unit ) {
      echo '<option value="' . $css_unit . '" ';
      selected( $fields['unit'], $css_unit );
      echo '>' . $css_unit . '</option>';
    }
    ?>
    </select>    
  </label>
</p>
