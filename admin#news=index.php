<?php
include('layout_admin/tpl_header.php');
?>

            <div class="grid_12">
                <?php echo $pager->mainTitle($mainTitle); ?>
            </div>

            <div class="grid_12">
                <?php echo redirect_message(); ?>
                
                <div class="float-right">
<?php if( Region::checkAuth( array('action'=>'add') ) ){ ?>
                    <!-- Button -->
                    <a href="<?php echo View::url( array('action'=>'add') ); ?>" class="button">
                    	<span>新增<?php echo $mainName; ?> <img src="<?php echo View::image_url('plus-small.gif'); ?>" width="12" height="9"></span>
                    </a>
<?php } ?>
                </div>
                <div class="float-left">
                    <!-- Table records filtering -->
                    <a href="javascript: void(0);" rel="openSearch" class="button">
                    	<span>內容檢索 <img src="<?php echo View::image_url('icons/mail-find.png'); ?>" width="9" height="9"></span>
                    </a>
                </div>
                <div id="openSearch" style="display:none;">
                    <?php //echo $formrender->getFormHtml($form , 'search'); ?>
                    <?php echo $form; ?>
            		<div style="clear: both;"></div>
                </div>
            </div>
            <div class="grid_12">
                
                <!-- Example table -->
                <div class="module">
                	<h2><span><?php echo $pager->searchInfo($searchInfo); //顯示列表的檢索範圍 ?></span></h2>
                    
                    <div class="module-table-body">
                    	<form name="frmList" action="<?php echo ME; ?>" method="post">
                    	<input name="mode" type="hidden" value="">
                        <div class="table-apply">
                            <?php echo $pager->itemsChecker(); //顯示列表選擇器(全選、清除...) ?>
                            &nbsp;
                            <span>選取操作:</span> 
<script>
var batchRoutes = {
    'active':'<?php echo url(array('action'=>'m_edit'));?>',
    'inactive':'<?php echo url(array('action'=>'m_edit'));?>',
    'delete':'<?php echo url(array('action'=>'m_delete'));?>',
};
</script>
                            <select class="input-medium" onchange="javascript: batch.operation(this.value, batchRoutes );">
                                <option value="" selected="selected">--- 選擇動作 ---</option>
<?php if( Region::checkAuth( array('action'=>'m_edit') ) ){ ?>
                                <option value="active">顯示文章</option>
                                <option value="inactive">隱藏文章</option>
<?php } ?>
<?php if( Region::checkAuth( array('action'=>'m_delete') ) ){ ?>
                                <option value="delete">刪除</option>
<?php } ?>
                            </select>
                        </div>
                        <div class="pager" id="pager">
                            <div class="info"><?php echo $pager->pageInfo($pageID, $pageRows, count($rows), $totalItems); ?></div>
                        </div>
                        <div style="clear: both;"></div>
                        <table class="">
                        	<thead>
                                <tr>
                                    <th class="header" style="width: 50px;">#</th>
                                    <th class="header" style="">標題</th>
                                    <th class="header" style="width: 50px;">顯示</th>
                                    <th class="header" style="width: 120px;">作者</th>
                                    <th class="header" style="width: 170px">發佈日期</th>
                                    <th style="width: 70px"></th>
                                </tr>
                            </thead>
                            <tbody>
<?php foreach($rows as $key=>$r){ ?>
                                <tr class="<?php echo ( ($key%2)==0 )?'even':'odd'; ?>">
                                    <td class="item-index">
                                    	<input name="items[]" value="<?php echo $r['id']; ?>" type="checkbox">
                                        <?php echo ($pageID-1)*$pageRows + ($key+1); ?>.
                                    </td>
                                    <td>
                                        <?php echo $r['name']; ?>
                                    </td>
<?php if( Region::checkAuth( array('action'=>'active') ) ){ ?>
                                    <td><?php if( $r['is_active']=='1' ){ ?>
                                        <a href="<?php echo View::url( array('action'=>'inactive', 'params'=>array($r['id'])) ); ?>"><img src="<?php echo View::image_url('tick-circle.gif'); ?>" alt="直接顯示" width="16" height="16"></a>
                                    <?php }else{ ?>
                                        <a href="<?php echo View::url( array('action'=>'active', 'params'=>array($r['id'])) ); ?>"><img src="<?php echo View::image_url('minus-circle.gif'); ?>" alt="暫時隱藏" width="16" height="16"></a>
                                    <?php } ?></td>
<?php }else{ ?>
                                    <td><?php if( $r['is_active']=='1' ){ ?>
                                        <img src="<?php echo View::image_url('tick-circle.gif'); ?>" alt="已啟用" width="16" height="16">
                                    <?php }else{ ?>
                                        <img src="<?php echo View::image_url('minus-circle.gif'); ?>" alt="已停用" width="16" height="16">
                                    <?php } ?></td>
<?php } ?>
                                    <td>
                                        <?php echo $r['author']; ?>
                                    </td>
                                   <td><?php
                                    $now=mktime();
                                    $opened_time=strtotime($r['published']);
                                    if( $now < $opened_time ){
                                        echo '<a href="javascript: void(0);" title="尚未發佈"><img src="'.View::image_url('notification-slash.gif').'" alt="尚未發佈" width="16" height="16"></a> ';
                                    }
                                    if( $now >= $opened_time ){
                                        echo '<a href="javascript: void(0);" title="已發布"><img src="'.View::image_url('tick-on-white.gif').'" alt="已發布" width="16" height="16"></a> ';
                                    }
                                    if( !empty($r['published']) && $r['published']!='0000-00-00 00:00:00' ){
                                        echo date( 'Y-m-d H:i' , strtotime($r['published']) );
                                    }else{
                                        echo 'Never';
                                    }
                                    ?></td>
                                    <td>
<?php if( Region::checkAuth( array('action'=>'archives') ) ){ ?>
                                        <a href="<?php echo View::url( array('action'=>$r['id']) ); ?>" title="檢視資訊"><img src="<?php echo View::image_url('icons/mail-find.png'); ?>" alt="檢視資訊" width="16" height="16"></a>
<?php } ?>
<?php if( Region::checkAuth( array('action'=>'edit') ) ){ ?>
                                        <a href="<?php echo View::url( array('action'=>'edit', 'params'=>array($r['id'])) ); ?>" title="編輯"><img src="<?php echo View::image_url('icons/edit.png'); ?>" alt="編輯" width="16" height="16"></a>
<?php } ?>
<?php if( Region::checkAuth( array('action'=>'delete') ) ){ ?>
                                        <a href="<?php echo View::url( array('action'=>'delete', 'params'=>array($r['id'])) ); ?>" title="刪除"><img src="<?php echo View::image_url('bin.gif'); ?>" alt="刪除" width="16" height="16"></a>
<?php } ?>
                                    </td>
                                </tr>
<?php } ?>
<?php if( count($rows)<1 ){ ?>
                                <tr class="even">
                                    <td colspan="6" style="height:100px;line-height:100px;" class="align-center"> 尚無法提供任何資料 </td>
                                </tr>
<?php } ?>
                            </tbody>
                        </table>
                        </form>
                     </div> <!-- End .module-table-body -->
                </div> <!-- End .module -->
                
<!--
                     <div class="pagination">           
                		<a href="" class="button"><span><img src="<?php echo View::image_url('arrow-stop-180-small.gif'); ?>" alt="First" width="12" height="9"> First</span></a> 
                        <a href="" class="button"><span><img src="<?php echo View::image_url('arrow-180-small.gif'); ?>" alt="Previous" width="12" height="9"> Prev</span></a>
                        <div class="numbers">
                            <span>Page:</span> 
                            <a href="">1</a> 
                            <span>|</span> 
                            <a href="">2</a> 
                            <span>|</span> 
                            <span class="current">3</span> 
                            <span>|</span> 
                            <a href="">4</a> 
                            <span>|</span> 
                            <a href="">5</a> 
                            <span>|</span> 
                            <a href="">6</a> 
                            <span>|</span> 
                            <a href="">7</a> 
                            <span>|</span> 
                            <span>...</span> 
                            <span>|</span> 
                            <a href="">99</a>
                        </div> 
                        <a href="" class="button"><span>Next <img src="<?php echo View::image_url('arrow-000-small.gif'); ?>" alt="Next" width="12" height="9"></span></a> 
                        <a href="" class="button last"><span>Last <img src="<?php echo View::image_url('arrow-stop-000-small.gif'); ?>" alt="Last" width="12" height="9"></span></a>
                        <div style="clear: both;"></div> 
                     </div>
-->                
                     <div class="pagination">
                        <?php
                            echo $pager->render( $pageID, $totalItems );
                        ?>
                        
                        <div style="clear: both;"></div> 
                     </div>
                

                
			</div> <!-- End .grid_12 -->


<?php
include('layout_admin/tpl_footer.php');
?>