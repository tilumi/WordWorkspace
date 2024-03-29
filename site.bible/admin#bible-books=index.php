<?php
include('layout_admin/tpl_header.php');
include('layout_admin/helper.blocks.php');
list( $rows, $totalItems, $pageID, $pageRows, $form, $searchInfo ) = APP::$appBuffer;
?>

            <div class="grid_12">
<p>
<?php echo View::anchor('/', '管理首頁'); ?>
 »
<?php echo View::anchor('..', '聖經維護 Bible'); ?>
 »
<?php echo APP::$mainTitle; ?>
</p>

                <?php echo Blocks::mainTitle( APP::$mainTitle ); ?>
            </div>

            <div class="grid_12">
                <?php echo redirect_message(); ?>
                
                <div class="float-right">
<?php if( ACL::checkAuth( 'add' ) ){ ?>
                    <!-- Button -->
                    <a href="<?php echo url( 'add.html' ); ?>" class="button">
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
    'active':'<?php echo url("m_edit.html");?>',
    'inactive':'<?php echo url("m_edit.html");?>',
    'delete':'<?php echo url("m_delete.html");?>',
};
</script>
                            <select class="input-medium" onchange="javascript: batch.operation(this.value, batchRoutes );">
                                <option value="" selected="selected">--- 選擇動作 ---</option>
<?php if( ACL::checkAuth( 'm_edit' ) ){ ?>
                                <option value="active">顯示文章</option>
                                <option value="inactive">隱藏文章</option>
<?php } ?>
<?php if( ACL::checkAuth( 'm_delete' ) ){ ?>
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
                                    <th class="header" style="width: 70px">新舊約</th>
                                    <th class="header" style="width: 70px"><?php echo APP::$mainName; ?>型別</th>
                                    <th class="header" style="width: 140px"><?php echo APP::$mainName; ?>名稱(中)</th>
                                    <th class="header" style="width: 140px"><?php echo APP::$mainName; ?>名稱(韓)</th>
                                    <th class="header" style="width: 140px"><?php echo APP::$mainName; ?>名稱(英)</th>
                                    <th class="header" style="">簡介</th>
                                    <th class="header" style="width: 140px">最後更新</th>
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
                                        <?php echo ($r['testament']==='OT')?'舊約':'新約'; ?>
                                    </td>
                                    <td>
                                        <?php echo $r['category_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $r['name']; ?> (<?php echo $r['short']; ?>)
                                    </td>
                                    <td>
                                        <?php echo $r['name_kr']; ?> (<?php echo $r['short_kr']; ?>)
                                    </td>
                                    <td>
                                        <?php echo $r['name_en']; ?> (<?php echo $r['short_en']; ?>)
                                    </td>
                                    <td>
                                        <?php echo mb_substr($r['info'], 0, 30); ?> ...
                                    </td>
                                    <td>
                                        <?php echo ($r['updated']!=='0000-00-00 00:00:00')? substr($r['updated'],0,16) :'(從未)'; ?>
                                    </td>
                                    <td>
<?php if( ACL::checkAuth( 'archives' ) ){ ?>
                                        <a href="<?php echo url('archives/'.$r['urn'].'.html'); ?>" title="檢視資訊"><img src="<?php echo layout_url('admin', '/images/icons/mail-find.png'); ?>" alt="檢視資訊" width="16" height="16"></a>
<?php } ?>
<?php if( ACL::checkAuth( 'edit' ) ){ ?>
                                        <a href="<?php echo url('edit/'.$r['urn'].'.html'); ?>" title="編輯"><img src="<?php echo layout_url('admin', '/images/icons/edit.png'); ?>" alt="編輯" width="16" height="16"></a>
<?php } ?>
<?php /*if( ACL::checkAuth( 'delete' ) ){ ?>
                                        <a href="<?php echo url('delete/'.$r['urn'].'.html'); ?>" title="刪除"><img src="<?php echo layout_url('admin', '/images/bin.gif'); ?>" alt="刪除" width="16" height="16"></a>
<?php }*/ ?>
<?php if( ACL::checkAuth( 'chapters' ) ){ ?>
                                        <a href="<?php echo url('chapters/'.$r['urn'].'.html'); ?>" title="卷章管理"><img src="<?php echo layout_url('admin', '/images/text-list-bullets-icon.png'); ?>" alt="卷章管理" width="16" height="16"></a>
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
                		<a href="" class="button"><span><img src="<?php echo layout_url('admin', '/images/arrow-stop-180-small.gif'); ?>" alt="First" width="12" height="9"> First</span></a> 
                        <a href="" class="button"><span><img src="<?php echo layout_url('admin', '/images/arrow-180-small.gif'); ?>" alt="Previous" width="12" height="9"> Prev</span></a>
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
                        <a href="" class="button"><span>Next <img src="<?php echo layout_url('admin', '/images/arrow-000-small.gif'); ?>" alt="Next" width="12" height="9"></span></a> 
                        <a href="" class="button last"><span>Last <img src="<?php echo layout_url('admin', '/images/arrow-stop-000-small.gif'); ?>" alt="Last" width="12" height="9"></span></a>
                        <div style="clear: both;"></div> 
                     </div>
-->                
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