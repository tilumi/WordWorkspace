<?php
include('layout_admin/tpl_header.php');
?>

            <!-- Form elements -->    
            <div class="grid_7">
<p>
<?php echo View::anchor('.', 'Home'); ?>
 »
變更密碼
</p>

<?php echo redirect_message(); ?>

<?php //echo $formrender->getFormHtml($form); ?>
<?php echo $form; ?>

        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->
                


<?php
include('layout_admin/tpl_footer.php');
?>