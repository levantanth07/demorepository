/*
 * jQuery Cycle Plugin (with Transition Definitions)
 * Examples and documentation at: http://malsup.com/jquery/cycle/
 * Copyright (c) 2007-2008 M. Alsup
 * Version: 2.32 (21-DEC-2008)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * Requires: jQuery v1.2.3 or later
 *
 * Based on the work of:
 *	1) Matt Oakes (http://portfolio.gizone.co.uk/applications/slideshow/)
 *	2) Torsten Baldes (http://medienfreunde.com/lab/innerfade/)
 *	3) Benjamin Sterling (http://www.benjaminsterling.com/experiments/jqShuffle/)
 */
;(function($) {
var ver = '2.32';
var ie6 = $.browser.msie && /MSIE 6.0/.test(navigator.userAgent);
function log() {
	if (window.console && window.console.log)
		window.console.log('[cycle] ' + Array.prototype.join.call(arguments,''));
};
$.fn.cycle = function(options) {
	if (this.length == 0) {
		// is your DOM ready?  http://docs.jquery.com/Tutorials:Introducing_$(document).ready()
		log('terminating; zero elements found by selector' + ($.isReady ? '' : ' (DOM not ready)'));
		return this;
	}
	var opt2 = arguments[1];
	return this.each(function() {
		if (options === undefined || options === null)
			options = {};
		if (options.constructor == String) {
			switch(options) {
			case 'stop':
				if (this.cycleTimeout) clearTimeout(this.cycleTimeout);
				this.cycleTimeout = 0;
				$(this).data('cycle.opts', '');
				return;
			case 'pause':
				this.cyclePause = 1;
				return;
			case 'resume':
				this.cyclePause = 0;
				if (opt2 === true) { // resume now!
					options = $(this).data('cycle.opts');
					if (!options) {
						log('options not found, can not resume');
						return;
					}
					if (this.cycleTimeout) {
						clearTimeout(this.cycleTimeout);
						this.cycleTimeout = 0;
					}
					go(options.elements, options, 1, 1);
				}
				return;
			default:
				options = { fx: options };
			};
		}
		else if (options.constructor == Number) {
			// go to the requested slide
			var num = options;
			options = $(this).data('cycle.opts');
			if (!options) {
				log('options not found, can not advance slide');
				return;
			}
			if (num < 0 || num >= options.elements.length) {
				log('invalid slide index: ' + num);
				return;
			}
			options.nextSlide = num;
			if (this.cycleTimeout) {
				clearTimeout(this.cycleTimeout);
				this.cycleTimeout = 0;
			}
			go(options.elements, options, 1, num >= options.currSlide);
			return;
		}
		// stop existing slideshow for this container (if there is one)
		if (this.cycleTimeout) clearTimeout(this.cycleTimeout);
		this.cycleTimeout = 0;
		this.cyclePause = 0;
		var $cont = $(this);
		var $slides = options.slideExpr ? $(options.slideExpr, this) : $cont.children();
		var els = $slides.get();
		if (els.length < 2) {
			log('terminating; too few slides: ' + els.length);
			return; // don't bother
		}
		// support metadata plugin (v1.0 and v2.0)
		var opts = $.extend({}, $.fn.cycle.defaults, options || {}, $.metadata ? $cont.metadata() : $.meta ? $cont.data() : {});
		if (opts.autostop)
			opts.countdown = opts.autostopCount || els.length;
		$cont.data('cycle.opts', opts);
		opts.container = this;
		opts.elements = els;
		opts.before = opts.before ? [opts.before] : [];
		opts.after = opts.after ? [opts.after] : [];
		opts.after.unshift(function(){ opts.busy=0; });
		if (opts.continuous)
			opts.after.push(function() { go(els,opts,0,!opts.rev); });
		// clearType corrections
		if (ie6 && opts.cleartype && !opts.cleartypeNoBg)
			clearTypeFix($slides);
		// allow shorthand overrides of width, height and timeout
		var cls = this.className;
		opts.width = parseInt((cls.match(/w:(\d+)/)||[])[1]) || opts.width;
		opts.height = parseInt((cls.match(/h:(\d+)/)||[])[1]) || opts.height;
		opts.timeout = parseInt((cls.match(/t:(\d+)/)||[])[1]) || opts.timeout;
		if ($cont.css('position') == 'static')
			$cont.css('position', 'relative');
		if (opts.width)
			$cont.width(opts.width);
		if (opts.height && opts.height != 'auto')
			$cont.height(opts.height);
		if (opts.startingSlide) opts.startingSlide = parseInt(opts.startingSlide);
		if (opts.random) {
			opts.randomMap = [];
			for (var i = 0; i < els.length; i++)
				opts.randomMap.push(i);
			opts.randomMap.sort(function(a,b) {return Math.random() - 0.5;});
			opts.randomIndex = 0;
			opts.startingSlide = opts.randomMap[0];
		}
		else if (opts.startingSlide >= els.length)
			opts.startingSlide = 0; // catch bogus input
		var first = opts.startingSlide || 0;
		$slides.css({position: 'absolute', top:0, left:0}).hide().each(function(i) {
			var z = first ? i >= first ? els.length - (i-first) : first-i : els.length-i;
			$(this).css('z-index', z)
		});
		$(els[first]).css('opacity',1).show(); // opacity bit needed to handle reinit case
		if ($.browser.msie) els[first].style.removeAttribute('filter');
		if (opts.fit && opts.width)
			$slides.width(opts.width);
		if (opts.fit && opts.height && opts.height != 'auto')
			$slides.height(opts.height);
		if (opts.containerResize) {
			var maxw = 0, maxh = 0;
			for(var i=0; i < els.length; i++) {
				var $e = $(els[i]), w = $e.outerWidth(), h = $e.outerHeight();
				maxw = w > maxw ? w : maxw;
				maxh = h > maxh ? h : maxh;
			}
			$cont.css({width:maxw+'px',height:maxh+'px'});
		}
		if (opts.pause)
			$cont.hover(function(){this.cyclePause++;},function(){this.cyclePause--;});
		// run transition init fn
		var init = $.fn.cycle.transitions[opts.fx];
		if ($.isFunction(init))
			init($cont, $slides, opts);
		else if (opts.fx != 'custom')
			log('unknown transition: ' + opts.fx);
		$slides.each(function() {
			var $el = $(this);
			this.cycleH = (opts.fit && opts.height) ? opts.height : $el.height();
			this.cycleW = (opts.fit && opts.width) ? opts.width : $el.width();
		});
		opts.cssBefore = opts.cssBefore || {};
		opts.animIn = opts.animIn || {};
		opts.animOut = opts.animOut || {};
		$slides.not(':eq('+first+')').css(opts.cssBefore);
		if (opts.cssFirst)
			$($slides[first]).css(opts.cssFirst);
		if (opts.timeout) {
			opts.timeout = parseInt(opts.timeout);
			// ensure that timeout and speed settings are sane
			if (opts.speed.constructor == String)
				opts.speed = $.fx.speeds[opts.speed] || parseInt(opts.speed);
			if (!opts.sync)
				opts.speed = opts.speed / 2;
			while((opts.timeout - opts.speed) < 250)
				opts.timeout += opts.speed;
		}
		if (opts.easing)
			opts.easeIn = opts.easeOut = opts.easing;
		if (!opts.speedIn)
			opts.speedIn = opts.speed;
		if (!opts.speedOut)
			opts.speedOut = opts.speed;
		opts.slideCount = els.length;
		opts.currSlide = first;
		if (opts.random) {
			opts.nextSlide = opts.currSlide;
			if (++opts.randomIndex == els.length)
				opts.randomIndex = 0;
			opts.nextSlide = opts.randomMap[opts.randomIndex];
		}
		else
			opts.nextSlide = opts.startingSlide >= (els.length-1) ? 0 : opts.startingSlide+1;
		// fire artificial events
		var e0 = $slides[first];
		if (opts.before.length)
			opts.before[0].apply(e0, [e0, e0, opts, true]);
		if (opts.after.length > 1)
			opts.after[1].apply(e0, [e0, e0, opts, true]);
		if (opts.click && !opts.next)
			opts.next = opts.click;
		if (opts.next)
			$(opts.next).bind('click', function(){return advance(els,opts,opts.rev?-1:1)});
		if (opts.prev)
			$(opts.prev).bind('click', function(){return advance(els,opts,opts.rev?1:-1)});
		if (opts.pager)
			buildPager(els,opts);
		// expose fn for adding slides after the show has started
		opts.addSlide = function(newSlide, prepend) {
			var $s = $(newSlide), s = $s[0];
			if (!opts.autostopCount)
				opts.countdown++;
			els[prepend?'unshift':'push'](s);
			if (opts.els)
				opts.els[prepend?'unshift':'push'](s); // shuffle needs this
			opts.slideCount = els.length;
			$s.css('position','absolute');
			$s[prepend?'prependTo':'appendTo']($cont);
			if (prepend) {
				opts.currSlide++;
				opts.nextSlide++;
			}
			if (ie6 && opts.cleartype && !opts.cleartypeNoBg)
				clearTypeFix($s);
			if (opts.fit && opts.width)
				$s.width(opts.width);
			if (opts.fit && opts.height && opts.height != 'auto')
				$slides.height(opts.height);
			s.cycleH = (opts.fit && opts.height) ? opts.height : $s.height();
			s.cycleW = (opts.fit && opts.width) ? opts.width : $s.width();
			$s.css(opts.cssBefore);
			if (opts.pager)
				$.fn.cycle.createPagerAnchor(els.length-1, s, $(opts.pager), els, opts);
			if (typeof opts.onAddSlide == 'function')
				opts.onAddSlide($s);
		};
		if (opts.timeout || opts.continuous)
			this.cycleTimeout = setTimeout(
				function(){go(els,opts,0,!opts.rev)},
				opts.continuous ? 10 : opts.timeout + (opts.delay||0));
	});
};
function go(els, opts, manual, fwd) {
	if (opts.busy) return;
	var p = opts.container, curr = els[opts.currSlide], next = els[opts.nextSlide];
	if (p.cycleTimeout === 0 && !manual)
		return;
	if (!manual && !p.cyclePause &&
		((opts.autostop && (--opts.countdown <= 0)) ||
		(opts.nowrap && !opts.random && opts.nextSlide < opts.currSlide))) {
		if (opts.end)
			opts.end(opts);
		return;
	}
	if (manual || !p.cyclePause) {
		if (opts.before.length)
			$.each(opts.before, function(i,o) { o.apply(next, [curr, next, opts, fwd]); });
		var after = function() {
			if ($.browser.msie && opts.cleartype)
				this.style.removeAttribute('filter');
			$.each(opts.after, function(i,o) { o.apply(next, [curr, next, opts, fwd]); });
		};
		if (opts.nextSlide != opts.currSlide) {
			opts.busy = 1;
			if (opts.fxFn)
				opts.fxFn(curr, next, opts, after, fwd);
			else if ($.isFunction($.fn.cycle[opts.fx]))
				$.fn.cycle[opts.fx](curr, next, opts, after);
			else
				$.fn.cycle.custom(curr, next, opts, after, manual && opts.fastOnEvent);
		}
		if (opts.random) {
			opts.currSlide = opts.nextSlide;
			if (++opts.randomIndex == els.length)
				opts.randomIndex = 0;
			opts.nextSlide = opts.randomMap[opts.randomIndex];
		}
		else { // sequence
			var roll = (opts.nextSlide + 1) == els.length;
			opts.nextSlide = roll ? 0 : opts.nextSlide+1;
			opts.currSlide = roll ? els.length-1 : opts.nextSlide-1;
		}
		if (opts.pager)
			$.fn.cycle.updateActivePagerlink(opts.pager, opts.currSlide);
	}
	if (opts.timeout && !opts.continuous)
		p.cycleTimeout = setTimeout(function() { go(els,opts,0,!opts.rev) }, getTimeout(curr,next,opts,fwd));
	else if (opts.continuous && p.cyclePause)
		p.cycleTimeout = setTimeout(function() { go(els,opts,0,!opts.rev) }, 10);
};
$.fn.cycle.updateActivePagerlink = function(pager, currSlide) {
	$(pager).find('a').removeClass('activeSlide').filter('a:eq('+currSlide+')').addClass('activeSlide');
};
function getTimeout(curr, next, opts, fwd) {
	if (opts.timeoutFn) {
		var t = opts.timeoutFn(curr,next,opts,fwd);
		if (t !== false)
			return t;
	}
	return opts.timeout;
};
// advance slide forward or back
function advance(els, opts, val) {
	var p = opts.container, timeout = p.cycleTimeout;
	if (timeout) {
		clearTimeout(timeout);
		p.cycleTimeout = 0;
	}
	if (opts.random && val < 0) {
		// move back to the previously display slide
		opts.randomIndex--;
		if (--opts.randomIndex == -2)
			opts.randomIndex = els.length-2;
		else if (opts.randomIndex == -1)
			opts.randomIndex = els.length-1;
		opts.nextSlide = opts.randomMap[opts.randomIndex];
	}
	else if (opts.random) {
		if (++opts.randomIndex == els.length)
			opts.randomIndex = 0;
		opts.nextSlide = opts.randomMap[opts.randomIndex];
	}
	else {
		opts.nextSlide = opts.currSlide + val;
		if (opts.nextSlide < 0) {
			if (opts.nowrap) return false;
			opts.nextSlide = els.length - 1;
		}
		else if (opts.nextSlide >= els.length) {
			if (opts.nowrap) return false;
			opts.nextSlide = 0;
		}
	}
	if (opts.prevNextClick && typeof opts.prevNextClick == 'function')
		opts.prevNextClick(val > 0, opts.nextSlide, els[opts.nextSlide]);
	go(els, opts, 1, val>=0);
	return false;
};
function buildPager(els, opts) {
	var $p = $(opts.pager);
	$.each(els, function(i,o) {
		$.fn.cycle.createPagerAnchor(i,o,$p,els,opts);
	});
   $.fn.cycle.updateActivePagerlink(opts.pager, opts.startingSlide);
};
$.fn.cycle.createPagerAnchor = function(i, el, $p, els, opts) {
	var a = (typeof opts.pagerAnchorBuilder == 'function')
		? opts.pagerAnchorBuilder(i,el)
		: '<a href="#">'+(i+1)+'</a>';
	if (!a)
		return;
	var $a = $(a);
	// don't reparent if anchor is in the dom
	if ($a.parents('body').length == 0)
		$a.appendTo($p);
	$a.bind(opts.pagerEvent, function() {
		opts.nextSlide = i;
		var p = opts.container, timeout = p.cycleTimeout;
		if (timeout) {
			clearTimeout(timeout);
			p.cycleTimeout = 0;
		}
		if (typeof opts.pagerClick == 'function')
			opts.pagerClick(opts.nextSlide, els[opts.nextSlide]);
		go(els,opts,1,opts.currSlide < i);
		return false;
	});
	if (opts.pauseOnPagerHover)
		$a.hover(function() { opts.container.cyclePause++; }, function() { opts.container.cyclePause--; } );
};
// this fixes clearType problems in ie6 by setting an explicit bg color
function clearTypeFix($slides) {
	function hex(s) {
		var s = parseInt(s).toString(16);
		return s.length < 2 ? '0'+s : s;
	};
	function getBg(e) {
		for ( ; e && e.nodeName.toLowerCase() != 'html'; e = e.parentNode) {
			var v = $.css(e,'background-color');
			if (v.indexOf('rgb') >= 0 ) {
				var rgb = v.match(/\d+/g);
				return '#'+ hex(rgb[0]) + hex(rgb[1]) + hex(rgb[2]);
			}
			if (v && v != 'transparent')
				return v;
		}
		return '#ffffff';
	};
	$slides.each(function() { $(this).css('background-color', getBg(this)); });
};
$.fn.cycle.custom = function(curr, next, opts, cb, immediate) {
	var $l = $(curr), $n = $(next);
	$n.css(opts.cssBefore);
	var speedIn = immediate ? 1 : opts.speedIn;
	var speedOut = immediate ? 1 : opts.speedOut;
	var easeIn = immediate ? null : opts.easeIn;
	var easeOut = immediate ? null : opts.easeOut;
	var fn = function() {$n.animate(opts.animIn, speedIn, easeIn, cb)};
	$l.animate(opts.animOut, speedOut, easeOut, function() {
		if (opts.cssAfter) $l.css(opts.cssAfter);
		if (!opts.sync) fn();
	});
	if (opts.sync) fn();
};
$.fn.cycle.transitions = {
	fade: function($cont, $slides, opts) {
		$slides.not(':eq('+opts.startingSlide+')').css('opacity',0);
		opts.before.push(function() { $(this).show() });
		opts.animIn	   = { opacity: 1 };
		opts.animOut   = { opacity: 0 };
		opts.cssBefore = { opacity: 0 };
		opts.cssAfter  = { display: 'none' };
		opts.onAddSlide = function($s) { $s.hide(); };
	}
};
$.fn.cycle.ver = function() { return ver; };
// override these globally if you like (they are all optional)
$.fn.cycle.defaults = {
	fx:			  'fade', // one of: fade, shuffle, zoom, scrollLeft, etc
	timeout:	   4000,  // milliseconds between slide transitions (0 to disable auto advance)
	timeoutFn:     null,  // callback for determining per-slide timeout value:  function(currSlideElement, nextSlideElement, options, forwardFlag)
	continuous:	   0,	  // true to start next transition immediately after current one completes
	speed:		   1000,  // speed of the transition (any valid fx speed value)
	speedIn:	   null,  // speed of the 'in' transition
	speedOut:	   null,  // speed of the 'out' transition
	next:		   null,  // selector for element to use as click trigger for next slide
	prev:		   null,  // selector for element to use as click trigger for previous slide
	prevNextClick: null,  // callback fn for prev/next clicks:	function(isNext, zeroBasedSlideIndex, slideElement)
	pager:		   null,  // selector for element to use as pager container
	pagerClick:	   null,  // callback fn for pager clicks:	function(zeroBasedSlideIndex, slideElement)
	pagerEvent:	  'click', // name of event which drives the pager navigation
	pagerAnchorBuilder: null, // callback fn for building anchor links:  function(index, DOMelement)
	before:		   null,  // transition callback (scope set to element to be shown):     function(currSlideElement, nextSlideElement, options, forwardFlag)
	after:		   null,  // transition callback (scope set to element that was shown):  function(currSlideElement, nextSlideElement, options, forwardFlag)
	end:		   null,  // callback invoked when the slideshow terminates (use with autostop or nowrap options): function(options)
	easing:		   null,  // easing method for both in and out transitions
	easeIn:		   null,  // easing for "in" transition
	easeOut:	   null,  // easing for "out" transition
	shuffle:	   null,  // coords for shuffle animation, ex: { top:15, left: 200 }
	animIn:		   null,  // properties that define how the slide animates in
	animOut:	   null,  // properties that define how the slide animates out
	cssBefore:	   null,  // properties that define the initial state of the slide before transitioning in
	cssAfter:	   null,  // properties that defined the state of the slide after transitioning out
	fxFn:		   null,  // function used to control the transition: function(currSlideElement, nextSlideElement, options, afterCalback, forwardFlag)
	height:		  'auto', // container height
	startingSlide: 0,	  // zero-based index of the first slide to be displayed
	sync:		   1,	  // true if in/out transitions should occur simultaneously
	random:		   0,	  // true for random, false for sequence (not applicable to shuffle fx)
	fit:		   0,	  // force slides to fit container
	containerResize: 1,	  // resize container to fit largest slide
	pause:		   0,	  // true to enable "pause on hover"
	pauseOnPagerHover: 0, // true to pause when hovering over pager link
	autostop:	   0,	  // true to end slideshow after X transitions (where X == slide count)
	autostopCount: 0,	  // number of transitions (optionally used with autostop to define X)
	delay:		   0,	  // additional delay (in ms) for first transition (hint: can be negative)
	slideExpr:	   null,  // expression for selecting slides (if something other than all children is required)
	cleartype:	   0,	  // true if clearType corrections should be applied (for IE)
	nowrap:		   0,	  // true to prevent slideshow from wrapping
	fastOnEvent:   0	  // force immediate transition when triggered manually (via pager or prev/next)
};
})(jQuery);