jQuery(function($){

    //KATEGORİ EŞLEŞTİRME - KAYDET
    $(document).on('click', '.wc_trendyol_categories_matching_btn', function(){

        swal_wait('Okuyunuz...', 'Sadece seçtiğiniz kategoriler eşleştiriliyor.');

        var me = $(this);

        $(me).prop('disabled', true);

        var form_data = $('.wc_trendyol_categories_matching_frm').serialize();

        var data = {
            'action': 'wc_trendyol_categories_matching_save_all',
            form_data
        };

        jQuery.post(ajaxurl, data, function(response){
            if(response.status === 'success'){
                location.reload();
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
    //KATEGORİ EŞLEŞTİRME - KAYDET

    //ALT KATEGORİLERE UYGULA
    $(document).on('click', '.apply_sub_cat_main_trendyol_cat_id', function(){

        var parent          = $(this).closest('.wc_trendyol_form_group_inline');
        var wc_cat_id       = $(parent).data('wc_cat_id');
        var trendyol_cat_id = $(' select',parent).val();

        $('.wc_trendyol_table .wc_trendyol_form_group_inline[data-wc_parent_id="' + wc_cat_id + '"] select').each(function(i,e){
            $(e).val(trendyol_cat_id).trigger('change');
        })

        return false;
    })
    //ALT KATEGORİLERE UYGULA

});