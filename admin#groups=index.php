<?php
include('layout_admin/tpl_header.php');
include('layout_admin/helper.blocks.php');
list( $rows, $totalItems, $pageID, $pageRows, $form, $searchInfo ) = APP::$appBuffer;
?>


            <div class="grid_12">
                <?php echo Blocks::mainTitle(APP::$mainTitle); ?>
                
                <div class="float-right">
<?php if( ACL::checkAuth('managers.index') ){ ?>
                    <a href="<?php echo View::url('/managers/'); ?>" class="button">
                    	<span>返回 系統管理員 <img src="<?php echo layout_url('admin', '/images/user.gif'); ?>" alt="New article" width="12" height="12"></span>
                    </a>
<?php } ?>
                </div>
            </div>

            <div class="grid_12">
                <?php echo redirect_message(); ?>
                
                <div class="float-right">
<?php if( ACL::checkAuth('add') ){ ?>
                    <!-- Button -->
                    <a href="<?php echo url('add.html'); ?>" class="button">
                    	<span>新增<?php echo APP::$mainName; ?> <img src="<?php echo layout_url('admin', '/images/plus-small.gif'); ?>" width="12" height="9"></span>
                    </a>
<?php } ?>
                </div>
                <div class="float-left">
                    <!-- Table records filtering -->
                    <a href="javascript: void(0);" rel="openSearch" class="button">
                    	<span>內容檢索 <img src="<?php echo layout_url('admin', '/images/icons/mail-find.png'); ?>" width="9" height="9"></span>
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
                	<h2><span><?php echo Blocks::searchInfo($searchInfo); //顯示列表的檢索範圍 ?></span></h2>
                    
                    <div class="module-table-body">
                    	<form name="frmList" action="<?php echo ME; ?>" method="post">
                    	<input name="mode" type="hidden" value="">
                        <div class="table-apply">
                            <?php echo Blocks::itemsChecker(); //顯示列表選擇器(全選、清除...) ?>
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
<?php if( ACL::checkAuth('m_edit') ){ ?>
                                <option value="active">顯示文章</option>
                                <option value="inactive">隱藏文章</option>
<?php } ?>
<?php if( ACL::checkAuth('m_delete') ){ ?>
                                <option value="delete">刪除</option>
<?php } ?>
                            </select>
                        </div>
                        <div class="pager" id="pager">
                            <div class="info"><?php echo Blocks::pageInfo($pageID, $pageRows, count($rows), $totalItems); ?></div>
                        </div>
                        <div style="clear: both;"></div>
                        <table class="">
                        	<thead>
                                <tr>
                                    <th class="header" style="width: 50px;">#</th>
                                    <th class="header" style="">名稱</th>
                                    <th class="header" style="">說明</th>
                                    <th class="header" style="width: 50px;">排序</th>
                                    <th class="header" style="width: 50px;">顯示</th>
                                    <th style="width: 100px;"></th>
                                </tr>
                            </thead>
                            <tbody>
<?php foreach($rows as $key=>$r){ ?>
                                <tr class="<?php echo ( ($key%2)==0 )?'even':'odd'; ?>">
                                    <td class="align-center">
                                    	<input name="items[]" value="<?php echo $r['id']; ?>" type="checkbox">
                                        <?php echo ($key+1); ?>.
                                    </td>
                                    <td>
                                        <?php echo $r['name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $r['info']; ?>
                                    </td>
                                    <td>
                                        <?php echo $r['sort']; ?>
                                    </td>
<?php if( ACL::checkAuth('active') ){ ?>
                                    <td><?php if( $r['is_active']=='1' ){ ?>
                                        <a href="<?php echo url('inactive/'.$r['id'].'.html'); ?>"><img src="<?php echo layout_url('admin', '/images/tick-circle.gif'); ?>" alt="顯示" width="16" height="16"></a>
                                    <?php }else{ ?>
                                        <a href="<?php echo url('active/'.$r['id'].'.html'); ?>"><img src="<?php echo layout_url('admin', '/images/minus-circle.gif'); ?>" alt="暫時隱藏" width="16" height="16"></a>
                                    <?php } ?></td>
<?php }else{ ?>
                                    <td><?php if( $r['is_active']=='1' ){ ?>
                                        <img src="<?php echo layout_url('admin', '/images/tick-circle.gif'); ?>" alt="已啟用" width="16" height="16">
                                    <?php }else{ ?>
                                        <img src="<?php echo layout_url('admin', '/images/minus-circle.gif'); ?>" alt="已停用" width="16" height="16">
                                    <?php } ?></td>
<?php } ?>
                                    <td>
<?php if( ACL::checkAuth('edit') ){ ?>
                                        <a href="<?php echo url('edit/'.$r['id'].'.html'); ?>" title="編輯"><img src="<?php echo layout_url('admin', '/images/icons/edit.png'); ?>" alt="edit" width="16" height="16"></a>
<?php } ?>
<?php if( ACL::checkAuth('privileges') ){ ?>
                                        <a href="<?php echo url('privileges/'.$r['id'].'.html'); ?>" title="設定權限"><img src="<?php echo layout_url('admin', '/images/user.gif'); ?>" alt="privileges" width="16" height="16"></a>
<?php } ?>
<?php if( ACL::checkAuth('view') ){ ?>
                                        <a href="<?php echo url('view/'.$r['id'].'.html'); ?>" title="檢視資訊及權限"><img src="<?php echo layout_url('admin', '/images/icons/application-view-list.png'); ?>" alt="view" width="16" height="16"></a>
<?php } ?>
<?php if( ACL::checkAuth('delete') ){ ?>
                                        <a href="<?php echo url('delete/'.$r['id'].'.html'); ?>" title="刪除"><img src="<?php echo layout_url('admin', '/images/bin.gif'); ?>" alt="delete" width="16" height="16"></a>
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
                
                     <div class="pagination">
                        <?php
                            echo Blocks::render( $pageID, $totalItems );
                        ?>
                        
                        <div style="clear: both;"></div> 
                     </div>
                

                
			</div> <!-- End .grid_12 -->
<?php
include('layout_admin/tpl_footer.php');
?>