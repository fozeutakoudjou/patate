<!-- BEGIN PAGE HEADER-->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>data-tables/DT_bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->
    <div class="page-container">
    
    <div class="row">
			<div class="col-md-12">
        		<h3 class="page-title"><?php echo $this->l("Liste des Utilisateurs", 'Utilisateurs'); ?></h3>
						<ul class="page-breadcrumb breadcrumb">
							<li>
								<i class="fa fa-home"></i>
								<a href="index.html">Acceuil</a>
								<i class="fa fa-angle-right"></i> 
							</li>
							<li>
								<a href="#">Utilisateurs</a>
								<i class="fa fa-angle-right"></i>
							</li>
							<li>
               					 <a href="utilisateurs.html">Listes des Utilisateurs</a>
            				</li>
						</ul>
						<!-- END PAGE TITLE & BREADCRUMB-->
			</div>
		</div>
	  		<!-- END PAGE HEADER-->
				
		<form name="" action="actiongroupeduser.html" method="post" id="groupaction">
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box grey-cascade">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-globe"></i>Managed User
							</div>
							<div class="tools">
								<a href="javascript:;" class="collapse">
								</a>
								<a href="javascript:;" class="reload">
								</a>
								<a href="javascript:;" class="remove">
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<div class="table-toolbar">
								<div class="btn-group">
									<a href="add-user.html" id="sample_editable_1_new" class="btn green">
									Ajout d'utilisateur
									<i class="fa fa-plus"></i></a>
									</div>
								<div class="btn-group pull-right">
									<button class="btn dropdown-toggle" data-toggle="dropdown">Tools <i class="fa fa-angle-down"></i>
									</button>
									<ul class="dropdown-menu pull-right">
										<li>
											<a href="#">
											Print </a>
										</li>
										<li>
											<a href="#">
											Save as PDF </a>
										</li>
										<li>
											<a href="csvutilisateurs.html">
											Export to Excel </a>
										</li>
									</ul>
								</div>	
							</div>
							<div id="sample_1_wrapper" class="dataTables_wrapper" role="grid">
								<div class="row">
									<div class="col-md-6 col-sm-12">	
										<div id="sample_1_length" class="dataTables_length">
											<label>
												<select class="form-control input-small input-inline" size="1"  name="actionselect" id="actionselect">
													<option value="" selected="selected">Selectionner une Action</option>
													<option value="delete">Supprimer</option>
													<option value="active">Activer</option>
													<option value="unactive">désactiver</option>
												</select>
												Actions en Masse
											</label>
										</div>
									</div>
									<div class="col-md-6 col-sm-12">	
										<div class="input-group">
											<input id="searchzone" name="searchzone" class="form-control input-medium input-inline" type="text" placeholder="Rechercher">
												<input id="btnsearchzone" class="btn submit" type="button" value="ok">
										</div>
									</div>
								</div>
								<div class="table-scrollable">
									<table id="sample_1" class="table table-striped  table-bordered table-hover dataTable" aria-describedby="sample_1_info">
										<thead>
											<tr role="row">
												<th class="table-checkbox sorting_disabled" role="columnheader" rowspan="1" colspan="1" style="width:24px;" aria-label="">
												</th>
												<th class="sorting" role="cloumnheader" tabindex="0" aria-controls="sample_1" rowspan="1" colspan="1" style="width:85px;" aria-label="Avatar activate to sort columnn ascending">
													Avatar
												</th>
												<th class="sorting" role="cloumnheader" tabindex="0" aria-controls="sample_1" rowspan="1" colspan="1" style="width:85px;" aria-label="Username: activate to sort columnn ascending">
													Pseudo/Name 
												</th>
												<th class="sorting" role="cloumnheader" tabindex="0" aria-controls="sample_1" rowspan="1" colspan="1" style="width:85px;" aria-label="Email: activate to sort columnn ascending">
													Email
												</th>
												<th class="sorting" role="cloumnheader" tabindex="0" aria-controls="sample_1" rowspan="1" colspan="1" style="width:85px;" aria-label="Surname and Name: activate to sort columnn ascending">
													Groupe
												</th>
												<th>
													Status
												</th>
												<th>
													Actions
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
												<td><?php echo $data->getPseudo().'/'.$data->getNom().' '.$data->getprenom() ?></td>
												<td><a href="<?php echo $data->getEmail()?>">
														<?php echo $data->getEmail() ?></a></td>
												<td> Nom du groupe</td>
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
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>
			
		</form>
			<input id="actiontodo" type="hidden" value="./actiongroupeduser.html" name="actiontodo" >
	</div>	
		
		<!-- BEGIN PAGE LEVEL PLUGINS -->
		<script type="text/javascript" src="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>select2/select2.min.js"></script>
		<script type="text/javascript" src="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>data-tables/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>data-tables/DT_bootstrap.js"></script>
		<!-- END PAGE LEVEL PLUGINS -->
		<!-- BEGIN PAGE LEVEL SCRIPTS -->
		<script src="<?php echo _ASSETS_GLOBAL_SCRIPTS_DIR_;?>metronic.js" type="text/javascript"></script>
		<script src="<?php echo _ASSETS_GLOBAL_SCRIPTS_DIR_;?>datatable.js" type="text/javascript"></script>
		<script src="<?php echo _ASSETS_ADMIN_LAYOUT_DIR_;?>scripts/layout.js" type="text/javascript"></script>
		<script src="<?php echo _ASSETS_ADMIN_PAGES_DIR_;?>scripts/table-managed.js"></script>
		<!-- 
		<script>
			jQuery(document).ready(function() {       
				      
				Metronic.init(); // init metronic core components
				Layout.init(); // init current layout
				TableManaged.init();
			});
		</script>
					-->
	
	
