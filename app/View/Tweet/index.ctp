
<form name="keyword" method="GET" style="float:right;">
  <select name="id" onChange="document.forms['keyword'].submit();">
	<option>キーワードを選択してください
	<?php foreach($DataHash['keywords'] as $i){ ?>
	<option value="<?php echo $i['id']; ?>"><?php echo $i['keyword']; ?>
	  <?php } ?>
  </select>
</form>
<h1><?php echo isset($DataHash['keyword']) ? $DataHash['keyword'] : 'キーワード未選択'; ?></h1>

<?php if(isset($DataHash['tweets'])){ ?>
<table class="list">
  <tr>
	<th>Num</th>
	<th>DateTime</th>
	<th>Tweet</th>
  </tr>
  <?php foreach($DataHash['tweets'] as $num =>$tweet){ ?>
  <tr style="font-size:12px;">
	<td><?php echo $num+1; ?></td>
	<td style="width:100px;"><?php echo date('y/m/d H:i', strtotime($tweet['datetime'])); ?></td>
	<td><?php echo str_replace($DataHash['keyword'], '<span style="color:#f00;font-weight:bold;">'.$DataHash['keyword'].'</span>', $tweet['text']); ?></td>
  </tr>
  <?php } ?>
</table>
<?php } ?>

