<?php

/*
  Plugin Name: Loan Smart Calculator
  Plugin URI: http://www.nksnow.com
  Description: Uses for loan calculator with lots of customization & shortcodes..
  Version: 3.1.1
  Author: New Kingdom Studios
  Author URI: http://www.nksnow.com
  License: GPLv2 or later
  Text Domain: smartcalc
 */
ob_start();
global $wpdb;
if (!defined('SMART_CALC_PATH'))
    define('SMART_CALC_PATH', plugin_dir_path(__FILE__));

if (!defined('SMART_CALC_URL'))
    define('SMART_CALC_URL', plugin_dir_url(__FILE__));

if (!defined('SMART_CALC_TBL'))
    define('SMART_CALC_TBL', $wpdb->prefix . "smart_calculator");

if (!defined('SMART_SAVINGS_CALC_TBL'))
    define('SMART_SAVINGS_CALC_TBL', $wpdb->prefix . "smart_savings_calculator");

if (!defined('SMART_CALC_ADMIN_PAGE'))
    define('SMART_CALC_ADMIN_PAGE', admin_url() . "admin.php?page=loan-smart-calculator&action=create");

if (!defined('SMART_SAVINGS_CALC_ADMIN_PAGE'))
    define('SMART_SAVINGS_CALC_ADMIN_PAGE', admin_url() . "admin.php?page=savings-calculator&action=create");


if (!class_exists('smartCalculatorInit')) {

    class smartCalculatorInit {

        public function __construct() {
            register_activation_hook(__FILE__, array($this, 'fireOnActivation'));
            register_uninstall_hook(__FILE__, array($this, 'fireOnUninstall'));

            $this->includes();

            add_action('admin_enqueue_scripts', array($this, 'loadAdminScripts'));
            add_action('wp_enqueue_scripts', array($this, 'loadFrontScripts'));
            add_shortcode('smartcalc', array($this, 'loan_calc_shortcode_func'));
            add_shortcode('smartcalcsavings', array($this, 'loan_calc_savings_shortcode_func'));

            add_shortcode('smartloan_dropdown', array($this, 'smart_loan_dropdown_calc_func'));
            add_shortcode('smartsavings_dropdown', array($this, 'smart_savings_dropdown_calc_func'));
        }

        public function loan_calc_shortcode_func($atts) {

            $output = NULL;
            $loanCalc = new loanCalculator();

            $atts = shortcode_atts(array(
                'id' => false
                    ), $atts);

            if (empty($atts['id'])) {
                $output .= '<div class="calc_error">Please use valid shortcode format. Ex: [smartcalc ID="test"]</div>';
            } else if (!$loanCalc->check_shortcode_exists($atts['id'])) {
                $output .= '<div class="calc_error">Invalid shortcode. Please use valid one.</div>';
            } else {

                $output .= smart_calculator_frm($atts['id']);
            }

            return $output;
        }

        public function loan_calc_savings_shortcode_func($atts) {

            $output = NULL;
            $loanCalc = new savingsLoanCalculator();

            $atts = shortcode_atts(array(
                'id' => false
                    ), $atts);

            if (empty($atts['id'])) {
                $output .= '<div class="calc_error">Please use valid shortcode format. Ex: [smartcalc ID="test"]</div>';
            } else if (!$loanCalc->check_shortcode_exists($atts['id'])) {
                $output .= '<div class="calc_error">Invalid shortcode. Please use valid one.</div>';
            } else {

                $output .= savings_calculator_frm($atts['id']);
            }

            return $output;
        }

        public function smart_loan_dropdown_calc_func() {
            $output = NULL;


            $loan_title = get_option('_loan_title');
            $loan_title = ($loan_title != '') ? $loan_title : 'List of Calculator';

            $loanCalc = new loanCalculator();
            /* Get All Loan Calculator shortcodes */
            $all_shortcodes = $loanCalc->get_calculator_shortcodes();
            $opt = '';
            $first_cal_ID = '';
            $dropdown_background_color = '';
            $i = 0;
            foreach ($all_shortcodes as $shortcode) {

                $cal = json_decode($shortcode->cal_title);
                $monthly_payment = json_decode($shortcode->monthly_payment);
                if ($i == 0) :
                    $first_cal_ID = $shortcode->shortcode_key;
                    $dropdown_background_color = $monthly_payment->box_color;
                endif;
                $opt_selected = (isset($_GET['cal_id']) && $_GET['cal_id'] != '' && $_GET['cal_id'] == $shortcode->shortcode_key) ? 'selected="selected"' : '';
                $opt .= '<option  ' . $opt_selected . ' value="' . $shortcode->shortcode_key . '">' . $cal->title . '</option>';
                $i++;
            }
            $cal_id = (isset($_GET['cal_id']) && $_GET['cal_id'] != '') ? strip_tags(trim($_GET['cal_id'])) : $first_cal_ID;
            $getLoanobj = new loanCalculator($cal_id);
            $getLoanSettings = $getLoanobj->store_calculator_settings();
            $dropdown_background_color = $getLoanSettings->monthly_payment->box_color;

            $output .= '<style type="text/css">';
            $output .= '.loan_dropdown_wrap .sbHolder{float: right;width: 56%; border:none;background: '.$dropdown_background_color.' !important;}';
            $output .= '</style>';


            $output .= '<input type="hidden" id="smt_current_url" name="smt_current_url" value="' . get_permalink() . '"/>';
            $output .= '<div class="loan_dropdown_wrap loan_dropdown_wrap_head" style="width:465px; padding: 0 30px 0 50px; display: block; margin:0 auto; margin-bottom:10px;">';
            $output .= '<label style="width:40%; font-size:14px; float:left; padding-top: 7px;">' . $loan_title . '</label> <select class="loan_dropdown_select" name="saving_loan_dropdown">' . $opt . '</select>';
            $output .= '<div class="clear"></div>';
            $output .= '</div>';

            $output .= smart_calculator_frm($cal_id);

            return $output;
        }

        public function smart_savings_dropdown_calc_func() {
            $output = NULL;


            $loanCalc = new savingsLoanCalculator();
            /* Get All Loan Calculator shortcodes */
            $all_shortcodes = $loanCalc->get_calculator_shortcodes();

            $loan_sv_title = get_option('_loan_sv_title');
            $loan_sv_title = ($loan_sv_title != '') ? $loan_sv_title : 'List of Calculator';
            $dropdown_background_color = '';
            $opt = '';
            $first_cal_ID = '';
            $i = 0;
            foreach ($all_shortcodes as $shortcode) {

                $cal = json_decode($shortcode->cal_title);
                $monthly_payment = json_decode($shortcode->monthly_payment);
                if ($i == 0) :
                    $first_cal_ID = $shortcode->shortcode_key;
                    $dropdown_background_color = $monthly_payment->box_color;

                endif;

                $opt_selected = (isset($_GET['cal_id']) && $_GET['cal_id'] != '' && $_GET['cal_id'] == $shortcode->shortcode_key) ? 'selected="selected"' : '';

                $opt .= '<option ' . $opt_selected . ' value="' . $shortcode->shortcode_key . '">' . $cal->title . '</option>';
                $i++;
            }

            $cal_id = (isset($_GET['cal_id']) && $_GET['cal_id'] != '') ? strip_tags(trim($_GET['cal_id'])) : $first_cal_ID;
            $getLoanobj = new savingsLoanCalculator($cal_id);
            $getLoanSettings = $getLoanobj->store_calculator_settings();
            $dropdown_background_color = $getLoanSettings->monthly_payment->box_color;
            
            $output .= '<style type="text/css">';
            $output .= '.loan_dropdown_wrap .sbHolder{float: right;width: 56%; border:none;background: '.$dropdown_background_color.' !important;}';
            $output .= '</style>';


            $output .= '<input type="hidden" id="smt_current_url" name="smt_current_url" value="' . get_permalink() . '"/>';
            $output .= '<div class="loan_dropdown_wrap loan_dropdown_wrap_head" style="width:465px; padding: 0 30px 0 50px; display: block; margin:0 auto; margin-bottom:10px;">';
            $output .= '<label style="width:40%; font-size:14px; float:left; padding-top: 7px;">' . $loan_sv_title . '</label> <select class="loan_dropdown_select" name="saving_loan_dropdown">' . $opt . '</select>';
            $output .= '<div class="clear"></div>';
            $output .= '</div>';

            $output .= savings_calculator_frm($cal_id);

            return $output;
        }

        public function includes() {

            /* admin */
            require_once SMART_CALC_PATH . 'admin/smt_admin_menu_creation.php';


            /* Front */
            require_once SMART_CALC_PATH . 'class/loanCalculator.php';
            require_once SMART_CALC_PATH . 'class/savingsLoanCalculator.php';

            require_once SMART_CALC_PATH . 'shortcodes/smt_shortcodes.php';
            require_once SMART_CALC_PATH . 'ajax/smart_calc_ajax.php';
        }

        public function loadAdminScripts() {
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('smt_colorpicker_js', SMART_CALC_URL . 'js/spectrum.js');
            wp_enqueue_script('smt_selectbox_js', SMART_CALC_URL . 'js/jquery.selectbox-0.2.js');

            wp_enqueue_script('smt_cal_js', SMART_CALC_URL . 'js/smt_calc.js');
            wp_localize_script('smt_cal_js', 'smartCal', array('cancelImage' => SMART_CALC_URL . 'images/cross.png'));

            wp_enqueue_style('smt_colorpicker_css', SMART_CALC_URL . 'css/spectrum.css');
            wp_enqueue_style('smt_select_css', SMART_CALC_URL . 'css/jquery.selectbox.css');
            wp_enqueue_style('smt_cal_css', SMART_CALC_URL . 'css/smt_calc.css');
        }

        public function loadFrontScripts() {
            wp_enqueue_script('jquery');

            wp_enqueue_script('smt_selectbox_js', SMART_CALC_URL . 'js/jquery.selectbox-0.2.js');
            wp_enqueue_script('smt_front_cal_js', SMART_CALC_URL . 'js/smart_calc_front.js');
            wp_localize_script('smt_front_cal_js', 'smartCal', array('ajaxurl' => admin_url('admin-ajax.php')));

            wp_enqueue_style('smt_select_css', SMART_CALC_URL . 'css/jquery.selectbox.css');
            wp_enqueue_style('smt_front_cal_css', SMART_CALC_URL . 'css/smart_calc_front.css');
        }

        public function fireOnActivation() {
            global $wpdb;

            $table_query = 'CREATE TABLE IF NOT EXISTS `' . SMART_CALC_TBL . '` (
                            `ID` bigint(20) NOT NULL AUTO_INCREMENT,
                            `shortcode_key` varchar(100) NOT NULL,
                            `cal_title` text NOT NULL,
                            `currency` text NOT NULL,
                            `amount` text NOT NULL,
                            `options_dropmenu` text NOT NULL,
                            `percentage` text NOT NULL,
                            `monthly_payment` text NOT NULL,
                            `months` text NOT NULL,
                            `amount_message` text NOT NULL,
                            `message_restriction` text NOT NULL,
                            `bg_color` text NOT NULL,
                            `cal_button` text NOT NULL,
                            `create_date` datetime NOT NULL,
                            PRIMARY KEY (`ID`),
                            UNIQUE KEY `shortcode_key` (`shortcode_key`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';

            $wpdb->query($table_query);

            $table_query1 = 'CREATE TABLE IF NOT EXISTS `' . SMART_SAVINGS_CALC_TBL . '` (
                            `ID` bigint(20) NOT NULL AUTO_INCREMENT,
                            `shortcode_key` varchar(100) NOT NULL,
                            `cal_title` text NOT NULL,
                            `currency` text NOT NULL,
                            `amount` text NOT NULL,
                            `savings_type` text NOT NULL,
                            `percentage` text NOT NULL,
                            `monthly_payment` text NOT NULL,
                            `months` text NOT NULL,
                            `amount_message` text NOT NULL,
                            `message_restriction` text NOT NULL,
                            `bg_color` text NOT NULL,
                            `cal_button` text NOT NULL,
                            `create_date` datetime NOT NULL,
                            PRIMARY KEY (`ID`),
                            UNIQUE KEY `shortcode_key` (`shortcode_key`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';

            $wpdb->query($table_query1);
        }

        public function fireOnUninstall() {
            require_once SMART_CALC_PATH . 'uninstall.php';
        }

    }

    new smartCalculatorInit();
}
