<?php if(!$item->isContentOnly()):?>
<div class="<?php echo $item->getAdditional('topWrapperClasses');?>">
<?php if($item->canDrawEditionFormAtTop()): echo $item->createFormBlock()->generate(); endif;?>
<?php endif;?>
<?php if($item->isDecorated()):?>
	<div class="portlet light bordered <?php echo $item->drawWrapperClasses();?>" <?php echo $item->drawWrapperAttributes();?> style="<?php echo $item->drawVisible();?>">
	<?php if($item->isAjaxActivatorEnabled()):?>
		<div class="portlet-title">
			<?php echo $item->createAjaxActivator()->generate();?>
		</div>
	<?php endif;?>
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
	<div>
<?php endif;?>
	<form class="<?php echo $item->getAdditional('formClasses');?>" action="<?php echo $item->getFormAction();?>" method="<?php echo $item->getMethod();?>" enctype="<?php echo $item->getEnctype();?>">
	<?php echo $item->generateContent();?>
	<div class="table-container table-responsive">
		<table class="table table-striped table-bordered table-hover table-checkable <?php echo $item->drawClasses();?>" <?php echo $item->drawAttributes();?>>
			<thead>
				<?php $columns = $item->getColumns();?>
				<tr role="row" class="heading">
					<?php if($item->needRowSelector() && !$item->isEmpty()):?>
						<th>
							<?php echo $item->createRowSelector(true)->generate();?>
						</th>
					<?php endif;?>
					<?php $columnCount = 0;?>
					<?php foreach($columns as $column):?>
						<?php $column->prepare();?>
						<th class="<?php if($column->isSortable()):?>sortable_column <?php if($column->isActiveSortColumn()):?>active_sort<?php endif;?><?php endif;?>">
							<?php echo $column->getLabel();?>
							<?php if($column->isSortable()):?>
								<?php $sortLinks = $column->getSortLinks();?>
								<?php $sortLinks['asc']->setTitle($tools->l('Sort ascending')); echo $sortLinks['asc']->generate();?>
								<?php $sortLinks['desc']->setTitle($tools->l('Sort descending')); echo $sortLinks['desc']->generate();?>
							<?php endif;?>
						</th>
						<?php $columnCount += 1;?>
					<?php endforeach;?>
					<?php if($item->needActionColumn()):?>
						<th>
							<?php echo $tools->l('Actions');?>
						</th>
						<?php $columnCount += 1;?>
					<?php endif;?>
				</tr>
				<?php if($item->hasSearchColumn()):?>
					<tr role="row" class="filter">
						<?php if($item->needRowSelector() && !$item->isEmpty()):?><td> </td><?php endif;?>
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
				<?php if($item->isEmpty()):?>
					<tr>
						<td class="list-empty" colspan="<?php echo $columnCount;?>">
							<div class="list-empty-msg">
								<i class="fa fa-warning list-empty-icon"></i><?php echo $item->getEmptyRowText();?>
							</div>
						</td>
					</tr>
				<?php else:?>
					<?php $values = $item->getValue();?>
					<?php foreach($values as $value):?>
						<?php echo $item->createRow($value)->generate();?>
					<?php endforeach;?>
				<?php endif;?>
			</tbody>
		</table>
		<div class="row">
			<div class="col-lg-4"><?php echo $item->drawBulkActions();?></div>
			<div class="col-lg-8">
				<?php if($item->canDisplayItemsPerPageOptions()):?>
					<div class="pagination">
						<?php echo $tools->l('Display');?>
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							<?php echo $item->getItemsPerPageLabel();?>
							<i class="fa fa-caret-down"></i>
						</button>
						<ul class="dropdown-menu">
							<?php $options = $item->getItemsPerPageOptions();?>
							<?php foreach($options as $value => $label):?>
								<li class="<?php if($item->isActiveItemPerPage($value)):?>active<?php endif;?>">
									<?php echo $item->createItemPerPageLink($value, $label)->generate();?>
								</li>
							<?php endforeach;?>
						</ul>
						<?php echo sprintf($tools->l('/ %s result(s)'), $item->getTotalResult());?>
					</div>
				<?php endif;?>
				<?php echo $item->drawPagination();?>
			</div>
			<?php if($item->hasFooter()):?>
				<?php $footers = $item->getFooters();?>
				<?php foreach($footers as $footer):?>
					<?php echo $footer->generate();?>
				<?php endforeach;?>
			<?php endif;?>
			
		</div>
		
	</div>
<?php if(!$item->isContentOnly()):?>
	</form>
<?php endif;?>
<?php if($item->isDecorated()):?>
		</div>
	</div>
<?php else:?>
	</div>
<?php endif;?>

<?php if(!$item->isContentOnly()):?>
<?php if($item->canDrawEditionFormAtBottom()): echo $item->createFormBlock()->generate(); endif;?>
</div>
<?php endif;?>