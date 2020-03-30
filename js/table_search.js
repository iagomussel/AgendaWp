$(".search").keyup(function() {
    var target_id = $(this).attr("target")
    if (target_id == "") {
      return false
    }
    // When value of the input is not blank
    if ('' != this.value) {
      var reg = new RegExp(this.value, 'i'); // case-insesitive
      $(target_id + ' tbody').find('tr').each(function() {
        var $me = $(this);
        if (!$me.children('td').text().match(reg)) {
          $me.hide();
        } else {
          $me.show();
        }
      });
    } else {
      $(target_id + ' tbody').find('tr').show();
    }
  })