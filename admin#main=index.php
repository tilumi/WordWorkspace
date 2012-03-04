<?php
include('layout_admin/tpl_header.php');
?>

            <div class="grid_12">
                <?php //echo redirect_message(); ?>
            </div>
            <!-- Dashboard icons -->
            <div class="grid_7">
            	<a href="<?php echo url( '/news/' ); ?>" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/apps/view-pim-news.png'); ?>" alt="edit" width="64" height="64">
                	<span>新聞中心</span>
                </a>
                
<!--
            	<a href="<?php echo url( array('plugin'=>'products') ); ?>" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/apps/packing-icon.png'); ?>" alt="edit" width="64" height="64">
                	<span>商品管理</span>
                </a>
                
            	<a href="<?php echo url( array('plugin'=>'essences') ); ?>" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/apps/Search-Images-icon.png'); ?>" alt="edit" width="64" height="64">
                	<span>作品精選</span>
                </a>
                
            	<a href="<?php echo url( array('plugin'=>'orders') ); ?>" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/apps/ordering-icon.png'); ?>" alt="edit" width="64" height="64">
                	<span>訂單管理</span>
                </a>
                
            	<a href="<?php echo url( array('plugin'=>'albums') ); ?>" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/apps/pictures-icon.png'); ?>" alt="edit" width="64" height="64">
                	<span>相簿管理</span>
                </a>

            	<a href="<?php echo url( array('plugin'=>'musics') ); ?>" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/apps/folder-music-icon-64.png'); ?>" alt="edit" width="64" height="64">
                	<span>歌曲管理</span>
                </a>

            	<a href="<?php echo url( array('plugin'=>'downloads') ); ?>" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/apps/Globe-Download-icon.png'); ?>" alt="edit" width="64" height="64">
                	<span>檔案下載</span>
                </a>

            	<a href="<?php echo url( array('plugin'=>'comments') ); ?>" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/apps/select-language-icon.png'); ?>" alt="edit" width="64" height="64">
                	<span>留言管理</span>
                </a>

            	<a href="<?php echo url( '/' ); ?>" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/apps/product-icon.png'); ?>" alt="edit" width="64" height="64">
                	<span>條碼系統</span>
                </a>
                
            	<a href="<?php echo url( array('plugin'=>'songs', 'action'=>'archives') ); ?>" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/apps/folder-music-icon-64.png'); ?>" alt="edit" width="64" height="64">
                	<span>歌曲管理</span>
                </a>
-->
            	<a href="<?php echo url( 'chpasswd.html' ); ?>" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/apps/preferences-desktop-user-password.png'); ?>" alt="edit" width="64" height="64">
                	<span>變更密碼</span>
                </a>
                
            	<a href="<?php echo url( '/managers/' ); ?>" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/Crystal_Clear_user.gif'); ?>" alt="edit" width="64" height="64">
                	<span>系統管理員</span>
                </a>
                
<!--
            	<a href="" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/Crystal_Clear_write.gif'); ?>" alt="edit" width="64" height="64">
                	<span>New article</span>
                </a>
                
                <a href="" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/Crystal_Clear_file.gif'); ?>" alt="edit" width="64" height="64">
                	<span>Upload file</span>
                </a>
                
                <a href="" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/Crystal_Clear_files.gif'); ?>" alt="edit" width="64" height="64">
                	<span>Articles</span>
                </a>
                
                <a href="" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/Crystal_Clear_calendar.gif'); ?>" alt="edit" width="64" height="64">
                	<span>Calendar</span>
                </a>
                
                <a href="" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/Crystal_Clear_user.gif'); ?>" alt="edit" width="64" height="64">
                	<span>My profile</span>
                </a>
                
                <a href="" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/Crystal_Clear_stats.gif'); ?>" alt="edit" width="64" height="64">
                	<span>Stats</span>
                </a>
                
                <a href="" class="dashboard-module">
                	<img src="<?php echo layout_url( APP::$prefix, '/images/Crystal_Clear_settings.gif'); ?>" alt="edit" width="64" height="64">
                	<span>Settings</span>
                </a>
-->
                <div style="clear: both;"></div>
            </div> <!-- End .grid_7 -->
            
            <!-- Account overview -->
            <div class="grid_5">
                <div class="module">
                        <h2><span>使用者資訊</span></h2>
                        
                        <div class="module-table-body">
                            <table>
                                <tr>
                                    <th style="width:100px;">名稱: </th>
                                    <td>
                                        <?php echo $_SESSION['administrator']['username']; ?>
                                        <a style="float:right;" href="<?php echo url( array('plugin'=>'main','action'=>'userinfo') );?>">設定我的名稱</a>
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <th>帳號: </th>
                                    <td>
                                        <?php echo $_SESSION['administrator']['userid'];?>
                                        <a style="float:right;" href="<?php echo url( array('plugin'=>'main','action'=>'change_password') );?>">變更密碼</a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>最後登入時間: </th>
                                    <td>
                                    <?php
                                    $last_login=$_SESSION['administrator']['last_login'];
                                    $content = 'Never';
                                    if( $last_login!='0000-00-00 00:00:00' ){
                                        $wday=array('日','一','二','三','四','五','六');
                                        $content = '星期'.$wday[date('w')].' ';
                                        $content.= date('n/j, g:iA, Y', strtotime($last_login) );
                                    }
                                    echo $content;
                                    ?>
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <th>最後登入位址: </th>
                                    <td>
                                    <?php
                                    $last_login_ip=$_SESSION['administrator']['last_login_ip'];
                                    $content = 'No Data';
                                    if( !empty($last_login_ip) ){
                                        $content = $last_login_ip;
                                    }
                                    echo $content;
                                    ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                </div>
                <div class="module">
                        <h2><span>您的最近<?php echo count($logs);?>筆帳號動態</span></h2>
                        
                        <div class="module-table-body">
                            <table>
<?php 
        $i=0;
        foreach( $logs as $log ){
            $i+=1; 
            $time = date('n/j g:i', strtotime( $log['created'] ) );
?>
                                <tr class="<?php echo ($i%2)?'even':'odd';?>">
                                    <td><?php echo $log['ip'];?></td>
                                    <td><?php echo $log['name'];?></td>
                                    <td align="right"><?php echo $time;?></td>
                                </tr>

<?php
        }
?>
                                <tr>
                                    <td colspan="3"><a class="float-right" href="<?php echo url(array('plugin'=>'syslog')); ?>">更多動態 ...</a></td>
                                </tr>
                            </table>
<!--
                             <div>
                                 <div class="indicator">
                                     <div style="width: 23%;"></div>
                                 </div>
                                 <p>Your storage space: 23 MB out of 100MB</p>
                             </div>
                             
                             <div>
                                 <div class="indicator">
                                     <div style="width: 100%;"></div>
                                 </div>
                                 <p>Your bandwidth (January): 1 GB out of 1 GB</p>
                             </div>
                        	<p>
                                Need to switch to a bigger plan?<br>
                                <a href="">click here</a><br>
                            </p>
-->

                        </div>
                </div>
                <div style="clear: both;"></div>
            </div> <!-- End .grid_5 -->


<?php
include('layout_admin/tpl_footer.php');
?>