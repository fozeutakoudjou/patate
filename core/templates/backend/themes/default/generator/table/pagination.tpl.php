<?php $end = $item->getEndPage();?>
<?php $start = $item->getStartPage();?>
<ul class="pagination">
	<li class="prev <?php if(!$item->isFirstEnabled()):?>disabled<?php endif?>">
		<?php $link = $item->createFirstLink(); $link->setTitle($tools->l('First')); echo $link->generate();?>
	</li>
	<li class="prev  <?php if(!$item->isPrevEnabled()):?>disabled<?php endif?>">
		<?php $link = $item->createPrevLink(); $link->setTitle($tools->l('Prev')); echo $link->generate();?>
	</li>
	<?php for($page = $start; $page <= $end; $page++):?>
		<li class="<?php echo $item->drawActive($page);?>"><?php echo $item->createLink($page)->generate();?></li>
	<?php endfor;?>
	<li class="next <?php if(!$item->isNextEnabled()):?>disabled<?php endif?>">
		<?php $link = $item->createNextLink(); $link->setTitle($tools->l('Next')); echo $link->generate();?>
	</li>
	<li class="next <?php if(!$item->isLastEnabled()):?>disabled<?php endif?>">
		<?php $link = $item->createLastLink(); $link->setTitle($tools->l('Last')); echo $link->generate();?>
	</li>
</ul>