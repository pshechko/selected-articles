jQuery(document).on('click', '[seleced-articles-role="cache"]', function (e) {
    e.stopPropagation();
    e.preventDefault();

    var $this = jQuery(this),
            $cache = $this.parent().next('.cache');

    $cache
            .children()
            .slice(0, 10)
            .insertBefore($this);

    if (!$cache.children().length)
        $this.remove();
});

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

function make_sortable(number, args) {
    
    update_index(number, args);
    
    jQuery("#" + args.list + ", #" + args.selected).sortable({
        connectWith: "#" + args.list + ", #" + args.selected,
        update: function (e, ui) {
            jQuery('#' + args.list + ' [seleced-articles-role="input"]').removeAttr('name');
            jQuery('#' + args.selected + ' [seleced-articles-role="input"]').each(function () {
                jQuery(this).attr('name', args.name).val(jQuery(this).attr('article-id'));
            });
        }
    }).disableSelection();
}