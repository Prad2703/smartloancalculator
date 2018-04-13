<?php
/**
 * Create/Edit Shortcodes & Settings Page
 */
$loanCalculator = new savingsLoanCalculator();

$calc_frm_id = (isset($_GET['calc_frm_id']) && $_GET['calc_frm_id'] != '') ? strip_tags(trim($_GET['calc_frm_id'])) : 0;
$calc_shortcode_text = ($calc_frm_id) ? 'Edit' : 'Create';

if ($calc_frm_id) {
    $get_calc_shortcode = $loanCalculator->get_shortcode_key($calc_frm_id);

    $shortcodeObj = new savingsLoanCalculator($get_calc_shortcode->shortcode_key);
    $get_shortcode_settings = $shortcodeObj->store_calculator_settings();
}

/* start Post the creation form */

if (isset($_POST['create_calc_shortcode_btn'])) {

    $calc_shortcode_key = strip_tags(trim($_POST['calc_shortcode_key']));

    $msg = NULL;
    $redirect_to = SMART_SAVINGS_CALC_ADMIN_PAGE;
    if (empty($calc_shortcode_key)) {

        $msg = '<div id="message" class="error"><p>Please enter shortcode unique key</p></div>';
    } else if ($loanCalculator->check_shortcode_exists($calc_shortcode_key)) {

        $msg = '<div id="message" class="error"><p>This shortcode key already exsits.</p></div>';
    } else {

        /* Save the Calulator Shortcode */
        $save_with_defaults_attr = $loanCalculator->store_calculator_settings();

        /* $save_with_defaults_attr->cal_title->text_color = '#FFFFFF';
          $save_with_defaults_attr->cal_title->box_color = '#525252';

          $save_with_defaults_attr->currency->box_color = '#0F69A4';
          $save_with_defaults_attr->currency->text_color = '#FFFFFF';

          $save_with_defaults_attr->amount->box_color = '#0F69A4';
          $save_with_defaults_attr->amount->text_color = '#FFFFFF';




          $save_with_defaults_attr->monthly_payment->box_color = '#14517A';
          $save_with_defaults_attr->monthly_payment->text_color = '#FAB03F';

          $save_with_defaults_attr->months->box_color = '#ECF0F5';
          $save_with_defaults_attr->months->text_color = '#525252';

          $save_with_defaults_attr->percentage->box_color = '#FFFFFF';
          $save_with_defaults_attr->percentage->text_color = '#FAB03F';

          $save_with_defaults_attr->options->box_color = '#ECF0F5';
          $save_with_defaults_attr->options->text_color = '#525252';

          $save_with_defaults_attr->message_restriction->text_color = '#FFFFFF';

          $save_with_defaults_attr->bg_color->box_color = '#E5A13D'; */


        $data = array(
            'shortcode_key' => $calc_shortcode_key,
            'cal_title' => json_encode($save_with_defaults_attr->cal_title),
            'currency' => json_encode($save_with_defaults_attr->currency),
            'amount' => json_encode($save_with_defaults_attr->amount),
            'monthly_payment' => json_encode($save_with_defaults_attr->monthly_payment),
            'months' => json_encode($save_with_defaults_attr->months),
            'percentage' => json_encode($save_with_defaults_attr->percentage),
            'savings_type' => json_encode($save_with_defaults_attr->savings_type),
            'message_restriction' => json_encode($save_with_defaults_attr->message_restriction),
            'bg_color' => json_encode($save_with_defaults_attr->bg_color),
            'cal_button' => json_encode($save_with_defaults_attr->cal_button),
            'create_date' => date('Y-m-d')
        );

        $ID = $loanCalculator->save_calc_shortcode($data);

        $get_row = $loanCalculator->get_shortcode_key($ID);
        $new_obj = new savingsLoanCalculator($get_row->shortcode_key);

        $new_records = $new_obj->store_calculator_settings();

        $new_records->cal_title->text_color = '#FFFFFF';
        $new_records->cal_title->box_color = '#525252';

        $new_records->currency->box_color = '#0F69A4';
        $new_records->currency->text_color = '#FFFFFF';

        $new_records->amount->box_color = '#0F69A4';
        $new_records->amount->text_color = '#FFFFFF';

        $new_records->monthly_payment->box_color = '#14517A';
        $new_records->monthly_payment->text_color = '#FAB03F';

        $new_records->months->box_color = '#0F69A4';
        $new_records->months->text_color = '#FFFFFF';

        $new_records->percentage->box_color = '#FFFFFF';
        $new_records->percentage->text_color = '#FAB03F';

        $new_records->savings_type->box_color = '#0F69A4';
        $new_records->savings_type->text_color = '#FFFFFF';

        $new_records->message_restriction->text_color = '#FFFFFF';

        $new_records->bg_color->box_color = '#063048';

        $new_records->cal_button->box_color = '#063048';
        $new_records->cal_button->text_color = '#FFFFFF';

        $update_data = array(
            'cal_title' => json_encode($new_records->cal_title),
            'currency' => json_encode($new_records->currency),
            'amount' => json_encode($new_records->amount),
            'monthly_payment' => json_encode($new_records->monthly_payment),
            'months' => json_encode($new_records->months),
            'percentage' => json_encode($new_records->percentage),
            'savings_type' => json_encode($new_records->savings_type),
            'message_restriction' => json_encode($new_records->message_restriction),
            'bg_color' => json_encode($new_records->bg_color),
            'cal_button' => json_encode($new_records->cal_button),
        );

        $new_obj->save_calc_shortcode($update_data);

        if ($ID) {
            $redirect_to = SMART_SAVINGS_CALC_ADMIN_PAGE . '&calc_frm_id=' . $ID;
        }
    }
    set_transient('smart_calc_error', $msg, 30);
    wp_redirect($redirect_to);
    exit;
}

/* Save the switcher form */
if (isset($_POST['smart_style_switcher_btn'])) {

    $calculator_title = $_POST['cal_title'];
    $currency = $_POST['currency'];
    $amount = $_POST['amount'];
    $monthly_payment = $_POST['monthly_payment'];
    $months = $_POST['months'];
    $savings_type = $_POST['savings_type'];
    $percentage = $_POST['percentage'];
    $background = $_POST['background_color'];
    $background_file = $_FILES['background_file'];

    $background_image_apply = $_POST['background_image_apply'];

    $message_restriction = $_POST['message_restriction'];

    $cal_button = $_POST['cal_button'];

    $get_calc_shortcode = $loanCalculator->get_shortcode_key($calc_frm_id);

    $newshortcodeObj = new savingsLoanCalculator($get_calc_shortcode->shortcode_key);
    $new_shortcode_settings = $newshortcodeObj->store_calculator_settings();

    $new_shortcode_settings->cal_title->text_color = $calculator_title['text_color'];
    $new_shortcode_settings->cal_title->box_color = $calculator_title['box_color'];

    $new_shortcode_settings->currency->box_color = $currency['box_color'];
    $new_shortcode_settings->currency->text_color = $currency['text_color'];

    $new_shortcode_settings->amount->box_color = $amount['box_color'];
    $new_shortcode_settings->amount->text_color = $amount['text_color'];




    $new_shortcode_settings->monthly_payment->box_color = $monthly_payment['box_color'];
    $new_shortcode_settings->monthly_payment->text_color = $monthly_payment['text_color'];

    $new_shortcode_settings->months->box_color = $months['box_color'];
    $new_shortcode_settings->months->text_color = $months['text_color'];

    $new_shortcode_settings->percentage->box_color = $percentage['box_color'];
    $new_shortcode_settings->percentage->text_color = $percentage['text_color'];

    $new_shortcode_settings->savings_type->box_color = $savings_type['box_color'];
    $new_shortcode_settings->savings_type->text_color = $savings_type['text_color'];

    $new_shortcode_settings->message_restriction->text_color = $message_restriction['text_color'];

    $new_shortcode_settings->bg_color->box_color = $background;
    $new_shortcode_settings->bg_color->image_apply = $background_image_apply;

    $new_shortcode_settings->cal_button->box_color = $cal_button['box_color'];
    $new_shortcode_settings->cal_button->text_color = $cal_button['text_color'];


    if ($background_file['name'] != '') {

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $overrides = array('test_form' => false);
        $upload = wp_handle_upload($background_file, $overrides);

        if (isset($upload['url']) && $upload['url'] != '') {
            $new_shortcode_settings->bg_color->file = $upload['url'];
        }
    }




    /* save the record */
    $data = array(
        'cal_title' => json_encode($new_shortcode_settings->cal_title),
        'currency' => json_encode($new_shortcode_settings->currency),
        'amount' => json_encode($new_shortcode_settings->amount),
        'monthly_payment' => json_encode($new_shortcode_settings->monthly_payment),
        'months' => json_encode($new_shortcode_settings->months),
        'percentage' => json_encode($new_shortcode_settings->percentage),
        'savings_type' => json_encode($new_shortcode_settings->savings_type),
        'message_restriction' => json_encode($new_shortcode_settings->message_restriction),
        'bg_color' => json_encode($new_shortcode_settings->bg_color),
        'cal_button' => json_encode($new_shortcode_settings->cal_button)
    );


    $newshortcodeObj->save_calc_shortcode($data);

    $msg = '<div class="updated" id="message"><p>Settings successfully saved</p></div>';
    set_transient('calc_settings_msg', $msg, 30);

    wp_safe_redirect(SMART_SAVINGS_CALC_ADMIN_PAGE . '&calc_frm_id=' . $calc_frm_id);
    exit;
}


/* Start to save the Shorcode Settings form data */

if (isset($_POST['shortcode_settings_btn'])) {

    $calculator_title = $_POST['cal_title'];
    $currency = $_POST['currency'];
    $amount = $_POST['amount'];
    $months = $_POST['months'];
    $savings_type = $_POST['savings_type'];
    $percentage = $_POST['percentage'];
    $monthly_payment = $_POST['monthly_payment'];
    $message_restrictions = $_POST['message_restrictions'];
    $background = $_POST['background_color'];

    $cal_button = $_POST['cal_button'];


    $savings_options_arr = [];

    if (is_array($savings_type) && count($savings_type) > 0) {

        foreach ($savings_type['months'] as $key => $val):

            $savings_type_name = $savings_type['name'][$key];
            $sv_currency_arr = array();


            foreach ($val as $curr => $mo):
                $sv_currency_arr[$savings_type['currency'][$key][$curr]] = $mo;
            endforeach;
            $savings_options_arr[$savings_type_name] = $sv_currency_arr;
        endforeach;
    }



    $get_calc_shortcode = $loanCalculator->get_shortcode_key($calc_frm_id);

    $newshortcodeObj = new savingsLoanCalculator($get_calc_shortcode->shortcode_key);
    $new_shortcode_settings = $newshortcodeObj->store_calculator_settings();

    $new_shortcode_settings->cal_title->title = $calculator_title['title'];
    $new_shortcode_settings->cal_title->show = $calculator_title['show'];


    $new_shortcode_settings->currency->title = $currency['title'];
    $new_shortcode_settings->currency->dropmenu = $currency['options'];

    $new_shortcode_settings->amount->title = $amount['title'];
    /* $new_shortcode_settings->amount->min = $amount['min'];
      $new_shortcode_settings->amount->max = $amount['max']; */
    /* $new_shortcode_settings->amount->min_error_msg = $amount['min_error_msg'];
      $new_shortcode_settings->amount->max_error_msg = $amount['max_error_msg']; */


    $new_shortcode_settings->months->title = $months['title'];
    /* $new_shortcode_settings->months->min = $months['min'];
      $new_shortcode_settings->months->max = $months['max'];
      $new_shortcode_settings->months->min_error_msg = $months['min_error_msg'];
      $new_shortcode_settings->months->max_error_msg = $months['max_error_msg']; */

    $new_shortcode_settings->savings_type->title = $savings_type['title'];
    $new_shortcode_settings->savings_type->saving_options = $savings_options_arr;

    $new_shortcode_settings->percentage->title = $percentage['title'];
    $new_shortcode_settings->percentage->show = $percentage['show'];

    $new_shortcode_settings->monthly_payment->title = $monthly_payment['title'];
    $new_shortcode_settings->monthly_payment->top_msg = stripslashes($monthly_payment['top_msg']);
    $new_shortcode_settings->monthly_payment->bottom_msg = stripslashes($monthly_payment['bottom_msg']);
    $new_shortcode_settings->monthly_payment->show = $monthly_payment['show'];
    $new_shortcode_settings->monthly_payment->top_show = $monthly_payment['top_show'];

    $new_shortcode_settings->message_restriction->title = stripslashes($message_restrictions['title']);
    $new_shortcode_settings->message_restriction->show = $message_restrictions['show'];

    $new_shortcode_settings->bg_color->show = $background['show'];

    $new_shortcode_settings->cal_button->show = $cal_button['show'];
    $new_shortcode_settings->cal_button->title = $cal_button['title'];


    /* Save the data */

    /* save the record */
    $data = array(
        'cal_title' => json_encode($new_shortcode_settings->cal_title),
        'currency' => json_encode($new_shortcode_settings->currency),
        'amount' => json_encode($new_shortcode_settings->amount),
        'monthly_payment' => json_encode($new_shortcode_settings->monthly_payment),
        'months' => json_encode($new_shortcode_settings->months),
        'percentage' => json_encode($new_shortcode_settings->percentage),
        'savings_type' => json_encode($new_shortcode_settings->savings_type),
        'message_restriction' => json_encode($new_shortcode_settings->message_restriction),
        'bg_color' => json_encode($new_shortcode_settings->bg_color),
        'cal_button' => json_encode($new_shortcode_settings->cal_button)
    );


    $newshortcodeObj->save_calc_shortcode($data);

    $msg = '<div class="updated" id="message"><p>Settings successfully saved</p></div>';
    set_transient('calc_settings_msg', $msg, 30);

    wp_safe_redirect(SMART_SAVINGS_CALC_ADMIN_PAGE . '&calc_frm_id=' . $calc_frm_id);
    exit;
}
?>


<h2 class="smt_top_header"><?php echo $calc_shortcode_text; ?> Savings Calculator</h2>
<?php if (!$calc_frm_id): ?>
    <?php
    echo $smt_error_msg = get_transient('smart_calc_error');
    delete_transient('smart_calc_error');
    ?>
    <form id="" name="shortcode_frm_settings" class="" method="post">
        <table class="widefat">
            <tr>
                <td>Enter Shortcode key</td>
                <td><input type="text" size="32" required="true" name="calc_shortcode_key" value="" placeholder="give unique key(ex: test)" /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>Generated Shortcode will be [smartcalcsavings ID="test"]</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <div class="">
                        <button class="button-primary" type="submit" name="create_calc_shortcode_btn"><?php echo $calc_shortcode_text; ?></button>
                    </div>
                </td>
            </tr>
        </table>

    </form>
<?php else : ?>
    <?php
    echo get_transient('calc_settings_msg');
    delete_transient('calc_settings_msg');

    $currency_box_color = ($get_shortcode_settings->currency->box_color != '') ? $get_shortcode_settings->currency->box_color : '#ebf0f4';
    $currency_text_color = ($get_shortcode_settings->currency->text_color != '') ? $get_shortcode_settings->currency->text_color : '#ffffff';


    $options_box_color = ($get_shortcode_settings->savings_type->box_color != '') ? $get_shortcode_settings->savings_type->box_color : '#ebf0f4';

    $options_text_color = ($get_shortcode_settings->savings_type->text_color != '') ? $get_shortcode_settings->savings_type->text_color : '#ffffff';

    $backgorund_tarnsp = hex2rgb($get_shortcode_settings->bg_color->box_color);
    $background_style = ($get_shortcode_settings->bg_color->image_apply == 1) ? 'url(\'' . $get_shortcode_settings->bg_color->file . '\')' : '' .$get_shortcode_settings->bg_color->box_color. '';
    ?>

    <style type="text/css">
        .sbHolder{ background: <?php echo $currency_box_color; ?> !important;}
        .sbSelector{ color: <?php echo $currency_text_color; ?> !important;}
        .sbOptions li a{ color: #333 !important;}

        .sbHolder1{ background: <?php echo $options_box_color; ?> !important;}
        .sbSelector1{ color: <?php echo $options_text_color; ?> !important;}
        .sbOptions1 li a{ color: #333 !important;}

        .sbToggle{ background: url(<?php echo SMART_CALC_URL; ?>images/select-white.png) 3px 12px no-repeat !important;}
    </style>
    <div class="cal-plug-page">
        <h2 class="nav-tab-wrapper">
            <a href="#" data-toggle="calc_settings_tab" class="nav-tab smart_tab nav-tab-active">Calculator Settings</a>
            <a href="#" data-toggle="switcher_tab" class="nav-tab smart_tab">Style Switcher</a>

        </h2>
        <div class="smt-tab-contant">

            <!-- Calc SEttings -->

            <div id="calc_settings_tab">
                <form name="smt_cal_settings_frm" id="" method="post" action="">
                    <div class="calc_settings_part">
                        <div class="calc_head_icon"><img src="<?php echo plugins_url() . '/smartcalculator/images/loan-icon.png'; ?>"></div>
                        <header>Smart Loan Calculator</header>
                        <div class="sub-heading">Calculator Editor</div>
                    </div>

                    <div class="calc_settings_part">
                        <h2>top title box</h2>
                        <div class="cals_form_field">
                            <label>Set top box title</label>
                            <input class="shortcode_sett_title" name="cal_title[title]" type="text" value="<?php echo $get_shortcode_settings->cal_title->title; ?>" data-placeholder="Calculate your Credit" placeholder="Calculate your Credit">

                        </div>
                        <div class="cals_form_field">
                            <div class="layout_box loan-smart-right-step4 loan-radio-sec">
                                <input type="radio" id="r1" name="cal_title[show]" <?php checked('1', $get_shortcode_settings->cal_title->show); ?> value="1"/>
                                <label for="r1"><span></span>Show</label> 
                                <input type="radio" id="r2" name="cal_title[show]" <?php checked('0', $get_shortcode_settings->cal_title->show); ?> value="0"/>
                                <label for="r2"><span></span>Hide</label>
                            </div>
                        </div>
                    </div>
                    <div class="calc_settings_part">
                        <h2>Currency Box</h2>
                        <div class="cals_form_field">
                            <label>Set currency box title</label>
                            <input type="text" class="shortcode_sett_title" name="currency[title]" value="<?php echo $get_shortcode_settings->currency->title; ?>" data-placeholder="Enter currency title" placeholder="Enter currency title">

                        </div>

                    </div>
                    <div class="calc_settings_part">
                        <h2>Amount Box</h2>
                        <div class="cals_form_field">
                            <label>Set amount box title</label>  
                            <input type="text" class="shortcode_sett_title" name="amount[title]" value="<?php echo $get_shortcode_settings->amount->title; ?>" data-placeholder="Request amount" placeholder="Request amount">
                            <!-- <span class="edit-icon"><a class="smart_title_edit" href="#"><img src="<?php echo plugins_url() . '/smartcalculator/images/edit.png'; ?>"> Edit text</a></span> -->
                        </div>

                    </div>
                    <div class="calc_settings_part">
                        <h2>months Box</h2>
                        <div class="cals_form_field">
                            <label>Set months box title</label>  
                            <input type="text" class="shortcode_sett_title" name="months[title]" value="<?php echo $get_shortcode_settings->months->title; ?>" data-placeholder="Enter month box title" placeholder="Enter month box title">
                            <!-- <span class="edit-icon"><a class="smart_title_edit" href="#"><img src="<?php echo plugins_url() . '/smartcalculator/images/edit.png'; ?>"> Edit text</a></span> -->
                        </div>



                    </div>
                    <div class="calc_settings_part">
                        <h2>Type of Savings</h2>
                        <div class="cals_form_field">
                            <label>Set Savings box title</label>
                            <input type="text" class="shortcode_sett_title" name="savings_type[title]" value="<?php echo $get_shortcode_settings->savings_type->title; ?>" data-placeholder="Enter type of savings title" placeholder="Enter type of savings title">
                            <!-- <span class="edit-icon"><a class="smart_title_edit" href="#"><img src="<?php echo plugins_url() . '/smartcalculator/images/edit.png'; ?>"> Edit text</a></span> -->
                        </div>
                        <div class="cals_form_field">
                            <label>Add Type Of Savings</label>
                            <div class="calc-btn"><a href="#" id="smt_add_savings">+ Add</a></div>
                            <div class="add_select_wrap saving_box_wrap">

                                <?php
                                $savings_type_settings = $get_shortcode_settings->savings_type->saving_options;
                                /* echo "<pre>";
                                  print_r($savings_type_settings);
                                  echo "</pre>"; */
                                ?>
                                <?php if (is_object($get_shortcode_settings->savings_type->saving_options) || is_array($get_shortcode_settings->savings_type->saving_options) && count($get_shortcode_settings->savings_type->saving_options) > 0): ?>
                                    <?php
                                    $i = 0;
                                    foreach ($savings_type_settings as $key => $val):
                                        ?>
                                        <div class="savings_box_wrap">
                                            <div class="saving-del"><a href="#" class="del_saving_box"><img src="<?php echo SMART_CALC_URL . 'images/cross.png'; ?>"></a></div>
                                            <div class="first-input-wrap"><input type="text" name="savings_type[name][<?php echo $i; ?>]" class="shortcode_sett_title" value="<?php echo $key; ?>" data-placeholder="Enter type of savings" placeholder="Enter type of savings"></div>
                                            <?php
                                            $j = 0;
                                            foreach ($val as $curr => $mo):
                                                ?>
                                                <div class="second-input-wrap" data-count="<?php echo $j; ?>">
                                                    <div class="saving_box_inn">
                                                        <div class="saving_box_single"><input type="text" name="savings_type[currency][<?php echo $i; ?>][<?php echo $j; ?>]" value="<?php echo $curr; ?>" data-placeholder="USD,$" placeholder="USD,$" class="shortcode_sett_title sv_currency_field">
                                                            <?php if ($j == 0): ?>
                                                                <span><a href="#" class="add_sv_currency">Add</a></span>
                                                            <?php endif; ?>   
                                                        </div>
                                                    </div>


                                                    <div class="saving_box_inn">
                                                        <?php
                                                        $k = 0;
                                                        foreach ($mo as $m => $v):
                                                            ?>
                                                            <div class="saving_box_single"><input type="text" name="savings_type[months][<?php echo $i; ?>][<?php echo $j; ?>][<?php echo $k; ?>]" value="<?php echo $v; ?>" data-placeholder="Months,%" placeholder="Months,%" class="shortcode_sett_title sv_months_field">
                                                                <?php if ($k == 0): ?>
                                                                    <span><a href="#" class="add_sv_months">Add</a></span>
                                                                <?php else: ?>
                                                                    <a href="#" class="del_saving_month"><img src="<?php echo SMART_CALC_URL . '/images/cross.png' ?>"></a>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php
                                                            $k++;
                                                        endforeach;
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                                $j++;
                                            endforeach;
                                            ?>
                                            <div class="clear"></div>
                                        </div>
                                        <?php
                                        $i++;
                                    endforeach;
                                    ?>
                                <?php endif; ?>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="calc_settings_part">
                        <h2>Percentage box</h2>
                        <div class="cals_form_field">
                            <label>Set percentage box title</label>
                            <input type="text" class="shortcode_sett_title" name="percentage[title]" data-placeholder="Calculate your Credit" placeholder="Calculate your Credit" value="<?php echo $get_shortcode_settings->percentage->title; ?>">
                            <!-- <span class="edit-icon"><a class="smart_title_edit" href="#"><img src="<?php echo plugins_url() . '/smartcalculator/images/edit.png'; ?>"> Edit text</a></span> -->
                        </div>
                        <div class="cals_form_field">
                            <div class="layout_box loan-smart-right-step4 loan-radio-sec">
                                <input type="radio" id="r9" name="percentage[show]" <?php checked('1', $get_shortcode_settings->percentage->show); ?> value="1"/>
                                <label for="r9"><span></span>Show</label> 
                                <input type="radio" id="r10" name="percentage[show]" <?php checked('0', $get_shortcode_settings->percentage->show); ?> value="0"/>
                                <label for="r10"><span></span>Hide</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="calc_settings_part">
                        <h2>Calculator Button</h2>
                        <div class="cals_form_field">
                            <label>Set button title</label>
                            <input type="text" class="shortcode_sett_title" name="cal_button[title]" data-placeholder="Enter button title" placeholder="Enter button title" value="<?php echo $get_shortcode_settings->cal_button->title; ?>">
                        </div>
                        <div class="cals_form_field">
                            <div class="layout_box loan-smart-right-step4 loan-radio-sec">
                                <input type="radio" id="r13" name="cal_button[show]" <?php checked('1', $get_shortcode_settings->cal_button->show); ?> value="1"/>
                                <label for="r13"><span></span>Show</label> 
                                <input type="radio" id="r14" name="cal_button[show]" <?php checked('0', $get_shortcode_settings->cal_button->show); ?> value="0"/>
                                <label for="r14"><span></span>Hide</label>
                            </div>
                        </div>

                    </div>
                    
                    <div class="calc_settings_part">
                        <h2>Payment Box Result</h2>
                        <div class="twopart_section">
                            <div class="cals_field-left">
                                <div class="cals_form_field">
                                    <label>Set payment top comment</label>
                                    <textarea class="shortcode_sett_title" name="monthly_payment[top_msg]" data-placeholder="We estimate your payment monthly in" placeholder="We estimate your payment monthly in"><?php echo $get_shortcode_settings->monthly_payment->top_msg; ?></textarea>
                                   <!--  <span class="edit-icon edit_align_middle"><a class="smart_title_edit" href="#"><img src="<?php echo plugins_url() . '/smartcalculator/images/edit.png'; ?>"> Edit text</a></span> -->
                                </div>
                                <div class="cals_form_field">
                                    <div class="layout_box loan-smart-right-step4 loan-radio-sec">
                                        <input type="radio" id="r7" name="monthly_payment[top_show]" <?php checked('1', $get_shortcode_settings->monthly_payment->top_show); ?> value="1"/>
                                        <label for="r7"><span></span>Show</label> 
                                        <input type="radio" id="r8" name="monthly_payment[top_show]" <?php checked('0', $get_shortcode_settings->monthly_payment->top_show); ?> value="0"/>
                                        <label for="r8"><span></span>Hide</label>
                                    </div>
                                </div>
                            </div>
                            <div class="cals_field-left">
                                <div class="cals_form_field">
                                    <label>Set payment bottom comment</label>
                                    <textarea class="shortcode_sett_title" name="monthly_payment[bottom_msg]" data-placeholder="Base in yourdetails." placeholder="Base in yourdetails."><?php echo $get_shortcode_settings->monthly_payment->bottom_msg; ?></textarea>
                                    <!-- <span class="edit-icon edit_align_middle"><a class="smart_title_edit" href="#"><img src="<?php echo plugins_url() . '/smartcalculator/images/edit.png'; ?>"> Edit text</a></span> -->
                                </div>
                                <div class="cals_form_field">
                                    <div class="layout_box loan-smart-right-step4 loan-radio-sec">
                                        <input type="radio" id="r3" name="monthly_payment[show]" <?php checked('1', $get_shortcode_settings->monthly_payment->show); ?> value="1"/>
                                        <label for="r3"><span></span>Show</label> 
                                        <input type="radio" id="r4" name="monthly_payment[show]" <?php checked('0', $get_shortcode_settings->monthly_payment->show); ?> value="0"/>
                                        <label for="r4"><span></span>Hide</label>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="cals_form_field">
                            <label>Set payment restriction comment</label>
                            <textarea class="shortcode_sett_title" name="message_restrictions[title]" data-placeholder="*This is a pre approved credit, but you must submit the necessary documentation before signing your credit." placeholder="*This is a pre approved credit, but you must submit the necessary documentation before signing your credit."><?php echo $get_shortcode_settings->message_restriction->title; ?></textarea>
                            <!-- <span class="edit-icon edit_align_middle"><a class="smart_title_edit" href="#"><img src="<?php echo plugins_url() . '/smartcalculator/images/edit.png'; ?>"> Edit text</a></span> -->
                        </div>
                        <div class="cals_form_field">
                            <div class="layout_box loan-smart-right-step4 loan-radio-sec">
                                <input type="radio" id="r5" name="message_restrictions[show]" <?php checked('1', $get_shortcode_settings->message_restriction->show); ?> value="1"/>
                                <label for="r5"><span></span>Show</label> 
                                <input type="radio" id="r6" name="message_restrictions[show]" <?php checked('0', $get_shortcode_settings->message_restriction->show); ?> value="0"/>
                                <label for="r6"><span></span>Hide</label>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="calc_settings_part">
                        <h2>Calculator color background</h2>
                        <div class="cals_form_field">
                            <div class="layout_box loan-smart-right-step4 loan-radio-sec">
                                <input type="radio" id="r12" name="background_color[show]" <?php checked('1', $get_shortcode_settings->bg_color->show); ?> value="1"/>
                                <label for="r12"><span></span>Show</label> 
                                <input type="radio" id="r11" name="background_color[show]" <?php checked('0', $get_shortcode_settings->bg_color->show); ?> value="0"/>
                                <label for="r11"><span></span>Hide</label>
                            </div>
                        </div>

                    </div>
                    
                    <div class="cals-save-btn">
                        <input type="submit" name="shortcode_settings_btn" value="SAVE">
                    </div>
                </form>
                <div class="clear"></div>
            </div>

            <!-- End Calc SEttings -->

            <div id="switcher_tab" style="display: none;">
                <form name="edit_cal_shortcode_swticher_frm" id="edit_cal_shortcode_swticher_frm" method="post" action="" enctype="multipart/form-data">

                    <div class="wrapper" style="overflow: hidden;">
                        <div class="left_wrapper">
                            <div class="pick-color-sec">
                                <h3><span class="drop-icon"><img src="<?php echo plugins_url() . '/smartcalculator/images/drop.png'; ?>"></span> Color Selector </h3>
                                <ul class="pick-color-inner">
                                    <li>
                                        <label>Title color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="color" data-id="preview_cal_title" style="background: <?php echo $get_shortcode_settings->cal_title->text_color; ?>" name="cal_title[text_color]" class="" value="<?php echo $get_shortcode_settings->cal_title->text_color; ?>"></div>
                                    </li>
                                    <li>
                                        <label>Subtitle color text</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="color" data-id="calc-label" style="background: <?php echo $get_shortcode_settings->cal_title->box_color; ?>" name="cal_title[box_color]" class="" value="<?php echo $get_shortcode_settings->cal_title->box_color; ?>"></div>
                                    </li>

                                </ul> 


                                <ul class="pick-color-inner">

                                    <li>
                                        <label>Type of savings text color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="color" data-id="preview_options" style="background: <?php echo $get_shortcode_settings->savings_type->text_color; ?>" name="savings_type[text_color]" class="" value="<?php echo $get_shortcode_settings->savings_type->text_color; ?>"></div>
                                    </li>
                                    <li>
                                        <label>Type of savings box color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="background" data-id="preview_options" style="background: <?php echo $get_shortcode_settings->savings_type->box_color; ?>" name="savings_type[box_color]" class="" value="<?php echo $get_shortcode_settings->savings_type->box_color; ?>"></div>
                                    </li>

                                    <li>
                                        <label>Currency text color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="color" data-id="preview_currency" style="background: <?php echo $get_shortcode_settings->currency->text_color; ?>" name="currency[text_color]" class="" value="<?php echo $get_shortcode_settings->currency->text_color; ?>"></div>
                                    </li>
                                    <li>
                                        <label>Currency box color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="background" data-id="preview_currency" style="background: <?php echo $get_shortcode_settings->currency->box_color; ?>" name="currency[box_color]" class="" value="<?php echo $get_shortcode_settings->currency->box_color; ?>"></div>
                                        <div class="color-pick-box"></div>
                                    </li>

                                    <li>
                                        <label>Amount text color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="color" data-id="preview_amount" style="background: <?php echo $get_shortcode_settings->amount->text_color; ?>" name="amount[text_color]" class="" value="<?php echo $get_shortcode_settings->amount->text_color; ?>"></div>
                                    </li>
                                    <li>
                                        <label>Amount box color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="background" data-id="preview_amount" style="background: <?php echo $get_shortcode_settings->amount->box_color; ?>" name="amount[box_color]" class="" value="<?php echo $get_shortcode_settings->amount->box_color; ?>"></div>
                                    </li>

                                    <li>
                                        <label>Month text color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="color" data-id="preview_months" style="background: <?php echo $get_shortcode_settings->months->text_color; ?>" name="months[text_color]" class="" value="<?php echo $get_shortcode_settings->months->text_color; ?>"></div>
                                    </li>
                                    <li>
                                        <label>Month box color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="background" data-id="preview_months" style="background: <?php echo $get_shortcode_settings->months->box_color; ?>" name="months[box_color]" class="" value="<?php echo $get_shortcode_settings->months->box_color; ?>"></div>
                                    </li>

                                    <li>
                                        <label>Percentage text color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="color" data-id="preview_percentage_text" style="background: <?php echo $get_shortcode_settings->percentage->text_color; ?>" name="percentage[text_color]" class="" value="<?php echo $get_shortcode_settings->percentage->text_color; ?>"></div>
                                    </li>
                                    <li>
                                        <label>Percentage box color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="background" data-id="preview_percentage_box" style="background: <?php echo $get_shortcode_settings->percentage->box_color; ?>" name="percentage[box_color]" class="" value="<?php echo $get_shortcode_settings->percentage->box_color; ?>"></div>
                                    </li>

                                    <div class="clear"></div>
                                </ul>

                                <ul class="pick-color-inner">
                                     <li>
                                        <label>Button text color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="color" data-id="preview_cal_button" style="background: <?php echo $get_shortcode_settings->cal_button->text_color; ?>" name="cal_button[text_color]" class="" value="<?php echo $get_shortcode_settings->cal_button->text_color; ?>"></div>
                                    </li>
                                    <li>
                                        <label>Button color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="background" data-id="preview_cal_button" style="background: <?php echo $get_shortcode_settings->cal_button->box_color; ?>" name="cal_button[box_color]" class="" value="<?php echo $get_shortcode_settings->cal_button->box_color; ?>"></div>
                                    </li>
                                </ul>

                                <ul class="pick-color-inner">
                                    <li>
                                        <label>Payment text color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="color" data-id="preview_payment_text" style="background: <?php echo $get_shortcode_settings->monthly_payment->text_color; ?>" name="monthly_payment[text_color]" class="" value="<?php echo $get_shortcode_settings->monthly_payment->text_color; ?>"></div>
                                    </li>
                                    <li>
                                        <label>Bottom text color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="color" data-id="preview_message" style="background: <?php echo $get_shortcode_settings->message_restriction->text_color; ?>" name="message_restriction[text_color]" class="" value="<?php echo $get_shortcode_settings->message_restriction->text_color; ?>"></div>
                                    </li>
                                    <li>
                                        <label>Top background color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="background" data-id="preview_top_box" style="background: <?php echo $get_shortcode_settings->monthly_payment->box_color; ?>" name="monthly_payment[box_color]" class="" value="<?php echo $get_shortcode_settings->monthly_payment->box_color; ?>"></div>
                                    </li>
                                </ul> 

                                <ul class="pick-color-inner">
                                    <li>
                                        <label>Background color</label>
                                        <div class="sh_witcher pick-color-input"><input type="text" data-change="background" data-id="preview_background" style="background: <?php echo $get_shortcode_settings->bg_color->box_color; ?>" name="background_color" class="" value="<?php echo $get_shortcode_settings->bg_color->box_color; ?>"></div>
                                        <div class="color-pick-box"></div>
                                    </li>
                                    <li>
                                        <label>Background Image</label>
                                        <div class="bg_up_btn">
                                            <input type="file" id="bg_file" name="background_file" style="border: none;background: none; height: auto; display: none;">
                                            <div class="custom-brow"><a href="#" id="bg_initiate" style="background: #14517A;">Upload</a></div>
                                        </div>



                                        <div class="color-pick-box"></div>
                                        <?php if ($get_shortcode_settings->bg_color->file != ''): ?>
                                            <div class="bg_up_img"><img width="50px" height="50px" src="<?php echo $get_shortcode_settings->bg_color->file; ?>" /></div>
                                        <?php endif; ?>
                                    </li>

                                    <li>
                                        <label style="margin-right: 24px;">Apply Background</label>
                                        <div class="cals_form_field">
                                            <div class="layout_box loan-smart-right-step4 loan-radio-sec">
                                                <input type="radio" id="r30" name="background_image_apply" <?php checked('0', $get_shortcode_settings->bg_color->image_apply); ?> value="0"/>
                                                <label for="r30"><span></span>Color</label> 
                                                <input type="radio" id="r31" name="background_image_apply" <?php checked('1', $get_shortcode_settings->bg_color->image_apply); ?> value="1"/>
                                                <label for="r31"><span></span>Image</label>
                                            </div>
                                        </div>
                                    </li>
                                </ul> 

                            </div>

                            <div class="prev-cal">
                                <div class="smart_calc_wrapper" id="preview_background" style=" width:550px; background:<?php echo $background_style; ?>; padding-bottom: 50px;">
                                    <h2><span id="preview_cal_title" style="color:<?php echo $get_shortcode_settings->cal_title->text_color; ?>">Calculator Preview</span></h2>
                                    <div class="smart_calc_main_form" style="">
                                        <div class="smart_calc_top" id="preview_top_box" style="background:<?php echo $get_shortcode_settings->monthly_payment->box_color; ?>; padding: 15px 0 0 0;">

                                            <div class="smart_calc_sec">
                                                <div class="button custom-select ff-hack">
                                                    <label class="calc-label" style=""><img src="<?php echo SMART_CALC_URL; ?>/images/icon-saving.png" /></label>
                                                    <select id="preview_options" class="smart_opt_selectbox" name="smart_options">
                                                        <option value="">Type of savings</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="smart_calc_sec">
                                                <div class="button custom-select ff-hack">
                                                    <label class="calc-label" style=""><img src="<?php echo SMART_CALC_URL; ?>/images/icon-currency.png" /></label>
                                                    <select class="smart_selectbox" id="preview_currency" name="smart_currency">
                                                        <option value="">Currency</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="smart_calc_sec">
                                                <label class="calc-label" style=""><img src="<?php echo SMART_CALC_URL; ?>/images/icon-amount.png" /></label>
                                                <input type="text" id="preview_amount" style="background:<?php echo $get_shortcode_settings->amount->box_color; ?>; color:<?php echo $get_shortcode_settings->amount->text_color; ?>;" name="smart_loan_amount" value="Amount" placeholder="Amount" />
                                            </div>
                                            <div class="smart_calc_sec">
                                                <label class="calc-label" style=""><img src="<?php echo SMART_CALC_URL; ?>/images/icon-month.png" /></label>
                                                <input type="text" id="preview_months" style="background:<?php echo $get_shortcode_settings->months->box_color; ?>; color:<?php echo $get_shortcode_settings->months->text_color; ?>;" name="smart_loan_months" value="Months" placeholder="Months">
                                            </div>

                                            <div class="smart_calc_sec" id="preview_percentage_box" style="background:<?php echo $get_shortcode_settings->percentage->box_color; ?>;">
                                                <p class="form-txt" style=" color: <?php echo $get_shortcode_settings->percentage->text_color; ?>" id="preview_percentage_text">18% percentage per year</p>
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="smart_calc_sec" style="text-align:center;">
                                              
                                                <input id="preview_cal_button" style="float: none; border-radius:0px;border:none;line-height: 25px;width:80%;background:<?php echo $get_shortcode_settings->cal_button->box_color?>;color:<?php echo $get_shortcode_settings->cal_button->text_color ?>" type="button" value="<?php echo $get_shortcode_settings->cal_button->title ?>" />
                                            </div>

                                        <div class="smart_calc_bottom" id="preview_payment_box" style="background:#ffffff;padding:0px 0px 15px 0px;">

                                           

                                            <div class="smart_message_top_section" >
                                                <div class="smart_message_top preview_message" style="color:<?php echo $get_shortcode_settings->message_restriction->text_color; ?>">
                                                    <span id="smart_calc_top_msg">Payment Top Message</span>
                                                </div>
                                                <div class="smart_monthly_payment" id="preview_payment_text" style="color:<?php echo $get_shortcode_settings->monthly_payment->text_color; ?>;">
                                                    <span>$ 2888.02</span>
                                                </div>
                                                <div class="smart_message_bottom preview_message" style="color:<?php echo $get_shortcode_settings->message_restriction->text_color; ?>">
                                                    <span id="smart_calc_bottom_msg">Payment Bottom Message</span>
                                                </div>
                                            </div>

                                            <div class="smart_message_restrictions preview_message" style="color:<?php echo $get_shortcode_settings->message_restriction->text_color; ?>">
                                                <span id="smart_message_restrictions_text">*Message restriction</span>
                                            </div>

                                        </div>

                                    </div>
                                </div>   
                            </div>

                        </div>

                    </div>
                    <!--loan-save-btn -->
                    <div class="cals-save-btn"><input type="submit" name="smart_style_switcher_btn" value="Save" /></div>
                </form>
            </div>

            <div class="clear"></div>
        </div>

    </div>

<?php
endif;