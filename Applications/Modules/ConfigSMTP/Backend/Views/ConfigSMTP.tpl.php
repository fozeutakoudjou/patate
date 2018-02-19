<!-- BEGIN PAGE HEADER-->
		<div class="row">
			<div class="col-md-12">
        		<h3 class="page-title"><?php echo $this->l("Email Configuration", 'ConfigSMTP'); ?></h3>
						<ul class="page-breadcrumb breadcrumb">
							<li>
								<i class="fa fa-home"></i>
								<a href="index.html">Acceuil</a>
								<i class="fa fa-angle-right"></i>
							</li>
							<li>
								<a href="configurations.html">Configurations</a>
								<i class="fa fa-angle-right"></i>
							</li>
							<li>
               					 <a href="emailconfig.html">Email Configuration</a>
            				</li>
						</ul>
						<!-- END PAGE TITLE & BREADCRUMB-->
			</div>
		</div>
	  		<!-- END PAGE HEADER-->


				<div class="col-md-12">
					<!-- BEGIN Portlet PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title" >
							<div class="caption">
								<i class="fa fa-envelope-o"></i>e-mail config
							</div>
						</div>
				  </div>
						<div  class="portlet-body form" >
                            <form class="form-horizontal" role="form" method="POST">
								<div class="form-body">
									<div class="table">
									    <?php echo $dataForm  ?>
									    
									</div>
								</div>
							</form>	
						</div>	
				</div>
						
