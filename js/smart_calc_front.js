jQuery(document).ready(function($) {

    $('select.smart_selectbox').selectbox({
        /*onChange: function(val, inst) {
         //$('#currency_msg').siblings('.smart_loader').show();
         if(inst.id == 'smart_savings_type_curr') {
         $('form#smart_savings_loan_cal_frm').trigger('submit');
         } else {
         $('form#smart_loan_cal_frm').trigger('submit');
         }
         
         }*/
    });

    $('select.loan_dropdown_select').selectbox({
        onChange: function(val, inst) {
            window.location.href = $('#smt_current_url').val() + '?cal_id=' + val;
        }
    });

    $('select.smart_opt_selectbox').selectbox({
        classHolder: 'sbHolder1',
        classSelector: 'sbSelector1',
        classOptions: 'sbOptions1',
        onChange: function(val, inst) {
            //$('#options_msg').siblings('.smart_loader').show();
            if (inst.id == 'smart_savings_type') {
                $('select.smart_selectbox').selectbox('detach');
                $('select.smart_selectbox').html('<option value="">Currency</option>');
                $('select.smart_selectbox').selectbox('attach');

                $('form#smart_savings_loan_cal_frm').trigger('submit');
            } else {
                if ($('#l_cal_button').length == 0) {
                    $('form#smart_loan_cal_frm').trigger('submit');
                }
            }

        }
    });

    $(document.body).on('click', 'ul.sbOptions', function() {
        if ($(this).closest('form').prop('id') == 'smart_savings_loan_cal_frm') {
            $('form#smart_savings_loan_cal_frm').trigger('submit');
        } else {
            $('form#smart_loan_cal_frm').trigger('submit');
        }
    });

    /*Submit the form when activity occurred*/
    $('form#smart_loan_cal_frm').on('submit', function(e) {
        e.preventDefault();
        var currncy_field = $('select[name="smart_currency"]').val();
        var display_amount_field = $('input[name="smart_display_loan_amount"]').val();

        var amount_field = $('input[name="smart_loan_amount"]').val();
        var month_field = $('input[name="smart_month"]').val();
        var options_field = $('select[name="smart_options_dropmenu"]').val();

        var symbol_section = $('span#cal_symbol');

        var replaced_amount = display_amount_field.replace(',', '');

        var hasCalButton = $('#l_cal_button');
        var calButtonAction = $('input#l_btn_action').val();

        $('input[name="smart_loan_amount"]').val(replaced_amount);

        var data = $(this).serialize();

        /*Hide All error div*/
        $('#amount_error_msg').hide().text('');
        $('#month_error_msg').hide().text('');

        $.post(smartCal.ajaxurl, data, function(resp) {
            //$('.smart_loader').hide();
            if (resp.flag === true) {
                symbol_section.html(' ' + resp.currency_symbol);
                $('input[name="smart_display_loan_amount"]').val(resp.currency_showing);
                $('input[name="smart_loan_amount"]').val(resp.currency_sym);

                /*$('#currency_msg').removeClass('uncheck').addClass('check').fadeIn();
                 $('#amount_msg').removeClass('uncheck').addClass('check').fadeIn();
                 $('#month_msg').removeClass('uncheck').addClass('check').fadeIn();
                 $('#options_msg').removeClass('uncheck').addClass('check').fadeIn();*/
                if (resp.percentage_text != '') {
                    $('#smart_calc_percentage').text(resp.percentage_text).fadeIn();
                }


                if (hasCalButton.length != 0 && calButtonAction == 1 && resp.results != '') {
                    $('#smart_monthly_payment_text').text(resp.results).fadeIn();
                    $('input#l_btn_action').val('');
                } else if (hasCalButton.length == 0 && resp.results != '') {
                    $('#smart_monthly_payment_text').text(resp.results).fadeIn();
                }
            } else {

                $('input[name="smart_display_loan_amount"]').val(resp.currency_showing);
                $('input[name="smart_loan_amount"]').val(resp.currency_sym);
                symbol_section.html(' ' + resp.currency_symbol);
                /*Reset All Values*/
                $('#smart_calc_percentage').text('').fadeOut();
                $('#smart_monthly_payment_text').text('').fadeOut();



                /*if (resp.currency_error == '' && currncy_field != '') {
                 $('#currency_msg').removeClass('uncheck').addClass('check').fadeIn();
                 } else if (currncy_field != '') {
                 $('#currency_msg').removeClass('check').addClass('uncheck').fadeIn();
                 
                 }*/

                if (resp.amount_error == '' && amount_field != '') {
                    //$('#amount_msg').removeClass('uncheck').addClass('check').fadeIn();
                } else if (amount_field != '') {
                    //$('#amount_msg').removeClass('check').addClass('uncheck').fadeIn();
                    $('#amount_error_msg').text(resp.amount_error).fadeIn();
                }
                if (resp.month_error == '' && month_field != '') {
                    //$('#month_msg').removeClass('uncheck').addClass('check').fadeIn();
                } else if (month_field != '') {
                    //$('#month_msg').removeClass('check').addClass('uncheck').fadeIn();
                    $('#month_error_msg').text(resp.month_error).fadeIn();
                }

                if (resp.percentage_error == '' && options_field != '') {
                    //$('#options_msg').removeClass('uncheck').addClass('check').fadeIn();
                } else if (options_field != '') {
                    //$('#options_msg').removeClass('check').addClass('uncheck').fadeIn();
                }


            }

        }, 'json');

    });

    $('form#smart_savings_loan_cal_frm').on('submit', function(e) {
        e.preventDefault();
        var savings_field = $('select[name="smart_savings_type"]').val();
        var currncy_field = $('select[name="smart_currency"]').val();
        var display_amount_field = $('input[name="smart_display_loan_amount"]').val();

        var amount_field = $('input[name="smart_loan_amount"]').val();
        var month_field = $('input[name="smart_month"]').val();
        var options_field = $('select[name="smart_options_dropmenu"]').val();

        var symbol_section = $('span#cal_symbol');

        var replaced_amount = display_amount_field.replace(',', '');

        $('input[name="smart_loan_amount"]').val(replaced_amount);


        var hasCalButton = $('#l_cal_button');
        var calButtonAction = $('input#l_btn_action').val();
        var data = $(this).serialize();

        /*Hide All error div*/
        $('#amount_error_msg').hide().text('');
        $('#month_error_msg').hide().text('');

        $.post(smartCal.ajaxurl, data, function(resp) {
            //$('.smart_loader').hide();
            if (resp.flag === true) {
                symbol_section.html(' ' + resp.currency_symbol);
                $('input[name="smart_display_loan_amount"]').val(resp.currency_showing);
                $('input[name="smart_loan_amount"]').val(resp.currency_sym);
                /*$('#savings_msg').removeClass('uncheck').addClass('check').fadeIn();
                 $('#currency_msg').removeClass('uncheck').addClass('check').fadeIn();
                 $('#amount_msg').removeClass('uncheck').addClass('check').fadeIn();
                 $('#month_msg').removeClass('uncheck').addClass('check').fadeIn();*/

                if (resp.percentage_text != '') {
                    $('#smart_calc_percentage').text(resp.percentage_text).fadeIn();
                }
                if (hasCalButton.length != 0 && calButtonAction == 1 && resp.results != '') {
                    $('#smart_monthly_payment_text').text(resp.results).fadeIn();
                    $('input#l_btn_action').val('');
                } else if (hasCalButton.length == 0 && resp.results != '') {
                    $('#smart_monthly_payment_text').text(resp.results).fadeIn();
                }
            } else {

                $('input[name="smart_display_loan_amount"]').val(resp.currency_showing);
                if (currncy_field != '') {
                    $('input[name="smart_loan_amount"]').val(resp.currency_sym);
                }
                symbol_section.html(' ' + resp.currency_symbol);
                /*Reset All Values*/
                $('#smart_calc_percentage').text('').fadeOut();
                $('#smart_monthly_payment_text').text('').fadeOut();

                if (resp.savings_type == '') {
                    //$('#savings_msg').removeClass('uncheck').addClass('check').fadeIn();
                }

                if (resp.currency_error == '' && currncy_field != '') {
                    //$('#currency_msg').removeClass('uncheck').addClass('check').fadeIn();
                } else if (currncy_field != '') {
                    //$('#currency_msg').removeClass('check').addClass('uncheck').fadeIn();

                }

                if (resp.currency_options != '') {
                    $('select[name="smart_currency"]').html(resp.currency_options);
                    $('select[name="smart_currency"]').selectbox("detach");

                    $('select[name="smart_currency"]').selectbox("attach");


                }

                if (resp.amount_error == '' && amount_field != '') {
                    // $('#amount_msg').removeClass('uncheck').addClass('check').fadeIn();
                } else if (amount_field != '') {
                    //$('#amount_msg').removeClass('check').addClass('uncheck').fadeIn();
                    $('#amount_error_msg').text(resp.amount_error).fadeIn();
                }
                if (resp.month_error == '' && month_field != '') {
                    $('#month_msg').removeClass('uncheck').addClass('check').fadeIn();
                } else if (month_field != '') {
                    //$('#month_msg').removeClass('check').addClass('uncheck').fadeIn();
                    $('#month_error_msg').text(resp.month_error).fadeIn();
                }

                /*if (resp.percentage_error == '' && options_field != '') {
                 $('#options_msg').removeClass('uncheck').addClass('check').fadeIn();
                 } else if (options_field != '')
                 $('#options_msg').removeClass('check').addClass('uncheck').fadeIn();*/


            }

        }, 'json');

    });

    /*$('form#smart_loan_cal_frm select').on('click', function() {
     $('form#smart_loan_cal_frm').trigger('submit');
     });*/

    $('form#smart_loan_cal_frm input').on('blur', function() {
        
            $(this).prop('placeholder', $(this).data('placeholder'));
       
        if ($('#l_cal_button').length == 0) {
            $('form#smart_loan_cal_frm').trigger('submit');
        }
    });

    $('form#smart_savings_loan_cal_frm input').on('blur', function() {
        
            $(this).prop('placeholder', $(this).data('placeholder'));
       
        if ($('#l_cal_button').length == 0) {
            $('form#smart_savings_loan_cal_frm').trigger('submit');
        }
    });

    $('form#smart_loan_cal_frm input[type="text"], form#smart_savings_loan_cal_frm input[type="text"]').on('focus', function() {
        $(this).prop('placeholder', '');
    });

    $('#l_cal_button').on('click', function(e) {
        $('input#l_btn_action').val('1');

        if ($(this).closest('form').prop('id') == 'smart_savings_loan_cal_frm') {
            $('form#smart_savings_loan_cal_frm').trigger('submit');
        } else {
            $('form#smart_loan_cal_frm').trigger('submit');
        }
    });
});

function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
}

function replaceNubmerFormat(num) {
    var res = num.toString().replace(',', '');
    return res;

}

function hexToRgb(hex, alpha) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    var toString = function() {
        if (this.alpha == undefined) {
            return "rgb(" + this.r + ", " + this.g + ", " + this.b + ")";
        }
        if (this.alpha > 1) {
            this.alpha = 1;
        } else if (this.alpha < 0) {
            this.alpha = 0;
        }
        return "rgba(" + this.r + ", " + this.g + ", " + this.b + ", " + this.alpha + ")";
    }
    if (alpha == undefined) {
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16),
            toString: toString
        } : null;
    }
    if (alpha > 1) {
        alpha = 1;
    } else if (alpha < 0) {
        alpha = 0;
    }
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16),
        alpha: alpha,
        toString: toString
    } : null;
}
