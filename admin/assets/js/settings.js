jQuery(function ($) {

    //MENU JS
    function menu_tab(menu_name) {

        $('.wc_trendyol_settings_container .wc_trendyol_settings_sidebar .wc_trendyol_settings_menus li a').removeClass('active')
        $('.wc_trendyol_settings_container .wc_trendyol_settings_sidebar .wc_trendyol_settings_menus li[data-menu_name="' + menu_name + '"] a').addClass('active')

        $('.wc_trendyol_settings_content .wc_trendyol_tab_content').hide();
        $('.wc_trendyol_settings_content .wc_trendyol_tab_content[data-menu_name="' + menu_name + '"]').show();

    }

    var url_hash = (window.location.hash).replace('#', '') || 'general_settings';
    menu_tab(url_hash);

    $('.wc_trendyol_settings_container .wc_trendyol_settings_sidebar .wc_trendyol_settings_menus li').on('click', function () {
        var menu_name = $(this).data('menu_name');
        menu_tab((menu_name));
    })
    //MENU JS

    //LICENSE CONTROL
    $('.wc_trendyol_license_control_btn').on('click', function () {

        swal_wait();

        var me = $(this);
        $(me).prop('disabled', true);

        var wc_trendyol_license = $('.wc_trendyol_license').val();

        var data = {
            'action': 'wc_trendyol_license_control',
            wc_trendyol_license
        };

        jQuery.post(ajaxurl, data, function (response) {
            if (response.status === 'success') {
                Swal.fire({
                    title: 'Eklenti Aktif Edildi!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'Tamam'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $('.wc_trendyol_settings_frm').slideDown();
                    }
                })
            } else {
                Swal.fire({
                    title: 'Bilgi',
                    text: response.message,
                    icon: 'warning',
                    confirmButtonText: 'Tamam'
                })
            }
            $(me).prop('disabled', false);
        }).fail(function (response) {
            Swal.fire({
                title: 'Bilgi',
                text: 'Sorgu hatası',
                icon: 'warning',
                confirmButtonText: 'Tamam'
            })
        });

        return false;
    })
    //LICENSE CONTROL

    //SETTINGS SAVE BTN
    $('.wc_trendyol_settings_save_btn').on('click', function () {

        var form = $('.wc_trendyol_settings_frm').get(0);
        if(form.checkValidity()){

            swal_wait();

            var me = $(this);
            $(me).prop('disabled', true);

            var wc_trendyol_settings = $('.wc_trendyol_settings_frm').serialize();

            var data = {
                'action': 'wc_trendyol_save_settings',
                wc_trendyol_settings
            };

            jQuery.post(ajaxurl, data, function (response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'İşlem Başarılı',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'Tamam'
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    })
                } else {
                    Swal.fire({
                        title: 'Bilgi',
                        text: response.message,
                        icon: 'warning',
                        confirmButtonText: 'Tamam'
                    })
                }

                $(me).prop('disabled', false);
            }).fail(function (response) {
                Swal.fire({
                    title: 'Bilgi',
                    text: 'Sorgu hatası',
                    icon: 'warning',
                    confirmButtonText: 'Tamam'
                })
            });

        }else{
            return true;
        }

        return false;
    })
    //SETTINGS SAVE BTN

    //MAIN BRAND SELECT
    $('.wc_trendyol_brand_search').on('change', function () {

        var main_brand_id = $(this).val();
        console.log(main_brand_id)

    })
    //MAIN BRAND SELECT

    //CAT PAIRING
    $('.wc_trendyol_cat_pairing_save_btn').on('click', function () {

        swal_wait();

        var me = $(this);
        $(me).prop('disabled', true);

        var trendyol_category_id = $('.wc_trendyol_cat_pairing_frm').serialize();

        var data = {
            action: 'wc_trendyol_categories_matching',
            trendyol_category_id
        };

        jQuery.post(ajaxurl, data, function (response) {
            if (response.status === 'success') {
                Swal.fire({
                    title: 'İşlem Başarılı',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'Tamam'
                })
            } else {
                Swal.fire({
                    title: 'Bilgi',
                    text: response.message,
                    icon: 'warning',
                    confirmButtonText: 'Tamam'
                })
            }

            $(me).prop('disabled', false);
        }).fail(function (response) {
            Swal.fire({
                title: 'Bilgi',
                text: 'Sorgu hatası',
                icon: 'warning',
                confirmButtonText: 'Tamam'
            })
        });

        return false;
    })
    //CAT PAIRING

    //OTHER PLUGINS - INSTALL BTN
    $('.wc_trendyol_install_other_plugin_btn').on('click', function () {

        var me = $(this);
        var me_old_text = $(me).text();
        $(me).prop('disabled', true).html('<i class="fa-solid fa-hourglass-start fa-bounce"></i>');

        var nonce = $(this).data('nonce');
        var plugin_slug = $(this).data('plugin_slug');

        var data = {
            'action': 'wc_trendyol_install_other_plugins_ajax',
            plugin_slug,
            nonce
        };

        jQuery.post(ajaxurl, data, function (response) {
            if (response.status === 'success') {
                Swal.fire({
                    title: 'İşlem Başarılı',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'Tamam'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                })
            } else {
                Swal.fire({
                    title: 'Bilgi',
                    text: response.message,
                    icon: 'warning',
                    confirmButtonText: 'Tamam'
                })
            }
        }).fail(function (response) {
            Swal.fire({
                title: 'Bilgi',
                text: 'Sorgu hatası',
                icon: 'warning',
                confirmButtonText: 'Tamam'
            })
        });

        return false;
    })
    //OTHER PLUGINS - INSTALL BTN

    //OTHER PLUGINS - ACTIVE BTN
    $('.wc_trendyol_active_other_plugin_btn').on('click', function () {

        var me = $(this);
        var me_old_text = $(me).text();
        $(me).prop('disabled', true).html('<i class="fa-solid fa-hourglass-start fa-bounce"></i>');

        var nonce = $(this).data('nonce');
        var plugin_slug = $(this).data('plugin_slug');

        var data = {
            'action': 'wc_trendyol_active_other_plugins_ajax',
            plugin_slug,
            nonce
        };

        jQuery.post(ajaxurl, data, function (response) {
            if (response.status === 'success') {
                Swal.fire({
                    title: 'İşlem Başarılı',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'Tamam'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                })
            } else {
                Swal.fire({
                    title: 'Bilgi',
                    text: response.message,
                    icon: 'warning',
                    confirmButtonText: 'Tamam'
                })
            }
        }).fail(function (response) {
            Swal.fire({
                title: 'Bilgi',
                text: 'Sorgu hatası',
                icon: 'warning',
                confirmButtonText: 'Tamam'
            })
        });

        return false;
    })
    //OTHER PLUGINS - ACTIVE BTN
});