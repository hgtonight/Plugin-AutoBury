/* Copyright 2016 Zachary Doll */
jQuery(document).ready(function($) {
  var string = gdn.definition('AutoBury.Translation', 'This item is buried, click to show');
  $('.DataList .Item.Buried').each(function() {
      var $overlay = $('<div class="BuriedOverlay">' + string + '</div>');
      $(this).append($overlay);
  });
  
  $('.BuriedOverlay').click(function() {
      $(this).parent('.Item.Buried').addClass('Revealed');
  });
});
