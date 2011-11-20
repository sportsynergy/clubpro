<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?=$trackingid?>']);
  _gaq.push(['_trackPageview']);
  _gaq.push(['_setCustomVar',
             1,                // This custom var is set to slot #1.  Required parameter.
             'Site',    // The name of the custom variable.  Required parameter.
             '<?=get_sitecode()?>',        // The value of the custom variable.  Required parameter.
                               //  (possible values might be Free, Bronze, Gold, and Platinum)
             1                 // Sets the scope to visitor-level.  Optional parameter.
        ]); 

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>