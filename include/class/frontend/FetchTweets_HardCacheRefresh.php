<?php
class FetchTweets_HardCacheRefresh {    
    
    /**
     * @since            0.0.1
     */
    static public function clearCaches( $aPrefixes=array( 'FTWS', 'FTWSFeedMs' ) ) {
        
        $aPrefixes = $aPrefixes ? $aPrefixes : array( 'FTWS', 'FTWSFeedMs' );    // a callback gives an empty parameter value regardless of the defined default parameter value.
        foreach( $aPrefixes as $sPrefix ) {
            $GLOBALS['wpdb']->query( "DELETE FROM `" . $GLOBALS['table_prefix'] . "options` WHERE `option_name` LIKE ( '_transient_%{$sPrefix}%' )" );
            $GLOBALS['wpdb']->query( "DELETE FROM `" . $GLOBALS['table_prefix'] . "options` WHERE `option_name` LIKE ( '_transient_timeout_%{$sPrefix}%' )" );
        }
            
    }        
    
    /**
     * Schedule the single event.
     * @since            0.0.2
     */
    static public function clearCachesSingleEvent() {

        wp_schedule_single_event( time() + 120, 'fetch_tweets_action_transient_hard_refresh_single' );
        
        self::clearCaches();
        
    }
    
    /**
     * @since            0.0.1
     */
    static public function notifyOwnerByEmail( $sNotification='', $sEmailAddress='' ) {
        
        if ( ! $sEmailAddress ) {
            return;
        }
        wp_mail( 
            $sEmailAddress,     // email address
            __( 'Fetch Tweets Hard Cache Refresh Notification', 'fetch-tweets-hard-cache-refresh' ),     // subject
            __( 'The caches of Fetch Tweets have been cleared: ', 'fetch-tweets-hard-cache-refresh' )    // message
            . date( "Y/m/d H:i:s", current_time( 'timestamp' ) ) . ' '
            . site_url() . PHP_EOL
            . $sNotification
        );         
        
    }
    
    /**
     * @since            0.0.2
     */
    public static function addEvent() {
        wp_schedule_event( time(), 'hourly', 'fetch_tweets_action_transient_hard_refresh_hourly' );
        wp_schedule_single_event( time(), 'fetch_tweets_action_transient_hard_refresh_single' );

    } // end activate

    /**
     * @since            0.0.2
     */
    public static function removeEvent() {
        wp_clear_scheduled_hook( 'fetch_tweets_action_transient_hard_refresh_hourly' );
    } // end activate
    
}