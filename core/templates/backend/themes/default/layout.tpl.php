<?php echo $header;?>
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<div class="page-sidebar-wrapper">
		<div class="page-sidebar navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			<ul class="page-sidebar-menu  page-header-fixed page-sidebar-menu-hover-submenu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
				<!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
				<li class="sidebar-toggler-wrapper">
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<div class="sidebar-toggler">
					</div>
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
				</li>
				<!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
				<li class="sidebar-search-wrapper hidden-xs">
					<!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
					<!-- DOC: Apply "sidebar-search-bordered" class the below search form to have bordered search box -->
					<!-- DOC: Apply "sidebar-search-bordered sidebar-search-solid" class the below search form to have bordered & solid search box -->
					<form class="sidebar-search" action="extra_search.html" method="POST">
						<a href="javascript:;" class="remove">
						</a>
						<div class="input-group">
							<input type="text" class="form-control" placeholder="Search...">
							<span class="input-group-btn">
								<!-- DOC: value=" ", that is, value with space must be passed to the submit button -->
								<input class="btn submit" type="button" type="button" value=" "/>
							</span>
						</div>
					</form>
					<!-- END RESPONSIVE QUICK SEARCH FORM -->
				</li>
				<?php if(isset($menuContent)):?><?php echo $menuContent;?><?php endif;?>
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>

<!-- END SIDEBAR -->
<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content <?php echo $pageContentClasses;?>">
			<div class="<?php echo $pageSuccessClasses;?>"><?php $tools->includeTpl('notification/confirmations', false);?></div>
			<?php $tools->includeTpl('notification/informations', false);?>
			<?php $tools->includeTpl('notification/warnings', false);?>
			<div class="<?php echo $pageErrorsClasses;?>"><?php $tools->includeTpl('notification/errors', false);?></div>
			
			<?php echo $page; ?>
			<!-- END CONTENT -->
		</div>	
	</div>	
</div>
<?php echo $footer;?>