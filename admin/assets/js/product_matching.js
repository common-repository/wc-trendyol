jQuery(function($){

    function product_matching_sku_control_one_line(tr_element){

        var main_sku         = $(' .wc_product_main_sku_input', tr_element).val().trim();
        var product_sku      = $(' .wc_trendyol_product_sku_input', tr_element).val().trim();
        var trendyol_barcode = $(' .wc_trendyol_barcode', tr_element).val().trim();
        var product_type     = $(tr_element).data('product_type');

        if(main_sku === product_sku){
            $(' .wc_product_main_sku_input', tr_element).addClass('wc_trendyol_sku_error');
            $(' .wc_trendyol_product_sku_input', tr_element).addClass('wc_trendyol_sku_error');
            $(' .wc_trendyol_barcode', tr_element).addClass('wc_trendyol_sku_error');

            swal_wait('Eşitleme kural hatası', 'Ana ürün SKU ile alt ürün SKU aynı olamaz. Bu ürün eşitlemede sorunlara yol açar', true)

        }
        else if(main_sku === trendyol_barcode && product_type === 'variation'){
            $(' .wc_product_main_sku_input', tr_element).addClass('wc_trendyol_sku_error');
            $(' .wc_trendyol_product_sku_input', tr_element).addClass('wc_trendyol_sku_error');
            $(' .wc_trendyol_barcode', tr_element).addClass('wc_trendyol_sku_error');

            swal_wait('Eşitleme kural hatası', 'Trendyol Barkodu ile Ana SKU aynı olamaz. Bu ürün eşitlemede sorunlara yol açar', true)

        }
        else{
            $(' .wc_product_main_sku_input', tr_element).removeClass('wc_trendyol_sku_error');
            $(' .wc_trendyol_product_sku_input', tr_element).removeClass('wc_trendyol_sku_error');
            $(' .wc_trendyol_barcode', tr_element).removeClass('wc_trendyol_sku_error');
        }

    }

    var selector = '.wc_trendyol_product_matching_page .wc_product_main_sku_input, .wc_trendyol_product_matching_page .wc_trendyol_product_sku_input, .wc_trendyol_product_matching_page .wc_trendyol_barcode';
    $(document).on('keyup', selector, function(){

        var tr_element = $(this).parent().parent();
        product_matching_sku_control_one_line(tr_element);

    })

    //TOPLU URUN EŞLEŞİTİRME - SATIR KAYDET
    $(document).on('click', '.wc_trendyol_save_line_btn', function(){

        var me = $(this);

        $(me).prop('disabled', true);

        var parent = $(me).parent().parent();

        var wc_trendyol_product_id        = $(parent).data('product_id');
        var wc_trendyol_product_sku_input = $(' .wc_trendyol_product_sku_input', parent).val();
        var wc_product_main_sku_input     = $(' .wc_product_main_sku_input', parent).val();
        var wc_trendyol_barcode           = $(' .wc_trendyol_barcode', parent).val();

        var data = {
            'action': 'wc_trendyol_product_matching_save_line',
            wc_trendyol_product_id,
            wc_trendyol_product_sku_input,
            wc_product_main_sku_input,
            wc_trendyol_barcode,
        };

        jQuery.post(ajaxurl, data, function(response){
            if(response.status === 'success'){
                Swal.fire({
                    title            : 'Bilgi',
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
    //TOPLU URUN EŞLEŞİTİRME - SATIR KAYDET

    //TOPLU URUN EŞLETİRME - OTOMATİK SKU OLUŞTURMA
    $(document).on('click', '.wc_trendyol_auto_sku_generator_btn', function(){

        Swal.fire({
            title            : 'Otomatik SKU Oluştur',
            html             : `<input type="text" id="main_product_prefix" class="swal2-input main_product_prefix" placeholder="Ana Ürün Ön Eki. ÖRN: ANA" style="width: 274px"> <input type="text" id="sub_product_prefix" class="swal2-input sub_product_prefix" placeholder="Alt Ürün Ön Eki. ÖRN: ALT" style="width: 274px"><div class="wc_trendyol_preview_sku">SKU Önizlemesi: <br>ANA-XXXX<br>ALT-XXXX-1<br>ALT-XXXX-2</div>`,
            confirmButtonText: 'Oluştur',
            focusConfirm     : false,
            preConfirm       : () => {

                const main_product_prefix = Swal.getPopup().querySelector('.main_product_prefix').value
                const sub_product_prefix  = Swal.getPopup().querySelector('.sub_product_prefix').value

                if(!main_product_prefix || !sub_product_prefix){
                    Swal.showValidationMessage(`Lütfen kutuları doldurun`)
                }
                return {
                    main_product_prefix,
                    sub_product_prefix
                }
            }
        }).then((result) => {

            const main_product_prefix = result.value.main_product_prefix
            const sub_product_prefix  = result.value.sub_product_prefix

            Swal.fire({
                title             : 'Var olan skuların üstüne yazılsın mı?',
                text              : "Dolu olan skuların da değişmesini istiyorsanız 'Evet, Yazılsın!' seçmelisiniz. Sadece boş olanların kutulara sku üretilmesini istiyorsanız 'Hayır' seçeneği seçin",
                icon              : 'warning',
                showCancelButton  : true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor : '#d33',
                cancelButtonText  : 'Hayır',
                confirmButtonText : 'Evet, Yazılsın!'
            }).then((result) => {
                if(result.isConfirmed){

                    var sub_index = 1;
                    $('.wc_trendyol_product_matching_table_tr').each(function(i, e){
                        var parent = $(e).parent().parent();

                        var product_id        = $(e).data('product_id');
                        var parent_product_id = $(e).data('parent_product_id');

                        if(parent_product_id === 0){
                            sub_index = 1;
                            $(' .wc_product_main_sku_input ', e).val(main_product_prefix + '-' + product_id);
                            $(' .wc_trendyol_barcode ', e).val(main_product_prefix + '-' + product_id);
                        }
                        else{
                            $(' .wc_trendyol_product_sku_input ', e).val(sub_product_prefix + '-' + parent_product_id + '-' + sub_index);
                            $(' .wc_trendyol_barcode ', e).val(sub_product_prefix + '-' + parent_product_id + '-' + sub_index);
                            sub_index++;
                        }

                    })
                }
                else{
                    $('.wc_trendyol_product_sku_input').each(function(i, e){
                        var parent           = $(e).parent().parent();
                        var value            = $(e).val();
                        var trendyol_barcode = $(' .wc_trendyol_barcode', parent).val();
                        if(trendyol_barcode === ""){
                            $(' .wc_trendyol_barcode', parent).val(value);
                        }
                    })

                }
            })

        })

        return false;
    })
    //TOPLU URUN EŞLETİRME - OTOMATİK SKU OLUŞTURMA

    //TOPLU URUN EŞLEŞTİRME - TÜMÜNÜ KAYDET
    $(document).on('click', '.wc_trendyol_product_matching_page .wc_trendyol_save_all_btn', function(){

        swal_wait('Okuyunuz...', 'Sadece bu listedeki ürünler eşleştiriliyor. Diğer sayfalar için de aynı işlemi yapmalısınız');

        var me = $(this);

        $(me).prop('disabled', true);

        var parent = $(me).parent().parent();

        var form_data = $('.wc_trendyol_product_matching_page :input').serialize();

        var data = {
            'action': 'wc_trendyol_product_matching_save_all',
            form_data
        };

        jQuery.post(ajaxurl, data, function(response){
            if(response.status === 'success'){
                // swal.close();
                $('.table_refresh_btn').trigger('click');
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
    //TOPLU URUN EŞLEŞTİRME - TÜMÜNÜ KAYDET

    //TOPLU URUN EŞLEŞTİRME - OTOMATİK KONTROL ET
    $(document).on('click', '.wc_trendyol_all_product_matching_control_now_background_btn', function(){

        Swal.fire({
            icon             : 'info',
            title            : 'Emin Misiniz?',
            text             : 'Bu işlem web sitenizdeki sku ile trendyoldaki barkod kısmına göre ürünlerinizi otomatik eşitler eğer sku ve barkodlar aynı değilse bu işlemi başlatmayın!',
            showCancelButton : true,
            confirmButtonText: 'Eşitle',
            cancelButtonText : 'İptal Et',
        }).then((result) => {
            if(result.isConfirmed){

                swal_wait()

                var data = {
                    'action': 'wc_trendyol_all_product_matching_control_now_background',
                };

                jQuery.post(ajaxurl, data, function(response){
                    if(response.status === 'success'){
                        Swal.fire({
                            title            : 'Bilgi',
                            text             : response.message,
                            icon             : 'info',
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
                });

            }
        })

        return false;
    })
    //TOPLU URUN EŞLEŞTİRME - OTOMATİK KONTROL ET

    //TOPLU URUN EŞLEŞTİRME - TRENDYOL BARKOD
    $(document).on('click', '.wc_trendyol_barcode_sync_btn', function(){

        $('.wc_trendyol_barcode:not([readonly])').each(function(i, e){

            var parent_tr  = $(e).parent().parent();
            var value      = $(e).val();
            var wc_sub_sku = $(' input.wc_trendyol_product_sku_input', parent_tr).val();
            if(value == ''){
                console.log(wc_sub_sku)
                $(e).val(wc_sub_sku);
            }

        })

        return false;
    })
    //TOPLU URUN EŞLEŞTİRME - TRENDYOL BARKOD

});