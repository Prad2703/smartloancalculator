<?php

/**
 * AJAX: 
 */
/*
 * Calculate Loan Section
 * @Formula
 * P = ( r * A ) / ( 1 - (1+r)^-n)
 * rate = $options
 * n = $months
 * A = $amount  
 */

add_action('wp_ajax_smart_loan_calulator', 'smart_loan_calulator_func');
add_action('wp_ajax_nopriv_smart_loan_calulator', 'smart_loan_calulator_func');

if (!function_exists('smart_loan_calulator_func')) {

    function smart_loan_calulator_func() {
        $resp_arr = array(
            'currency_error' => '',
            'currency_sym' => 'USD',
            'currency_symbol' => '$',
            'amount_error' => '',
            'month_error' => '',
            'percentage_error' => '',
            'percentage_text' => '',
            'top_text' => '',
            'bottom_text' => '',
            'restriction_text' => '',
            'flag' => false,
            'common_error' => '',
            'results' => ''
        );

        $shortcode_key = strip_tags($_POST['smt_shortcode_key']);
        
        $currency = strip_tags($_POST['smart_currency']);
        $loan_amount = strip_tags($_POST['smart_loan_amount']);
        $display_loan_amount = strip_tags($_POST['smart_display_loan_amount']);
        $months = strip_tags($_POST['smart_month']);
        $options = strip_tags($_POST['smart_options_dropmenu']);
        


        $lonaCalc = new loanCalculator();

        if (!$lonaCalc->check_shortcode_exists($shortcode_key)) {
            $resp_arr['common_error'] = 'Invalid shortcode';
        }

        if ($loan_amount == '0') {
            $loan_amount = $display_loan_amount;
        }
        $loan_amount = ($loan_amount == '') ? '0' : $loan_amount;

        /* Get the new instance of the loanCal Class */
        $getLoanCalc = new loanCalculator($shortcode_key);
        $getLoanSettings = $getLoanCalc->store_calculator_settings();


        
        if(count($getLoanSettings->currency->dropmenu) == 1) {
            $currency = '0';
        }
        
        if(count($getLoanSettings->options->dropmenu) == 1) {
            $options = '0';
        }
        
        $loan_sym = $lonaCalc->extract_values($getLoanSettings->currency->dropmenu[$currency], 1);
        $exp_loan_amount = explode($loan_sym, trim($loan_amount));

        $amount = $loan_amount;



        if ($currency == '') {
            $resp_arr['currency_error'] = 'Please select a currency';
        } elseif ($amount == '') {
            $resp_arr['amount_error'] = 'Please enter amount';
        } elseif (number_format(str_replace(',', '', $amount), 2, '.', '') < str_replace(',', '', $lonaCalc->extract_values($getLoanSettings->currency->dropmenu[$currency], 2))) {
            $resp_arr['amount_error'] = $getLoanSettings->amount->min_error_msg . ' ' . $loan_sym . $lonaCalc->extract_values($getLoanSettings->currency->dropmenu[$currency], 2);
        } elseif (number_format(str_replace(',', '', $amount), 2, '.', '') > str_replace(',', '', $lonaCalc->extract_values($getLoanSettings->currency->dropmenu[$currency], 3))) {
            $resp_arr['amount_error'] = $getLoanSettings->amount->max_error_msg . ' ' . $loan_sym . $lonaCalc->extract_values($getLoanSettings->currency->dropmenu[$currency], 3);
        } elseif (empty($months)) {
            $resp_arr['month_error'] = 'Please enter month.';
        } elseif ($months < $getLoanSettings->months->min) {
            $resp_arr['month_error'] = $getLoanSettings->months->min_error_msg . ' ' . $getLoanSettings->months->min;
        } elseif ($months > $getLoanSettings->months->max) {
            $resp_arr['month_error'] = $getLoanSettings->months->max_error_msg . ' ' . $getLoanSettings->months->max;
        } elseif ($options == '') {
            $resp_arr['percentage_error'] = 'Please select a options.';
        } else {

            $rate_with_percentage = $lonaCalc->extract_values($getLoanSettings->options->dropmenu[$options], 1);
            $rate_extract_percentage = explode('%', trim($rate_with_percentage));

            $rate = $rate_extract_percentage[0] / 1200;
            $pow_rate = 1 + $rate;
            $pow_cal = 1 - pow($pow_rate, -$months);

            $calculation_step1 = ($rate * str_replace(',', '', $amount));
            $calculation_step2 = ($calculation_step1 / $pow_cal);

            $price_format = number_format($calculation_step2, 2, '.', ',');
            $resp_arr['flag'] = TRUE;

            $resp_arr['results'] = $lonaCalc->extract_values($getLoanSettings->currency->dropmenu[$currency], 1) . ' ' . $price_format;

            $resp_arr['top_msg'] = $getLoanSettings->monthly_payment->top_msg;
            $resp_arr['bottom_msg'] = $getLoanSettings->monthly_payment->bottom_msg;

            if ($getLoanSettings->percentage->show):
                $resp_arr['percentage_text'] = trim($rate_with_percentage) . ' ' . $getLoanSettings->percentage->title;
            endif;
        }

        if ($currency != '') {
            $resp_arr['currency_sym'] = str_replace(',', '', $amount);
            $resp_arr['currency_showing'] = ($display_loan_amount == '') ? '' :number_format(str_replace(',', '', $amount), 2, '.', ',');
            $resp_arr['currency_symbol'] = $loan_sym;
        }

        /*if ($resp_arr['amount_error'] != '') {
            $resp_arr['currency_sym'] = '0';
        }*/

        echo json_encode($resp_arr);
        exit;
    }

}


/**
 * Formula:
 * result =(amount*roi/12) * month
 */

add_action('wp_ajax_smart_savings_loan_calulator', 'smart_savings_loan_calulator_func');
add_action('wp_ajax_nopriv_smart_savings_loan_calulator', 'smart_savings_loan_calulator_func');

if(!function_exists('smart_savings_loan_calulator_func')) {
    function smart_savings_loan_calulator_func(){
        $resp_arr = array(
            'savings_type' => '',
            'currency_options' => '',
            'currency_error' => '',
            'currency_sym' => 'USD',
            'currency_symbol' => '',
            'amount_error' => '',
            'month_error' => '',
            'percentage_error' => '',
            'percentage_text' => '',
            'top_text' => '',
            'bottom_text' => '',
            'restriction_text' => '',
            'flag' => false,
            'common_error' => '',
            'results' => ''
        );
        
        $savings_type = strip_tags($_POST['smart_savings_type']);
        $currency = strip_tags($_POST['smart_currency']);
        $loan_amount = strip_tags($_POST['smart_loan_amount']);
        $display_loan_amount = strip_tags($_POST['smart_display_loan_amount']);
        $months = strip_tags($_POST['smart_month']);
       
        $shortcode_key = strip_tags($_POST['smt_shortcode_key']);
        
        $lonaCalc = new savingsLoanCalculator();

        if (!$lonaCalc->check_shortcode_exists($shortcode_key)) {
            $resp_arr['common_error'] = 'Invalid shortcode';
        }
        
        if ($loan_amount == '0') {
            $loan_amount = $display_loan_amount;
        }
        $loan_amount = ($loan_amount == '') ? '0' : $loan_amount;
         $amount = $loan_amount;

        /* Get the new instance of the loanCal Class */
        $getLoanCalc = new savingsLoanCalculator($shortcode_key);
        $getLoanSettings = $getLoanCalc->store_calculator_settings();
        $savings_settings = $getLoanSettings->savings_type->saving_options;
        
        if($savings_type == '') {
            $resp_arr['savings_type'] = 'Please select savings type';
        } else if($currency == '') {
            $resp_arr['currency_error'] = 'Please select a currency';
        } else if($amount == '') {
            $resp_arr['amount_error'] = 'Please enter amount';
        } elseif (empty($months)) {
            $resp_arr['month_error'] = 'Please enter month.';
        } else {
            
            
            $month_and_interests = $lonaCalc->get_ROI($savings_settings, $savings_type, $currency, $months);
            
            $results = ( (str_replace(',', '', $amount)*$month_and_interests['roi']/(12*100)) ) * $month_and_interests['mo'];
            
            $price_format = number_format($results, 2, '.', ',');
            $resp_arr['flag'] = TRUE;

            $resp_arr['results'] = $lonaCalc->extract_values($currency, 1) . ' ' . $price_format;

            $resp_arr['top_msg'] = $getLoanSettings->monthly_payment->top_msg;
            $resp_arr['bottom_msg'] = $getLoanSettings->monthly_payment->bottom_msg;
            $rate_with_percentage = $month_and_interests['roi'] . '%';
            
            if ($getLoanSettings->percentage->show):
                $resp_arr['percentage_text'] = trim($rate_with_percentage) . ' ' . $getLoanSettings->percentage->title;
            endif;
            
            
            
        }
        
        if($savings_type != '' && $currency == '') {
            
            $currency_options = $lonaCalc->get_currency_from_key($savings_type, $savings_settings);
            
            if(is_array($currency_options) && count($currency_options) > 0 ) {
                $curr_opt = '<option value="">'. $getLoanSettings->currency->title.'</option>';
                foreach ($currency_options as $val):
                    $curr_opt .= '<option value="'. $val .'">'. $val .'</option>';
                endforeach;
                
                $resp_arr['currency_options'] = $curr_opt;
            }
        }
        
        if ($currency != '') {
            $resp_arr['currency_sym'] = str_replace(',', '', $amount);
            $resp_arr['currency_showing'] = ($display_loan_amount == '') ? '' :number_format(str_replace(',', '', $amount), 2, '.', ',');
            $resp_arr['currency_symbol'] = $getLoanCalc->extract_values($currency, 1);
        }
        
        echo json_encode($resp_arr);
        exit;
    }
}



