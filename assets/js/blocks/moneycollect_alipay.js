!function (e) {
    var t = {};

    function n(o) {
        if (t[o]) return t[o].exports;
        var r = t[o] = {i: o, l: !1, exports: {}};
        return e[o].call(r.exports, r, r.exports, n), r.l = !0, r.exports
    }

    n.m = e, n.c = t, n.d = function (e, t, o) {
        n.o(e, t) || Object.defineProperty(e, t, {enumerable: !0, get: o})
    }, n.r = function (e) {
        "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, {value: "Module"}), Object.defineProperty(e, "__esModule", {value: !0})
    }, n.t = function (e, t) {
        if (1 & t && (e = n(e)), 8 & t) return e;
        if (4 & t && "object" == typeof e && e && e.__esModule) return e;
        var o = Object.create(null);
        if (n.r(o), Object.defineProperty(o, "default", {
            enumerable: !0,
            value: e
        }), 2 & t && "string" != typeof e) for (var r in e) n.d(o, r, function (t) {
            return e[t]
        }.bind(null, r));
        return o
    }, n.n = function (e) {
        var t = e && e.__esModule ? function () {
            return e.default
        } : function () {
            return e
        };
        return n.d(t, "a", t), t
    }, n.o = function (e, t) {
        return Object.prototype.hasOwnProperty.call(e, t)
    }, n.p = "", n(n.s = 5)
}([function (e, t) {
    e.exports = window.wp.element
}, function (e, t) {
    e.exports = window.wp.htmlEntities
}, function (e, t) {
    e.exports = window.wp.i18n
}, function (e, t) {
    e.exports = window.wc.wcBlocksRegistry
}, function (e, t) {
    e.exports = window.wc.wcSettings
}, function (e, t, n) {
    "use strict";
    n.r(t);
    var o = n(0), r = n(2), c = n(3), i = n(1), l = n(4);
    const u = Object(l.getSetting)("moneycollect_alipay_data", {}), a = Object(r.__)("MoneyCollect Payments", "moneycollect"),
        s = Object(i.decodeEntities)(u.title) || a, f = e => Object(i.decodeEntities)(u.description || ""), d = {
            name: "moneycollect_alipay",
            label: Object(o.createElement)(e => {
                const {PaymentMethodLabel: t} = e.components;
                return Object(o.createElement)(t, {text: s})
            }, null),
            content: Object(o.createElement)(f, null),
            edit: Object(o.createElement)(f, null),
            canMakePayment: () => !0,
            ariaLabel: s,
            supports: {features: u.supports}
        };
    Object(c.registerPaymentMethod)(d)
}]);