<?php
class FTHCR_Admin extends FetchTweets_AdminPageFramework {
    
    public function setUp() {
        
        $this->setRootMenuPageBySlug( 'edit.php?post_type=fetch_tweets' );
        $this->addSubMenuItems(
            array(
                'page_slug'     => 'hard_cache_refresh',
                'title'         => __( 'Hard Cache Refresh', 'fetch-tweets-hard-cache-refresh' ),
                'order'         => 1000,
            )
        );
                
    }
    
    public function load_hard_cache_refresh( $oAdminPage ) {
         
        $this->addSettingFields(
            array(    // Single text field
                'field_id'      => 'interval',
                'title'         => __( 'Interval', 'fetch-tweets-hard-cache-refresh' ),
                'description'   => __( 'Set the interval in seconds', 'fetch-tweets-hard-cache-refresh' ),
                'type'          => 'number',
                'default'       => 900,    // 15 minutes
                'attributes'    => array(
                    'size'   => 40,
                    'min'    => 1,
                ),
            ),
            array(    // Single text field
                'field_id'      => 'notification_email',
                'title'         => __( 'Notification E-mail Address', 'fetch-tweets-hard-cache-refresh' ),
                'description'   => __( 'Set the notification e-mail address.', 'fetch-tweets-hard-cache-refresh' ),
                'type'          => 'text',
                'attributes'    => array(
                    'size'    => 40,
                ),
            ),
            array(
                'field_id'      => 'submit',
                'type'          => 'submit',
            )
        );        
        
    }
    
}
