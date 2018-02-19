<!-- BEGIN PAGE CONTENT-->
	<div class="row">
    <div class="col-md-12">
        <h3 class="page-title"><?php echo $this->l("Profile", 'Utilisateurs'); ?></h3>
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
				<a href="#"> <?php echo $userdata->getPseudo();?></a>
                
            </li>
        </ul>
        <!-- END PAGE TITLE & BREADCRUMB-->
    </div>
	</div>
	<div class="row profile">
		<div class="col-md-12">
			<!--BEGIN TABS-->
			<div class="tabbable tabbable-custom tabbable-full-width">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#tab_1_1" data-toggle="tab">
						Overview </a>
					</li>
					<li>
						<a href="#tab_1_3" data-toggle="tab">
						Account </a>
					</li>
					
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="tab_1_1">
						<div class="row">
							<div class="col-md-3">
								<ul class="list-unstyled profile-nav">
									<li>
										<img src="<?php echo _UPLOAD_DIR_.'Utilisateurs/'.$userdata->getAvatar()  ?>" class="img-responsive" alt=""/>
										<a href="#" class="profile-edit">
										edit </a>
									</li>
									<li>
										<a href="#">
										Projects </a>
									</li>
									<li>
										<a href="#">
										Messages <span>
										3 </span>
										</a>
									</li>
									<li>
										<a href="#">
										Friends </a>
									</li>
									<li>
										<a href="#">
										Settings </a>
									</li>
								</ul>
							</div>
							<div class="col-md-9">
								<div class="row">
									<div class="col-md-8 profile-info">
										<h1><?php echo $userdata->getNom().' '.$userdata->getPrenom();?></h1>
										<p>
											 Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt laoreet dolore magna aliquam tincidunt erat volutpat laoreet dolore magna aliquam tincidunt erat volutpat.
										</p>
										<p>
											<a href="#">
											www.mywebsite.com </a>
										</p>
										<ul class="list-inline">
											<li>
												<i class="fa fa-map-marker"></i> Spain
											</li>
											<li>
												<i class="fa fa-calendar"></i> 18 Jan 1982
											</li>
											<li>
												<i class="fa fa-briefcase"></i> Design
											</li>
											<li>
												<i class="fa fa-star"></i> Top Seller
											</li>
											<li>
												<i class="fa fa-heart"></i> BASE Jumping
											</li>
										</ul>
									</div>
									<!--end col-md-8-->
									<div class="col-md-4">
										<div class="portlet sale-summary">
											<div class="portlet-title">
												<div class="caption">
													 Sales Summary
												</div>
												<div class="tools">
													<a class="reload" href="javascript:;">
													</a>
												</div>
											</div>
											<div class="portlet-body">
												<ul class="list-unstyled">
													<li>
														<span class="sale-info">
														TODAY SOLD <i class="fa fa-img-up"></i>
														</span>
														<span class="sale-num">
														23 </span>
													</li>
													<li>
														<span class="sale-info">
														WEEKLY SALES <i class="fa fa-img-down"></i>
														</span>
														<span class="sale-num">
														87 </span>
													</li>
													<li>
														<span class="sale-info">
														TOTAL SOLD </span>
														<span class="sale-num">
														2377 </span>
													</li>
													<li>
														<span class="sale-info">
														EARNS </span>
														<span class="sale-num">
														$37.990 </span>
													</li>
												</ul>
											</div>
										</div>
									</div>
									<!--end col-md-4-->
								</div>
								<!--end row-->
								<div class="tabbable tabbable-custom tabbable-custom-profile">
									<ul class="nav nav-tabs">
										<li class="active">
											<a href="#tab_1_11" data-toggle="tab">
											Latest Customers </a>
										</li>
										<li>
											<a href="#tab_1_22" data-toggle="tab">
											Feeds </a>
										</li>
									</ul>
									<div class="tab-content">
										<div class="tab-pane active" id="tab_1_11">
											<div class="portlet-body">
												<table class="table table-striped table-bordered table-advance table-hover">
												<thead>
												<tr>
													<th>
														<i class="fa fa-briefcase"></i> Name
													</th>
													<th class="hidden-xs">
														<i class="fa fa-envelope"></i> Email
													</th>
													<th>
														<i class="fa fa-phone"></i> Telephone
													</th>
													<th>
														<i class="fa fa-bookmark"></i> Statut
													</th>
													<th>
														Actions
													</th>
												</tr>
												</thead>
												<tbody>
													<?php foreach($userprospect as $data):  ?>
												<tr>
													<td>
														 <?php echo $data->getNom();?>
														
													</td>
													<td class="hidden-xs">
														 <a href="#"><?php echo $data->getEmail();?></a>
													</td>
													<td>
														  <?php echo $data->getTelephone();?>
													</td>
													<td>
														  <?php echo $data->getStatut();?>
													</td>
													<td>
														<a class="btn default btn-xs green-stripe" href="prospect-edit-<?php echo $data->getId();?>.html">
														<i class="fa fa-edit"></i>Edit </a>
														<a class="btn default btn-xs green-stripe" href="prospect-delete-<?php echo $data->getId();?>.html" onclick="return(confirm('Êtes-vous sûr de Vous supprimer cet Utilisateur?'));">
														<i class="fa fa-trash-o"></i>Delete </a>
													</td>
												</tr>
												<?php endforeach;?>
												</tbody>
												</table>
											</div>
										</div>
										<!--tab-pane-->
										<div class="tab-pane" id="tab_1_22">
											<div class="tab-pane active" id="tab_1_1_1">
												<div class="scroller" data-height="290px" data-always-visible="1" data-rail-visible1="1">
													<ul class="feeds">
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-success">
																			<i class="fa fa-bell-o"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 You have 4 pending tasks. <span class="label label-danger label-sm">
																			Take action <i class="fa fa-share"></i>
																			</span>
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 Just now
																</div>
															</div>
														</li>
														<li>
															<a href="#">
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-success">
																			<i class="fa fa-bell-o"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New version v1.4 just lunched!
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 20 mins
																</div>
															</div>
															</a>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-danger">
																			<i class="fa fa-bolt"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 Database server #12 overloaded. Please fix the issue.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 24 mins
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-info">
																			<i class="fa fa-bullhorn"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New order received. Please take care of it.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 30 mins
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-success">
																			<i class="fa fa-bullhorn"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New order received. Please take care of it.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 40 mins
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-warning">
																			<i class="fa fa-plus"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New user registered.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 1.5 hours
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-success">
																			<i class="fa fa-bell-o"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 Web server hardware needs to be upgraded. <span class="label label-inverse label-sm">
																			Overdue </span>
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 2 hours
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-default">
																			<i class="fa fa-bullhorn"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New order received. Please take care of it.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 3 hours
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-warning">
																			<i class="fa fa-bullhorn"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New order received. Please take care of it.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 5 hours
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-info">
																			<i class="fa fa-bullhorn"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New order received. Please take care of it.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 18 hours
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-default">
																			<i class="fa fa-bullhorn"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New order received. Please take care of it.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 21 hours
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-info">
																			<i class="fa fa-bullhorn"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New order received. Please take care of it.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 22 hours
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-default">
																			<i class="fa fa-bullhorn"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New order received. Please take care of it.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 21 hours
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-info">
																			<i class="fa fa-bullhorn"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New order received. Please take care of it.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 22 hours
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-default">
																			<i class="fa fa-bullhorn"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New order received. Please take care of it.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 21 hours
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-info">
																			<i class="fa fa-bullhorn"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New order received. Please take care of it.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 22 hours
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-default">
																			<i class="fa fa-bullhorn"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New order received. Please take care of it.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 21 hours
																</div>
															</div>
														</li>
														<li>
															<div class="col1">
																<div class="cont">
																	<div class="cont-col1">
																		<div class="label label-info">
																			<i class="fa fa-bullhorn"></i>
																		</div>
																	</div>
																	<div class="cont-col2">
																		<div class="desc">
																			 New order received. Please take care of it.
																		</div>
																	</div>
																</div>
															</div>
															<div class="col2">
																<div class="date">
																	 22 hours
																</div>
															</div>
														</li>
													</ul>
												</div>
											</div>
										</div>
										<!--tab-pane-->
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--tab_1_2-->
					<div class="tab-pane" id="tab_1_3">
						<div class="row profile-account">
							<div class="col-md-3">
								<ul class="ver-inline-menu tabbable margin-bottom-10">
									<li class="active">
										<a data-toggle="tab" href="#tab_1-1">
										<i class="fa fa-cog"></i> Personal info </a>
										<span class="after">
										</span>
									</li>
									<li>
										<a data-toggle="tab" href="#tab_2-2">
										<i class="fa fa-picture-o"></i> Change Avatar </a>
									</li>
									<li>
										<a data-toggle="tab" href="#tab_3-3">
										<i class="fa fa-lock"></i> Change Password </a>
									</li>
								</ul>
							</div>
							<div class="col-md-9">
								<div class="tab-content">
									<div id="tab_1-1" class="tab-pane active">
										<form role="form" action="#" method="post">
											<input type="hidden" name="id"  class="form-control" value="<?php echo $userdata->getId();?>"/>
											<div class="form-group">
												<label class="control-label">Pseudo</label>
												<input type="text" name="pseudo" placeholder="Nom" class="form-control" value="<?php echo $userdata->getPseudo();?>"/>
											</div>
											<div class="form-group">
												<label class="control-label">First Name</label>
												<input type="text" name="nom" placeholder="Nom" class="form-control" value="<?php echo $userdata->getNom();?>"/>
											</div>
											<div class="form-group">
												<label class="control-label">Last Name</label>
												<input type="text" name="prenom" placeholder="Nom" class="form-control" value="<?php echo $userdata->getPrenom();?>"/>
											</div>
											<div class="form-group">
												<label class="control-label">Adress</label>
												<input type="text" name="adresse" placeholder="Adresse" class="form-control" value="<?php echo $userdata->getAdresse();?>"/>
											</div>
											<div class="form-group">
												<label class="control-label">E-mail</label>
												<input type="text" name="email" placeholder="contact@crystals-services.com" class="form-control" value="<?php echo $userdata->getEmail();?>"/>
											</div>
											<div class="form-group">
												<label class="control-label">Mobile Number 1</label>
												<input type="text" name="tel1" placeholder="Telephone 1" class="form-control" value="<?php echo $userdata->getTel1();?>"/>
											</div>
											<div class="form-group">
												<label class="control-label">Mobile Number 2</label>
												<input type="text" name="tel2" placeholder="Telephone 2" class="form-control" value="<?php echo $userdata->getTel2();?>"/>
											</div>
											<div class="form-group">
												<label class="control-label">Pays</label>
												<input type="text"  name="pays" placeholder="Pays" class="form-control" value="<?php echo $userdata->getPays();?>"/>
											</div>
											<div class="form-group">
												<label class="control-label">Ville</label>
												<input type="text" name="ville" placeholder="Ville" class="form-control" value="<?php echo $userdata->getVille();?>"/>
											</div>
											<div class="form-group">
												<label class="control-label">Code Postal</label>
												<input type="text" name="code_postal"  placeholder="Code Postal" class="form-control" value="<?php echo $userdata->getCode_postal();?>"/>
											</div>
											<div class="form-group">
												<label class="control-label">About</label>
												<textarea name="infos_complementaires" class="form-control" rows="3" placeholder="We are KeenThemes!!!"><?php echo $userdata->getInfos_complementaires();?></textarea>
											</div>
											<div class="margiv-top-10">
<!--												<a href="#" class="btn green">
												Save Changes </a>-->
												<input type="submit" class="btn green" name="submit_info" value="Saves Changes"/>
<!--												<a href="#" class="btn default">
												Cancel </a>-->
											</div>
										</form>
									</div>
									<div id="tab_2-2" class="tab-pane">
										<p>
											 Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod.
										</p>
										<form action="#" role="form" method="post">
											<input type="hidden" name="id"  class="form-control" value="<?php echo $userdata->getId();?>"/>
											<div class="form-group">
												<div class="fileinput fileinput-new" data-provides="fileinput">
													<div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
														<img id="placehold" src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt=""/>
													</div>
													<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
													</div>
													<div>
														<span class="btn default btn-file">
														<span class="fileinput-new">
														Select image </span>
														<span class="fileinput-exists">
														Change </span>
														<input type="file" id="avatar" name="avatar">
														</span>
														<a href="#" id="remove" class="btn default fileinput-exists" data-dismiss="fileinput">
														Remove </a>
													</div>
												</div>
												<div class="clearfix margin-top-10">
													<span class="label label-danger">
													NOTE! </span>
													<span>
													Attached image thumbnail is supported in Latest Firefox, Chrome, Opera, Safari and Internet Explorer 10 only </span>
												</div>
											</div>
											<div class="margin-top-10">
												<input type="submit" class="btn green" name="submit_avatar" value="Change Avatar"/>
<!--												<a href="#" class="btn green">
												Submit </a>
												<a href="#" class="btn default">
												Cancel </a>-->
											</div>
										</form>
									</div>
									<div id="tab_3-3" class="tab-pane">
										<form action="#" name="" method="post">
											<input type="hidden" name="id"  class="form-control" value="<?php echo $userdata->getId();?>"/>
											<div class="form-group">
												<label class="control-label">Current Password</label>
												<input type="password" name="password" class="form-control"/>
											</div>
											<div class="form-group">
												<label class="control-label">New Password</label>
												<input type="password" name="new_password" class="form-control"/>
											</div>
											<div class="form-group">
												<label class="control-label">Re-type New Password</label>
												<input type="password"  name="verif_new_password" class="form-control"/>
											</div>
											<div class="margin-top-10">
												<input type="submit" class="btn green" name="submit_password" value="Change Password"/>
<!--												<a href="#" class="btn green">
												Change Password </a>
												<a href="#" class="btn default">
												Cancel </a>-->
											</div>
										</form>
									</div>
									
								</div>
							</div>
							<!--end col-md-9-->
						</div>
					</div>
					<!--end tab-pane-->
					
				</div>
			</div>
			<!--END TABS-->
		</div>
	</div>
	<!-- END PAGE CONTENT-->

