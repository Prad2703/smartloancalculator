<?php

/**
 * CLASS:: LOAN CALCULATOR SETTINGS & Calculation
 */
if (!class_exists('savingsLoanCalculator')) {

    class savingsLoanCalculator {

        private $shortcode_key;
        private $shortcode_id;
        public $calculator_settings;
        public $static_attr = array('title' => '', 'box_color' => '#ffff', 'text_color' => '#ffff', 'min' => '', 'max' => '', 'show' => 1, 'top_show' => 1, 'max_error_msg' => '', 'min_error_msg' => '', 'top_msg' => '', 'bottom_msg' => '', 'error' => '', 'saving_options' => array(), 'file' => '', 'image_apply' => '0');
        public $default_fields;
        protected $db;

        public function __construct($shortcode_key = NULL) {
            global $wpdb;
            $this->db = &$wpdb;
            $defaults_attr = new stdClass();
            foreach ($this->static_attr as $key => $val) {
                $defaults_attr->$key = $val;
            }

            $this->default_fields = $defaults_attr;

            $this->calculator_settings = new stdClass();
            $this->calculator_settings->currency = $this->default_fields;
            $this->calculator_settings->amount = $this->default_fields;
            $this->calculator_settings->savings_type = $this->default_fields;
            $this->calculator_settings->percentage = $this->default_fields;
            $this->calculator_settings->monthly_payment = $this->default_fields;
            $this->calculator_settings->amount_message = $this->default_fields;
            $this->calculator_settings->message_restriction = $this->default_fields;
            $this->calculator_settings->bg_color = $this->default_fields;
            $this->calculator_settings->months = $this->default_fields;
            $this->calculator_settings->cal_title = $this->default_fields;
            $this->calculator_settings->cal_button = $this->default_fields;

            if (!empty($shortcode_key))
                $this->shortcode_key = $shortcode_key;
        }

        public function check_shortcode_exists($shortcode_key) {
            $query = "SELECT * FROM " . SMART_SAVINGS_CALC_TBL . " WHERE shortcode_key='{$shortcode_key}'";
            return $this->db->get_row($query);
        }

        public function save_calc_shortcode($data) {
            if ($this->shortcode_key) {
                $this->db->update(SMART_SAVINGS_CALC_TBL, $data, array('shortcode_key' => $this->shortcode_key));
            } else {
                $this->db->insert(SMART_SAVINGS_CALC_TBL, $data);
                return $this->db->insert_id;
            }
        }

        public function get_calculator_shortcodes() {
            $query = "SELECT * FROM " . SMART_SAVINGS_CALC_TBL . "";
            return $this->db->get_results($query);
        }

        public function get_current_shortcode_settings() {


            $query = "SELECT * FROM " . SMART_SAVINGS_CALC_TBL . " WHERE shortcode_key='{$this->shortcode_key}'";
            return $this->db->get_row($query);
        }

        public function get_shortcode_key($frm_id) {
            $query = "SELECT * FROM " . SMART_SAVINGS_CALC_TBL . " WHERE ID='{$frm_id}'";
            return $this->db->get_row($query);
        }

        /**
         * 
         * @return type
         */
        public function store_calculator_settings() {

            $calculator_settings = $this->get_current_shortcode_settings();

            $this->calculator_settings->frm_id = $calculator_settings->ID;
            $this->calculator_settings->shortcode_key = $calculator_settings->shortcode_key;

            if (isset($calculator_settings->cal_title) && $calculator_settings->cal_title != '') {
                $this->calculator_settings->cal_title = json_decode($calculator_settings->cal_title);
            }

            if (isset($calculator_settings->currency) && $calculator_settings->currency != '') {
                $this->calculator_settings->currency = json_decode($calculator_settings->currency);
            }

            if (isset($calculator_settings->amount) && $calculator_settings->amount != '') {
                $this->calculator_settings->amount = json_decode($calculator_settings->amount);
            }

            if (isset($calculator_settings->savings_type) && $calculator_settings->savings_type != '') {
                $this->calculator_settings->savings_type = json_decode($calculator_settings->savings_type);
            }

            if (isset($calculator_settings->percentage) && $calculator_settings->percentage != '') {
                $this->calculator_settings->percentage = json_decode($calculator_settings->percentage);
            }

            if (isset($calculator_settings->monthly_payment) && $calculator_settings->monthly_payment != '') {
                $this->calculator_settings->monthly_payment = json_decode($calculator_settings->monthly_payment);
            }

            if (isset($calculator_settings->months) && $calculator_settings->months != '') {
                $this->calculator_settings->months = json_decode($calculator_settings->months);
            }



            if (isset($calculator_settings->amount_message) && $calculator_settings->amount_message != '') {
                $this->calculator_settings->amount_message = json_decode($calculator_settings->amount_message);
            }

            if (isset($calculator_settings->message_restriction) && $calculator_settings->message_restriction != '') {
                $this->calculator_settings->message_restriction = json_decode($calculator_settings->message_restriction);
            }

            if (isset($calculator_settings->bg_color) && $calculator_settings->bg_color != '') {
                $this->calculator_settings->bg_color = json_decode($calculator_settings->bg_color);
            }

            if (isset($calculator_settings->cal_button) && $calculator_settings->cal_button != '') {
                $this->calculator_settings->cal_button = json_decode($calculator_settings->cal_button);
            }

            return $this->calculator_settings;
        }

        /**
         * 
         * @param type $message
         * @return type
         */
        public function extract_values($values, $index = 0) {
            $explode = explode(',', trim($values));
            return trim($explode[$index]);
        }

        public function get_currency_from_key($key, $settings) {

            $object = $settings->$key;
            $arr = array();

            foreach ($object as $k => $val) {
                $arr[] = $k;
            }

            return $arr;
        }

        public function get_ROI($savings_settings, $savings_type, $currency, $month) {
            $results = array();
            $roi = '';
            $get_all_months_with_percenatge = $savings_settings->$savings_type->$currency;

            $get_only_months = array();
            $get_percenatge = array();
            $i = 1;
            foreach ($get_all_months_with_percenatge as $key => $val) {
                if ($i < count($get_all_months_with_percenatge)):
                    $get_only_months[$key] = $this->extract_values($val);
                    $get_percenatge[$key] = $this->extract_values($val, 1);
                endif;
                $i++;
            }



            sort($get_only_months);




            /* Get Max month */
            $max_month_key = max($get_only_months);

            /* Get Min month */
            $min_month = min($get_only_months);

            $search_val = array_search($max_month_key, $get_only_months);
            $find_month = array_search($month, $get_only_months);


            if (isset($get_only_months[$find_month]) && $get_only_months[$find_month] == $month) {
                $explode = explode('%', $get_percenatge[$find_month]);
                $roi = $explode[0];
            } else if ($month > $max_month_key) {

                $last_value = end($get_all_months_with_percenatge);
                $explode = explode('%', $last_value);
                $roi = $explode[0];
            } else if ($month == $max_month_key) {
                $explode = explode('%', $get_percenatge[$search_val]);
                $roi = $explode[0];
            } else if ($month < $min_month) {

                $min_month_key = array_search($min_month, $get_only_months);
                $explode = explode('%', $get_percenatge[$min_month_key]);
                $roi = $explode[0];
                $month = $min_month;
            } else if ($month < $max_month_key) {

                $roi = '';
                $new_mo_key = '';
                $new_month = '';
                foreach ($get_only_months as $key => $mo) {

                    if ($month < $mo) {
                        $new_mo_key = $key - 1;
                        break;
                    }
                }

                $explode = explode('%', $get_percenatge[$new_mo_key]);
                $roi = $explode[0];
                //$month = $get_only_months[$new_mo_key];
            }

            $results['roi'] = $roi;
            $results['mo'] = $month;

            return $results;
        }

        public function get_second_highest_value($array) {

            $max_month_key = max($array);
        }

    }

}
