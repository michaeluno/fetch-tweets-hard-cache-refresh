<?php
class FetchTweets_HardCacheRefresh_Timer {    
    
    static $aStructure_Options = array(
        'interval'              => 900,
        'notification_email'    => '',
        '_last_timestamp'       => 0,
        '_do_refresh'           => false,
    );
    
    public function __construct() {

        $this->aOptions = $this->getOptions();

        // If called in the background, do refresh and exit.
        if ( $this->aOptions[ '_do_refresh' ] ) {
            if ( did_action( 'plugins_loaded' ) ) {
                $this->replyToDoRefresh();
            } else {
                add_action( 'plugins_loaded', array( $this, 'replyToDoRefresh' ) );    // wp_mail function needs to be loaded
            }
            return;
        }
        
        // If it's not expired, do nothing.
        if ( ! $this->_isExpired( $this->aOptions[ 'interval' ], $this->aOptions[ '_last_timestamp' ] ) ) { 
            return;
        }

        // Okay, it's expired. Set the option flag and load the site in the background and keep continuing the page load.
        $this->aOptions[ '_do_refresh' ] = true;
        update_option( 'FTHCR_Admin', $this->aOptions );    
        wp_remote_get( 
            site_url(), 
            array( 
                'timeout'   => 0.01, 
                'sslverify' => false, 
            ) 
        );    // this forces the task to be performed right away in the background.
        
    }
        protected function getOptions() {
            
            $aOptions = get_option( 'FTHCR_Admin', array() );
            return is_array( $aOptions )
                ? $aOptions + self::$aStructure_Options
                : self::$aStructure_Options;
                
        }
    
        private function _isExpired( $iInterval, $iLastTimeStamp ) {
            return ( ( int ) $iInterval + ( int ) $iLastTimeStamp ) < time();
        }
        
    /**
     * Called in the background.
     */
    public function replyToDoRefresh() {
             
        $this->aOptions[ '_do_refresh' ] = false;
        $this->aOptions[ '_last_timestamp' ] = time() + 5;    // add 5 seconds to prevent duplicated tasks when accessed too fast.
        $this->_refresh( $this->aOptions[ 'notification_email' ] );
        update_option( 'FTHCR_Admin', $this->aOptions );                            
        exit;        
                
    }
    
        private function _refresh( $sEmailAddress ) {
                        
            // There are cases that the background page load occurs before the option gets save. For that, set a flag transient to lock the process.
            if ( get_transient( 'FTWS_HARDCACHEREFRESH' ) !== false ) {
                return;
            }
            FetchTweets_HardCacheRefresh::clearCaches();
            set_transient( 'FTWS_HARDCACHEREFRESH', 'LCOKED', 5 );    // since the transient key prefix is FTWS_ it will be also deleted when hard refresh is performed.

            FetchTweets_HardCacheRefresh::notifyOwnerByEmail( 
                sprintf( 
                    __( 'The caches were cleared. The next refresh task will be performed at %1$s after someone visits the site.', 'fetch-tweets-hard-cache-refresh' ), 
                    date( "Y/m/d H:i:s", current_time( 'timestamp' ) + ( int ) $this->aOptions['interval'] ) 
                ),
                $sEmailAddress
            );

        }    
}