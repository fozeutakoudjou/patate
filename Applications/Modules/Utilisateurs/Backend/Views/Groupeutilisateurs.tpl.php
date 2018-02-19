
<!-- BEGIN PAGE HEADER-->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>data-tables/DT_bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->
    <div class="page-container">
    <?php?>
    <div class="row">
			<div class="col-md-12">
        		<h3 class="page-title"><?php echo $this->l("Liste Groupe Utilsateur", 'Utilisateurs'); ?></h3>
						<ul class="page-breadcrumb breadcrumb">
							<li>
								<i class="fa fa-home"></i>
								<a href="index.html">Acceuil</a>
								<i class="fa fa-angle-right"></i>
							</li>
							<li>
								<a href="utilisateurs.html">Utilisateurs</a>
								<i class="fa fa-angle-right"></i>
							</li>
							<li>
               					 <a href="groupeutilisateurs.html">Listes Groupe utilisateur</a>
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
								<i class="fa fa-globe"></i>Managed Group
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
								<a href="add-groupuser.html" id="sample_editable_1_new" class="btn green">
								Ajout d'un nouveau groupe
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
											<a href="#">
											Export to Excel </a>
										</li>
									</ul>
								</div>	
							</div>
							
							<table class="table table-striped  table-bordered" id="sample_1">
								<thead>
									<tr>
										<th>
										Id
										</th>
										<th>
											Nom Technique
										</th>
										<th>
											Nom
										</th>
										<th class="last">Actions</th>
									</tr>
								</thead>
								<tbody>
								 <?php foreach($datalist as $data):  ?>
        						<tr>
           							 <td><?php echo $data->id  ?></td>
            						<td><?php echo $data->technical  ?></td>
            						<td><?php echo $data->nom_groupe  ?></td>
            						<td class="last">
										<div class="btn-group">
											<a class="btn grey" href="groupeutilisateurs-edit-<?php echo $data->id ?>.html" title="editer ce groupe d'utilisateur">
												<i class="fa fa-edit fa-lg"></i>Modifier </a>																
											<a class="btn grey"  href="groupeutilisateurs-delete-<?php echo $data->id ?>.html" onclick="return(confirm('Êtes-vous sûr de Vouloir supprimer ce Groupe d\'Utilisateurs?'));" title="Supprimer ce groupe d'utilisateur">
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
			
		</form>
	</div>
