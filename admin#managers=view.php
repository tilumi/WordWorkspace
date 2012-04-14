<?php
include('layout_admin/tpl_header.php');
include('layout_admin/helper.blocks.php');
list( $data, $privs_html ) = APP::$appBuffer;
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

            </div> <!-- End .grid_12 -->


            <div class="grid_5">
                <div class="module">
                	<h2><span>檢視<?php echo $mainName; ?></span></h2>
                    
                    <div class="module-table-body">
                        <table>
                            <tr>
                                <th style="width:100px;">帳號: </th>
                                <td>
                                    <?php echo $data['userid']; ?>
                                </td>
                            </tr>
                            <tr class="odd">
                                <th>用戶名稱: </th>
                                <td>
                                    <?php echo $data['username'];?>
                                </td>
                            </tr>
                            <tr>
                                <th>上次登入時間: </th>
                                <td>
                                    <?php
                                    if( $data['last_login']!=='0000-00-00 00:00:00' ){
                                        echo substr($data['last_login'], 0, 16);
                                    }else{
                                        echo '不曾登入';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr class="odd">
                                <th>上次登入位址: </th>
                                <td>
                                    <?php echo $data['last_login_ip'];?>
                                    <?php
                                    if( ! empty($data['last_login_ip']) ){
                                        echo $data['last_login_ip'];
                                    }else{
                                        echo '不曾登入';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>啟用狀態: </th>
                                <td>
                                    <?php if( $r['is_active']=='1' ){ ?>
                                        <img src="<?php echo layout_url('admin', '/images/tick-circle.gif'); ?>" alt="已啟用" width="16" height="16"> 啟用中
                                    <?php }else{ ?>
                                        <img src="<?php echo layout_url('admin', '/images/minus-circle.gif'); ?>" alt="已停用" width="16" height="16"> 已停用
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr class="odd">
                                <th>全域管理員: </th>
                                <td>
                                    <?php if( $data['is_super_user']=='1' ){ ?>
                                        <img src="<?php echo layout_url('admin', '/images/tick-on-white.gif'); ?>" alt="開發者" width="16" height="16"> 全域管理員
                                    <?php }else{ ?>
                                        <img src="<?php echo layout_url('admin', '/images/cross-on-white.gif'); ?>" alt="管理員" width="16" height="16"> 一般管理員
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th>建立日期: </th>
                                <td>
                                    <?php echo substr($data['created'], 0, 16);?>
                                </td>
                            </tr>
                            <tr class="odd">
                                <th>最後更新日期: </th>
                                <td>
                                    <?php
                                    if( $data['updated']!=='0000-00-00 00:00:00' ){
                                        echo substr($data['updated'], 0, 16);
                                    }else{
                                        echo '未曾更新';
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div> <!-- End .module-body -->
                </div> <!-- End .module -->
            </div> <!-- End .grid_5 -->

            <div class="grid_12" style="text-align:center;height:40px;">
<?php if( ACL::checkAuth('edit') ){ ?>
                <input type="button" class="submit-blue" value="編輯資訊" onclick="javascript: location.href='<?php echo url('./edit/'.$data['id'].'.html'); ?>';" />
<?php } ?>
<?php if( ACL::checkAuth('privileges') ){ ?>
                <input type="button" class="submit-blue" value="變更權限" onclick="javascript: location.href='<?php echo url('./privileges/'.$data['id'].'.html'); ?>';" />
<?php } ?>
                <input type="button" class="submit-green" value="回到上頁" onclick="javascript: location.href='<?php echo url('.'); ?>';" />
            </div> <!-- End .grid_12 -->


<?php if( ACL::checkAuth('view_privileges') ){ ?>

            <div class="grid_12">

<?php echo $privs_html; ?>

        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->

            <div class="grid_12" style="text-align:center;height:40px;">
<?php if( ACL::checkAuth('edit') ){ ?>
                <input type="button" class="submit-blue" value="編輯資訊" onclick="javascript: location.href='<?php echo url('./edit/'.$data['id'].'.html'); ?>';" />
<?php } ?>
<?php if( ACL::checkAuth('privileges') ){ ?>
                <input type="button" class="submit-blue" value="變更權限" onclick="javascript: location.href='<?php echo url('./privileges/'.$data['id'].'.html'); ?>';" />
<?php } ?>
                <input type="button" class="submit-green" value="回到上頁" onclick="javascript: location.href='<?php echo url('.'); ?>';" />
            </div> <!-- End .grid_12 -->

<?php } ?>


<?php
include('layout_admin/tpl_footer.php');
?>