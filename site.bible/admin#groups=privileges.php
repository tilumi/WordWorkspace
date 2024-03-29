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
<?php echo View::anchor('/', '管理首頁'); ?>
 »
<?php echo View::anchor('/managers/', '系統管理員 Managers'); ?>
 »
<?php echo View::anchor('.', $mainTitle); ?>
 »
<?php echo APP::$pageTitle; ?>
</p>

                <?php echo Blocks::mainTitle(APP::$pageTitle); ?>

<?php echo redirect_message(); ?>

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
        $(btn).addClass('submit-gray');
        hidden_input.get(0).value='neutral';
        break;
    case 'neutral':
        $(btn).removeClass('submit-gray');
        $(btn).addClass('submit-green');
        hidden_input.get(0).value='allow';
        break;
    }
}
function setStatus( className, type ){
    switch(type){
    case 'allow':
        $('.'+className).attr('class', className+' submit-green');
        $('.'+className).each( function(){
            var btn_id=$(this).attr('id');
            var id=btn_id.replace('btn-', '');
            var hidden_input=$('#current-'+id);
            hidden_input.get(0).value='allow';
        } );
        //$('.'+className).addClass('submit-green');
        break;
    case 'deny':
        $('.'+className).attr('class', className+' submit-red');
        $('.'+className).each( function(){
            var btn_id=$(this).attr('id');
            var id=btn_id.replace('btn-', '');
            var hidden_input=$('#current-'+id);
            hidden_input.get(0).value='deny';
        } );
        break;
    case 'neutral':
        $('.'+className).attr('class', className+' submit-gray');
        $('.'+className).each( function(){
            var btn_id=$(this).attr('id');
            var id=btn_id.replace('btn-', '');
            var hidden_input=$('#current-'+id);
            hidden_input.get(0).value='neutral';
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