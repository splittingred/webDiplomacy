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
});