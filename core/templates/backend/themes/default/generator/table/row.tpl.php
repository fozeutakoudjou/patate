<?php if(!$item->isContentOnly()):?>
	<tr class="<?php echo $item->drawClasses();?> <?php echo $item->drawWrapperClasses();?> <?php echo $item->getWrapperWidth();?> <?php echo $item->getWidth();?>" 
		<?php echo $item->drawAttributes();?> style="<?php echo $item->drawVisible();?>">
<?php endif;?>
<?php $table = $item->getTable();?>
<?php $columns = $table->getColumns();?>
<?php if($table->needRowSelector()):?>
	<td>
		<?php echo $table->createRowSelector(false)->generate();?>
	</td>
<?php endif;?>
<?php foreach($columns as $column):?>
	<?php $column->prepare();?>
	<?php echo $column->createCell($item->getValue())->generate();?>
<?php endforeach;?>
<?php if($table->needActionColumn()):?>
	<td>
		<?php echo $tools->l('Actions');?>
	</td>
<?php endif;?>
<?php if(!$item->isContentOnly()):?>
	</tr>
<?php endif;?>