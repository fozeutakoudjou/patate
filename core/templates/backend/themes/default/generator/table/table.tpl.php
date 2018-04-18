<?php $tools->addCSS($librariesDir.'datatables/datatables.min.css', array(), false);?>
<?php $tools->addCSS($librariesDir.'datatables/plugins/bootstrap/datatables.bootstrap.css', array(), false);?>

<?php $tools->addJS($librariesDir.'datatables/datatables.min.js', array(), false);?>
<?php $tools->addJS($librariesDir.'datatables/plugins/bootstrap/datatables.bootstrap.js', array(), false);?>
<?php if($item->isDecorated()):?>
	<div class="portlet light bordered <?php echo $item->drawWrapperClasses();?>" 
		style="<?php echo $item->drawVisible();?>">
	<?php if($item->hasHeader()):?>
		<div class="portlet-title">
			<?php if($item->hasIcon() || $item->hasLabel()):?>
				<div class="caption">
				<?php if($item->hasIcon()):?> <?php echo $item->getIcon()->generate();?> <?php endif;?>
				<?php echo $item->getLabel();?>
				<span class="badge"><?php echo $item->getTotalResult();?></span>
				</div>
			<?php endif;?>
			
			<?php if($item->hasActionBlock()):?>
				<?php $tableActions = $item->getTableActions();?>
				<?php $headers = $item->getHeaders();?>
				<div class="actions">
				<?php foreach($tableActions as $tableAction):?>
					<?php echo $tableAction->generate();?>
				<?php endforeach;?>
				<?php foreach($headers as $header):?>
					<?php echo $header->generate();?>
				<?php endforeach;?>
				</div>
			<?php endif;?>
		</div>
	<?php endif;?>
		<div class="portlet-body">
		
<?php else:?>
	<div class="<?php echo $item->drawWrapperClasses();?>" style="<?php echo $item->drawVisible();?>">
<?php endif;?>
<div class="table-container">
	<table class="table table-striped table-bordered table-hover table-checkable <?php echo $item->drawClasses();?>" <?php echo $item->drawAttributes();?>>
		<thead>
			<?php $columns = $item->getColumns();?>
			<tr role="row" class="heading">
				<?php if($item->needRowSelector()):?>
					<th>
						<?php echo $item->createRowSelector(true)->generate();?>
					</th>
				<?php endif;?>
				<?php foreach($columns as $column):?>
					<?php $column->prepare();?>
					<th>
						<?php echo $column->getLabel();?>
						<?php if($column->isSortable()):?>
							<?php $sortLinks = $column->getSortLinks();?>
							<?php echo $sortLinks['asc']->generate();?>
							<?php echo $sortLinks['desc']->generate();?>
						<?php endif;?>
					</th>
				<?php endforeach;?>
				<?php if($item->needActionColumn()):?>
					<th>
						<?php echo $tools->l('Actions');?>
					</th>
				<?php endif;?>
			</tr>
			<?php if($item->hasSearchColumn()):?>
				<tr role="row" class="filter">
					<?php if($item->needRowSelector()):?><td> </td><?php endif;?>
					<?php foreach($columns as $column):?>
						<td>
							<?php if($column->isSearchable()):?>
								<?php $searchFields = $column->getSearchFields();?>
								<?php foreach($searchFields as $field):?>
									<?php echo $field->generate();?>
								<?php endforeach;?>
							<?php endif;?>
						</td>
					<?php endforeach;?>
					<?php if($item->needActionColumn()):?>
					<td>
						<?php echo $item->getSearchButton()->generate();?>
						<?php if($item->needSearchResetButton()):?>
							<?php echo $item->getSearchResetButton()->generate();?>
						<?php endif;?>
					</td>
				<?php endif;?>
				</tr>
			<?php endif;?>
		</thead>
		<tbody>
			<?php $values = $item->getValue();?>
			<?php foreach($values as $value):?>
				<?php echo $item->createRow($value)->generate();?>
			<?php endforeach;?>
		</tbody>
	</table>
	<?php if($item->hasFooter()):?>
		<div class="row">
			<?php $footers = $item->getFooters();?>
			<?php foreach($footers as $footer):?>
				<?php echo $footer->generate();?>
			<?php endforeach;?>
		</div>
	<?php endif;?>
</div>

<?php if($item->isDecorated()):?>
		</div>
	</div>
<?php else:?>
	</div>
<?php endif;?>