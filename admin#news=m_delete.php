<?php
include('layout_admin/tpl_header.php');
include('layout_admin/helper.blocks.php');
list( $form ) = APP::$appBuffer;
$mainTitle = APP::$mainTitle;
$mainName = APP::$mainName;
?>
            <!-- Form elements -->    
            <div class="grid_12">
<p>
<?php echo View::anchor('..', 'Home'); ?>
 »
<?php echo View::anchor('.', $mainTitle); ?>
 »
多筆<?php echo APP::$mainName;?>刪除確認
</p>

<?php echo redirect_message(); ?>

<?php //echo $formrender->getFormHtml($form); ?>
<?php echo $form; ?>
          
        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->
                
<?php
include('layout_admin/tpl_footer.php');
?>