<?php
include('layout_admin/tpl_header.php');
include('layout_admin/helper.blocks.php');
list( $privs_html ) = APP::$appBuffer;
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
<?php echo APP::$pageTitle; ?>
</p>

                <?php echo Blocks::mainTitle( APP::$pageTitle ); ?>

<?php echo redirect_message(); ?>



                <div class="module">
                	<h2><span>檢視<?php echo $mainName; ?></span></h2>
                    
                    <div class="module-body">
                        <h3><?php echo $data['name'];?></h3>
                        <div style="height:16px;line-height:16px;margin-bottom:30px;">
                            <div class="float-right">
                                <?php echo date('Y.n.j g:iA', strtotime($data['published'])); ?>
                            </div>
                            發佈者: <?php echo $data['author']; ?>
                        </div>
                        <div style="font-size:16px;">
                            <?php echo $data['article'];?>
                        </div>
                        <div class="grid_12" style="text-align:center;">
                            <input type="button" class="submit-green" value="回到上頁" onclick="javascript: location.href='<?php echo url('.'); ?>';" />
                        </div>
                        <div style="clear: both;"></div>
                    </div> <!-- End .module-body -->
                </div> <!-- End .module -->


<?php echo $privs_html; ?>
                


        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->


<?php
include('layout_admin/tpl_footer.php');
?>