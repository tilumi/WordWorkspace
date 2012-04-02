<?php
include('layout_admin/tpl_header.php');
include('layout_admin/helper.blocks.php');
list( $rows, $totalItems, $pageID, $pageRows, $form, $searchInfo ) = APP::$appBuffer;
?>

            <div class="grid_12">
                <?php echo Blocks::mainTitle( APP::$mainTitle ); ?>
                
<?php if( ACL::checkAuth('groups.index') ){ ?>
                <div class="float-right">
                    <a href="<?php echo url( 'groups/' ); ?>" class="button">
                    	<span><?php echo APP::$mainName; ?>群組 <img src="<?php echo layout_url('admin', '/images/icons/system-users-4.png'); ?>" alt="User Groups" width="12" height="12"></span>
                    </a>
                </div>
<?php } ?>
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
    'normal_user':'<?php echo url(array('action'=>'m_priv'));?>',
    'super_user':'<?php echo url(array('action'=>'m_priv'));?>',
};
</script>
                            <select class="input-medium" onchange="javascript: batch.operation(this.value, batchRoutes );">
                                <option value="" selected="selected">--- 選擇動作 ---</option>
<?php if( ACL::checkAuth('active') ){ ?>
                                <option value="active">啟用帳號</option>
                                <option value="inactive">停用帳號</option>
<?php } ?>
<?php if( ACL::checkAuth('super_user') ){ ?>
                                <option value="normal_user">設定為「管理員」層級</option>
                                <option value="super_user">設定為「開發者」層級</option>
<?php } ?>
<?php if( ACL::checkAuth('delete') ){ ?>
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
                                    <th class="header" style="">帳戶名稱</th>
                                    <th class="header" style="">人員名稱</th>
                                    <th class="header" style="">所屬群組</th>
                                    <th class="header" style="width: 50px;">啟用</th>
<?php if( ACL::checkAuth('super_user') ){ ?>
                                    <th class="header" style="width: 70px;">全域管理</th>
<?php } ?>
                                    <th class="header" style="width: 140px;">最後登入</th>
                                    <th style="width: 120px"></th>
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
                                        <?php echo $r['userid']; ?>
                                    </td>
                                    <td>
                                        <?php echo $r['username']; ?>
                                    </td>
                                    <td>
                                        <?php
                                        if( isset($dignities[ $r['id'] ]) ){
                                            echo $dignities[ $r['id'] ][0]['name'];
                                        }else{
                                            echo '<span style="color:#999;">(未指定)</span>';
                                        }
                                        ?>
                                    </td>
<?php if( ACL::checkAuth('active') ){ ?>
                                    <td><?php if( $r['is_active']=='1' ){ ?>
                                        <a href="<?php echo url('inactive/'.$r['id'].'.html'); ?>"><img src="<?php echo layout_url('admin', '/images/tick-circle.gif'); ?>" alt="已啟用" width="16" height="16"></a>
                                    <?php }else{ ?>
                                        <a href="<?php echo url('active/'.$r['id'].'.html'); ?>"><img src="<?php echo layout_url('admin', '/images/minus-circle.gif'); ?>" alt="已停用" width="16" height="16"></a>
                                    <?php } ?></td>
<?php }else{ ?>
                                    <td><?php if( $r['is_active']=='1' ){ ?>
                                        <img src="<?php echo layout_url('admin', '/images/tick-circle.gif'); ?>" alt="已啟用" width="16" height="16">
                                    <?php }else{ ?>
                                        <img src="<?php echo layout_url('admin', '/images/minus-circle.gif'); ?>" alt="已停用" width="16" height="16">
                                    <?php } ?></td>
<?php } ?>
<?php if( ACL::checkAuth('super_user') ){ ?>
                                    <td><?php if( $r['is_super_user']=='1' ){ ?>
                                        <a href="<?php echo url('normal_user/'.$r['id'].'.html'); ?>"><img src="<?php echo layout_url('admin', '/images/tick-on-white.gif'); ?>" alt="開發者" width="16" height="16"></a>
                                    <?php }else{ ?>
                                        <a href="<?php echo url('super_user/'.$r['id'].'.html'); ?>"><img src="<?php echo layout_url('admin', '/images/cross-on-white.gif'); ?>" alt="管理員" width="16" height="16"></a>
                                    <?php } ?></td>
<?php } ?>
                                    <td><?php
                                    $last_login=strtotime($r['last_login']);
                                    if( is_numeric($last_login) && $last_login!='0' ){
                                        echo date( 'Y-m-d H:i' , $last_login );
                                    }else{
                                        echo 'Never';
                                    }
                                    ?></td>
                                    <td>
<?php if( ACL::checkAuth('edit') ){ ?>
                                        <a href="<?php echo url('edit/'.$r['id'].'.html'); ?>" title="編輯"><img src="<?php echo layout_url('admin', '/images/icons/edit.png'); ?>" alt="edit" width="16" height="16"></a>
<?php } ?>
<?php if( ACL::checkAuth('privileges') ){ ?>
<?php       if( $r['is_super_user']==0 ){ //只有一般人員才能設定權限 ?>
                                        <a href="<?php echo url('privileges/'.$r['id'].'.html'); ?>" title="設定權限"><img src="<?php echo layout_url('admin', '/images/user.gif'); ?>" alt="privileges" width="16" height="16"></a>
<?php       } ?>
<?php } ?>
<?php if( ACL::checkAuth('dignity') ){ ?>
                                        <a href="<?php echo url('dignity/'.$r['id'].'.html'); ?>" title="設定管理員身分"><img src="<?php echo layout_url('admin', '/images/icons/system-users-4.png'); ?>" alt="view" width="16" height="16"></a>
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
<?php if( ACL::checkAuth('super_user') ){ ?>
                                    <td colspan="8" style="height:100px;line-height:100px;" class="align-center"> 尚無法提供任何資料 </td>
<?php }else{ ?>
                                    <td colspan="7" style="height:100px;line-height:100px;" class="align-center"> 尚無法提供任何資料 </td>
<?php } ?>
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