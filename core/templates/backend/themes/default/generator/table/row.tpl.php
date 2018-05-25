<?php if(!$item->isContentOnly()):?>
	<tr class="<?php echo $item->drawClasses();?> <?php echo $item->drawWrapperClasses();?> <?php echo $item->getWrapperWidth();?> <?php echo $item->getWidth();?>" 
		<?php echo $item->drawAttributes();?> style="<?php echo $item->drawVisible();?>">
<?php endif;?>
<?php $table = $item->getTable();?>
<?php $columns = $table->getColumns();?>
<?php if($table->needRowSelector()):?>
	<td>
		<?php echo $table->createRowSelector(false, $item->getValue())->generate();?>
	</td>
<?php endif;?>
<?php foreach($columns as $column):?>
	<?php $column->prepare();?>
	<?php echo $column->createCell($item)->generate();?>
<?php endforeach;?>
<?php if($table->needActionColumn()):?>
	<td>
		<?php if($table->hasRowActions()):?>
			<div class="btn-group">
				<?php $defaultAction = $item->getDefaultRowAction()->createNewLink($item->getValue()); $defaultAction->addClass('btn btn-default');?>
				<?php echo $defaultAction->generate();?>
				<?php if($item->hasOthersRowActions()):?>
					<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-angle-down"></i>&nbsp;
					<?php $othersActions = $item->getOthersRowActions();?>
					</button>
					<ul class="dropdown-menu" role="menu">
						<?php foreach($othersActions as $othersAction):?>
							<li>
								<?php echo $othersAction->createNewLink($item->getValue())->generate();?>
							</li>
						<?php endforeach;?>
					</ul>
				<?php endif;?>
			</div>
		<?php endif;?>
	</td>
<?php endif;?>
<?php if(!$item->isContentOnly()):?>
	</tr>
<?php endif;?>