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
        // A nice easy one, this!
        $post_title = strip_tags( (string)$the_post->{'regular-title'} );

        // Only do any more if content is required
        if ( 'excerpt' == $local_params['content_type'] ) {
            $post_body = $this->trim_words(
                (string)$the_post->{'regular-body'},
                $local_params['excerpt_size'],
                '&hellip; <a href="' . $the_post['url'] . '">[more]</a>'
            );
        } else if ( 'full' == $local_params['content_type'] ) {
            $post_body = strip_tags( (string)$the_post->{'regular-body'}, '<p>' );
        }
        break;
    case 'link':            // Anotated link URL
        // The link text will make a good title
        $post_title = strip_tags( (string)$the_post->{'link-text'} );

        // Only do any more if content is required
        if ( 'excerpt' == $local_params['content_type'] ) {
            $post_body = $this->trim_words(
                (string)$the_post->{'link-description'},
                $local_params['excerpt_size'],
                '&hellip; <a href="' . $the_post['url'] . '">[more]</a>'
            );
        } else if ( 'full' == $local_params['content_type'] ) {
            $post_body = strip_tags( (string)$the_post->{'link-description'}, '<p>' );
        }
        break;
    case 'quote':           // Quoted text
        // Use the (first sentence of) attributed source as a title
        $title_split = preg_split( 
            '/[.?!:]/', 
            strip_tags(
                preg_replace(
                    array( '/<a.*?<\/a>/', '/<p>:<\/p>/' ),
                    '',
                    (string)$the_post->{'quote-source'}
                )
            ), 
            2
        );
        $post_title = $title_split[0];

        // And the quote itself as the body - only do any more is required
        if ( 'excerpt' == $local_params['content_type'] ) {
            $post_body = $this->trim_words(
                (string)$the_post->{'quote-text'},
                $local_params['excerpt_size'],
                '&hellip; <a href="' . $the_post['url'] . '">[more]</a>'
            );
        } else if ( 'full' == $local_params['content_type'] ) {
            $post_body = strip_tags( (string)$the_post->{'quote-text'}, '<p>' );
        }
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
        } else {
            // No title found, so pluck out the first sentence instead
            $title_split = preg_split( 
                '/[.?!:]/', 
                strip_tags(
                    preg_replace(
                        array( '/<a.*?<\/a>/', '/<p>:<\/p>/' ),
                        '',
                        (string)$the_post->{'photo-caption'}
                    )
                ), 
                2
            );
            $post_title = $title_split[0];
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
        // The title is easy!
        $post_title = (string)$the_post->{'converstion-title'};

        // And as much of the body as we require
        if ( 'excerpt' == $local_params['content_type'] ) {
            $post_body = $this->trim_words(
                (string)$the_post->{'conversation-text'},
                $local_params['excerpt_size'],
                '&hellip; <a href="' . $the_post['url'] . '">[more]</a>'
            );
        } else if ( 'full' == $local_params['content_type'] ) {
            $post_body = strip_tags( (string)$the_post->{'conversation-text'}, '<p>' );
        }
        break;
    case 'video':           // Video
        // Try to finda caption
        $title_split = preg_split(
            '/[.?!:]/',
            strip_tags(
                preg_replace(
                    array( '/<a.*?<\/a>/', '/<p>:<\/p>/' ),
                    '',
                    (string)$the_post->{'video-caption'}
                )
            ),
            2
        );
        $post_title = $title_split[0];

        // Only do any more if content is required
        if ( 'none' != $local_params['content_type'] ) {
            // Derive an appropriately sized version of the media
            $media_url = '';
            $media_width = 0;
            foreach( $the_post->{'video-player'} as $the_video ) {
                if ( ( $the_video['max-width'] <= $local_params['media_width'] )
                  && ( $the_video['max-width'] > $media_width ) ) {
                    $media_url = (string)$the_video;
                    $media_width = $the_video['max-width'];
                }
            }
            if ( $media_width > 0 ) {
                $post_media = $media_url;
            }

            // And as much of the body as we require
            if ( 'excerpt' == $local_params['content_type'] ) {
                $post_body = $this->trim_words(
                    (string)$the_post->{'video-caption'},
                    $local_params['excerpt_size'],
                    '&hellip; <a href="' . $the_post['url'] . '">[more]</a>'
                );
            } else {
                $post_body = strip_tags( (string)$the_post->{'video-caption'}, '<p>' );
            }
        }
        break;
    case 'audio':           // Audio
        // Extract the title from the caption
        $dom = new DOMDocument();
        $dom->loadHTML( (string)$the_post->{'audio-caption'} );
        $xpath = new DOMXpath( $dom );
        $xres = $xpath->query( '//*[name()="h1" or name()="h2" or name()="h3"]' );
        if ( $xres->length > 0 ) {
            // Save the title
            $post_title = $xres->item(0)->nodeValue;

            // And remove it from the DOM document
            $xres->item(0)->parentNode->removeChild($xres->item(0));
        } else {
            // No title found, so pluck out the first sentence instead
            $title_split = preg_split( 
                '/[.?!:]/', 
                strip_tags(
                    preg_replace(
                        array( '/<a.*?<\/a>/', '/<p>:<\/p>/' ),
                        '',
                        (string)$the_post->{'audio-caption'}
                    )
                ), 
                2
            );
            $post_title = $title_split[0];
        }
        
        // And now the content, if required
        if ( 'none' != $local_params['content_type'] ) {
            $post_media = (string)$the_post->{'audio-player'};

            // And as much of the body as we require
            if ( 'excerpt' == $local_params['content_type'] ) {
                $post_body = $this->trim_words(
                    (string)$the_post->{'audio-caption'},
                    $local_params['excerpt_size'],
                    '&hellip; <a href="' . $the_post['url'] . '">[more]</a>'
                );
            } else {
                $post_body = strip_tags( (string)$the_post->{'audio-caption'}, '<p>' );
            }
        }
        break;
    case 'answer':          // Question and answer
        // The question can be the title, and the answer the body!
        $post_title = strip_tags( (string)$the_post->{'question'} );
        if ( 'excerpt' == $local_params['content_type'] ) {
            $post_body = $this->trim_words(
                (string)$the_post->{'answer'},
                $local_params['excerpt_size'],
                '&hellip; <a href="' . $the_post['url'] . '">[more]</a>'
            );
        } else if ( 'full' == $local_params['content_type'] ) {
            $post_body = strip_tags( (string)$the_post->{'answer'}, '<p>' );
        }
        break;
    }

    // Post title; if we haven't managed to find one, default to the slug
    if ( empty( $post_title ) ) {
        $post_title = ucwords( str_replace( '-', ' ', $the_post['slug'] ) );
    }

    // No slug? Err, crap! 
    if ( empty( $post_title ) ) {
        $post_title = (string)$the_post['type'];
    }

    // And we're ready!
    echo '<a href="' . esc_url( $the_post['url'] ) . '"><h3>' 
       . esc_html( $post_title ) . '</h3></a>';

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
