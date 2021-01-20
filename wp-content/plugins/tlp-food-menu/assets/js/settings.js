(function ($) {

    $(document).ready(function () {
        $(".rt-tab-nav li:first-child a").trigger('click');
    });

    if ($.fn.select2 && $(".fmp-select2").length) {
        $("select.fmp-select2").select2({
            minimumResultsForSearch: Infinity
        });
    }

    if ($(".fmp-color").length) {
        $(".fmp-color").wpColorPicker();
    }

    if ($("#fmsc_sc_settings_meta .fmp-color").length) {
        var cOptions = {
            defaultColor: false,
            change: function (event, ui) {
                renderFmpPreview();
            },
            clear: function () {
                renderFmpPreview();
            },
            hide: true,
            palettes: true
        };
        $("#fmsc_sc_settings_meta .fmp-color").wpColorPicker(cOptions);
    }

    /* rt tab active navigation */
    $(".rt-tab-nav li").on('click', 'a', function (e) {
        e.preventDefault();
        var $this = $(this),
            container = $this.parents('.rt-tab-container'),
            nav = container.children('.rt-tab-nav'),
            content = container.children(".rt-tab-content"),
            $id = $this.attr('href');
        content.hide();
        nav.find('li').removeClass('active');
        $this.parent().addClass('active');
        container.find($id).show();
    });


    function renderFmpPreview() {
        var target = $("#fmsc_sc_settings_meta");
        if (target.length) {
            var data = target.find('input[name],select[name],textarea[name]').serialize();
            data = data + '&' + $.param({'sc_id': $('#post_ID').val() || 0});
            fmpPreviewAjaxCall(null, 'fmpPreviewAjaxCall', data, function (data) {
                if (!data.error) {
                    $("#fmp-preview-container").html(data.data);
                }
            });
        }
    }

    $("#fmsc_sc_settings_meta").on('change', 'select,input', function () {
        renderFmpPreview();
    });
    $("#fmsc_sc_settings_meta").on("input propertychange", function () {
        renderFmpPreview();
    });
    renderFmpPreview();

    function fmpPreviewAjaxCall(element, action, arg, handle) {
        var data;
        if (action) data = "action=" + action;
        if (arg) data = arg + "&action=" + action;
        if (arg && !action) data = arg;

        var n = data.search(fm.nonceId);
        if (n < 0) {
            data = data + "&" + fm.nonceId + "=" + fm.nonce;
        }
        $.ajax({
            type: "post",
            url: fm.ajaxurl,
            data: data,
            beforeSend: function () {
                $('#fmsc_sc_preview_meta').addClass('loading');
                $('.fmp-response .spinner').addClass('is-active');
            },
            success: function (data) {
                $('#fmsc_sc_preview_meta').removeClass('loading');
                $('.fmp-response .spinner').removeClass('is-active');
                handle(data);
            }
        });
    }


    $("#fmp-settings-form").on('submit', function (e) {
        e.preventDefault();
        var form = $(this),
            response_wrap = form.next('.rt-response'),
            arg = form.serialize(),
            bindElement = $('#tlpSaveButton');
        response_wrap.hide();
        AjaxCall(bindElement, 'tlpFmSettingsUpdate', arg, function (data) {
            if (!data.error) {
                response_wrap.removeClass('error').addClass('success');
                response_wrap.show('slow').text(data.msg);
            } else {
                response_wrap.addClass('error').removeClass('success');
                response_wrap.show('slow').text(data.msg);
            }
        });
    });

    function AjaxCall(element, action, arg, handle) {
        var data;
        if (action) data = "action=" + action;
        if (arg) data = arg + "&action=" + action;
        if (arg && !action) data = arg;
        var n = data.search("tlp_fm_nonce");
        if (n < 0) {
            data = data + "&tlp_fm_nonce=" + tpl_fm_var.tlp_fm_nonce;
        }
        $.ajax({
            type: "post",
            url: ajaxurl,
            data: data,
            beforeSend: function () {
                $("<span class='tlp_loading'>Loading ...</span>").insertAfter(element);
            },
            success: function (data) {
                $(".tlp_loading").remove();
                handle(data);
            }
        });
    }
})(jQuery);
