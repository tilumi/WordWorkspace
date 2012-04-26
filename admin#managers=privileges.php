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
<?php echo View::anchor('/', '主控面板'); ?>
 »
<?php echo View::anchor('.', $mainTitle); ?>
 »
<?php echo APP::$pageTitle; ?>
</p>

                <?php echo Blocks::mainTitle(APP::$pageTitle); ?>

<?php echo redirect_message(); ?>

<style>
input.submit-locked{
    color:orange;
    border:2px solid orange;
}
</style>
<script>
function changeStatus( btn ){
    var btn_id=btn.id;
    var id=btn_id.replace('btn-', '');
    hidden_input=$('#current-'+id);
    current_status=hidden_input.get(0).value;
    switch(current_status){
    case 'allow':
        $(btn).removeClass('submit-green');
        $(btn).addClass('submit-red');
        hidden_input.get(0).value='deny';
        break;
    case 'deny':
        $(btn).removeClass('submit-red');
        $(btn).addClass('submit-green');
        hidden_input.get(0).value='allow';
        break;
    }
}
function setStatus( className, type ){
    switch(type){
    case 'allow':
        $('.'+className).removeClass('submit-red').addClass('submit-green').each( function(){
            var btn_id=$(this).attr('id');
            var id=btn_id.replace('btn-', '');
            var hidden_input=$('#current-'+id);
            hidden_input.get(0).value='allow';
        } );
        //$('.'+className).addClass('submit-green');
        break;
    case 'deny':
        $('.'+className).removeClass('submit-green').addClass('submit-red').each( function(){
            var btn_id=$(this).attr('id');
            var id=btn_id.replace('btn-', '');
            var hidden_input=$('#current-'+id);
            hidden_input.get(0).value='deny';
        } );
        break;
    }
}
</script>


<?php //echo $formrender->getFormHtml($form, 'privileges'); ?>
<?php echo $form; ?>

        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->
                
<?php
include('layout_admin/tpl_footer.php');
?>