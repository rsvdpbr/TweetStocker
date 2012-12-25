<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<?php echo $this->Html->charset(); ?>
	<title>TweetStocker</title>
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('style');
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
  </head>
  <body>
	<div id="container">
	  <div id="header">
		TweetStocker
		<div id="header_menu">
		  <?php foreach($DataHash['header'] as $text => $link){ ?>
		  ［<?php echo $this->Html->link(' '.$text.' ', $link); ?>］
		  <?php } ?>
		</div>
	  </div>
	  <div id="header_border"></div>
	  <div id="wrapper">
		<div id="content">
		  <?php echo $this->Session->flash(); ?>
		  <?php echo $this->fetch('content'); ?>
		</div>
	  </div>
	  <div id="footer_border"></div>
	  <div id="footer">
		<?php foreach($DataHash['footer'] as $text => $link){ ?>
		［<?php echo $this->Html->link(' '.$text.' ', $link); ?>］
		<?php } ?>
	  </div>
	  <?php //pr($DataHash); ?>
	  <?php echo $this->element('sql_dump'); ?>
	</div>
  </body>
</html>
