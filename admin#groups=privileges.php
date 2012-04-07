            <!-- Form elements -->    
            <div class="grid_12">
<p>
<?php echo View::anchor('..', 'Home'); ?>
 »
<?php echo View::anchor('.', $mainTitle); ?>
 »
權限設定
</p>

<script>
function changeStatus( btn ){
    var btn_id=btn.id;
    var priv_id=btn_id.replace('btn-', '');
    hidden_input=$('#priv-'+priv_id);
    current_status=hidden_input.get(0).value;
    switch(current_status){
    case 'allow':
        $(btn).removeClass('submit-green');
        $(btn).addClass('submit-red');
        hidden_input.get(0).value='deny';
        break;
    case 'omit':
        $(btn).removeClass('submit-gray');
        $(btn).addClass('submit-green');
        hidden_input.get(0).value='allow';
        break;
    case 'deny':
        $(btn).removeClass('submit-red');
        $(btn).addClass('submit-gray');
        hidden_input.get(0).value='omit';
        break;
    }
}
</script>


<?php echo redirect_message(); ?>

<?php //echo $formrender->getFormHtml($form, 'privileges'); ?>
<?php echo $form; ?>

        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->
                
