(function($) {
    var plugins = ['combobox','datagrid','tree'];
    for(key in plugins){
        $.fn[plugins[key]].defaults.method = 'get';
    }
    
})(jQuery);