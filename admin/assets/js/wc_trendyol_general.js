jQuery(function($){

    //TRENDYOL SELECT2
    function normal_search_select2(){
        $('.wc_trendyol_normal_search').select2({
            sortResults: data => data.sort((a, b) => a.text.localeCompare(b.text)),
        });
    }

    normal_search_select2();

    brand_search()

    function search_cat_attr_values(){
        $('.wc_trendyol_search_cat_attr_values').select2({
            minimumInputLength: 1,
            sortResults       : data => data.sort((a, b) => a.text.localeCompare(b.text)),
            ajax              : {
                url           : ajaxurl, // AJAX URL is predefined in WordPress admin
                dataType      : 'json',
                type          : 'post',
                delay         : 250, // delay in ms while typing when to perform a AJAX search
                data          : function(params){

                    var trendyol_cat_id      = $(this).data('trendyol_cat_id');
                    var trendyol_cat_attr_id = $(this).data('trendyol_attr_id');

                    return {
                        search_text: params.term, // search query
                        trendyol_cat_id,
                        trendyol_cat_attr_id,
                        action     : 'wc_trendyol_search_cat_attr_values' // AJAX action for admin-ajax.php
                    };
                },
                processResults: function(data){
                    var options = [];
                    if(data){

                        // data is the array of arrays, and each of them contains ID and the Label of the option
                        $.each(data, function(index, text){ // do not forget that "index" is just auto incremented value
                            options.push({
                                id  : text[0],
                                text: text[1]
                            });
                        });

                    }
                    return {
                        results: options
                    };
                },
                cache         : true
            },
        });
    }

    search_cat_attr_values()

    //TRENDYOL SELECT2

    function required_input(){
        $('*[required]').each(function(i, e){
            var parent = $(e).parent();
            $(' label', parent).append('<span style="color:#ff0000; margin-left:5px">*</span>').addClass('border-danger');
        })
    }

    required_input();

    //JUST NUMBER - INT
    $(document).on('input', '.just_int', function(event){
        var value = $(this).val();
        var regex = /^[0-9]\d*$/;
        if(!regex.test(value)){
            $(this).val(value.slice(0, -1));
        }
    });

    $(document).on('input', '.just_float', function(){
        var value = $(this).val();
        value     = value.replace(',', '.');
        var regex = /^[0-9]\d*(\.\d{0,2})?$/;
        if(!regex.test(value)){
            $(this).val(value.slice(0, -1));
        }
        else{
            $(this).val(value);
        }
    });
    //JUST NUMBER - INT

    //OTOMATİK TABLO YÜKLEME
    function auto_load_table(table_name, paged = 1, wc_cat_id = null, search = null, params = null){

        if(wc_cat_id > 0){

            swal_wait();

            var current_page = get_url_param('page');
            var action       = get_url_param('action');
            var new_url      = '/wp-admin/admin.php?page=' + current_page + '&wc_cat_id=' + wc_cat_id + '&paged=' + paged + '&action=' + action;
            window.history.pushState({path: new_url}, '', new_url);

            var data = {
                'action': table_name,
                paged,
                search,
                wc_cat_id,
                params
            };

            jQuery.post(ajaxurl, data, function(response){
                if(response.status === 'success'){
                    $('.wc_trendyol_autoload_table').html(response.data)
                    $('.wc_trendyol_card_footer').html(response.pagination)
                    swal.close();
                    $('.wc_trendyol_autoload_table').trigger('autoload_finished');

                    normal_search_select2();
                }
                else{
                    Swal.fire({
                        title            : 'Bilgi',
                        text             : (response.message || 'XX'),
                        icon             : 'warning',
                        confirmButtonText: 'Tamam'
                    })
                }
            }).fail(function(response){
                Swal.fire({
                    title            : 'Hata',
                    text             : 'Sorgu Hatası. Lütfen sayfayı ctrl + f5 tuşlarına aynı an da basarak yenieyin.',
                    icon             : 'error',
                    confirmButtonText: 'Tamam'
                })
            });

            $('.wc_trendyol_autoload_table').trigger('refresh');
        }

    }

    if($('.wc_trendyol_autoload_table').length > 0){
        var paged      = get_url_param('paged') ?? 1;
        var wc_cat_id  = $('.wc_cat_id').val();
        var load_table = $('.wc_trendyol_autoload_table').data('load_table');
        var params     = $('.wc_trendyol_card :input').serialize();
        auto_load_table(load_table, paged, wc_cat_id, null, params);
    }
    //OTOMATİK TABLO YÜKLEME

    //SAYFALAMA
    $(document).on('click', '.wc_trendyol_pagination a', function(){
        var paged      = $(this).data('paged');
        var wc_cat_id  = $('.wc_cat_id').val();
        var load_table = $('.wc_trendyol_autoload_table').data('load_table');
        var params     = $('.wc_trendyol_card .wc_trendyol_card_header :input').serialize();
        auto_load_table(load_table, paged, wc_cat_id, null, params);
        return false;
    })
    //SAYFALAMA

    //SELECTBOX DEĞİŞİMİ
    $(document).on('change', '.wc_trendyol_card .wc_trendyol_card_toolbar select.refresh_table', function(){
        var wc_cat_id  = $('.wc_cat_id').val();
        var load_table = $('.wc_trendyol_autoload_table').data('load_table');
        var params     = $('.wc_trendyol_card :input').serialize();
        auto_load_table(load_table, 1, wc_cat_id, null, params);
        return false;
    })
    //SELECTBOX DEĞİŞİMİ

    //INPUT ENTER
    $(document).on('keyup', '.wc_trendyol_card .wc_trendyol_card_toolbar input', function(e){
        if(e.keyCode === 13){

            var wc_cat_id  = $('.wc_cat_id').val();
            var load_table = $('.wc_trendyol_autoload_table').data('load_table');
            var params     = $('.wc_trendyol_card :input').serialize();
            auto_load_table(load_table, 1, wc_cat_id, null, params);

        }
    })
    //INPUT ENTER

    //TABLO YENİLE
    $('.table_refresh_btn').on('click', function(){
        var wc_cat_id  = $('.wc_cat_id').val();
        var load_table = $('.wc_trendyol_autoload_table').data('load_table');
        var params     = $('.wc_trendyol_card :input').serialize();
        var paged      = get_url_param('paged') || 1;
        auto_load_table(load_table, paged, wc_cat_id, null, params);
    })
    //TABLO YENİLE

    //TRENDYOL MODAL
    $('.wc_trendyol_modal_close_btn').on('click', function(){
        $('.wc_trendyol_modal').hide();
    })

    $('.wc_trendyol_modal_open_btn').on('click', function(){
        var modal = $(this).data('modal_class');
        $(modal).show();
    })
    //TRENDYOL MODAL

    //OTHER PLUGIN ACTIVE LICENSE
    $('.wc_trendyol_other_plugin_active_license_btn').on('click', function(){

        var wc_trendyol_plugin_slug = $(this).data('plugin_slug');
        var parent                  = $(this).closest('.wc_trendyol_other_plugin_install');
        var wc_trendyol_license     = $(' .wc_trendyol_other_plugin_license', parent).val();

        swal_wait();

        var me = $(this);
        $(me).prop('disabled', true);

        var data = {
            'action': 'wc_trendyol_license_control',
            wc_trendyol_plugin_slug,
            wc_trendyol_license,
        };

        jQuery.post(ajaxurl, data, function(response){
            if(response.status === 'success'){
                Swal.fire({
                    title            : 'Eklenti Aktif Edildi!',
                    text             : response.message,
                    icon             : 'success',
                    confirmButtonText: 'Tamam'
                }).then(function(result){
                    if(result.isConfirmed){
                        location.reload();
                    }
                })
            }
            else{
                Swal.fire({
                    title            : 'Bilgi',
                    text             : response.message,
                    icon             : 'warning',
                    confirmButtonText: 'Tamam'
                })
            }
            $(me).prop('disabled', false);
        }).fail(function(response){
            Swal.fire({
                title            : 'Bilgi',
                text             : 'Sorgu hatası',
                icon             : 'warning',
                confirmButtonText: 'Tamam'
            })
        });

        return false;
    })
    //OTHER PLUGIN ACTIVE LICENSE
});

//SWAL
function swal_wait(title = 'Lütfen Bekleyin...', text = 'İşlem uzun sürebilir', close_btn = false){
    Swal.fire({
        title            : title,
        text             : text,
        icon             : 'info',
        showConfirmButton: close_btn,
        confirmButtonText: 'Tamam',
        allowOutsideClick: false
    })
}

//SWAL

//URL PARAM
function get_url_param(name, url){
    if(!url){
        url = window.location.href;
    }
    name        = name.replace(/[\[\]]/g, '\\$&');
    var regex   = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if(!results){
        return null;
    }
    if(!results[2]){
        return '';
    }
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

//URL PARAM

function brand_search(){
    jQuery('.wc_trendyol_brand_search').select2({
        allowClear        : true,
        placeholder       : {
            id      : "-1",
            text    : "Ana Markanız",
            selected: 'selected'
        },
        minimumInputLength: 3,
        sortResults       : data => data.sort((a, b) => a.text.localeCompare(b.text)),
        ajax              : {
            url           : ajaxurl, // AJAX URL is predefined in WordPress admin
            dataType      : 'json',
            type          : 'post',
            delay         : 250, // delay in ms while typing when to perform a AJAX search
            data          : function(params){
                return {
                    q     : params.term, // search query
                    action: 'wc_trendyol_search_brand' // AJAX action for admin-ajax.php
                };
            },
            processResults: function(data){
                var options = [];
                if(data){

                    // data is the array of arrays, and each of them contains ID and the Label of the option
                    jQuery.each(data, function(index, text){ // do not forget that "index" is just auto incremented value
                        options.push({
                            id  : text[0],
                            text: text[1]
                        });
                    });

                }
                return {
                    results: options
                };
            },
            cache         : true
        },
    });
}