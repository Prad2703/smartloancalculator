jQuery(document).ready(function($) {


    var currecny_val_box = '<div class="add_select_box currecncy_box"><input type="text" name="currency[options][]" value="" data-placeholder="USD,$,min,max" placeholder="USD,$,min,max"><a href="#" class="del_options_val"><img src="' + smartCal.cancelImage + '"></a></div>';
    var percentage_val_box = '<div class="add_select_box options_box"><input type="text" name="options_dropmenu[options][]" value="" data-placeholder="Mortgage,10%" placeholder="Mortgage,10%"><a href="#" class="del_options_val"><img src="' + smartCal.cancelImage + '"></a></div>';



    $('select.smart_selectbox').selectbox({
        onChange: function(val, inst) {
            $('form#smart_loan_cal_frm').trigger('submit');
        }
    });

    $('a#bg_initiate').on('click', function(e) {
        e.preventDefault();
        $('#bg_file').trigger('click');
    });

    $('select.smart_opt_selectbox').selectbox({
        classHolder: 'sbHolder1',
        classSelector: 'sbSelector1',
        classOptions: 'sbOptions1',
        onChange: function(val, inst) {
            $('form#smart_loan_cal_frm').trigger('submit');
        }
    });


    $('.smart_tab').on('click', function(e) {
        e.preventDefault();
        $('.smart_tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        $('#calc_settings_tab').toggle();
        $('#switcher_tab').toggle();

    });

    $('.sh_witcher').spectrum({
        preferredFormat: "hex",
        showInput: true,
        chooseText: "Ok",
        cancelText: "cancel",
        showAlpha: false,
        move: function(color) {
            var _hexColor = color.toHexString();
            $(this).children('input').val(color);
            $(this).children('input').css('background', color);
            var _target_id = $(this).children('input').data('id');
            var _change_prop = $(this).children('input').data('change');

            if (_target_id == 'preview_currency') {
                if (_change_prop == 'background')
                    $('.sbHolder').attr('style', _change_prop + ':' + _hexColor + ' !important;');
                else
                    $('.sbSelector').attr('style', _change_prop + ':' + _hexColor + ' !important;');

            } else if (_target_id == 'preview_options') {
                if (_change_prop == 'background')
                    $('.sbHolder1').attr('style', _change_prop + ':' + _hexColor + ' !important;');
                else
                    $('.sbSelector1').attr('style', _change_prop + ':' + _hexColor + ' !important;');
            } else if (_target_id == 'preview_message') {
                $('.' + _target_id).css(_change_prop, color);
            } else if (_target_id == 'preview_payment_box') {

                if (_change_prop == 'background') {
                    $('#' + _target_id).css(_change_prop, color);
                    $('#' + _target_id).css('border-color', 'transparent transparent ' + _hexColor + ';');
                } else
                    $('#' + _target_id).css(_change_prop, color);
            } else if (_target_id == 'calc-label') {
                $('.calc-label').css(_change_prop, color);
            } else if (_target_id == 'preview_background') {

                $('#' + _target_id).css(_change_prop, 'rgba(' + Math.round(color._r) + ',' + Math.round(color._g) + ',' + Math.round(color._b) + ',0.2)');
            } else {
                $('#' + _target_id).css(_change_prop, color);
            }
        },
        beforeShow: function() {
            $(this).spectrum('set', $(this).children('input').val());
        },
        change: function(color) {
            var _hexColor = color.toHexString();
            $(this).children('input').val(color);
            $(this).children('input').css('background', color);
            var _target_id = $(this).children('input').data('id');
            var _change_prop = $(this).children('input').data('change');

            if (_target_id == 'preview_currency') {
                if (_change_prop == 'background')
                    $('.sbHolder').attr('style', _change_prop + ':' + _hexColor + ' !important;');
                else
                    $('.sbSelector').attr('style', _change_prop + ':' + _hexColor + ' !important;');

            } else if (_target_id == 'preview_options') {
                if (_change_prop == 'background')
                    $('.sbHolder1').attr('style', _change_prop + ':' + _hexColor + ' !important;');
                else
                    $('.sbSelector1').attr('style', _change_prop + ':' + _hexColor + ' !important;');
            } else if (_target_id == 'preview_message') {
                $('.' + _target_id).css(_change_prop, color);
            } else if (_target_id == 'preview_payment_box') {

                if (_change_prop == 'background') {
                    $('#' + _target_id).css(_change_prop, color);
                    $('#' + _target_id).css('border-color', 'transparent transparent ' + _hexColor + ';');
                } else
                    $('#' + _target_id).css(_change_prop, color);
            } else if (_target_id == 'preview_background') {

                $('#' + _target_id).css(_change_prop, 'rgba(' + Math.round(color._r) + ',' + Math.round(color._g) + ',' + Math.round(color._b) + ',0.2)');
            } else {
                $('#' + _target_id).css(_change_prop, color);
            }
        }

    });

    $('.smart_title_edit').on('click', function(e) {
        e.preventDefault();

        $(this).parent().siblings('input,textarea').attr('placeholder', '');
    });

    $(document.body).on('blur', '.shortcode_sett_title', function() {
        if (($(this).prop('name') == 'amount[min]' || $(this).prop('name') == 'amount[max]') && $.trim($(this).val()) != '') {
            var n = replaceNubmerFormat($.trim($(this).val()));
            var number = parseFloat(n).toFixed(2);

            var formatted_number = formatNumber(number);
            $(this).val(formatted_number);
        }
        $(this).prop('placeholder', $(this).data('placeholder'));

    });

    $(document.body).on('click', '.shortcode_sett_title', function() {
        if (($(this).prop('name') == 'amount[min]' || $(this).prop('name') == 'amount[max]') && $.trim($(this).val()) != '') {
            var n = replaceNubmerFormat($.trim($(this).val()));
            var number = parseFloat(n).toFixed(2);

            var formatted_number = formatNumber(number);
            $(this).val(formatted_number);
        }
        $(this).prop('placeholder', '');

    });

    $('a#smt_currency_add').on('click', function(e) {
        e.preventDefault();

        $(this).parent('div').siblings('div.add_select_wrap').append(currecny_val_box);
        //$('div.currecncy_box').last().children('input').focus();

    });

    $('a#smt_add_percenatge').on('click', function(e) {
        e.preventDefault();

        $(this).parent('div').siblings('div.add_select_wrap').append(percentage_val_box);
        //$('div.options_box').last().children('input').prop('placeholder', '').focus();


    });

    $('a#smt_add_savings').on('click', function(e) {
        e.preventDefault();
        var savings_box_no = $('div.savings_box_wrap').length;

        var savings_box = '<div class="savings_box_wrap"><div class="saving-del"><a href="#" class="del_saving_box"><img src="' + smartCal.cancelImage + '"></a></div>';
        /*Savings Type Field*/
        savings_box += '<div class="first-input-wrap"><input type="text" name="savings_type[name][' + savings_box_no + ']" class="shortcode_sett_title" data-placeholder="Enter type of savings" placeholder="Enter type of savings"></div>';
        /*2nd box Wrapper*/
        savings_box += '<div class="second-input-wrap" data-count="0">';

        /*Currency Type Field*/
        savings_box += '<div class="saving_box_inn"><div class="saving_box_single"><input type="text" name="savings_type[currency][' + savings_box_no + '][0]" data-placeholder="USD,$" placeholder="USD,$" class="shortcode_sett_title sv_currency_field"><span><a href="#" class="add_sv_currency">Add</a></span></div></div>';
        /*Percentage, Months Type Field*/
        savings_box += '<div class="saving_box_inn"><div class="saving_box_single"><input type="text" name="savings_type[months][' + savings_box_no + '][0][0]" data-placeholder="Months,%" placeholder="Months,%" class="shortcode_sett_title sv_months_field"><span><a href="#" class="add_sv_months">Add</a></span></div></div>';

        savings_box += '</div>';

        savings_box += '<div class="clear"></div></div>';


        $(this).parent('div').siblings('div.add_select_wrap').append(savings_box);

    });

    $(document.body).on('click', 'a.add_sv_currency', function(e) {
        e.preventDefault();
        var closest_savings_box_no = $(this).closest('.savings_box_wrap').index();
        var currency_count = $(this).closest('.savings_box_wrap').children('.second-input-wrap').length;


        /*Currency Type Field*/
        var savings_box1 = '<div class="second-input-wrap" data-count="' + currency_count + '">';
        savings_box1 += '<div class="saving_box_inn"><div class="saving_box_single"><input type="text" name="savings_type[currency][' + closest_savings_box_no + '][]" data-placeholder="USD,$" placeholder="USD,$" class="shortcode_sett_title sv_currency_field"></div></div>';
        /*Percentage, Months Type Field*/
        savings_box1 += '<div class="saving_box_inn"><div class="saving_box_single"><input type="text" name="savings_type[months][' + closest_savings_box_no + '][][]" data-placeholder="Months,%" placeholder="Months,%" class="shortcode_sett_title sv_months_field"><span><a href="#" class="add_sv_months">Add</a></span></div></div>';

        savings_box1 += '</div>';


        $(this).closest('div.savings_box_wrap').children('div.clear').before(savings_box1);

    });

    $(document.body).on('click', 'a.add_sv_months', function(e) {
        e.preventDefault();
        var closest_savings_box_no = $(this).closest('.savings_box_wrap').index();
        var closest_currency_box_no = $(this).closest('div.second-input-wrap').index();
        //console.log($(this).closest('div.saving_box_inn').parent('div.second-input-wrap').length);
        //closest_currency_box_no = jQuery(this).parentsUntil('.second-input-wrap').parent().index(".second-input-wrap");
        closest_currency_box_no = jQuery(this).parentsUntil('.second-input-wrap').parent().data('count');



        var savings_box2 = '<div class="saving_box_single"><input type="text" name="savings_type[months][' + closest_savings_box_no + '][' + closest_currency_box_no + '][]" data-placeholder="Months,%" placeholder="Months,%" class="shortcode_sett_title sv_months_field"><a href="#" class="del_saving_month"><img src="' + smartCal.cancelImage + '"></a></div>';

        $(this).closest('div.saving_box_inn').append(savings_box2);

    });

    $(document.body).on('click', 'a.del_saving_box', function(e) {
        e.preventDefault();
        var removalDiv = $(this).parent().parent();
        $(this).parent().parent().slideUp(function() {
            removalDiv.remove();
        });
    });
    
    $(document.body).on('click', 'a.del_saving_month', function(e) {
        e.preventDefault();
        var removalDiv = $(this).parent();
        removalDiv.slideUp(function() {
            removalDiv.remove();
        });

    });
    
    $(document.body).on('click', '.del_options_val', function(e) {
        e.preventDefault();
        $(this).closest('div.add_select_box').remove();
    });

    $(document.body).on('blur', 'div.add_select_box input', function(e) {
        $(this).prop('placeholder', $(this).data('placeholder'));
    });

    $(document.body).on('focus', 'div.add_select_box input', function(e) {
        $(this).prop('placeholder', '');
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