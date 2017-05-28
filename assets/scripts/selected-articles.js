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
    jQuery(document).on('click', '#' + ids.wrapper + ' [seleced-articles-role="cache"]', function (e) {
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

    jQuery(document).on('click', '#' + ids.selected + ' [seleced-articles-role="remove"]', function (e) {
        e.stopPropagation();
        e.preventDefault();
        jQuery(this).closest('[seleced-articles-role="article"]').insertAfter("#" + ids.list + " [seleced-articles-role='article']:last-of-type");
    });

    jQuery(document).on('click', '#' + ids.list + ' [seleced-articles-role="select"]', function (e) {
        e.stopPropagation();
        e.preventDefault();
        jQuery(this).closest('[seleced-articles-role="article"]').insertAfter("#" + ids.selected + " [seleced-articles-role='article']:last-of-type");
    });

    jQuery('#' + ids.search).on('change keyup', function () {
        var full_val = jQuery(this).val();
        var val = full_val.toLowerCase();
        var list = jQuery('#' + ids.list);
        var articles = list.find('[seleced-articles-role="article"]');
        if (val === "") {
            list.removeClass("search");
            list.find("[seleced-articles-role='forsearch']").remove();
        } else {
            list.addClass("search");
        }
        var unsuitable = articles.filter(function () {
            var title = jQuery(this).find('[seleced-articles-role="title"]');
            var search_query = val.toLowerCase();
            var title_original_content = title.text();
            var title_content = title_original_content.toLowerCase();
            var is_unsuitable = title_content.indexOf(search_query) === -1
            if (!is_unsuitable) {
                var forsearch = title.next('.forsearch');
                if (!forsearch.length) {
                    forsearch = title.clone().addClass('forsearch').attr('seleced-articles-role', 'forsearch').insertAfter(title);
                }
                var search_word_position = title_content.indexOf(search_query);
                var before_search = title_original_content.substr(0, search_word_position);
                var search_body = title_original_content.substr(search_word_position, full_val.length);
                var after_search = title_original_content.substr(search_word_position + full_val.length);

                var search_highlighted =
                        before_search +
                        "<span class='search-higlight'>" +
                        search_body +
                        "</span>" +
                        after_search

                forsearch.html(search_highlighted);
            }
            return is_unsuitable;
        });
        articles.addClass('hidden').not(unsuitable).removeClass('hidden')
    })
}

function init_tabs(jsparams) {
    jQuery("#" + jsparams.ids.wrapper).tabs();
    console.log("#" + jsparams.ids.wrapper);
}

function make_sortable(jsparams) {
    var ids = jsparams.ids;
    var hidden_trigger_change = jQuery('#'+ids.hidden_trigger_change);
   // console.log(hidden_trigger_change,hidden_trigger_change.val())

    jQuery("#" + ids.list + ", #" + ids.selected).sortable({
        connectWith: "#" + ids.list + ", #" + ids.selected,
        items: '> li:not(button)',
        update: function (e, ui) {
            jQuery('#' + ids.list + ' [seleced-articles-role="input"]').removeAttr('name');
            jQuery('#' + ids.selected + ' [seleced-articles-role="input"]').each(function () {
                jQuery(this).attr('name', ids.name).val(jQuery(this).attr('article-id'));
            });
            hidden_trigger_change.val(parseInt(hidden_trigger_change.val()) + 1).trigger('change');
        }
    }).disableSelection();
}
