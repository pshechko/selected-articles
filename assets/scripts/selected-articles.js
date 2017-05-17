function update_index(number, args) {
    if (number !== '__i__')
        return;

    var parts = args.list.split(number);
    var elements = jQuery('[id^="' + parts[0] + '"][id$="' + parts[1] + '"]:not([id*="' + number + '"])');
    elements = elements.map(function (index, el) {
        return parseInt(el.id.replace(parts[0], '').replace(parts[1], ''));
    });
    var max = Math.max.apply(Math, elements);
    for (var index in args) {
        args[index] = args[index].replace(number, max);
    }
}

function add_listeners(jsparams) {
    var ids = jsparams.ids;
    var vars = jsparams.vars;
    jQuery(document).on('click', '#'+ ids.wrapper +' [seleced-articles-role="cache"]', function (e) {
        e.stopPropagation();
        e.preventDefault();

        var $this = jQuery(this),
                $cache = $this.parent().next('.cache');

        $cache
                .children()
                .slice(0, vars.load_next)
                .insertBefore($this);

        if (!$cache.children().length)
            $this.remove();
    });
}

function init_tabs(jsparams){
    jQuery( "#"+jsparams.ids.wrapper ).tabs();
    console.log("#"+jsparams.ids.wrapper);
}

function make_sortable(jsparams) {
    var ids = jsparams.ids;
    jQuery("#" + ids.list +", #" + ids.selected).sortable({
        connectWith: "#" + ids.list + ", #" + ids.selected,
        update: function (e, ui) {
            jQuery('#' + ids.list + ' [seleced-articles-role="input"]').removeAttr('name');
            jQuery('#' + ids.selected + ' [seleced-articles-role="input"]').each(function () {
                jQuery(this).attr('name', ids.name).val(jQuery(this).attr('article-id'));
            });
        }
    }).disableSelection();
}
