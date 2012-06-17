<?php
include('layout_admin/tpl_header.php');
include('layout_admin/helper.blocks.php');
list( $rows, $totalItems, $pageID, $pageRows, $form, $searchInfo ) = APP::$appBuffer;
?>
            <div class="grid_12">
<p>
<?php echo View::anchor('/', '管理首頁'); ?>
 »
<?php echo APP::$mainTitle; ?>
</p>

                <?php echo Blocks::mainTitle( APP::$mainTitle ); ?>
            </div>

            <div class="grid_12">
                <?php echo redirect_message(); ?>
                
                <div class="float-right">
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
                        </div>
                        <div class="pager" id="pager">
                            <div class="info"><?php echo Blocks::pageInfo($pageID, $pageRows, count($rows), $totalItems); ?></div>
                        </div>
                        <div style="clear: both;"></div>
                        <table class="">
                        	<thead>
                                <tr>
                                    <th class="header" style="width: 50px;">#</th>
                                    <th class="header" style="width: 150px">時間日期</th>
                                    <th class="header" style="width: 70px">層級</th>
                                    <th class="header" style="width: 70px">類型</th>
                                    <th class="header" style="width: 120px">來源IP</th>
                                    <th class="header" style="">紀錄內容</th>
                                </tr>
                            </thead>
                            <tbody>
<?php foreach($rows as $key=>$r){ ?>
                                <tr class="<?php echo ( ($key%2)==0 )?'even':'odd'; ?>">
                                    <td class="item-index">
                                    	<input name="items[]" value="<?php echo $r['id']; ?>" type="checkbox">
                                        <?php echo ($pageID-1)*$pageRows + ($key+1); ?>.
                                    </td>
                                   <td><?php
                                    if( !empty($r['created']) && $r['created']!='0000-00-00 00:00:00' ){
                                        echo date( 'Y-m-d H:i' , strtotime($r['created']) );
                                    }else{
                                        echo 'Never';
                                    }
                                    ?></td>
                                    <td>
                                        <?php echo $r['prior']; ?>
                                    </td>
                                    <td>
                                        <?php echo $r['type']; ?>
                                    </td>
                                    <td>
                                        <?php echo $r['ip']; ?>
                                    </td>
                                    <td>
                                        <?php echo $r['name']; ?>
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