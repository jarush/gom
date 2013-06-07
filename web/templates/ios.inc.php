<!-- Fullscreen web app capable -->
<meta name="apple-mobile-web-app-capable" content="yes" />

<!-- Do not set the width to device-width for iPhone 5 -->
<meta name="viewport" content="initial-scale=1.0,user-scalable=no,maximum-scale=1" media="(device-height: 568px)" />

<!-- Fav and touch icons -->
<link rel="apple-touch-icon" sizes="144x144" href="/img/icon.144.png">
<link rel="apple-touch-icon" sizes="114x114" href="/img/icon.114.png">
<link rel="apple-touch-icon" sizes="72x72" href="/img/icon.72.png">
<link rel="apple-touch-icon" href="/img/icon.57.png">
<link rel="shortcut icon" href="/ico/favicon.png">

<script>
// Check if it's an iOS device in web app mode
if (('standalone' in window.navigator) && window.navigator.standalone) {
  $(function() {
    // Override links to use a javascript function to open links
    $('a').click(function (event) {
      event.preventDefault();
      window.location = $(this).attr('href');
    });
  });
}
</script>
