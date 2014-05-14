function f2_tumblr_switch_visibility( field, entered, target ) {
    if ( target == entered ) {
        jQuery('#'+field).prop( 'disabled', false );
    } else {
        jQuery('#'+field).prop( 'disabled', true );
    }
}
