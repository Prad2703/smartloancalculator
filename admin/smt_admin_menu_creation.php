<?php
/**
 * Pages For Menu Creation
 */
add_action('admin_menu', 'smt_admin_menu_func');

function smt_admin_menu_func() {
    add_menu_page('Smart Calculator', 'Smart Calculator', 'manage_options', 'loan-smart-calculator', 'loan_smart_calculator_func', SMART_CALC_URL . 'images/calculater-icon.png');
    add_submenu_page('loan-smart-calculator', 'Loan Calculator', 'Loan Calculator', 'manage_options', 'loan-smart-calculator', 'loan_smart_calculator_func');
    add_submenu_page('loan-smart-calculator', 'Savings Calculator', 'Savings Calculator', 'manage_options', 'savings-calculator', 'savings_smart_calculator_func');
    add_submenu_page('loan-smart-calculator', 'Create Loan Shortcode', 'Create Loan Shortcode', 'manage_options', 'loan-smart-calculator&action=create', 'loan_smart_calculator_func');
    add_submenu_page('loan-smart-calculator', 'Create Savings Shortcode', 'Create Savings Shortcode', 'manage_options', 'savings-calculator&action=create', 'savings_smart_calculator_func');
    add_submenu_page('loan-smart-calculator', 'Dropdown Shortcode', 'Dropdown Shortcode', 'manage_options', 'dropdown-calculator', 'smart_drop_shortcode_calculator_func');
}

function loan_smart_calculator_func() {
    $loanCalculator = new loanCalculator();
    global $wpdb;
    /* Delete */
    if (isset($_GET['action']) && $_GET['action'] === 'del' && $_GET['frm_id'] != '') {

        $frm_id = $_GET['frm_id'];
        $query = "DELETE FROM " . SMART_CALC_TBL . " WHERE ID='$frm_id'";
        $wpdb->query($query);

        set_transient('smt_calc_del_msg', '<div class="updated" id="message"><p>Loan calculator shortcode deleted successfuly.</p></div>', 30);
        wp_safe_redirect(admin_url() . "admin.php?page=loan-smart-calculator");
        exit;
    }

    if (isset($_GET['duplicate_id']) && (int) trim($_GET['duplicate_id']) != 0) {
        $duplicate_from = strip_tags(trim($_GET['duplicate_id']));
        $query = "SELECT * FROM " . SMART_CALC_TBL . " WHERE ID='$duplicate_from'";
        $duplicate_cal_results = $wpdb->get_results($query);

        if (is_array($duplicate_cal_results) && count($duplicate_cal_results) > 0) {
            $new_calc_shortcode_key = 'loan_cal' . mt_rand(1, 222);
            $data = array(
                'shortcode_key' => $new_calc_shortcode_key,
                'cal_title' => $duplicate_cal_results[0]->cal_title,
                'currency' => $duplicate_cal_results[0]->currency,
                'amount' => $duplicate_cal_results[0]->amount,
                'monthly_payment' => $duplicate_cal_results[0]->monthly_payment,
                'months' => $duplicate_cal_results[0]->months,
                'percentage' => $duplicate_cal_results[0]->percentage,
                'options_dropmenu' => $duplicate_cal_results[0]->options_dropmenu,
                'message_restriction' => $duplicate_cal_results[0]->message_restriction,
                'bg_color' => $duplicate_cal_results[0]->bg_color,
                'create_date' => date('Y-m-d')
            );

            $loanCalculator->save_calc_shortcode($data);

            wp_safe_redirect(admin_url() . "admin.php?page=loan-smart-calculator");
            exit;
        }
    }
    ?>
    <div id="wpbody" role="main">
        <div class="wrap">
            <?php
            if (isset($_GET['action']) && $_GET['action'] === 'create') {
                require_once SMART_CALC_PATH . 'admin/smt_admin_calulator_settings.php';
            } else {

                /* Get All Loan Calculator shortcodes */
                $all_shortcodes = $loanCalculator->get_calculator_shortcodes();
                ?>

                <h2>Calculator Shortcodes<a href="<?php echo admin_url() . 'admin.php?page=loan-smart-calculator&action=create' ?>" class="add-new-h2">Add New</a></h2>

                <?php
                echo get_transient('smt_calc_del_msg');
                delete_transient('smt_calc_del_msg');
                ?>

                <br class="clear">
                <table class="wp-list-table widefat fixed striped posts">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column">Calculator Title</th>
                            <th scope="col" class="manage-column">Shortcode</th>
                            <th scope="col" class="manage-column">Create Date</th>
                            <th scope="col" class="manage-column">Action</th>
                        </tr>   
                    </thead>
                    <tbody id="the-list">
                        <?php if (is_array($all_shortcodes) && count($all_shortcodes) > 0) : ?>
                            <?php foreach ($all_shortcodes as $calc_shortcode): ?>
                                <?php
                                $cal = json_decode($calc_shortcode->cal_title);
                                ?>
                                <tr>
                                    <td><?php echo $cal->title; ?></td>
                                    <td><p class="add-new-h2">[smartcalc ID="<?php echo $calc_shortcode->shortcode_key; ?>"]</p></td>
                                    <td><?php echo date('Y-m-d', strtotime($calc_shortcode->create_date)); ?></td>

                                    <td><a href="<?php echo SMART_CALC_ADMIN_PAGE . '&calc_frm_id=' . $calc_shortcode->ID; ?>">Edit</a>|<a href="<?php echo admin_url() . 'admin.php?page=loan-smart-calculator&action=del&frm_id=' . $calc_shortcode->ID; ?>">Delete</a>|<a href="<?php echo SMART_CALC_ADMIN_PAGE . '&duplicate_id=' . $calc_shortcode->ID; ?>">Duplicate</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr class="no-items">
                                <td class="colspanchange" colspan="4">Please create loan calculator shortcode</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php
            }
            ?>
        </div>
    </div> 
    <?php
}

function savings_smart_calculator_func() {
    $loanCalculator = new savingsLoanCalculator();
    global $wpdb;
    /* Delete */
    if (isset($_GET['action']) && $_GET['action'] === 'del' && $_GET['frm_id'] != '') {

        $frm_id = $_GET['frm_id'];
        $query = "DELETE FROM " . SMART_SAVINGS_CALC_TBL . " WHERE ID='$frm_id'";
        $wpdb->query($query);

        set_transient('smt_calc_del_msg', '<div class="updated" id="message"><p>Loan calculator shortcode deleted successfuly.</p></div>', 30);
        wp_safe_redirect(admin_url() . "admin.php?page=savings-calculator");
        exit;
    }

    if (isset($_GET['duplicate_id']) && (int) trim($_GET['duplicate_id']) != 0) {
        $duplicate_from = strip_tags(trim($_GET['duplicate_id']));
        $query = "SELECT * FROM " . SMART_SAVINGS_CALC_TBL . " WHERE ID='$duplicate_from'";
        $duplicate_cal_results = $wpdb->get_results($query);

        if (is_array($duplicate_cal_results) && count($duplicate_cal_results) > 0) {
            $new_calc_shortcode_key = 'savings_cal' . mt_rand(1, 222);
            $data = array(
                'shortcode_key' => $new_calc_shortcode_key,
                'cal_title' => $duplicate_cal_results[0]->cal_title,
                'currency' => $duplicate_cal_results[0]->currency,
                'amount' => $duplicate_cal_results[0]->amount,
                'monthly_payment' => $duplicate_cal_results[0]->monthly_payment,
                'months' => $duplicate_cal_results[0]->months,
                'percentage' => $duplicate_cal_results[0]->percentage,
                'savings_type' => $duplicate_cal_results[0]->savings_type,
                'message_restriction' => $duplicate_cal_results[0]->message_restriction,
                'bg_color' => $duplicate_cal_results[0]->bg_color,
                'create_date' => date('Y-m-d')
            );

            $loanCalculator->save_calc_shortcode($data);

            wp_safe_redirect(admin_url() . "admin.php?page=savings-calculator");
            exit;
        }
    }
    ?>
    <div id="wpbody" role="main">
        <div class="wrap">
            <?php
            if (isset($_GET['action']) && $_GET['action'] === 'create') {
                require_once SMART_CALC_PATH . 'admin/smt_admin_savings_ca_settings.php';
            } else {

                /* Get All Loan Calculator shortcodes */
                $all_shortcodes = $loanCalculator->get_calculator_shortcodes();
                ?>

                <h2>Savings Calculator Shortcodes<a href="<?php echo admin_url() . 'admin.php?page=savings-calculator&action=create' ?>" class="add-new-h2">Add New</a></h2>

                <?php
                echo get_transient('smt_calc_del_msg');
                delete_transient('smt_calc_del_msg');
                ?>

                <br class="clear">
                <table class="wp-list-table widefat fixed striped posts">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column">Calculator Title</th>
                            <th scope="col" class="manage-column">Shortcode</th>
                            <th scope="col" class="manage-column">Create Date</th>
                            <th scope="col" class="manage-column">Action</th>
                        </tr>   
                    </thead>
                    <tbody id="the-list">
                        <?php if (is_array($all_shortcodes) && count($all_shortcodes) > 0) : ?>
                            <?php foreach ($all_shortcodes as $calc_shortcode): ?>
                                <?php
                                $cal = json_decode($calc_shortcode->cal_title);
                                ?>
                                <tr>
                                    <td><?php echo $cal->title; ?></td>
                                    <td><p class="add-new-h2">[smartcalcsavings ID="<?php echo $calc_shortcode->shortcode_key; ?>"]</p></td>
                                    <td><?php echo date('Y-m-d', strtotime($calc_shortcode->create_date)); ?></td>

                                    <td><a href="<?php echo SMART_SAVINGS_CALC_ADMIN_PAGE . '&calc_frm_id=' . $calc_shortcode->ID; ?>">Edit</a>|<a href="<?php echo admin_url() . 'admin.php?page=savings-calculator&action=del&frm_id=' . $calc_shortcode->ID; ?>">Delete</a>|<a href="<?php echo SMART_SAVINGS_CALC_ADMIN_PAGE . '&duplicate_id=' . $calc_shortcode->ID; ?>">Duplicate</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr class="no-items">
                                <td class="colspanchange" colspan="4">Please create savings loan calculator shortcode</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php
            }
            ?>
        </div>
    </div> 
    <?php
}

function smart_drop_shortcode_calculator_func() {
    
    if(isset($_POST['smart_dropdown_settings_btn'])) {
        
        update_option('_loan_title', $_POST['loan_title']);
        update_option('_loan_sv_title', $_POST['loan_savings_title']);
        
        wp_redirect(admin_url() .'admin.php?page=dropdown-calculator');
        exit;
    }
    
    $loan_title = get_option('_loan_title');
    $loan_sv_title = get_option('_loan_sv_title');
    ?>
    <div id="wpbody" role="main">
        <div class="wrap">
            <h2>Calculator Dropdown Shortcodes</h2>
            <form name="sv_frm_update" method="post" action="">
            <table class="widefat">
                <tr>
                    <td>Loan Calculator Dropdown Shortcode</td>
                    <td><label>Set Title <input type="text" name="loan_title" required="true" value="<?php echo $loan_title; ?>" /></label></td>
                    <td><p class="add-new-h2">[smartloan_dropdown]</p></td>
                </tr>
                <tr>
                    <td>Savings Calculator Dropdown Shortcode</td>
                    <td><label>Set Title <input type="text" name="loan_savings_title" required="true"  value="<?php echo $loan_sv_title; ?>" /></label></td>
                    <td><p class="add-new-h2">[smartsavings_dropdown]</p></td>
                </tr>
                <tr>
                    <td colspan="3"><input class="button-primary" type="submit" name="smart_dropdown_settings_btn" value="Save" /></td>
                    
                </tr>
            </table>
                </form>
        </div>
    </div>
    <?php
}
