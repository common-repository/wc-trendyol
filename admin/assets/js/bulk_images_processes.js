jQuery(function($){

    //TOPLU GÖRSEL İŞLEMLERİ
    function add_product_image(element, wc_product_id, image_id){

        var data = {
            'action': 'wc_trendyol_product_batch_images_processing_add_images',
            wc_product_id,
            image_id,
        };

        jQuery.post(ajaxurl, data, function(response){
            if(response.status === 'success'){
                $(element).removeClass('wait')
            }else{
                Swal.fire({
                    title            : 'Bilgi',
                    text             : response.message,
                    icon             : 'warning',
                    confirmButtonText: 'Tamam'
                })
            }

            // $(me).prop('disabled', false);
        });

    }

    function del_product_image(element, wc_product_id, image_id){

        var data = {
            'action': 'wc_trendyol_product_batch_images_processing_del_images',
            wc_product_id,
            image_id,
        };

        jQuery.post(ajaxurl, data, function(response){
            if(response.status === 'success'){
                $(element).remove();
            }else{
                Swal.fire({
                    title            : 'Bilgi',
                    text             : response.message,
                    icon             : 'warning',
                    confirmButtonText: 'Tamam'
                })
            }
        });

    }

    function send_trendyol_images(wc_product_id){

        swal_wait();

        var data = {
            'action': 'wc_trendyol_product_batch_images_processing_save_trendyol',
            wc_product_id,
        };

        jQuery.post(ajaxurl, data, function(response){
            if(response.status === 'success'){
                Swal.fire({
                    title            : 'Bilgi',
                    text             : response.message,
                    icon             : 'warning',
                    confirmButtonText: 'Tamam'
                })
            }else{
                Swal.fire({
                    title            : 'Bilgi',
                    text             : response.message,
                    icon             : 'warning',
                    confirmButtonText: 'Tamam'
                })
            }
        });

    }

    $(document).on('click', '.wc_trendyol_add_images_product_btn', function(){

        var me            = $(this);
        var parent        = $(me).parent().parent();
        var wc_product_id = $(this).data('product_id');
        var frame         = wp.media({
            title   : 'Ürününüz için görsel seçiniz',
            button  : {
                text: 'Görsel Ekle'
            },
            multiple: true  // Set to true to allow multiple files to be selected
        });

        frame.on('close', function(){
            var selection = frame.state().get('selection');
            if(!selection.length){
                // clearField();
            }
        });

        frame.on('select', function(){

            var images = frame.state().get('selection')
            images.each(function(attachment){
                var image_id = attachment.attributes.id;
                var element  = $('<div class="wc_trendyol_product_image_content wait"><img src="' + attachment.attributes.url + '" width="100"/><div class="wc_trendyol_images_del_btn" data-tooltip="true" data-tooltip_text="Görseli Sil" data-product_id="' + wc_product_id + '" data-image_id="' + attachment.attributes.id + '"><i class="fa-solid fa-xmark"></i></div></div>');
                $(' .wc_trendyol_images_list', parent).append(element);
                add_product_image(element, wc_product_id, image_id)
            });

        });

        frame.open();

        return false;
    })

    $(document).on('click', '.wc_trendyol_images_del_btn', function(){

        var parent        = $(this).parent();
        var element       = $(parent).addClass('wait');
        var wc_product_id = $(this).data('product_id');
        var image_id      = $(this).data('image_id');

        del_product_image(element, wc_product_id, image_id);

        return false;
    })

    $(document).on('click', '.wc_trendyol_send_images_trendyol_line_btn', function(){

        var wc_product_id = $(this).data('product_id');
        console.log(wc_product_id);

        send_trendyol_images(wc_product_id);

        return false;
    })
    //TOPLU GÖRSEL İŞLEMLERİ

});