<?php echo link_tag('css/content.css'); ?>
<?php echo link_tag('css/qawiki.css'); ?>

<script type="text/javascript" src="<?php echo base_url() ?>js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>js/jquery-ui.min.js"></script>

<script type="text/javascript">
  var base_url = "<?php echo base_url() ?>";
</script>
<body style="background: none">
<div id="main">
      {yield}
</div>
</body>