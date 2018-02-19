<?php 
  $dataM=$data['Module'];
  unset($data['Module']);
  $dataP=$data['Parent'];
  unset($data['Parent']);
 // var_dump($data[0]);die();
  ?>
<div class="row">
			<div class="col-md-12">
        		<h3 class="page-title"><?php echo $this->l("Edition d'un menu", 'Menu'); ?></h3>
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
               					 <a href="#">Edition</a>
            				</li>
						</ul>
						<!-- END PAGE TITLE & BREADCRUMB-->
			</div>
		
	<form class="form-horizontal" method="post">	
             <legend>Modification de l'element Racine</legend>
                     <div class="form-group"> 
							<label class="control-label col-md-3">
									Front_link:
									<span class="required" aria-required="true"> * </span>
							</label>
								<div class="col-md-4">
								  <div class="radio-list">
												<span>
														<input type="hidden" name="id"  value="<?php echo $data[0]->getId();?>">
														<input  type="radio" <?php if(strcasecmp($data[0]->getType_link(),"0")==0) echo 'checked=checked'; else "disabled='disabled'";?> value="0" name="type_link">
												</span> Non
												<span>
														<input  type="radio" <?php if(strcasecmp($data[0]->getType_link(),"1")==0) echo 'checked=checked' ; else "disabled='disabled'";  ?> value="1" name="type_link">
												</span> Oui
													
							  </div>
							</div>  
				   </div>
				   <div class="form-group"> 
							<label class="control-label col-md-3">
									Catalogue:
									<span class="required" aria-required="true"> * </span>
							</label>
								<div class="col-md-4">
										<select  class="form-control" name="module">
										     <?php foreach ($dataM as $module): ?>
										       <option value="<?php echo $module;?>" <?php if(strcasecmp($data[0]->getModule(),$module)==0) echo "selected='selected'" ;else echo "disabled='disabled'" ?> > <?php echo $module?></option>      
										    <?php  endforeach;?>   
									 </select>
							 </div>	 
					  </div>
					<div class="form-group"> 
							<label class="control-label col-md-3">
									Parent:
									<span class="required" aria-required="true"> * </span>
							</label>
								<div class="col-md-4">
										<select  class="form-control" name="parent">
										     <?php foreach ($dataP as $key=>$value): ?>
										       <option value="<?php echo $key;?>" <?php if(strcasecmp($data[0]->getParent(),$key)==0) echo "selected=selected"; ?> > <?php echo $value?></option>      
										    <?php  endforeach;?>   
									 </select>
							 </div>
					  </div>  
					  <div class="form-group">
								<label class="control-label col-md-3">
										Nom Public
									<span class="required" aria-required="true"> * </span>
								</label> 
								<div class="col-md-4">
										<input class="form-control" type="text" data-required="1" value="<?php echo $data[0]->getTitre() ?>"  name="titre">
						         </div>
						 </div>    
						<div class="form-group">
								<label class="control-label col-md-3">
										Lien
									<span class="required" aria-required="true"> * </span>
								</label> 
								<div class="col-md-4">
										<input class="form-control" type="text" data-required="1" value="<?php echo $data[0]->getLien() ?>"   name="lien">
										 
										<span class="help-block">Respecter le format:&nbsp;<strong>#</strong>&nbsp;ou&nbsp;<strong>/admin/lien.html</strong></span>
						         </div>
						    </div> 
						    
						  <div class="form-group">
       		                       <label class="control-label col-md-3">
							 			Logo:
											<span class="required" aria-required="true"> * </span>
							 		</label>				 			
							 		<div class="col-md-4">
												<select  class="form-control" name="logo">
													<option value="NULL"<?php if(strcasecmp($data[0]->getLogo(),"NULL")==0) echo "selected=selected"?> >Pas de logo</option>
													<option value="fa fa-cogs"<?php  if(strcasecmp($data[0]->getLogo(),"fa fa-cogs")==0) echo "selected=selected"?> >Configuration</option>
													<option value="fa fa-home" <?php  if(strcasecmp($data[0]->getLogo(),"fa fa-home")==0) echo "selected=selected"?> >Home</option>
													<option value="fa fa-table"  <?php  if(strcasecmp($data[0]->getLogo(),"fa fa-table")==0) echo "selected=selected"?> >Table</option>
													<option value="fa fa-user" <?php  if(strcasecmp($data[0]->getLogo(),"fa fa-user")==0) echo "selected=selected"?> >User</option>
													<option value="fa fa-puzzle-piece" <?php  if(strcasecmp($data[0]->getLogo(),"fa fa-puzzle-piece")==0) echo "selected=selected"?> >Puzzle</option>		
												</select>
														<span class="help-block"> Logo representant le Module</span>
									</div>	
					</div>
					<?php 
					  if(strcasecmp($data[0]->getParent(),"NULL")==0){
							$position=explode(',',$data[0]->getPosition());	  
					 ?>		   
									<div class="form-group">
									 	 <label class="  control-label col-md-4">Greffer a la Position:</label>
											<div class="col-md-8">
												<div class="checkbox-list">
														<label class="checkbox-inline">
																<span><input  type="checkbox" name="position[]" <?php for($i=0;$i<count($position);$i++){  if(strcasecmp($position[$i],"Header")==0) echo 'checked=checked'; } ?> value="Header"></span>
																<i>Header</i>
														</label>
														<label class="checkbox-inline">
																<span><input  type="checkbox" name="position[]" <?php for($i=0;$i<count($position);$i++){   if(strcasecmp($position[$i],"Left")==0) echo 'checked=checked'; } ?>  value="Left"></span> 
																<i>Left</i>
														</label>
														<label class="checkbox-inline">
																<span><input  type="checkbox" name="position[]" <?php for($i=0;$i<count($position);$i++){  if(strcasecmp($position[$i],"Right")==0) echo 'checked=checked'; } ?> value="Right"></span>
																<i>Right</i> 
														</label>
														<label class="checkbox-inline">
																<span>	<input  type="checkbox" name="position[]"<?php for($i=0;$i<count($position);$i++){   if(strcasecmp($position[$i],"Footer")==0) echo 'checked=checked'; } ?>  value="Footer"></span>
																 <i>Footer</i>
														</label>
												</div>				 			
							                </div>	
							            </div>    
									
						<?php 
					 }?>    
       		  <?php
       		 if(count($data)>1){ 
					 ?>
       		     		 <br/>
								<legend>Modification des sous elements</legend>
							 				<table class="table table-striped table-hover table-bordered" id="sample_1">
													<thead>
														<tr>
										            
										                    <th><?php echo $this->l('Check', 'Menu'); ?></th>
										                    <th><?php echo $this->l('Lien', 'Menu'); ?></th>
										                    <th><?php echo $this->l('Nom Public', 'Menu')?>
										                        <span class="required" aria-required="true"> * </span></th>
										                     <th><?php echo $this->l('Logo Present', 'Menu'); ?></th>
														</tr>	
													</thead>
											  <tbody>
													<?php 		
															$id=0;    
															unset($data[0]);
															 foreach ($data as $data2):
															 			$id++;
																		list($admin,$chemin)=explode("/",substr($data2->getLien(),1)); ?>
													            <tr>
													               <td>
													               <input type="hidden" name="nblien" value="<?php echo count($data);?>">
													               <input type="hidden" name="id<?php echo $id;?>"  value="<?php echo $data2->getId();?>">
													                	<input type="checkbox" name="lien<?php echo $id;?>" value="<?php echo $data2->getLien();?>" checked="checked" disabled='disabled'/>
													                	<input type="hidden" name="lien<?php echo $id;?>"  value="<?php echo $data2->getLien();?>"> </td>
													                <td><?php echo $chemin;?></td>
													                <td><input class="form-control" type="text" data-required="1" name="titre<?php echo $id;?>" value="<?php echo $data2->getTitre()?>" /> </td>
													                 <td>
													                 	<select  class="form-control" name="logo<?php echo $id;?>">
																					<option value="NULL"<?php if(strcasecmp($data2->getLogo(),"NULL")==0) echo "selected=selected"?> >Pas de logo</option>
																					<option value="fa fa-cogs"<?php  if(strcasecmp($data2->getLogo(),"fa fa-cogs")==0) echo "selected=selected"?> >Configuration</option>
																					<option value="fa fa-home" <?php  if(strcasecmp($data2->getLogo(),"fa fa-home")==0) echo "selected=selected"?> >Home</option>
																					<option value="fa fa-table"  <?php  if(strcasecmp($data2->getLogo(),"fa fa-table")==0) echo "selected=selected"?> >Table</option>
																					<option value="fa fa-user" <?php  if(strcasecmp($data2->getLogo(),"fa fa-user")==0) echo "selected=selected"?> >User</option>
																					<option value="fa fa-puzzle-piece" <?php  if(strcasecmp($data2->getLogo(),"fa fa-puzzle-piece")==0) echo "selected=selected"?> >Puzzle</option>		
												                       </select>
													                 </td>
													            </tr>
										     <?php 
										        	endforeach; ?>		
									     </tbody>
									</table
									
       		     <?php } ?>
       		     
       		      		 	  		
	           <div class="form-actions fluid">
					<div class="col-md-offset-6 col-md-9">
						<button class="btn green" type="submit" value="valider"><?php echo $this->l("Valider", 'Menu'); ?></button>
				    </div>
		      </div>   
   </form>
</div>   