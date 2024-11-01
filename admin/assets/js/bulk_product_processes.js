jQuery(function($){

    //SİTEYE KAYDET - BTN
    $(document).on('click', '.wc_trendyol_bulk_product_processes_save_btn', function(){

        var parent = $(this).parent().parent()
        var me     = $(this);
        $(me).prop('disabled', true)

        var wc_cat_id               = $('select.wc_cat_id').val();
        var wc_product_id           = $(this).data('wc_product_id');
        var trendyol_product_title  = $(' .trendyol_product_title', parent).val();
        var website_stock_qty       = $(' .website_stock_qty', parent).val();
        var trendyol_stock_qty      = $(' .trendyol_stock_qty', parent).val();
        var website_sale_price      = $(' .website_sale_price', parent).val();
        var website_discount_price  = $(' .website_discount_price', parent).val();
        var trendyol_sale_price     = $(' .trendyol_sale_price', parent).val();
        var trendyol_discount_price = $(' .trendyol_discount_price', parent).val();

        var data = {
            'action': 'wc_trendyol_bulk_product_processes_save_line',
            wc_cat_id,
            wc_product_id,
            trendyol_product_title,
            website_stock_qty,
            trendyol_stock_qty,
            website_sale_price,
            website_discount_price,
            trendyol_sale_price,
            trendyol_discount_price,
        };

        jQuery.post(ajaxurl, data, function(response){
            if(response.status === 'success'){
                Swal.fire({
                    title            : 'Başarılı',
                    text             : (response.message || 'XX'),
                    icon             : 'success',
                    confirmButtonText: 'Tamam'
                })
            }
            else{
                Swal.fire({
                    title            : 'Bilgi',
                    text             : (response.message || 'XX'),
                    icon             : 'warning',
                    confirmButtonText: 'Tamam'
                })
            }
            $(me).prop('disabled', false)
        }).fail(function(response){
            Swal.fire({
                title            : 'Hata',
                text             : 'Sorgu Hatası. Lütfen sayfayı ctrl + f5 tuşlarına aynı an da basarak yenieyin.',
                icon             : 'error',
                confirmButtonText: 'Tamam'
            })
            $(me).prop('disabled', false)
        });

        return false;
    })
    //SİTEYE KAYDET - BTN

    //TÜMÜNÜ KAYDET - BTN
    $('.wc_trendyol_bulk_product_processes_save_all_btn').on('click', function(){

        swal_wait('Okuyunuz...', 'Sadece bu listedeki ürünlerin değerleri kayıt ediliyor. Diğer sayfalar için de aynı işlemi yapmalısınız');

        var me = $(this);

        $(me).prop('disabled', true);

        var parent = $(me).parent().parent();

        var form_data = $('.wc_trendyol_autoload_table :input').serialize();

        var data = {
            'action': 'wc_trendyol_bulk_product_processes_save_all',
            form_data
        };

        jQuery.post(ajaxurl, data, function(response){
            if(response.status === 'success'){
                Swal.fire({
                    title            : 'Başarılı',
                    text             : response.message,
                    icon             : 'success',
                    confirmButtonText: 'Tamam'
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
        }).fail((response) => {
            Swal.fire({
                title            : 'Hata',
                text             : 'Sorgu hatası. Muhtemelen sunucu taraflı bir hata oluştu.',
                icon             : 'warning',
                confirmButtonText: 'Tamam'
            })
        });

        return false;
    })
    //TÜMÜNÜ KAYDET - BTN

    //STOK EŞİTLE
    function wc_trendyol_auto_sync_stock(parent_element = '.wc_trendyol_table tr'){

        var w_a_t_sync = $('#w_a_t_sync:checked').val();

        $(parent_element).each(function(i, e){

            if(w_a_t_sync === 'on'){
                var website_stock_qty  = $(' .website_stock_qty', e).val();
                var trendyol_stock_qty = $(' .trendyol_stock_qty', e).val();
                $(' .trendyol_stock_qty', e).val(website_stock_qty).trigger('input');
            }

        })

    }

    function wc_trendyol_auto_sync_price(parent_element = '.wc_trendyol_table tr'){

        var w_a_t_sync = $('#w_a_t_sync:checked').val();

        $(parent_element).each(function(i, e){

            if(w_a_t_sync === 'on'){
                var website_stock_qty = $(' .website_sale_price', e).val();
                $(' .trendyol_sale_price', e).val(website_stock_qty);

                var website_discount_price = $(' .website_discount_price', e).val();
                $(' .trendyol_discount_price', e).val(website_discount_price);
            }

        })

    }

    $(document).on('keyup', '.website_stock_qty', function(){
        var parent_tr = $(this).parent().parent();
        wc_trendyol_auto_sync_stock(parent_tr);
        return false;
    })

    $(document).on('keyup', '.website_sale_price, .website_discount_price', function(){
        var parent_tr = $(this).parent().parent();
        wc_trendyol_auto_sync_price(parent_tr);
        return false;
    })
    //STOK EŞİTLE

    //WEB SİTE TOPLU FİYAT DEĞİŞTİR ÖNİZLEME
    function calc_website_preview(){

        var wc_trendyol_change_website_price_value_input  = parseFloat($('.wc_trendyol_change_website_price_value_input').val() || 0); //number
        var wc_trendyol_change_website_price_action_input = $('.wc_trendyol_change_website_price_action_input').val(); // + veya - değer
        var wc_trendyol_change_website_price_rate_input   = $('.wc_trendyol_change_website_price_rate_input').val(); // yuzde veya sabit

        var wc_trendyol_website_calc_preview_old_price = parseFloat($('.wc_trendyol_website_calc_preview_old_price').val()); // eski fiyat

        var new_price = 0;

        if(wc_trendyol_change_website_price_action_input === '-'){
            wc_trendyol_change_website_price_value_input = wc_trendyol_change_website_price_value_input * -1;
        }

        if(wc_trendyol_change_website_price_rate_input === 'sabit'){
            new_price = wc_trendyol_website_calc_preview_old_price + wc_trendyol_change_website_price_value_input;
        }
        else if(wc_trendyol_change_website_price_rate_input === 'yuzde'){
            new_price = wc_trendyol_website_calc_preview_old_price + ((wc_trendyol_website_calc_preview_old_price / 100) * wc_trendyol_change_website_price_value_input);
        }

        $('.wc_trendyol_website_calc_preview_new_price').val(new_price.toFixed(2));
    }

    $('.wc_trendyol_change_website_price_modal .wc_trendyol_form_group_inline :input').on('change input', function(){
        calc_website_preview();
    })
    //WEB SİTE TOPLU FİYAT DEĞİŞTİR ÖNİZLEME

    //WEB SİTE TOPLU FİYAT DEĞİŞTİR - BU KATEGORİDEKİ
    $('.wc_trendyol_change_this_wc_cat_website_product_price_modal_btn').on('click', function(){

        var wc_trendyol_change_website_price_value_input  = parseFloat($('.wc_trendyol_change_website_price_value_input').val() || 0); //number
        var wc_trendyol_change_website_price_action_input = $('.wc_trendyol_change_website_price_action_input').val(); // + veya - değer
        var wc_trendyol_change_website_price_rate_input   = $('.wc_trendyol_change_website_price_rate_input').val(); // yuzde veya sabit
        var wc_cat_id                                     = get_url_param('wc_cat_id');

        Swal.fire({
            title             : 'Emin Misiniz?',
            text              : "Bu işlem seçtiğiniz kategorideki tüm ürünlerin fiyatını değiştirecektir. İşlem geri alınamaz!!!",
            icon              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor : '#d33',
            confirmButtonText : 'Eminim',
            cancelButtonText  : 'İptal',
        }).then((result) => {
            if(result.isConfirmed){

                swal_wait();

                var data = {
                    'action': 'wc_trendyol_change_this_wc_cat_website_product_price',
                    wc_trendyol_change_website_price_value_input,
                    wc_trendyol_change_website_price_action_input,
                    wc_trendyol_change_website_price_rate_input,
                    wc_cat_id,
                };

                jQuery.post(ajaxurl, data, function(response){
                    if(response.status === 'success'){

                        Swal.fire({
                            title            : 'Bilgi',
                            text             : response.message,
                            icon             : 'success',
                            confirmButtonText: 'Tamam'
                        })

                        $('.table_refresh_btn').trigger('click');
                        $('.wc_trendyol_modal_close_btn').trigger('click');

                    }
                    else{
                        Swal.fire({
                            title            : 'Bilgi',
                            text             : response.message,
                            icon             : 'warning',
                            confirmButtonText: 'Tamam'
                        })
                    }
                }).fail(function(response){
                    Swal.fire({
                        title            : 'Bilgi',
                        text             : 'Sorgu Hatası',
                        icon             : 'warning',
                        confirmButtonText: 'Tamam'
                    })
                });

            }
        })

    })
    //WEB SİTE TOPLU FİYAT DEĞİŞTİR - BU KATEGORİDEKİ

    //WEB SİTE TOPLU FİYAT DEĞİŞTİR - TÜM ÜRÜNLER
    $('.wc_trendyol_change_all_website_product_price_modal_btn').on('click', function(){

        var wc_trendyol_change_website_price_value_input  = parseFloat($('.wc_trendyol_change_website_price_value_input').val() || 0); //number
        var wc_trendyol_change_website_price_action_input = $('.wc_trendyol_change_website_price_action_input').val(); // + veya - değer
        var wc_trendyol_change_website_price_rate_input   = $('.wc_trendyol_change_website_price_rate_input').val(); // yuzde veya sabit

        Swal.fire({
            title             : 'Emin Misiniz?',
            text              : "Bu işlem sitenizdeki tüm ürünlerin fiyatını değiştirecektir. İşlem geri alınamaz!!!",
            icon              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor : '#d33',
            confirmButtonText : 'Eminim',
            cancelButtonText  : 'İptal',
        }).then((result) => {
            if(result.isConfirmed){

                swal_wait();

                var data = {
                    'action': 'wc_trendyol_change_all_website_product_price',
                    wc_trendyol_change_website_price_value_input,
                    wc_trendyol_change_website_price_action_input,
                    wc_trendyol_change_website_price_rate_input,
                };

                jQuery.post(ajaxurl, data, function(response){
                    if(response.status === 'success'){

                        Swal.fire({
                            title            : 'Bilgi',
                            text             : response.message,
                            icon             : 'success',
                            confirmButtonText: 'Tamam'
                        })

                        $('.table_refresh_btn').trigger('click');
                        $('.wc_trendyol_modal_close_btn').trigger('click');

                    }
                    else{
                        Swal.fire({
                            title            : 'Bilgi',
                            text             : response.message,
                            icon             : 'warning',
                            confirmButtonText: 'Tamam'
                        })
                    }
                }).fail(function(response){
                    Swal.fire({
                        title            : 'Bilgi',
                        text             : 'Sorgu Hatası',
                        icon             : 'warning',
                        confirmButtonText: 'Tamam'
                    })
                });

            }
        })

    })
    //WEB SİTE TOPLU FİYAT DEĞİŞTİR - TÜM ÜRÜNLER

    //TRENDYOL TOPLU FİYAT DEĞİŞTİR ÖNİZLEME
    function calc_trendyol_preview(){

        var wc_trendyol_change_trendyol_price_value_input  = parseFloat($('.wc_trendyol_change_trendyol_price_value_input').val() || 0); //number
        var wc_trendyol_change_trendyol_price_action_input = $('.wc_trendyol_change_trendyol_price_action_input').val(); // + veya - değer
        var wc_trendyol_change_trendyol_price_rate_input   = $('.wc_trendyol_change_trendyol_price_rate_input').val(); // yuzde veya sabit

        var wc_trendyol_trendyol_calc_preview_old_price = parseFloat($('.wc_trendyol_trendyol_calc_preview_old_price').val()); // eski fiyat

        var new_price = 0;

        if(wc_trendyol_change_trendyol_price_action_input === '-'){
            wc_trendyol_change_trendyol_price_value_input = wc_trendyol_change_trendyol_price_value_input * -1;
        }

        if(wc_trendyol_change_trendyol_price_rate_input === 'sabit'){
            new_price = wc_trendyol_trendyol_calc_preview_old_price + wc_trendyol_change_trendyol_price_value_input;
        }
        else if(wc_trendyol_change_trendyol_price_rate_input === 'yuzde'){
            new_price = wc_trendyol_trendyol_calc_preview_old_price + ((wc_trendyol_trendyol_calc_preview_old_price / 100) * wc_trendyol_change_trendyol_price_value_input);
        }

        $('.wc_trendyol_trendyol_calc_preview_new_price').val(new_price.toFixed(2));
    }

    $('.wc_trendyol_change_trendyol_price_modal .wc_trendyol_form_group_inline :input').on('change input', function(){
        calc_trendyol_preview();
    })
    //TRENDYOL TOPLU FİYAT DEĞİŞTİR ÖNİZLEME

    //TRENDYOL TOPLU FİYAT DEĞİŞTİR - BU KATEGORİDEKİ
    $('.wc_trendyol_change_this_wc_cat_trendyol_product_price_modal_btn').on('click', function(){

        var wc_trendyol_change_trendyol_price_value_input  = parseFloat($('.wc_trendyol_change_trendyol_price_value_input').val() || 0); //number
        var wc_trendyol_change_trendyol_price_action_input = $('.wc_trendyol_change_trendyol_price_action_input').val(); // + veya - değer
        var wc_trendyol_change_trendyol_price_rate_input   = $('.wc_trendyol_change_trendyol_price_rate_input').val(); // yuzde veya sabit
        var wc_cat_id                                      = get_url_param('wc_cat_id');

        Swal.fire({
            title             : 'Emin Misiniz?',
            text              : "Bu işlem seçtiğiniz kategorideki tüm ürünlerin fiyatını değiştirecektir. İşlem geri alınamaz!!!",
            icon              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor : '#d33',
            confirmButtonText : 'Eminim',
            cancelButtonText  : 'İptal',
        }).then((result) => {
            if(result.isConfirmed){

                swal_wait();

                var data = {
                    'action': 'wc_trendyol_change_this_wc_cat_trendyol_product_price',
                    wc_trendyol_change_trendyol_price_value_input,
                    wc_trendyol_change_trendyol_price_action_input,
                    wc_trendyol_change_trendyol_price_rate_input,
                    wc_cat_id,
                };

                jQuery.post(ajaxurl, data, function(response){
                    if(response.status === 'success'){

                        Swal.fire({
                            title            : 'Bilgi',
                            text             : response.message,
                            icon             : 'success',
                            confirmButtonText: 'Tamam',
                            allowOutsideClick: false,
                            allowEscapeKey   : false,
                            didOpen          : () => {
                                // Confirm butonunu başlangıçta devre dışı bırak
                                const confirmButton = Swal.getConfirmButton();
                                if(confirmButton){
                                    confirmButton.disabled = true;
                                    confirmButton.classList.add('swal2-disabled'); // Butonu görsel olarak devre dışı bırak
                                }

                                // 5 saniye sonra butonu etkinleştir
                                setTimeout(() => {
                                    if(confirmButton){
                                        confirmButton.disabled = false;
                                        confirmButton.classList.remove('swal2-disabled'); // Stil sınıfını kaldır
                                    }
                                }, 5000);
                            }
                        }).then(function(){
                            $('.table_refresh_btn').trigger('click');
                            $('.wc_trendyol_modal_close_btn').trigger('click');
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
                }).fail(function(response){
                    Swal.fire({
                        title            : 'Bilgi',
                        text             : 'Sorgu Hatası',
                        icon             : 'warning',
                        confirmButtonText: 'Tamam'
                    })
                });

            }
        })

    })
    //TRENDYOL TOPLU FİYAT DEĞİŞTİR - BU KATEGORİDEKİ

    //WEB SİTE TOPLU FİYAT DEĞİŞTİR - TÜM ÜRÜNLER
    $('.wc_trendyol_change_all_trendyol_product_price_modal_btn').on('click', function(){

        var wc_trendyol_change_trendyol_price_value_input  = parseFloat($('.wc_trendyol_change_trendyol_price_value_input').val() || 0); //number
        var wc_trendyol_change_trendyol_price_action_input = $('.wc_trendyol_change_trendyol_price_action_input').val(); // + veya - değer
        var wc_trendyol_change_trendyol_price_rate_input   = $('.wc_trendyol_change_trendyol_price_rate_input').val(); // yuzde veya sabit

        Swal.fire({
            title             : 'Emin Misiniz?',
            text              : "Bu işlem sitenizdeki tüm ürünlerin fiyatını değiştirecektir. İşlem geri alınamaz!!!",
            icon              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor : '#d33',
            confirmButtonText : 'Eminim',
            cancelButtonText  : 'İptal',
        }).then((result) => {
            if(result.isConfirmed){

                swal_wait();

                var data = {
                    'action': 'wc_trendyol_change_all_trendyol_product_price',
                    wc_trendyol_change_trendyol_price_value_input,
                    wc_trendyol_change_trendyol_price_action_input,
                    wc_trendyol_change_trendyol_price_rate_input,
                };

                jQuery.post(ajaxurl, data, function(response){
                    if(response.status === 'success'){

                        Swal.fire({
                            title            : 'Bilgi',
                            text             : response.message,
                            icon             : 'success',
                            confirmButtonText: 'Tamam',
                            allowOutsideClick: false,
                            allowEscapeKey   : false,
                            didOpen          : () => {
                                // Confirm butonunu başlangıçta devre dışı bırak
                                const confirmButton = Swal.getConfirmButton();
                                if(confirmButton){
                                    confirmButton.disabled = true;
                                    confirmButton.classList.add('swal2-disabled'); // Butonu görsel olarak devre dışı bırak
                                }

                                // 5 saniye sonra butonu etkinleştir
                                setTimeout(() => {
                                    if(confirmButton){
                                        confirmButton.disabled = false;
                                        confirmButton.classList.remove('swal2-disabled'); // Stil sınıfını kaldır
                                    }
                                }, 5000);
                            }
                        }).then(function(){
                            $('.table_refresh_btn').trigger('click');
                            $('.wc_trendyol_modal_close_btn').trigger('click');
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
                }).fail(function(response){
                    Swal.fire({
                        title            : 'Bilgi',
                        text             : 'Sorgu Hatası',
                        icon             : 'warning',
                        confirmButtonText: 'Tamam'
                    })
                });

            }
        })

    })
    //WEB SİTE TOPLU FİYAT DEĞİŞTİR - TÜM ÜRÜNLER
})