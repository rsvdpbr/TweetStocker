<div class="form">
  <h1>ログイン</h1>
  <div class="text">
	<?php echo $this->Session->flash('auth'); ?>
	<?php echo $this->Form->create('Member', Array('url' => '/login')); ?>
	<div class='component'>
	  ユーザー名：
	  <?php echo $this->Form->input('username', Array('label' => false, 'div' => false)); ?>
	</div>
	<div class='component'>
	  パスワード：
	  <?php echo $this->Form->input('password', Array('label' => false, 'div' => false)); ?>
	</div>
	<?php echo $this->Form->end('ログイン'); ?>
  </div>
</div>
