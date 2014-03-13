function showAlert(alertClass, title, message) {
  var html = '';

  html += '<div id="alert" class="alert ' + alertClass + ' alert-dismissable">';
  html += '  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
  if (title) {
    html += '<strong>' + title + ':</strong> ';
  }
  html += message;
  html += '</div>';

  $('#alert-area').html(html);
}

