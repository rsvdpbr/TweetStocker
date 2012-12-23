
<h1>キーワード管理</h1>
<table class="list">
  <tr>
	<th>ID</th>
	<th>Keyword</th>
	<th>LastUpdate</th>
	<th>NumberOfTweets</th>
	<th>Action</th>
  </tr>
  <?php foreach($DataHash['keyword'] as $id => $data){ ?>
  <tr>
	<td><?php echo $id; ?></td>
	<td><?php echo $data['keyword']; ?></td>
	<td><?php echo date('Y年m月d日 H時i分s秒', strtotime($data['last_update'])); ?></td>
	<td><?php echo $data['count']; ?></td>
	<td>
	  <?php echo $this->Html->link('［更新］', '/'); ?>
	  <?php echo $this->Html->link('［削除］', '/'); ?>
	</td>
  </tr>
  <?php } ?>
</table>
<?php //pr($DataHash['keyword']); ?>
