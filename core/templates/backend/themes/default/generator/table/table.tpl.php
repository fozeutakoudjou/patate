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
				</div>
			<?php endif;?>
			<?php $headers = $item->getHeaders();?>
			<?php if(!empty($headers)):?>
				<div class="actions">
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
				</tr>
			<?php endif;?>
			<tr role="row" class="filter">
				<td> </td>
				<td>
					<input type="text" class="form-control form-filter input-sm" name="order_id"> </td>
				<td>
					<div class="input-group date date-picker margin-bottom-5" data-date-format="dd/mm/yyyy">
						<input type="text" class="form-control form-filter input-sm" readonly name="order_date_from" placeholder="From">
						<span class="input-group-btn">
							<button class="btn btn-sm default" type="button">
								<i class="fa fa-calendar"></i>
							</button>
						</span>
					</div>
					<div class="input-group date date-picker" data-date-format="dd/mm/yyyy">
						<input type="text" class="form-control form-filter input-sm" readonly name="order_date_to" placeholder="To">
						<span class="input-group-btn">
							<button class="btn btn-sm default" type="button">
								<i class="fa fa-calendar"></i>
							</button>
						</span>
					</div>
				</td>
				<td>
					<input type="text" class="form-control form-filter input-sm" name="order_customer_name"> </td>
				<td>
					<input type="text" class="form-control form-filter input-sm" name="order_ship_to"> </td>
				<td>
					<div class="margin-bottom-5">
						<input type="text" class="form-control form-filter input-sm" name="order_price_from" placeholder="From" /> </div>
					<input type="text" class="form-control form-filter input-sm" name="order_price_to" placeholder="To" /> </td>
				<td>
					<div class="margin-bottom-5">
						<input type="text" class="form-control form-filter input-sm margin-bottom-5 clearfix" name="order_quantity_from" placeholder="From" /> </div>
					<input type="text" class="form-control form-filter input-sm" name="order_quantity_to" placeholder="To" /> </td>
				<td>
					<select name="order_status" class="form-control form-filter input-sm">
						<option value="">Select...</option>
						<option value="pending">Pending</option>
						<option value="closed">Closed</option>
						<option value="hold">On Hold</option>
						<option value="fraud">Fraud</option>
					</select>
				</td>
				<td>
					<div class="margin-bottom-5">
						<button class="btn btn-sm green btn-outline filter-submit margin-bottom">
							<i class="fa fa-search"></i> Search</button>
					</div>
					<button class="btn btn-sm red btn-outline filter-cancel">
						<i class="fa fa-times"></i> Reset</button>
				</td>
			</tr>
		</thead>
		<tbody>
			
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