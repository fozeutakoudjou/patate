<table id="sample_1" class="table table-striped  table-bordered table-hover dataTable" aria-describedby="sample_1_info">
	<thead>
		<tr role="row">
			<th class="table-checkbox sorting_disabled" role="columnheader" rowspan="1" colspan="1" style="width:24px;" aria-label="">
			</th>
			<th class="sorting" role="cloumnheader" tabindex="0" aria-controls="sample_1" rowspan="1" colspan="1" style="width:85px;" aria-label="Avatar activate to sort columnn ascending">
				Avatar
			</th>
			<th class="sorting" role="cloumnheader" tabindex="0" aria-controls="sample_1" rowspan="1" colspan="1" style="width:85px;" aria-label="Username: activate to sort columnn ascending">
				Username
			</th>
			<th class="sorting" role="cloumnheader" tabindex="0" aria-controls="sample_1" rowspan="1" colspan="1" style="width:85px;" aria-label="Email: activate to sort columnn ascending">
				Email
			</th>
			<th class="sorting" role="cloumnheader" tabindex="0" aria-controls="sample_1" rowspan="1" colspan="1" style="width:85px;" aria-label="Surname and Name: activate to sort columnn ascending">
				Surname and Name
			</th>
			<th>
				Status
			</th>
			<th>
				Action
			</th>
		</tr>
	</thead>
	<tbody role="alert" aria-live="polite" aria-relevant="all">
			<?php foreach($datalist as $data):  ?>
		<tr class="gradeX odd">


			<td class="sortin_1">
				<input type="checkbox" name="eltcheck[]"  value="<?php echo $data->getId(); ?>">
			</td>
			<td><img alt="avatar" src="<?php echo _UPLOAD_DIR_.'Utilisateurs/'.$data->getAvatar()  ?>" width="50"/></td>
			<td><?php echo $data->getPseudo()  ?></td>
			<td><a href="<?php echo $data->getEmail()?>">
					<?php echo $data->getEmail() ?></a></td>
			<td><?php echo $data->getNom().' '.$data->getprenom() ?></td>
			<td><?php
					if($data->getIs_active())
				echo '<span class="label label-sm label-success">
				Approved </span>';
					else
				echo '<span class="label label-sm label-warning">
				Suspended </span>';
					?>
					</td>
					<td class="last">
						<div class="btn-group">
							<a class="btn grey" href="utilisateur-edit-<?php echo $data->id ?>.html" title="editer cet utilisateur">
								<i class="fa fa-edit fa-lg"></i>Modifier </a>																
							<a class="btn grey"  href="utilisateur-delete-<?php echo $data->id ?>.html" onclick="return(confirm('Êtes-vous sûr de Vous supprimer cet Utilisateur?'));">
								<i class="fa fa-trash-o fa-lg"></i>Supprimer </a>
						</div>	
					</td>
		</tr>
		<?php endforeach;?>


	</tbody>
</table>