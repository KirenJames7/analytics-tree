/*! GoTop v0.0.3 by @edgaraafelix | (c) 2011, 2015 Onwi. | https://github.com/edgaraafelix/jquery-gotop | Licensed under MIT (https://github.com/edgaraafelix/jquery-gotop/blob/master/LICENSE) */

if (typeof jQuery === 'undefined') {
  throw new Error('GoTop JavaScript requires jQuery');
}

/* global window, document, jQuery */

(function ( $ ) {
  
  'use strict';
  
  var GoTop = window.GoTop || {};
  
  GoTop = (function() {
    
    function GoTop(element, settings) {
      
      var _ = this;
      
      _.defaults = {
        animation: true,
        target: 'html, body',
        speed: 600,
        distance: 200,
        position: 'BOTTOM_RIGHT'
      };
      
      _.options = $.extend({}, _.defaults, settings);
      
      _.$element = null;

      _.animation = _.options.animation;
      if (typeof(_.animation) !== 'boolean') {
        throw new Error('Yo! Invalid type for \'animation\' option, should be \'boolean\'!');
      }

      _.target = _.options.target;
      if (typeof(_.target) !== 'string') {
        throw new Error('Yo! Invalid type for \'target\' option, should be \'string\'!');
      }

      _.speed = _.options.speed;
      if (typeof(_.speed) !== 'number') {
        throw new Error('Yo! Invalid type for \'speed\' option, should be \'number\'!');
      }

      _.distance = _.options.distance;
      if (typeof(_.distance) !== 'number') {
        throw new Error('Yo! Invalid type for \'distance\' option, should be \'number\'!');
      }

      _.position = _.options.position;
      if (typeof(_.position) !== 'string') {
        throw new Error('Yo! Invalid type for \'position\' option, should be \'string\'!');
      }

      _.init();
      
    }
    
    return GoTop;
    
  }());
  
  GoTop.prototype.positioning = function() {
    var _ = this;
    var position = 'bottom-right';
    switch (_.position) {
      case 'TOP_LEFT':
        position = 'top-left';
        break;
      case 'TOP_MIDDLE':
        position = 'top-middle';
        break;
      case 'TOP_RIGHT':
        position = 'top-right';
        break;
      case 'BOTTOM_LEFT':
        position = 'bottom-left';
        break;
      case 'BOTTOM_MIDDLE':
        position = 'bottom-middle';
        break;
      case 'BOTTOM_RIGHT':
        position = 'bottom-right';
        break;
      default:
        position = 'bottom-right';
        break;
    }
    if (null !== _.$element) {
      $(_.$element).addClass('bottom-right');
    }
  };
  
  GoTop.prototype.scrollDistance = function() {
    var _ = this;
    $(window).scroll(function() {
      if ( $(window).scrollTop() > _.distance ) {
        _.transitionIn();
      } else {
        _.transitionOut();
      }
    });
  };
  
  GoTop.prototype.scrollTop = function() {
    var _ = this;
    $(_.$element).on('click', function() {
      var isAlreadyOnTop = $(document).scrollTop() === 0;
      if (!isAlreadyOnTop) {
        $(_.target).animate({
          scrollTop: 0
        }, _.speed);
      }
      return false;
    });
  };

  GoTop.prototype.applyTransition = function(transitioned, transition) {
    var _ = this;
    if (null !== _.$element) {
      if ($(_.$element).hasClass(transitioned)) {
        $(_.$element).removeClass(transitioned);
      }
      $(_.$element).addClass(transition);
    }
  };

  GoTop.prototype.transitionIn = function() {
    var _ = this;
    if (true === _.animation) {
      _.applyTransition('fade-out', 'fade-in');
    } else {
      _.applyTransition('hidden', 'visible');
    }
  };
  
  GoTop.prototype.transitionOut = function() {
    var _ = this;
    if (true === _.animation) {
      _.applyTransition('fade-in', 'fade-out');
    } else {
      _.applyTransition('visible', 'hidden');
    }
  };

  GoTop.prototype.buildOut = function() {
    var _ = this;
    if (null === _.$element) {
      if ($('.go-top').length) {
        $('.go-top').remove();
      }
      $('<div class="go-top"><div class="up"/>').appendTo('body');
      _.$element = $('.go-top');
    }
    _.positioning();
  };
  
  GoTop.prototype.init = function() {
      var _ = this;
      _.buildOut();
      _.scrollDistance();
      _.scrollTop();
  };
  
  $.fn.goTop = function(options) {
    var _ = this;
    return _.each(function(index, element) {
      element.goTop = new GoTop(element, options);
    });
  };
  
}( jQuery ));
