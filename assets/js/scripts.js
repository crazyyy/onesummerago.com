// Avoid `console` errors in browsers that lack a console.
(function () {
  var method;
  var noop = function () {};
  var methods = ['assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error', 'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log', 'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd', 'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'];
  var length = methods.length;
  var console = (window.console = window.console || {});

  while (length--) {
    method = methods[length];

    // Only stub undefined methods.
    if (!console[method]) {
        console[method] = noop;
    }
}
}());

// Place any jQuery/helper plugins in here.

(function() {
  (function() {
    function c(a) {
      this.t = {};
      this.tick = function(a, c, b) {
        var d = void 0 != b ? b : (new Date).getTime();
        this.t[a] = [d, c];
        if (void 0 == b) try {
          window.console.timeStamp("CSI/" + a)
        } catch (e) {}
      };
      this.tick("start", null, a)
    }
    var a;
    window.performance && (a = window.performance.timing);
    var h = a ? new c(a.responseStart) : new c;
    window.jstiming = {
      Timer: c,
      load: h
    };
    if (a) {
      var b = a.navigationStart,
        e = a.responseStart;
      0 < b && e >= b && (window.jstiming.srt = e - b)
    }
    if (a) {
      var d = window.jstiming.load;
      0 < b && e >= b && (d.tick("_wtsrt", void 0, b), d.tick("wtsrt_",
        "_wtsrt", e), d.tick("tbsd_", "wtsrt_"))
    }
    try {
      a = null, window.chrome && window.chrome.csi && (a = Math.floor(window.chrome.csi().pageT), d && 0 < b && (d.tick("_tbnd", void 0, window.chrome.csi().startE), d.tick("tbnd_", "_tbnd", b))), null == a && window.gtbExternal && (a = window.gtbExternal.pageT()), null == a && window.external && (a = window.external.pageT, d && 0 < b && (d.tick("_tbnd", void 0, window.external.startE), d.tick("tbnd_", "_tbnd", b))), a && (window.jstiming.pt = a)
    } catch (k) {}
  })();
  window.tickAboveFold = function(c) {
    var a = 0;
    if (c.offsetParent) {
      do a += c.offsetTop; while (c = c.offsetParent)
    }
    c = a;
    750 >= c && window.jstiming.load.tick("aft")
  };
  var f = !1;

  function g() {
    f || (f = !0, window.jstiming.load.tick("firstScrollTime"))
  }
  window.addEventListener ? window.addEventListener("scroll", g, !1) : window.attachEvent("onscroll", g);
})();


var a = "&m=1",
  d = "(^|&)m=",
  e = "?",
  f = "?m=1";

function g() {
  var b = window.location.href,
    c = b.split(e);
  switch (c.length) {
    case 1:
      return b + f;
    case 2:
      return 0 <= c[1].search(d) ? null : b + a;
    default:
      return null
  }
}
var h = navigator.userAgent;
if (-1 != h.indexOf("Mobile") && -1 != h.indexOf("WebKit") && -1 == h.indexOf("iPad") || -1 != h.indexOf("Opera Mini") || -1 != h.indexOf("IEMobile")) {
  var k = g();
  k && window.location.replace(k)
};

if (window.jstiming) window.jstiming.load.tick('headEnd');

function setAttributeOnload(object, attribute, val) {
  if (window.addEventListener) {
    window.addEventListener('load',
      function() {
        object[attribute] = val;
      }, false);
  } else {
    window.attachEvent('onload', function() {
      object[attribute] = val;
    });
  }
}
