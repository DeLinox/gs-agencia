var getScript = jQuery.getScript;
jQuery.getScriptA = function(resources, callback) {
    var scripts = [];

    if (typeof(resources) === 'string') { scripts.push(resources) } else { scripts = resources; }

    var length = scripts.length,
        handler = function() { counter++; },
        deferreds = [],
        counter = 0,
        idx = 0;

    $.ajaxSetup({ async: false });
    for (; idx < length; idx++) {
        deferreds.push(
            getScript(scripts[idx], handler)
        );
    }

    jQuery.when.apply(null, deferreds).then(function() {
        callback();
    });
};
/**evaluar si no se repiten*/
var _cf = (function() {
  function _shift(x) {
    var parts = x.toString().split('.');
    return (parts.length < 2) ? 1 : Math.pow(15, parts[1].length);
  }
  return function() { 
    return Array.prototype.reduce.call(arguments, function (prev, next) { return prev === undefined || next === undefined ? undefined : Math.max(prev, _shift (next)); }, -Infinity);
  };
})();

Math.a = function () {
  var f = _cf.apply(null, arguments); if(f === undefined) return undefined;
  function cb(x, y, i, o) { return x + f * y; }
  return Array.prototype.reduce.call(arguments, cb, 0) / f;
};

Math.s = function (l,r) { var f = _cf(l,r); return (l * f - r * f) / f; };

Math.m = function () {
  var f = _cf.apply(null, arguments);
  function cb(x, y, i, o) { return (x*f) * (y*f) / (f * f); }
  return Array.prototype.reduce.call(arguments, cb, 1);
};

Math.d = function (l,r) { var f = _cf(l,r); return (l * f) / (r * f); };
/****************/
function esNumeroPositivo(value) {
    var patron = /^\d+(\.\d+)?$/,
            bool = patron.test(value);
    return bool;
}
function myRound(response) {
    response = parseFloat(response).toFixed(3);
    response = parseFloat(response).toFixed(2);
    return response;
}

$(function() {
    $.gs_loader = $('<div>').hide();
    $.gs_loader.append($('<div>', {
        'class': 'ui-widget-overlay',
        'style': 'z-index:9998'
    })).append = ($('<div>').html('<img src="' + $("#baseurl").val() + 'assets/img/cubo-loader.gif"/>').css({
        'position': 'fixed',
        'font': 'bold 12px Verdana, Arial, Helvetica, sans-serif',
        'left': '50%',
        'top': '50%',
        'z-index': '9999',
        'margin-left': '-32px',
        'margin-top': '-32px'
    })).appendTo($.gs_loader);
    $.gs_loader.appendTo($('body'));
});

function replaceAll(str, find, replace) {
    return str.replace(new RegExp(find, 'g'), replace);
}

function IsJsonStr(str) {
    try {
        var result = $.parseJSON(str);
    } catch (e) {
        return false;
    }
    return result;
}

function jsoneval_sData(data, callbackfn, eval) {
    if (!(data && (data = IsJsonStr(data)))) {
        data = {};
        data.mensaje = 'Error al recuperar datos del servidor';
        data.exito = false;
    }
    if (typeof callbackfn == 'function') {
        callbackfn.call(this, data);
    }
}

(function($) {
    $.fn.serializeJSON = function(obj) {
        var json = {};
        if (typeof(obj) != 'undefined')
            for (var k in obj)
                json[obj[k]] = [];
        $.each($(this).serializeArray(), function() {
            if (typeof(json[this.name]) == 'undefined')
                json[this.name] = this.value;
            else if (typeof(json[this.name]) == 'object')
                json[this.name].push(this.value);
        });
        return json;
    };

    $.fn.nextFocus = function() {
        $(this).bind("keydown", function(e) {
            var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
            if (key == 13) {
                e.preventDefault();
                var inputs = $(this).closest('form').find(':input[type=text]:visible');
                inputs.eq(inputs.index(this) + 1).focus();
            }
        });
    }

    $.fn.load_dialog = function(config) {
        var $contenedor;
        if (config.content !== undefined)
            $contenedor = config.content.appendTo($('body'));
        else
            $contenedor = $('<div class="modal fade" tabindex="-1">').appendTo($('body'));
        var set_dialog = function() {
            var ftmp = config.close;
            config.close = function() {
                if (ftmp !== undefined)
                    ftmp();
                $contenedor.remove();
            }
            $contenedor.find('.modal-title').text(config.title);
            $contenedor.modal({ 'show': true, backdrop: 'static' });
            $contenedor.on('hidden.bs.modal', function(e) {
                $contenedor.remove();
            })
            $.gs_loader.hide();
            if (config.loaded !== undefined)
                config.loaded($contenedor);
        }
        $.gs_loader.show();
        var url = $(this).attr('href');
        if (config.custom_url !== undefined)
            url = config.custom_url;
        if (url !== undefined) {
            $contenedor.load(url, config.data, function() {
                if (typeof(config.script) != 'undefined')
                    $.getScriptA(config.script, set_dialog);
                else
                    set_dialog();
            });
        } else {
            if (typeof(config.script) != 'undefined')
                $.getScriptA(config.script, set_dialog);
            else
                set_dialog();
        }
        return $contenedor;
    }

    jQuery.fn.dialog = function(options) {

        var $contdiag = $(this);
        if (options == "close") {
            $contdiag.modal('hide');
            console.log($contdiag)
            return false;
        }
        $html = $contdiag.html();
        $contdiag = $(diag);
        $contdiag.appendTo($('body')).find('.modal-body').html($html);
        $contdiag.modal({ 'show': true, backdrop: 'static' });
        $contdiag.on('hidden.bs.modal', function(e) {
            $contdiag.remove();
        });
        return $contdiag;
    }

    $.fn.getSerial = function(diselse, array) {
        var serial;
        if (diselse) {
            var backup = [];
            $(':disabled[name]', this).each(function() {
                $(this).attr("disabled", false);
                backup.push($(this));
            });
            if (typeof(array) == 'undefined')
                serial = this.serialize(); //serializar form
            else
                serial = this.serializeArray();

            $.each(backup, function() {
                this.attr("disabled", true);
            });
        } else {
            if (typeof(array) == 'undefined')
                serial = this.serialize();
            else
                serial = this.serializeArray();
        }
        return serial;
    }
    $.fn.dval = function (val) {
        if(typeof(val) == 'undefined'){
            return $(this).attr('dval')
        }
        $(this).val(myRound(val));
        $(this).attr('dval',val);
        return $(this);
    }
    $.fn.formPost = function(diselse, objdata, callbackfn) {
        if (typeof(objdata) == 'function') {
            callbackfn = objdata;
            objdata = {};
        }
        $.gs_loader.show();
        var serial = $(this).getSerial(diselse);
        serial = serial + '&' + $.param(objdata);
        $.post(this.attr('action'), serial, function(data) {
            $.gs_loader.hide();
            jsoneval_sData(data, callbackfn);
        }, 'html').fail(function(error) { $.gs_loader.hide();
            alert(error.responseText.replace(/(<([^>]+)>)/ig, "")) });
        return false;
    }
})(jQuery);


var htmldialog = `<div class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="myModalLabel">{title}</h4>
          </div>
          <div class="modal-body">
              ...
          </div>
          <div class="modal-footer">
            {btns}
          </div>
      </div>
    </div>
    </div>`;


$.alert = function(content) {
    html = replaceAll(htmldialog, "{title}", "Alerta");
    html = replaceAll(html, "{btns}", '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>');
    var $container = $(html);
    $container.find('.modal-body').html(content);
    $container.appendTo($('body')).find('.modal-body').html(content);
    $container.modal({ 'show': true, backdrop: 'static' });
    $container.on('hidden.bs.modal', function(e) {
        $container.remove();
    })
}
$.confirm = function(content, accept, cancel) {
    html = replaceAll(htmldialog, "{title}", "Confirmar");
    html = replaceAll(html, "{btns}", '<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary">Aceptar</button>');
    var $container = $(html);
    $container.find('.modal-body').html(content);
    $container.appendTo($('body')).find('.modal-body').html(content);
    $container.modal({ 'show': true, backdrop: 'static' });
    $container.on('hidden.bs.modal', function(e) {
        $container.remove();
    })

    $container.find('.btn-primary').click(function() {
        $container.on('hidden.bs.modal', function(e) {
            if (typeof(accept) == 'function')
                accept();
        })
        $container.modal('hide');
    });
}

$(function() {

    var selected = [];
    $.fn.reset_selected = function() {
        selected = [];
    }


    $.fn.DTFilter=function(table){
        oTimerId = null;
        $(this).keyup(function(){
            window.clearTimeout(oTimerId);
            oTimerId = window.setTimeout(function() {
                table.draw();
            }, 500);
        });
    }

    $.fn.load_simpleTable = function(config, buton, bot) {
        var $table = $(this);
        var wch = $table.attr('wch');

        var cols = Array();
        if (buton == true) {
            if (wch) {
                cols.push({
                    "data": null,
                    "orderable": false,
                    "width": "30",
                    'render': function(data, type, full, meta) {
                        if (typeof(bot) === "undefined") {
                            return '<input type="checkbox">';
                        } else {
                            return bot;
                        }
                    }
                })
            }
        }

        $table.find('tr .ths').each(function(i, item) {
            cols.push({ "data": $(item).text(), className: "edit" });
        })

        var table_config = {
            "processing": true,
            "serverSide": true,
            "bResetDisplay": true,
            "order": config.order,
            "ajax": {
                "url": config.data_source,
                "type": "POST",
                "data": function(data) {
                    return $.extend(data, $('' + config.cactions).serializeJSON());
                }
            },
            "rowCallback": function(row, data) {
                if (wch) {
                    var cellc = $(row).find('td').slice(1, 2);
                    var gb = "transparent", letra = "#000";
                    if(data.DT_htl){
                        if(data.DT_htl == "SI"){
                            var styles = {background: "#FF69B4"};
                            var cellt = $(row).find('td').slice(6,7);
                            cellt.css(styles);
                        }
                    }
                    if(data.DT_RowEsta){
                        $(row).find('td').slice(4, 6).css("text-align","center");
                        $(row).find('td').slice(7, 10).css("text-align","center");
                    }
                    if(data.DT_RowColor){
                        var txtc = "#000";
                        switch (data.DT_RowColor) {
                            case '1':
                                bgt = "#FF6600";
                                txtc = "#fff";
                                break;
                            case '2':
                                bgt = "#FFFF00";
                                break;
                            case '3':
                                bgt = "#00B0F0";
                                txtc = "#fff";
                                break;
                            default:
                                bgt = "#FFF";
                        }
                        var styles = {background: bgt, color: txtc};
                        var cellt = $(row).find('td').slice(3, 4);
                        cellt.css(styles);
                    }
                    if(data.DT_RowEsta == 'CONFIRMADO'){ gb = "#5cb85c"; letra = "#fff"}
                    else if(data.DT_RowEsta == 'ANULADO'){ gb = "#d9534f"; letra = "#fff";}
                    if(data.DT_PEstado == 'ANULADO'){$(row).find('td').slice(2, 3).addClass('anulado');}
                    var styles = {background: gb, color: letra};
                    cellc.css(styles);

                    $(row).find('input[type=checkbox]').change(function(e, a) {
                        var index = $.inArray(data.DT_RowId, selected);
                        if (index === -1&&$(this).is(':checked')) {
                            selected.push(data.DT_RowId);
                        } else if(!$(this).is(':checked')){
                            selected.splice(index, 1);
                        }
                        if($(this).is(':checked')) $(row).addClass('selected');
                        else $(row).removeClass('selected');
                        config.oncheck.call(this, row, data, selected);
                        e.preventDefault();
                        e.stopPropagation();
                    })
                    if ($.inArray(data.DT_RowId, selected) !== -1) {
                        $(row).addClass('selected').find('input[type=checkbox]').prop("checked", true);
                    }
                }
                if (typeof config.onrow == 'function') {
                    config.onrow.call(this, row, data, selected);
                }
            },
            "lengthChange": false,
            "searching": false,
            "pageLength": 60,
            "columns": cols,
        };
        var table = $table.DataTable(table_config)
        return table;
    }

})