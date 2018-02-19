
<!-- END PAGE LEVEL STYLES -->
  
    
    <div class="row">
			<div class="col-md-12">
        		<h3 class="page-title"><?php echo $this->l("Liste des Menus Cr&eacute;es", 'Menu'); ?></h3>
						<ul class="page-breadcrumb breadcrumb">
							<li>
								<i class="fa fa-home"></i>
								<a href="index.html">Acceuil</a>
								<i class="fa fa-angle-right"></i>
							</li>
							<li>
								<a href="#">Cr&eacute;er menu</a>
								<i class="fa fa-angle-right"></i>
							</li>
							<li>
               					 <a href="#">Listing</a>
            				</li>
						</ul>
						<!-- END PAGE TITLE & BREADCRUMB-->
			</div>
		</div>
	  		<!-- END PAGE HEADER-->
	  <form name="" action="actiongroupedadv.html" method="post" id="groupaction">	
	  	<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box grey-cascade">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-globe"></i>Menu
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
								<a href="cree-menu.html" id="sample_editable_1_new" class="btn green">
								Ajout d'un Menu
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
							
							<table class="table table-striped table-hover table-bordered" id="sample_1">
								<thead>
									<tr>
					                    <th></th>
					                    <th><?php echo $this->l('Id', 'Menu'); ?></th>
					                    <th><?php echo $this->l('Type de Liaison', 'Menu'); ?></th>
					                    <th><?php echo $this->l('Module', 'Menu'); ?></th>
					                    <th><?php echo $this->l('Nom Publique', 'Menu'); ?></th>
					                    <th><?php echo $this->l('Url Interne du Module', 'Menu'); ?></th>                               
					                    <th><?php echo $this->l('Parent du Module', 'Menu'); ?></th>
					                     <th><?php echo $this->l('Logo', 'Menu'); ?></th>
					                     <th><?php echo $this->l('Position', 'Menu'); ?></th>
                						<th>Action</th>
									</tr>	
								</thead>
								<tbody>
										<?php foreach($datalist as $data):?>
						            <tr>
						                <td class="first style1"><input type="checkbox" name="eltcheck[]" class="elttocheck" value="<?php echo $data->getId(); ?>"></td>
						                        <td><?php echo $data->getId(); ?></td>
						                        <td><?php if($data->getType_link()==0)echo "Backend";
						                                   else  echo "Frontend"; ?></td>
						                        <td><?php echo $data->getModule(); ?></td>
						                        <td><?php echo $data->getTitre(); ?></td>
						                        <td><?php echo $data->getLien(); ?></td>
						                        <td><?php echo $data->getParent(); ?></td>
						                        <td><?php if(strcasecmp($data->getLogo(),"NULL")==0)
						                        				echo "Non";
						                        		  else  echo "Oui";		
						                                 ?></td>
						                           <td><?php echo $data->getPosition(); ?></td>       
						                <td class="last"> 
						                    <?php if($this->app->employee()->haveRightTo('edit')){ ?>
						                        <a href="modMenu-<?php echo $data->getId() ?>.html" title="<?php echo $this->l('modifier', 'Menu'); ?>"><img src="<?php echo _THEME_BO_IMG_DIR_.'edit-icon.gif'; ?>" style="width:16px; height:16px;" alt="&Eacute;diter" /></a> 
						                    <?php } ?>
						                    <?php if($this->app->employee()->haveRightTo('delete')){ ?>
						                        <a class="delete_elt" href="deleteMenu-<?php echo $data->getId() ?>.html" title="<?php echo $this->l('supprimer', 'Menu'); ?>" onclick="return(confirm('<?php echo  $this->l('êtez vous sure de vouloir effectuer cette action?', 'Menu'); ?>'));"><img src="<?php echo _THEME_BO_IMG_DIR_.'hr.gif'; ?>" style="width:16px; height:16px;" alt="Supprimer" /></a>
						                    <?php } ?>
						                </td>
						
						            </tr>
						        <?php endforeach;?>
						     </tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" name="actiontodo" value="./actiongroupedMenu.html" id="actiontodo" />
		</form>
    	

        