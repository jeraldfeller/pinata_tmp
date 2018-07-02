/*! modernizr 3.2.0 (Custom Build) | MIT *
 * http://modernizr.com/download/?-canvas-cssanimations-csscolumns-cssfilters-csstransforms-csstransforms3d-cssvhunit-cssvmaxunit-cssvwunit-flexbox-input-smil-svg-svgclippaths-svgfilters-touchevents-video !*/
!function(e,t,n){function o(){return"function"!=typeof t.createElement?t.createElement(arguments[0]):p?t.createElementNS.call(t,"http://www.w3.org/2000/svg",arguments[0]):t.createElement.apply(t,arguments)}function r(e,t){return e-1===t||e===t||e+1===t}function a(){var e=t.body;return e||(e=o(p?"svg":"body"),e.fake=!0),e}function i(e,n,r,i){var s,l,c,p,u="modernizr",m=o("div"),v=a();if(parseInt(r,10))for(;r--;)c=o("div"),c.id=i?i[r]:u+(r+1),m.appendChild(c);return s=o("style"),s.type="text/css",s.id="s"+u,(v.fake?v:m).appendChild(s),v.appendChild(m),s.styleSheet?s.styleSheet.cssText=e:s.appendChild(t.createTextNode(e)),m.id=u,v.fake&&(v.style.background="",v.style.overflow="hidden",p=d.style.overflow,d.style.overflow="hidden",d.appendChild(v)),l=n(m,e),v.fake?(v.parentNode.removeChild(v),d.style.overflow=p,d.offsetHeight):m.parentNode.removeChild(m),!!l}var s=[],l={_version:"3.2.0",_config:{classPrefix:"",enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(e,t){var n=this;setTimeout(function(){t(n[e])},0)},addTest:function(e,t,n){s.push({name:e,fn:t,options:n})},addAsyncTest:function(e){s.push({name:null,fn:e})}},Modernizr=function(){};Modernizr.prototype=l,Modernizr=new Modernizr,Modernizr.addTest("svgfilters",function(){var t=!1;try{t="SVGFEColorMatrixElement"in e&&2==SVGFEColorMatrixElement.SVG_FECOLORMATRIX_TYPE_SATURATE}catch(n){}return t});var c=l._config.usePrefixes?" -webkit- -moz- -o- -ms- ".split(" "):[];l._prefixes=c;var d=t.documentElement,p="svg"===d.nodeName.toLowerCase();Modernizr.addTest("canvas",function(){var e=o("canvas");return!(!e.getContext||!e.getContext("2d"))}),Modernizr.addTest("video",function(){var e=o("video"),t=!1;try{(t=!!e.canPlayType)&&(t=new Boolean(t),t.ogg=e.canPlayType('video/ogg; codecs="theora"').replace(/^no$/,""),t.h264=e.canPlayType('video/mp4; codecs="avc1.42E01E"').replace(/^no$/,""),t.webm=e.canPlayType('video/webm; codecs="vp8, vorbis"').replace(/^no$/,""),t.vp9=e.canPlayType('video/webm; codecs="vp9"').replace(/^no$/,""),t.hls=e.canPlayType('application/x-mpegURL; codecs="avc1.42E01E"').replace(/^no$/,""))}catch(n){}return t});var u="CSS"in e&&"supports"in e.CSS,m="supportsCSS"in e;Modernizr.addTest("supports",u||m),Modernizr.addTest("svg",!!t.createElementNS&&!!t.createElementNS("http://www.w3.org/2000/svg","svg").createSVGRect);var v=o("input"),f="autocomplete autofocus list placeholder max min multiple pattern required step".split(" "),h={};Modernizr.input=function(t){for(var n=0,r=t.length;r>n;n++)h[t[n]]=!!(t[n]in v);return h.list&&(h.list=!(!o("datalist")||!e.HTMLDataListElement)),h}(f);var g={}.toString;Modernizr.addTest("smil",function(){return!!t.createElementNS&&/SVGAnimate/.test(g.call(t.createElementNS("http://www.w3.org/2000/svg","animate")))});var y=l.testStyles=i;y("#modernizr { height: 50vh; }",function(t){var n=parseInt(e.innerHeight/2,10),o=parseInt((e.getComputedStyle?getComputedStyle(t,null):t.currentStyle).height,10);Modernizr.addTest("cssvhunit",o==n)}),Modernizr.addTest("touchevents",function(){var n;if("ontouchstart"in e||e.DocumentTouch&&t instanceof DocumentTouch)n=!0;else{var o=["@media (",c.join("touch-enabled),("),"heartz",")","{#modernizr{top:9px;position:absolute}}"].join("");y(o,function(e){n=9===e.offsetTop})}return n}),y("#modernizr1{width: 50vmax}#modernizr2{width:50px;height:50px;overflow:scroll}#modernizr3{position:fixed;top:0;left:0;bottom:0;right:0}",function(t){var n=t.childNodes[2],o=t.childNodes[1],a=t.childNodes[0],i=parseInt((o.offsetWidth-o.clientWidth)/2,10),s=a.clientWidth/100,l=a.clientHeight/100,c=parseInt(50*Math.max(s,l),10),d=parseInt((e.getComputedStyle?getComputedStyle(n,null):n.currentStyle).width,10);Modernizr.addTest("cssvmaxunit",r(c,d)||r(c,d-i))},3),y("#modernizr { width: 50vw; }",function(t){var n=parseInt(e.innerWidth/2,10),o=parseInt((e.getComputedStyle?getComputedStyle(t,null):t.currentStyle).width,10);Modernizr.addTest("cssvwunit",o==n)}),Modernizr.addTest("svgclippaths",function(){return!!t.createElementNS&&/SVGClipPath/.test(g.call(t.createElementNS("http://www.w3.org/2000/svg","clipPath")))});var w="Moz O ms Webkit",S=l._config.usePrefixes?w.split(" "):[];l._cssomPrefixes=S;var T=l._config.usePrefixes?w.toLowerCase().split(" "):[];l._domPrefixes=T}(window,document);

$(function(){

    /*
     * Check for IE9
     */
    var $isIE9 = false;

    var browser = {
        isIe: function () {
            return navigator.appVersion.indexOf("MSIE") != -1;
        },
        navigator: navigator.appVersion,
        getVersion: function() {
            var version = 999; // we assume a sane browser
            if (navigator.appVersion.indexOf("MSIE") != -1)
            // bah, IE again, lets downgrade version number
                version = parseFloat(navigator.appVersion.split("MSIE")[1]);
            return version;
        }
    };

    if (browser.isIe() && browser.getVersion() == 9) {
        $isIE9 = true;
    }

    /*
     * Form Placholder replacement
     */


    if(!Modernizr.input.placeholder || $isIE9){
        //console.log('No placeholder')
        $('[placeholder]').focus(function() {
            var input = $(this);
            if (input.val() == input.attr('placeholder')) {
                input.val('');
                input.removeClass('placeholder');
            }
        }).blur(function() {
            var input = $(this);
            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                input.addClass('placeholder');
                input.val(input.attr('placeholder'));
            }
        }).blur().parents('form').submit(function() {
            $(this).find('[placeholder]').each(function() {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                }
            })
        });
    }


});


/******************
	FUNCTIONS
******************/


// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
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