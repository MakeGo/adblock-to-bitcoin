<?php
class a2bSettings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'a2b Settings Admin', 
            'adblock-to-bitcoin', 
            'manage_options', 
            'a2b-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'a2b_option_group' );
        ?>
        <div class="wrap">
            <h2>Configuration Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'a2b_options' );   
                do_settings_sections( 'a2b-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'a2b_options', // Option group
            'a2b_option_group', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'all_sections', // ID
            'adblock-to-bitcoin settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'a2b-setting-admin' // Page
        );  

        add_settings_field(
            'bitcoin_address', // ID
            'Bitcoin Address', // Title 
            array( $this, 'bitcoin_address_callback' ), // Callback
            'a2b-setting-admin', // Page
            'all_sections' // Section           
        );      

        add_settings_field(
            'suggested_donation_amount', // ID
            'Suggested Donation Amount (USD)', // Title 
            array( $this, 'suggested_donation_amount_callback' ), // Callback
            'a2b-setting-admin', // Page
            'all_sections' // Section           
        );      

        add_settings_field(
            'copy', // ID
            'Ad module copy', // Title 
            array( $this, 'copy_callback' ), // Callback
            'a2b-setting-admin', // Page
            'all_sections' // Section           
        );      

        add_settings_field(
            'display_always', 
            'Always display bitcoin solicitations?', 
            array( $this, 'display_settings_callback' ), 
            'a2b-setting-admin', 
            'all_sections'
        );      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        $new_input['bitcoin_address'] =  $input['bitcoin_address'] ;
        $new_input['display_always'] =  $input['display_always'] ;
        $new_input['suggested_donation_amount'] =  ($input['suggested_donation_amount']) ;
        $new_input['copy'] = ($input['copy']) ;

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print('For more information, check out <a href="http://github.com/owocki/adblock-to-bitcoin">adblock-to-bitcoin on github</a>.');
    }


    /** 
     * Get the settings option array and print one of its values
     */
    public function bitcoin_address_callback()
    {
        printf(
            '<input type="text" id="bitcoin_address" name="a2b_option_group[bitcoin_address]" value="%s" />',
            isset( $this->options['bitcoin_address'] ) ? esc_attr( $this->options['bitcoin_address']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function copy_callback()
    {
        printf(
            '<textarea id="copy" style="width: 500px;" name="a2b_option_group[copy]">%s</textarea>',
            isset( $this->options['copy'] ) ? esc_attr( $this->options['copy']) : get_default_copy()
        );
    }


    /** 
     * Get the settings option array and print one of its values
     */
    public function suggested_donation_amount_callback()
    {
        printf(
            '$<input type="text" id="suggested_donation_amount" name="a2b_option_group[suggested_donation_amount]" value="%s" />',
            isset( $this->options['suggested_donation_amount'] ) ? esc_attr( $this->options['suggested_donation_amount']) : '0.50'
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function display_settings_callback()
    {
        printf(
            '<input type=checkbox id="display_always" name="a2b_option_group[display_always]" value="1" %s />',
            isset( $this->options['display_always'] ) ? 'checked' : ''
        );
    }
}

if( is_admin() )
    $a2b_settings_page = new a2bSettings();


?>