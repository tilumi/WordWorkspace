            <!-- Form elements -->    
            <div class="grid_12">
<p>
<?php echo View::anchor('/', '主控面板'); ?>
 »
<?php echo View::anchor('.', $mainTitle); ?>
 »
多筆刪除確認
</p>

<?php echo redirect_message(); ?>

<?php //echo $formrender->getFormHtml($form); ?>
<?php echo $form; ?>
          
        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->
                
