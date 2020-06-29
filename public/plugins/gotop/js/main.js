$(function() {
    
  $('ul').find('a').on('click', function(event) {
    event.preventDefault();
    var targetId = $(this).attr('href');
    if ($('.collapse').hasClass('in')) {
      $('.collapse').collapse('toggle');
    }
    $('html, body').animate({
      scrollTop: ($(targetId).offset().top - 55) + 'px'
    }, 300);
  });

});
