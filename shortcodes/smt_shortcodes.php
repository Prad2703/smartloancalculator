<?php

/*
 * Shortcodes
 */

function smart_calculator_frm($shortcode_key) {
    $loanCalc = new loanCalculator();
    $getLoanobj = new loanCalculator($shortcode_key);
    $getLoanSettings = $getLoanobj->store_calculator_settings();

    $backgorund_tarnsp = hex2rgb($getLoanSettings->bg_color->box_color);

    $form = NULL;
    $currency_opt = NULL;
    $dropdown_options = NULL;

    $currency_box_color = ($getLoanSettings->currency->box_color != '') ? $getLoanSettings->currency->box_color : '#ebf0f4';
    $currency_text_color = ($getLoanSettings->currency->text_color != '') ? $getLoanSettings->currency->text_color : '#ffffff';


    $options_box_color = ($getLoanSettings->options->box_color != '') ? $getLoanSettings->options->box_color : '#ebf0f4';

    $options_text_color = ($getLoanSettings->options->text_color != '') ? $getLoanSettings->options->text_color : '#ffffff';

    $backgorund_styles = ($getLoanSettings->bg_color->image_apply == 1) ? 'background:url(\'' . $getLoanSettings->bg_color->file . '\')' : 'background:rgba(' . $backgorund_tarnsp[0] . ',' . $backgorund_tarnsp[1] . ',' . $backgorund_tarnsp[2] . ',1)';
    $background_color = ($getLoanSettings->bg_color->show) ? $backgorund_styles : '';

    $default_symbol = $loanCalc->extract_values($getLoanSettings->currency->dropmenu[0], 1);

    $form .= '<style type="text/css">';
    $form .= '.sbHolder{ background: ' . $currency_box_color . ' !important;}';
    $form .= '.sbHolder a,.sbHolder1 a{ border: none;!important;}';
    $form .= '.sbSelector{ color: ' . $currency_text_color . ' !important;}';
    $form .= '.sbOptions li a{ color: #333 !important;}';

    $form .= '.sbHolder1{ background: ' . $options_box_color . ' !important;}';
    $form .= '.sbSelector1{ color: ' . $options_text_color . ' !important;}';
    $form .= '.sbOptions1 li a{ color: #333 !important;}';

    $form .= '.sbToggle{ background: url(' . SMART_CALC_URL . 'images/select-white.png) 3px 12px no-repeat !important;}';
    $form .= '</style>';

    $form .= '<div class="smart_calc_wrapper" style="' . $background_color . '">';
    if ($getLoanSettings->cal_title->show):
        $form .= '<h2 style="background:#fab03f;"><span style=" color:' . $getLoanSettings->cal_title->text_color . '">' . $getLoanSettings->cal_title->title . '</span></h2>';
    endif;
    $form .= '<form name="smart_loan_cal_frm" id="smart_loan_cal_frm" method="post" class="smart_calc_main_form" style="">';
    $form .= '<input type="hidden" name="action" value="smart_loan_calulator">';
    $form .= '<input type="hidden" name="smt_shortcode_key" value="' . $shortcode_key . '" >';


    $form .= '<div class="smart_calc_top" style="background: ' . $getLoanSettings->monthly_payment->box_color . ';">'; //Start Form top section

    if (count($getLoanSettings->currency->dropmenu) > 1):

        /* Currency dropdown */
        $form .= '<div class="smart_calc_sec">';
        $form .= '<div class="custom-select ff-hack">';
        $form .= '<label class="calc-label" style=""><img src="'. SMART_CALC_URL.'/images/icon-currency.png" /></label><select class="smart_selectbox" name="smart_currency">';
        $form .= '<option value="">' . $getLoanSettings->currency->title . '</option>';

        if ($getLoanSettings->currency->dropmenu != ''):
            foreach ($getLoanSettings->currency->dropmenu as $key => $val):
                $currency_val = explode(',', $val);
                $currency_opt.= '<option value="' . $key . '">' . $currency_val[0] . ',' . $currency_val[1] . '</option>';
            endforeach;
        endif;
        $form .= $currency_opt;

        $form .= '</select>';
        $form .= '<div style="clear:both;"></div></div>';
        $form .= '<div style="display:none;" id="currency_msg"></div>';
        $form .= '<div style="display:none;" class="smart_loader"></div>';
        $form .= '</div>';

    endif;

    /* Loan amount */
    $form .= '<div class="smart_calc_sec"><label class="calc-label" style=""><img src="'. SMART_CALC_URL.'/images/icon-amount.png" /></label>';
    $form .= '<input type="text" style="border-radius:0px; background:' . $getLoanSettings->amount->box_color . ';color:' . $getLoanSettings->amount->text_color . ';border: none;height: 32px;width: 80%;margin: 0 auto;padding: 0px 10px;float: left;" name="smart_display_loan_amount" value="" placeholder="">';
    $form .= '<div style="display:none;" id="amount_msg"></div>';
    $form .= '<div style="display:none; color:red" id="amount_error_msg" class="smart_calc_error"></div>';
    $form .= '<div style="clear:both;"></div></div>';
    $form .= '<input type="hidden" name="smart_loan_amount" value="" />';

    /* Months */
    $form .= '<div class="smart_calc_sec"><label class="calc-label" style=""><img src="'. SMART_CALC_URL.'/images/icon-month.png" /></label>';
    $form .= '<input type="text" id="smart_month" style=" border-radius:0px; background:' . $getLoanSettings->months->box_color . ';color:' . $getLoanSettings->months->text_color . ';border: none;height: 32px;width: 80%;margin: 0 auto;padding: 0px 10px;float: left;" name="smart_month" value="" data-placeholder="' . $getLoanSettings->months->title . '" placeholder="">';
    $form .= '<div style="display:none;" id="month_msg"></div>';
    $form .= '<div style="display:none;color:red" id="month_error_msg"></div>';
    $form .= '<div style="clear:both;"></div></div>';

    /* Options dropdown */

    if (count($getLoanSettings->options->dropmenu) > 1):

        $form .= '<div class="smart_calc_sec">';
        $form .= '<div class="custom-select ff-hack"><label class="calc-label" style=""><img src="'. SMART_CALC_URL.'/images/icon-option.png" /></label>';
        $form .= '<select class="smart_opt_selectbox" name="smart_options_dropmenu">';
        $form .= '<option value="">' . $getLoanSettings->options->title . '</option>';

        if ($getLoanSettings->options->dropmenu != ''):
            foreach ($getLoanSettings->options->dropmenu as $key => $val):
                $dropdown_options.= '<option value="' . $key . '">' . $loanCalc->extract_values($val) . '</option>';
            endforeach;
        endif;
        $form .= $dropdown_options;

        $form .= '</select>';
        $form .= '<div style="clear:both;"></div></div>';
        $form .= '<div style="display:none;"  id="options_msg"></div>';
        $form .= '<div style="display:none;" class="smart_loader"></div>';
        $form .= '</div>';

    endif;

    /* Percentage */
    if ($getLoanSettings->percentage->show):
        $form .= '<div class="smart_calc_sec" style="width: 364px; margin-left: 77px; padding:0;background:' . $getLoanSettings->percentage->box_color . '">';
        $form .= '<p class="form-txt" style="line-height:30px;margin:0;padding:0;display:none; color:' . $getLoanSettings->percentage->text_color . '" id="smart_calc_percentage"></p>';
        $form .= '</div>';
    endif;

    

    $form .= '</div>'; // End Form top section
    
    /* Button */
    if ($getLoanSettings->cal_button->show):
        $form .= '<div class="cal_button_sec smart_calc_sec" style="text-align:center;margin-top:15px;">';
        $form .= '<input id="l_cal_button" style="text-transform: none; font-size: 16px;line-height:10px;min-width:80%;background:' . $getLoanSettings->cal_button->box_color . ';color:'. $getLoanSettings->cal_button->text_color.'" type="button" value="' . $getLoanSettings->cal_button->title . '" />';
        $form .= '<input type="hidden" id="l_btn_action" value="">';
        $form .= '<div style="clear:both;"></div></div>';
    endif;

    $form .= '<div class="smart_calc_bottom" style="padding:0px 0px 15px 0px;">'; // Start form bottom section
   

    $form .= '<div class="smart_message_top_section">';
    if ($getLoanSettings->monthly_payment->top_show):
        $form .= '<div class="smart_message_top" style="color:' . $getLoanSettings->message_restriction->text_color . '">'; //Message Top
        $form .= '<span id="smart_calc_top_msg">' . $getLoanSettings->monthly_payment->top_msg . '</span>';
        $form .= '</div>';
    endif;

    $form .= '<div class="smart_monthly_payment" style="color:' . $getLoanSettings->monthly_payment->text_color . '">'; //Monthly Payment
    $form .= '<span id="smart_monthly_payment_text">0.00</span>';
    $form .= '</div>';

    if ($getLoanSettings->monthly_payment->show):
        $form .= '<div class="smart_message_bottom" style="color:' . $getLoanSettings->message_restriction->text_color . '">'; //Message bottom
        $form .= '<span id="smart_calc_bottom_msg">' . $getLoanSettings->monthly_payment->bottom_msg . '</span>';
        $form .= '</div>';
    endif;

    $form .= '</div>';

    if ($getLoanSettings->message_restriction->show):
        $form .= '<div class="smart_message_restrictions" style="color:' . $getLoanSettings->message_restriction->text_color . '">'; //Message bottom
        $form .= '<span id="smart_message_restrictions_text">' . $getLoanSettings->message_restriction->title . '</span>';
        $form .= '</div>';
    endif;
    $form .= '</div>'; // End Form bottom section

    $form .= '</form>';
    $form .= '</div>';

    return $form;
}

/* Savings Calculator Shortcodes */

function savings_calculator_frm($shortcode_key) {

    $loanCalc = new savingsLoanCalculator();
    $getLoanobj = new savingsLoanCalculator($shortcode_key);
    $getLoanSettings = $getLoanobj->store_calculator_settings();

    $backgorund_tarnsp = hex2rgb($getLoanSettings->bg_color->box_color);

    $form = NULL;
    $currency_opt = NULL;
    $dropdown_options = NULL;

    /*     * echo "<pre>";
      print_r($getLoanSettings);
      echo "</pre>"; */

    $currency_box_color = ($getLoanSettings->currency->box_color != '') ? $getLoanSettings->currency->box_color : '#ebf0f4';
    $currency_text_color = ($getLoanSettings->currency->text_color != '') ? $getLoanSettings->currency->text_color : '#ffffff';


    $options_box_color = ($getLoanSettings->savings_type->box_color != '') ? $getLoanSettings->savings_type->box_color : '#ebf0f4';

    $options_text_color = ($getLoanSettings->savings_type->text_color != '') ? $getLoanSettings->savings_type->text_color : '#ffffff';

    $backgorund_styles = ($getLoanSettings->bg_color->image_apply == 1) ? 'background:url(\'' . $getLoanSettings->bg_color->file . '\')' : 'background:rgba(' . $backgorund_tarnsp[0] . ',' . $backgorund_tarnsp[1] . ',' . $backgorund_tarnsp[2] . ',1)';
    $background_color = ($getLoanSettings->bg_color->show) ? $backgorund_styles : '';

    $form .= '<style type="text/css">';
    $form .= '.sbHolder{ background: ' . $currency_box_color . ' !important;}';
    $form .= '.sbHolder a,.sbHolder1 a{ border: none;!important;}';
    $form .= '.sbSelector{ color: ' . $currency_text_color . ' !important;}';
    $form .= '.sbOptions li a{ color: #333 !important;}';

    $form .= '.sbHolder1{ background: ' . $options_box_color . ' !important;}';
    $form .= '.sbSelector1{ color: ' . $options_text_color . ' !important;}';
    $form .= '.sbOptions1 li a{ color: #333 !important;}';

    $form .= '.sbToggle{ background: url(' . SMART_CALC_URL . 'images/select-white.png) 3px 12px no-repeat !important;}';

    $form .= '.smart_calc_top{ width:100%;}';
//    $form .= '.smart_calc_sec .check{right:35px}';

    $form .= '</style>';

    $form .= '<div class="smart_calc_wrapper" style="' . $background_color . '">';
    if ($getLoanSettings->cal_title->show):
        $form .= '<h2 style="background:#fab03f;"><span style="color:' . $getLoanSettings->cal_title->text_color . '">' . $getLoanSettings->cal_title->title . '</span></h2>';
    endif;
    $form .= '<form name="smart_loan_cal_frm" id="smart_savings_loan_cal_frm" method="post" class="smart_calc_main_form" style="">';
    $form .= '<input type="hidden" name="action" value="smart_savings_loan_calulator">';
    $form .= '<input type="hidden" name="smt_shortcode_key" value="' . $shortcode_key . '" >';


    $form .= '<div class="smart_calc_top" style="background: ' . $getLoanSettings->monthly_payment->box_color . ';">'; //Start Form top section

    /* Type of Savings */
    $form .= '<div class="smart_calc_sec">';
    $form .= '<div class="custom-select ff-hack">';

    $form .= '<label class="calc-label" style=""><img src="'. SMART_CALC_URL.'/images/icon-saving.png" /></label><select class="smart_opt_selectbox" id="smart_savings_type" name="smart_savings_type">';
    $form .= '<option value="">' . $getLoanSettings->savings_type->title . '</option>';
    foreach ($getLoanSettings->savings_type->saving_options as $s_type => $s_type_settings):
        $form .= '<option value="' . $s_type . '">' . $s_type . '</option>';
    endforeach;
    $form .= '</select>';
    $form .= '</div><div class="clear"></div>';
    $form .= '<div style="display:none;" id="savings_msg"></div>';
    $form .= '</div>';

    /* Currency dropdown */
    $form .= '<div class="smart_calc_sec">';
    $form .= '<div class="custom-select ff-hack">';
    $form .= '<label class="calc-label" style=""><img src="'. SMART_CALC_URL.'/images/icon-currency.png" /></label><select class="smart_selectbox" id="smart_savings_type_curr" name="smart_currency">';
    $form .= '<option value="">' . $getLoanSettings->currency->title . '</option>';
    $form .= '</select>';
    $form .= '<div style="clear:both;"></div></div>';
    $form .= '<div style="display:none;" id="currency_msg"></div>';
    $form .= '<div style="display:none;" class="smart_loader"></div>';
    $form .= '</div>';

    /* Loan amount */
    $form .= '<div class="smart_calc_sec"><label class="calc-label" style=""><img src="'. SMART_CALC_URL.'/images/icon-amount.png" /></label>';
    $form .= '<input type="text" style="border-radius:0px; background:' . $getLoanSettings->amount->box_color . ';color:' . $getLoanSettings->amount->text_color . ';border: none;height: 32px;width: 80%;margin: 0 auto;padding: 0px 10px;float: left;" name="smart_display_loan_amount" value="" placeholder="">';
    $form .= '<div style="display:none;" id="amount_msg"></div>';
    $form .= '<div style="display:none; color:red" id="amount_error_msg" class="smart_calc_error"></div>';
    $form .= '<div style="clear:both;"></div></div>';
    $form .= '<input type="hidden" name="smart_loan_amount" value="" />';

    /* Months */
    $form .= '<div class="smart_calc_sec"><label class="calc-label" style=""><img src="'. SMART_CALC_URL.'/images/icon-month.png" /></label>';
    $form .= '<input type="text" id="smart_month" style="border-radius:0px; background:' . $getLoanSettings->months->box_color . ';color:' . $getLoanSettings->months->text_color . ';border: none;height: 32px;width: 80%;margin: 0 auto;padding: 0px 10px;float: left;" name="smart_month" value="" data-placeholder="' . $getLoanSettings->months->title . '" placeholder="">';
    $form .= '<div style="display:none;" id="month_msg"></div>';
    $form .= '<div style="display:none;color:red" id="month_error_msg"></div>';
    $form .= '<div style="clear:both;"></div></div>';



    /* Percentage */
    if ($getLoanSettings->percentage->show):
        $form .= '<div class="smart_calc_sec" style=" width: 364px; margin-left: 77px; padding:0;background:' . $getLoanSettings->percentage->box_color . '">';
        $form .= '<p class="form-txt" style="line-height:30px;margin:0;padding:display:none; color:' . $getLoanSettings->percentage->text_color . '" id="smart_calc_percentage"></p>';
        $form .= '</div>';
    endif;

    


    $form .= '</div>'; // End Form top section
    
    /* Button */
    if ($getLoanSettings->cal_button->show):
        $form .= '<div class="cal_button_sec" style="text-align:center; margin-top:15px;">';      
        $form .= '<input id="l_cal_button" style="text-transform: none; font-size: 16px; line-height:10px;min-width:80%;background:' . $getLoanSettings->cal_button->box_color . ';color:'. $getLoanSettings->cal_button->text_color.'" type="button" value="' . $getLoanSettings->cal_button->title . '" />';
        $form .= '<input type="hidden" id="l_btn_action" value="">';
        $form .= '<div style="clear:both;"></div></div>';
    endif;

    $form .= '<div class="smart_calc_bottom" style=" padding:15px;">'; // Start form bottom section
 

    $form .= '<div class="smart_message_top_section">';
    if ($getLoanSettings->monthly_payment->top_show):
        $form .= '<div class="smart_message_top" style="color:' . $getLoanSettings->message_restriction->text_color . '">'; //Message Top
        $form .= '<span id="smart_calc_top_msg">' . $getLoanSettings->monthly_payment->top_msg . '</span>';
        $form .= '</div>';
    endif;

    $form .= '<div class="smart_monthly_payment" style="color:' . $getLoanSettings->monthly_payment->text_color . '">'; //Monthly Payment
    $form .= '<span id="smart_monthly_payment_text">0.00</span>';
    $form .= '</div>';

    if ($getLoanSettings->monthly_payment->show):
        $form .= '<div class="smart_message_bottom" style="color:' . $getLoanSettings->message_restriction->text_color . '">'; //Message bottom
        $form .= '<span id="smart_calc_bottom_msg">' . $getLoanSettings->monthly_payment->bottom_msg . '</span>';
        $form .= '</div>';
    endif;

    $form .= '</div>';

    if ($getLoanSettings->message_restriction->show):
        $form .= '<div class="smart_message_restrictions" style="color:' . $getLoanSettings->message_restriction->text_color . '">'; //Message bottom
        $form .= '<span id="smart_message_restrictions_text">' . $getLoanSettings->message_restriction->title . '</span>';
        $form .= '</div>';
    endif;
    $form .= '</div>'; // End Form bottom section

    $form .= '</form>';
    $form .= '</div>';

    return $form;
}

function hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);

    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    $rgb = array($r, $g, $b);
    //return implode(",", $rgb); // returns the rgb values separated by commas
    return $rgb; // returns an array with the rgb values
}
