jQuery(function($){

    //TÜM FİYATLARI SİL
    $('.wc_trendyol_delete_all_product_sync_btn').on('click', function(event){

        Swal.fire({
            title             : 'Emin Misiniz?',
            text              : "Tüm aktarma işlemi duracak. Bu işlem geri alınamaz!!!",
            icon              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor : '#d33',
            confirmButtonText : 'Sil',
            cancelButtonText  : 'İptal',
        }).then((result) => {
            if(result.isConfirmed){

                swal_wait();

                var data = {
                    'action': 'wc_trendyol_delete_all_product_sync',
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
    //TÜM FİYATLARI SİL

    //ZAMANLANMIŞ EYLEMLERİ TEMİZLE
    $('.wc_trendyol_clear_actions_btn').on('click', function(event){

        Swal.fire({
            title             : 'Emin Misiniz?',
            text              : "Bu işlem geri alınamaz!!!",
            icon              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor : '#d33',
            confirmButtonText : 'Temizle',
            cancelButtonText  : 'İptal',
        }).then((result) => {
            if(result.isConfirmed){

                swal_wait();

                var data = {
                    'action': 'wc_trendyol_delete_all_actions',
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
    //ZAMANLANMIŞ EYLEMLERİ TEMİZLE

    //TRENDYOL ÜRÜN BİLGİLERİNİ SIFIRLAR
    $('.wc_trendyol_delete_all_product_trendyol_meta_btn').on('click', function(event){

        Swal.fire({
            title             : 'Emin Misiniz?',
            text              : "Bu işlem geri alınamaz!!!",
            icon              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor : '#d33',
            confirmButtonText : 'Sil',
            cancelButtonText  : 'İptal',
        }).then((result) => {
            if(result.isConfirmed){

                swal_wait();

                var data = {
                    'action': 'wc_trendyol_delete_all_product_trendyol_meta',
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
    //TRENDYOL ÜRÜN BİLGİLERİNİ SIFIRLAR


    //TRENDYOL ÜRÜN BİLGİLERİNİ SIFIRLAR
    $('.wc_trendyol_plugin_reset_btn').on('click', function(event){

        Swal.fire({
            title             : 'Emin Misiniz?',
            text              : "Bu işlem geri alınamaz!!!",
            icon              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor : '#d33',
            confirmButtonText : 'Her şeyi sil',
            cancelButtonText  : 'İptal',
        }).then((result) => {
            if(result.isConfirmed){

                swal_wait();

                var data = {
                    'action': 'wc_trendyol_plugin_reset',
                };

                jQuery.post(ajaxurl, data, function(response){
                    if(response.status === 'success'){

                        Swal.fire({
                            title            : 'Bilgi',
                            text             : response.message,
                            icon             : 'success',
                            confirmButtonText: 'Tamam'
                        }).then(function(result) {
                            location.reload();
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
    //TRENDYOL ÜRÜN BİLGİLERİNİ SIFIRLAR

});