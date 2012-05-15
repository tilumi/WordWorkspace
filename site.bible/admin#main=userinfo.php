<?php
include('layout_admin/tpl_header.php');
list( $form ) = APP::$appBuffer;
?>
            <!-- Form elements -->    
            <div class="grid_12">
<p>
<?php echo View::anchor('.', 'Home'); ?>
 »
帳戶設定
</p>

<?php echo redirect_message(); ?>

<?php //echo $formrender->getFormHtmlCode($form); ?>
<?php //echo $formrender->getFormHtml($form); ?>
<?php echo $form; ?>



        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->
                
<?php
include('layout_admin/tpl_footer.php');
?>