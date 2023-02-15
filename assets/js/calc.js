(function() {
    /**
     * Ajuste decimal de un número.
     *
     * @param {String}  tipo  El tipo de ajuste.
     * @param {Number}  valor El numero.
     * @param {Integer} exp   El exponente (el logaritmo 10 del ajuste base).
     * @returns {Number} El valor ajustado.
     */
    function decimalAdjust(type, value, exp) {
        // Si el exp no está definido o es cero...
        if (typeof exp === 'undefined' || +exp === 0) {
            return Math[type](value);
        }
        value = +value;
        exp = +exp;
        // Si el valor no es un número o el exp no es un entero...
        if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
            return NaN;
        }
        // Shift
        value = value.toString().split('e');
        value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
        // Shift back
        value = value.toString().split('e');
        return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
    }

    // Decimal round
    if (!Math.round10) {
        Math.round10 = function(value, exp) {
            return decimalAdjust('round', value, exp);
        };
    }
    // Decimal floor
    if (!Math.floor10) {
        Math.floor10 = function(value, exp) {
            return decimalAdjust('floor', value, exp);
        };
    }
    // Decimal ceil
    if (!Math.ceil10) {
        Math.ceil10 = function(value, exp) {
            return decimalAdjust('ceil', value, exp);
        };
    }
})();


function myRound(response) {
    response = Math.round10(response, -2)
    return parseFloat(response).toFixed(2);

}

/**evaluar si no se repiten*/
var _cf = (function() {
    function _shift(x) {
        var parts = x.toString().split('.');
        return (parts.length < 2) ? 1 : Math.pow(15, parts[1].length);
    }
    return function() {
        return Array.prototype.reduce.call(arguments, function(prev, next) { return prev === undefined || next === undefined ? undefined : Math.max(prev, _shift(next)); }, -Infinity);
    };
})();

Math.a = function() {
    var f = _cf.apply(null, arguments);
    if (f === undefined) return undefined;

    function cb(x, y, i, o) { return x + f * y; }
    return Array.prototype.reduce.call(arguments, cb, 0) / f;
};

Math.s = function(l, r) { var f = _cf(l, r); return (l * f - r * f) / f; };

Math.m = function() {
    var f = _cf.apply(null, arguments);

    function cb(x, y, i, o) { return (x * f) * (y * f) / (f * f); }
    return Array.prototype.reduce.call(arguments, cb, 1);
};

Math.d = function(l, r) { var f = _cf(l, r); return (l * f) / (r * f); };
/****************/


function esPositivo(value) {
    var patron = /^\d+(\.\d+)?$/,
        bool = patron.test(value);
    return bool;
}


(function($) {
    $.fn.inputInt = function() {
        $(this).css('text-align', 'right').keypress(function(e) {
            var key = e.which;
            if (key != 8 && key != 0 && !(key >= 48 && key <= 57))
                e.preventDefault();
        });
    }
    $.fn.inputFloat = function() {
        $(this).css('text-align', 'right').keypress(function(e) {

            var key = e.which;
            if (key == 46) {
                if ($(this).val().indexOf('.') != -1)
                    e.preventDefault();
            } else if (key != 8 && key != 45 && key != 0 && !(key >= 48 && key <= 57))
                e.preventDefault();
        }).blur(function() {
            if ($(this).val() != '') {
                $(this).val((Math.round(parseFloat($(this).val()) * 100) / 100).toFixed(2));
            }
        });
    }


    $.fn.dval = function(val) {
        if (typeof(val) == 'undefined') {
            return $(this).attr('dval')
        }
        $(this).val(myRound(val));
        $(this).attr('dval', val);
        return $(this);
    }

})(jQuery);