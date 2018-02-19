<?php

	$data=$results['data'];
	unset($results['data']);
	$autre=$results['Autre'];
	unset($results['Autre'])
?>
 
	
   <!-- Debut de lecture: modules parents se trouvant dans la BD -->
					<div class="form-group"> 
							<label class="control-label col-md-3">
									Parent:
									<span class="required" aria-required="true"> * </span>
							</label>
								<div class="col-md-4">
										<select  class="form-control" name="parent">
												<?php foreach ($data as $result=>$value):?>
														<option value="<?php echo $result;?>"><?php echo $value;?></option>
													<?php endforeach;?>
											</select>
										 </div>
					  </div>	
				<div class="form-group">		
					 <?php if(strcasecmp($autre,"Autre")==0 ){ ?>
					 
					 		<div class="form-group">
								<label class="control-label col-md-3">
										Nom Public
									<span class="required" aria-required="true"> * </span>
								</label> 
								<div class="col-md-4">
										<input class="form-control" type="text" data-required="1"  name="titre">
						         </div>
						    </div>    
							<div class="form-group">
								<label class="control-label col-md-3">
										Lien
									<span class="required" aria-required="true"> * </span>
								</label> 
								<div class="col-md-4">
										<input class="form-control" type="text" data-required="1" placeholder="<?php echo "/admin/".$autre;?>" name="lien">
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
													<option value="NULL">Pas de logo</option>
													<option value="fa fa-cogs">Configuration</option>
																<option value="fa fa-home">Home</option>
																<option value="fa fa-table">Table</option>
																<option value="fa fa-user">User</option>
																	<option value="fa fa-puzzle-piece">Puzzle</option>
															
															
															
															
													</select>
														<span class="help-block"> Logo representant le Module</span>
												 </div>	
	
						</div>
						<div class="form-group">
						 	 <label class="  control-label col-md-4">Greffer a la Position:</label>
								<div class="col-md-8">
									<div class="checkbox-list">
											<label class="checkbox-inline">
													<span><input  type="checkbox" name="position[]"  value="Header"></span>
													<i>Header</i>
											</label>
											<label class="checkbox-inline">
													<span><input  type="checkbox" name="position[]"  checked="checked" value="Left"></span> 
													<i>Left</i>
											</label>
											<label class="checkbox-inline">
													<span><input  type="checkbox" name="position[]" value="Right"></span>
													<i>Right</i> 
											</label>
											<label class="checkbox-inline">
													<span>	<input  type="checkbox" name="position[]" value="Footer"></span>
													 <i>Footer</i>
											</label>
									</div>				 			
				                </div>	
				            </div>    					
					
							<?php }else{  ?>
										  <br/>
										  <legend>Selectionner les sous Menus Possible</legend>
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
													<?php $id=0;		    
													    foreach ($results as $result=>$value):
													    $id++;
															 foreach ($value as $value1=>$value2):
																		list($admin,$chemin)=explode("/",substr($value2,1)); ?>
													            <tr>
													               <td>
													                	<input type="checkbox" name="lien<?php echo $id;?>" value="<?php echo $value2;?>" /> </td>
													                <td><?php echo $chemin;?></td>
													                <td><input class="form-control" type="text" data-required="1" name="titre<?php echo $id;?>" /> </td>
													                 <td>
													                 	<select  class="form-control" name="logo<?php echo $id;?>">
																					<option value="NULL">Pas de logo</option>
																					<option value="fa fa-cogs">Configuration</option>
																					<option value="fa fa-home">Home</option>
																					<option value="fa fa-table">Table</option>
																					<option value="fa fa-user">User</option>
																						<option value="fa fa-puzzle-piece">Puzzle</option>
																				
																		</select></td>
													            </tr>
										        <?php 
										        		endforeach; 
										        echo '<input type="hidden" name="nblien" value='.$id.'>';
										        		endforeach;?>
										        		
									     </tbody>
									</table>
				
						<?php } ?>		
					</div>
					
						<!--  <select id="id_loadlink" class="form-control" name="lien">-->
					 		
					 
					<br/>
	
	<div class="form-actions fluid">
		<div class="col-md-offset-6 col-md-9">
			<button class="btn green" type="submit" value="valider"><?php echo $this->l("Valider", 'Menu'); ?></button>
			<!--  <button class="btn default" type="button">Cancel</button>-->
		</div>
	</div>
			</form>