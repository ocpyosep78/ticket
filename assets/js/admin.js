function strip_tags (input, allowed) {
    allowed = (((allowed || "") + "")
               .toLowerCase()
               .match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
        commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    return input.replace(commentsAndPhpTags, '').replace(tags, function($0, $1){
        return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    });
}

function make_slug(s) {
    s = s.trim().toLowerCase();
    if (s.length) {
        s = s.replace(/[^ a-zA-z0-9_-]+/g, '');
        s = s.replace(/[^a-zA-z0-9_]+/g, '_');
    }
    return s;
}

function make_excerpt(s, count) {
    s = s.trim();
    if ( s.length ) {
        s = strip_tags(s);
        var words = s.split(' ');
        s = '';
        while( s.length < count ) {
            var word = words.shift();
            s += word + ' ';
            if ( words.length == 0 ) break;
        }
        s = s.substr(0,s.length-1);
        s = s.substr(0,count);
        s = s.replace(/&[^;\s]{0,6}$/, '');
    }
    return s;
}

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number+'').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function htmlspecialchars (string) {
    string = string.toString();
    string = string.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    return string;
}

function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>'; // Adjust comment to avoid issue on phpjs.org display
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

$(function() {
    $("#delete_button").click(function() {
        if ($('.chkrow:checked').length)
            return confirm('Hapus data yang terpilih?');
        else return false;
    });

    $("#id_checkall").click(function() {
        if ( this.checked ) {
            $(".chkrow").attr("checked", "checked");
            $(".chkrow").parent().parent().addClass('sel');
        } else {
            $(".chkrow").removeAttr("checked");
            $(".chkrow").parent().parent().removeClass('sel');
        }
    });

    $(".chkrow").click(function() {
        if ( !this.checked ) {
            $("#id_checkall").removeAttr("checked");
            $(this).parent().parent().removeClass('sel');
        } else {
            $(this).parent().parent().addClass('sel');
            if ( $(".chkrow:checked").length == $(".chkrow").length ) {
                $("#id_checkall").attr("checked", "checked");
            }
        }
    });

    $(".selectrow tr").click(function() {
        var checked = $(this).childen('.chkrow').attr('checked');
        if ( !checked ) {
            $("#id_checkall").removeAttr("checked");
            $(this).removeClass('sel');
        } else {
            $(this).parent().parent().addClass('sel');
            if ( $(".chkrow:checked").length == $(".chkrow").length ) {
                $("#id_checkall").attr("checked", "checked");
            }
        }
    });

    $(".inline_text").click(function() {
        var id = $(this).attr('rel');
        if (id && !$("#inline_holder #inline"+id).length) {
            $("#inline_holder").append('<input type="hidden" name="id[]" value="'+id+'" id="inline'+id+'" />');
        }
        $(this).next( '.inline_edit' ).removeClass('inline_edit');
        $(this).hide();
    });

    $('html').click(function() {
        if ($("li.level1").hasClass('tap'))
            $("li.level1").removeClass('tap');
    });

    $("li.level1 > a").click(function(event) {
        event.stopPropagation();

        var $parent = $(this).parent();
        if ( $parent.hasClass('tap') ) {
            $parent.removeClass('tap');
        } else {
            $("li.level1").removeClass('tap');
            $parent.addClass('tap');
        }

        if ( !$(this).hasClass('sub') ) {
            return true;
        }
        return false;
    }).hover(function(){
        var $parent = $(this).parent();
        if ( $parent.hasClass('tap') ) {
            $parent.removeClass('tap');
        } else {
            $("li.level1").removeClass('tap');
            $parent.addClass('tap');
        }
    }, function(){
        var $parent = $(this).parent();
        if ( $parent.hasClass('tap') )
            $parent.removeClass('tap');
    });

    $("li.level1 > holder").hover(function(){},function(){
        var $parent = $(this).parent();
        if ( $parent.hasClass('tap') )
            $parent.removeClass('tap');
    });

    $('.codesh').click(function() {
        $(this).children('.folded,.unfolded').toggle();
    });
});
