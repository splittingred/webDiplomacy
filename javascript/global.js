if (Prototype.BrowserFeatures.ElementExtensions) {
    let disablePrototypeJS = function (method, pluginsToDisable) {
        let handler = function (event) {
            event.target[method] = undefined;
            setTimeout(function () {
                delete event.target[method];
            }, 0);
        };
        pluginsToDisable.each(function (plugin) {
            jQuery(window).on(method + '.bs.' + plugin, handler);
        });
    },
    pluginsToDisable = ['collapse', 'dropdown', 'modal', 'tooltip', 'popover', 'tab'];
    disablePrototypeJS('show', pluginsToDisable);
    disablePrototypeJS('hide', pluginsToDisable);
}

jQuery(document).ready(function () {
    jQuery('[data-toggle="popover"]').popover({ delay: { show: 10, hide: 1000 }});
    jQuery('[data-toggle="tooltip"]').tooltip();
    jQuery('.select2').select2({ theme: 'bootstrap4' });

    jQuery('.select2-games').select2({
        ajax: {
            url: '/api/games.js',
            dataType: 'json',
            delay: 200,
            minimumInputLength: 3,
            data: function (params) {
                // Query parameters will be ?search=[term]&page=[page]
                return {
                    search: params.term,
                    page: params.page || 1
                }
            },
            processResults: function (data) {
                return {
                    results: jQuery.map(data.items, function (item) {
                        return {
                            text: item.name,
                            id: item.id
                        }
                    })
                };
            }
        }
    });
});