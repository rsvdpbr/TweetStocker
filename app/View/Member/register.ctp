<div class="form">
  <h1>ユーザー登録</h1>
  <div class="text">
	<?php echo $this->Session->flash('auth'); ?>
	<?php echo $this->Form->create('Member', Array('url' => '/register')); ?>
	<div class='component'>
	  ユーザー名：
	  <?php echo $this->Form->input('username', Array('label' => false, 'div' => false)); ?>
	</div>
	<div class='component'>
	  パスワード：
	  <?php echo $this->Form->input('password', Array('label' => false, 'div' => false)); ?>
	</div>
	<div class='component'>
	  パスワード（確認）：
	  <?php echo $this->Form->input('password_confirm', Array('type' => 'password', 'label' => false, 'div' => false)); ?>
	  <?php if(isset($DataHash['password_error']) && $DataHash['password_error']){ ?>
	  <div class="error-message">パスワードが一致しません。</div>
	  <?php } ?>
	</div>
	<?php echo $this->Form->end('アカウントを作成する'); ?>
  </div>
</div>
