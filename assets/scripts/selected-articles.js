jQuery(document).on('click', '[seleced-articles-role="cache"]', function (e) {
    e.stopPropagation();
    e.preventDefault();
    var $this = jQuery(this), $cache = $this.parent().next('.cache');
    $cache.children().slice(0, 10).insertBefore($this);
    if (!$cache.children().length) {
        $this.remove();
    }
});

function updateindex(number, args) {
    if (number === '__i__') {
        var parts = args.list.split(number);
        var existing = jQuery('[id^="' + parts[0] + '"][id$="' + parts[1] + '"]:not([id*="' + number + '"])');
        var exids = existing.map(function (i, el) {
            return parseInt(el.id.replace(parts[0], '').replace(parts[1], ''));
        });
        var max = Math.max.apply(Math, exids);
        for(var i in args){
            args[i] = args[i].replace(number, max);
        }
    }
}