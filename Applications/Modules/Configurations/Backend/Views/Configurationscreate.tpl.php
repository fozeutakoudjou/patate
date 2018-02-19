<div class="page-container">
    <!-- BEGIN PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <h3 class="page-title"><?php echo $this->l("Configuration generale", 'Configurations'); ?></h3>
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.html">Acceuil</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a href="#">Configurations</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                     <a href="configurations.html">Configuration Generale</a>
                </li>
            </ul>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN Portlet PORTLET-->
            <div class="portlet box blue">
                <div class="portlet-title" >
                    <div class="caption">
                        <i class="fa fa-gift"></i>Configuration generale
                    </div>
                </div>
            </div>
            <div  class="portlet-body form" >
                <form class="form-horizontal" role="form" method="POST">
                    <div class="form-body">
                        <?php echo $dataForm ?>
                    </div>	
                </form>
            </div>
        </div>
                <!-- END Portlet PORTLET-->	
    </div>
</div>			
		


