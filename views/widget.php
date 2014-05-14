<?php
// Parse the Tumblr feed
try {
    $tumblr_xml = new SimpleXMLElement( $tumblr_data );
} catch( Exception $e ) {
    return;
}

// If we're in a slideshow, enclose everything in a div
if ( 'slide' == $local_params['display_type'] ) {
    echo '<div class="f2-tumblr-slideshow" data-speed="'
       . $local_params['slide_speed'] . '">';
}

// And work through each element, rendering it appropriately
foreach( $tumblr_xml->posts->post as $the_post ) {
    // Wrap it in a div.
    echo '<div class="f2-tumblr-post">';

    // Forget the last thing we rendered, obviously... :)
    $post_title = '';
    $post_media = '';
    $post_body = '';

    // The exact processing depends on the post type
    switch( (string)$the_post['type'] ) {
    case 'regular':         // Plain text
echo "<p>regular</p>";
        break;
    case 'link':            // Anotated link URL
echo "<p>link</p>";
        break;
    case 'quote':           // Quoted text
echo "<p>quote</p>";
        break;
    case 'photo':           // Photograph
        // Try and extract some sort of sensible title from the caption
        $dom = new DOMDocument();
        $dom->loadHTML( (string)$the_post->{'photo-caption'} );
        $xpath = new DOMXpath( $dom );
        $xres = $xpath->query( '//*[name()="h1" or name()="h2" or name()="h3"]' );
        if ( $xres->length > 0 ) {
            // Save the title
            $post_title = $xres->item(0)->nodeValue;

            // And remove it from the DOM document
            $xres->item(0)->parentNode->removeChild($xres->item(0));
        }

        // Only do any more if content is required
        if ( 'none' != $local_params['content_type'] ) {
            // Derive an appropriately sized version of the media
            $media_url = '';
            $media_width = 0;
            foreach( $the_post->{'photo-url'} as $the_photo ) {
                if ( ( $the_photo['max-width'] <= $local_params['media_width'] )
                  && ( $the_photo['max-width'] > $media_width ) ) {
                    $media_url = (string)$the_photo;
                    $media_width = $the_photo['max-width'];
                }
            }
            if ( $media_width > 0 ) {
                $post_media = '<img class="' . $local_params['media_align']
                            . '" src="' . $media_url 
                            . '" alt="'. $post_title . '">';
            }

            // And as much of the body as we require
            if ( 'excerpt' == $local_params['content_type'] ) {
                $post_body = $this->trim_words(
                    $dom->saveHTML(),
                    $local_params['excerpt_size'],
                    '&hellip; <a href="' . $the_post['url'] . '">[more]</a>'
                );
            } else {
                $post_body = strip_tags( $dom->saveHTML(), '<p>' );
            }
        }
        break;
    case 'conversation':    // Chat
echo "<p>chat</p>";
        break;
    case 'video':           // Video
echo "<p>video</p>";
        break;
    case 'audio':           // Audio
echo "<p>audio</p>";
        break;
    case 'answer':          // Question and answer
echo "<p>answer</p>";
        break;
    }

    // Post title; if we haven't managed to find one, default to the slug
    if ( empty( $post_title ) ) {
        $post_title = ucwords( str_replace( '-', ' ', $the_post['slug'] ) );
    }

    // And we're ready!
    echo '<a href="' . $the_post['url'] . '"><h3>' . $post_title . '</h3></a>';

    // If we have any media, that next.
    echo '<div class="f2-tumblr-media ' . $local_params['media_align'] 
       . '">' . $post_media . '</div>';

    // And then the body - any trimming will have been done already here
    echo $post_body;

    // And close the div
    echo '</div>';
}

// Close any slideshow container
if ( 'slide' == $local_params['display_type'] ) {
    echo '</div>';
}
?>
