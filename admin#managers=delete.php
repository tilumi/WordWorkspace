            <!-- Form elements -->    
            <div class="grid_12">
<p>
<?php echo View::anchor('..', 'Home'); ?>
 »
<?php echo View::anchor('.', $mainTitle); ?>
 »
刪除<?php echo $mainName; ?>
</p>

<?php echo redirect_message(); ?>

<?php //echo $formrender->getFormHtml($form); ?>
<?php echo $form; ?>
          
        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->
                
