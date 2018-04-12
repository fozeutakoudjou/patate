<?php if($useOfFooter):?>
<div class="page-footer">
	<div class="page-footer-inner">
		<div class="copyright">
			<?php echo 'NAME';?> <?php echo 'is application developped by'; ?>
			<a href="<?php echo 'https://www.google.com';?>" title="<?php echo 'AUTHOR';?>" target="_blank"><?php echo 'AUTHOR';?></a>
			<span><?php echo date('Y');?> &copy;.&nbsp;<?php echo 'All right reserved'; ?></span>
		</div>
	</div>
	<div class="page-footer-tools">
		<span class="go-top">
		<i class="fa fa-angle-up"></i>
		</span>
	</div>
</div>
<?php endif;?>

<?php if(isset($modals)):?>
<div>
	<?php echo $modals;?>
</div>
<?php endif;?>
<?php $tools->includeTpl('javascript', false, array('partKey'=>$notHeadKey, 'libraryKey'=>$libraryKey, 'notLibraryKey'=>$notLibraryKey, 'jsFiles'=>$jsFiles, 'jsContents'=>$jsContents, 'jsVariables'=>$jsVariables), false);?>

<!--[if lt IE 9]>
<script src="<?php echo $librariesDir;?>js/respond.min.js"></script>
<script src="<?php echo $librariesDir;?>js/excanvas.min.js"></script> 
<![endif]-->
<script src="<?php echo $librariesDir;?>js/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?php echo $librariesDir;?>js/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?php echo $librariesDir;?>js/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?php echo $librariesDir;?>js/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?php echo $librariesDir;?>uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="<?php echo $librariesDir;?>bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
	
<script src="<?php echo $jsAdminThemeDir;?>metronic.js" type="text/javascript"></script>
<script src="<?php echo $jsAdminThemeDir;?>layout.js" type="text/javascript"></script>
<script src="<?php echo $jsAdminThemeDir;?>quick-sidebar.js" type="text/javascript"></script>
<script src="<?php echo $jsAdminThemeDir;?>demo.js" type="text/javascript"></script>

<?php if(isset($additionalFooter)):?>
	<?php echo $additionalFooter;?>
<?php endif;?>

</body>
</html>
