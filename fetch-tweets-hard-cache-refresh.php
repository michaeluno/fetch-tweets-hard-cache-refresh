<?php
/* 
 * Plugin Name:     Fetch Tweets - Hard Cache Refresh
 * Plugin URI:      http://en.michaeluno.jp/fetch-tweets
 * Description:     Performs hard-refresh against the Fetch Tweets caches.
 * Author:          miunosoft (Michael Uno)
 * Author URI:      http://michaeluno.jp
 * Version:         1.0.0
 */


if ( ! defined( 'ABSPATH' ) ) {
    return;
}

function FTHCR_Bootstrap() {

    if ( is_admin() ) {
        if ( ! class_exists( 'FetchTweets_AdminPageFramework' ) ) {
            return;
        }
        include( dirname( __FILE__ ) . '/include/class/backend/FTHCR_Admin.php' );
        new FTHCR_Admin;
    }

    include( dirname( __FILE__ ) . '/include/class/frontend/FetchTweets_HardCacheRefresh.php' );
    include( dirname( __FILE__ ) . '/include/class/frontend/FetchTweets_HardCacheRefresh_Timer.php' );
    new FetchTweets_HardCacheRefresh_Timer;    
    
}
add_action( 'fetch_tweets_action_after_loading_plugin', 'FTHCR_Bootstrap' );
