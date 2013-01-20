<?php
include('layout_admin/tpl_header.php');
include('layout_admin/helper.blocks.php');
list( $data ) = APP::$appBuffer;
$mainTitle = APP::$mainTitle;
$mainName = APP::$mainName;
?>
            <!-- Form elements -->    
            <div class="grid_12">

<p>
<?php echo View::anchor('/', '管理首頁'); ?>
 »
<?php echo View::anchor('.', $mainTitle); ?>
 »
<?php echo APP::$pageTitle; ?>
</p>

                <?php echo Blocks::mainTitle( APP::$pageTitle ); ?>

<?php echo redirect_message(); ?>

<?php
function showColumn( $data , $type='text' ){
    $default='(None)';
    
    switch( $type ){
        case 'image':
            if( empty($data) ){ return $default; }
            return '<div style="display:block;height:128px;width:128px;"></div>';
            break;
        case 'sexual':
            if( empty($data) || !in_array($data, array('male', 'female') ) ){ return $default; }
            if( $data=='male' ){ return '男'; }
            if( $data=='female' ){ return '女'; }
            break;
        case 'military':
            if( empty($data) || !in_array($data, array(0,1,2,3,4) ) ){ return $default; }
            $military=array(
                '0'=>'不適用',
                '1'=>'服畢兵役',
                '2'=>'服役中',
                '3'=>'尚未服役',
                '4'=>'免役',
            );
            return $military[$data];
            break;
        case 'date':
            if( empty($data) || $data=='0000-00-00' || $data=='0000-00-00 00:00:00' ){ return $default; }
            return date('Y年n月j日', strtotime($data));
            break;
        case 'datetime':
            if( empty($data) || $data=='0000-00-00' || $data=='0000-00-00 00:00:00' ){ return $default; }
            return date('Y年n月j日 G:i', strtotime($data));
            break;
        case 'text':
        default:
            if( empty($data) ){ return $default; }
            return $data;
    }
}
?>
                <div class="module">
                	<h2><span>檢視<?php echo $mainName; ?></span></h2>
                    
                    <div class="module-table-body">
                        <table class="">
                        	<tbody>
                                <tr>
                                    <th class="header"><?php echo APP::$mainName; ?>名稱</th>
                                    <th class="header">標準號</th>
                                    <th class="header">注音索引</th>
                                    <th class="header">漢語索引</th>
                                    <th class="header">標準KEY</th>
<?php if( ACL::checkAuth( 'active' ) ){ ?>
                                    <th class="header">顯示狀態</th>
<?php } ?>
                                    <th class="header">最後更新</th>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo $data['name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $data['std_id']; ?>
                                    </td>
                                    <td>
                                        <?php echo $data['mps_key']; ?>
                                    </td>
                                    <td>
                                        <?php echo $data['hanyu_key']; ?>
                                    </td>
                                    <td>
                                        <?php echo $data['play_key']; ?>
                                    </td>
<?php if( ACL::checkAuth( 'active' ) ){ ?>
                                    <td>
                                    <?php if( $data['is_active']=='1' ){ ?>
                                        <a href="<?php echo url('inactive/'.$r['id'].'.html'); ?>"><img src="<?php echo layout_url('admin', '/images/tick-circle.gif'); ?>" alt="直接顯示" width="16" height="16"></a>
                                    <?php }else{ ?>
                                        <a href="<?php echo url('active/'.$r['id'].'.html'); ?>"><img src="<?php echo layout_url('admin', '/images/minus-circle.gif'); ?>" alt="暫時隱藏" width="16" height="16"></a>
                                    <?php } ?>
                                    </td>
<?php }else{ ?>
                                    <td>
                                    <?php if( $data['is_active']=='1' ){ ?>
                                        <img src="<?php echo layout_url('admin', '/images/tick-circle.gif'); ?>" alt="已啟用" width="16" height="16">
                                    <?php }else{ ?>
                                        <img src="<?php echo layout_url('admin', '/images/minus-circle.gif'); ?>" alt="已停用" width="16" height="16">
                                    <?php } ?>
                                    </td>
<?php } ?>
                                    <td>
                                        <?php echo ($data['updated']!=='0000-00-00 00:00:00')? substr($data['updated'],0,16) :'(從未)'; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="grid_12" style="text-align:center;margin-bottom:10px;">
                            <input type="button" class="submit-green" value="回到上頁" onclick="javascript: location.href='<?php echo url('.'); ?>';" />
                            <input type="button" class="submit-blue" value="編輯「<?php echo $data['name']; ?>」" onclick="javascript: location.href='<?php echo url('./edit/'.$data['id'].'.html'); ?>';" />
                        </div>
                    </div> <!-- End .module-body -->
                </div> <!-- End .module -->

            </div> <!-- End .grid_12 -->
<?php
    $key='zh-tw';
    $lyrics[$key]=array();
    if( isset($data['lyrics'][$key]) ){
        $lyrics[$key]=$data['lyrics'][$key];
        unset($data['lyrics'][$key]);
    }
    $key='kr';
    $lyrics[$key]=array();
    if( isset($data['lyrics'][$key]) ){
        $lyrics[$key]=$data['lyrics'][$key];
        unset($data['lyrics'][$key]);
    }
    foreach( $lyrics as $r ){
        $articles=preg_split('/$\R?^/m', $r['article']);
?>
            <div class="grid_6">
                <div class="module">
                	<h2><span><?php echo $r['lang_name']; ?>歌詞</span></h2>
                    <div class="module-table-body">
                        <table style="font-size:16px;line-height:24px;">
                        	<tbody>
                                <tr>
                                    <th><?php echo $r['name']; ?></th>
                                </tr>
<?php
        $key=0;
        foreach( $articles as $row ){
            $key+=1;
?>
                                <tr class="<?php echo ( ($key%2)==0 )?'even':'odd'; ?>">
                                    <td><pre style="margin:0;"><?php echo $row; ?></pre></td>
                                </tr>
<?php
        }
?>
                            </tbody>
                        </table>
                        <div class="grid_12" style="text-align:center;margin-bottom:10px;">
                            <input type="button" class="submit-green" value="回到上頁" onclick="javascript: location.href='<?php echo url('.'); ?>';" />
                            <input type="button" class="submit-blue" value="編輯「<?php echo $data['name']; ?>」" onclick="javascript: location.href='<?php echo url('./edit/'.$data['id'].'.html'); ?>';" />
                        </div>
                    </div> <!-- End .module-body -->
                </div> <!-- End .module -->
            </div> <!-- End .grid_6 -->
<?php
    }
?>

            <div class="grid_12">

<?php
    foreach( $data['lyrics'] as $r ){
        $articles=preg_split('/$\R?^/m', $r['article']);
?>
                <div class="module">
                	<h2><span><?php echo $r['lang_name']; ?>歌詞</span></h2>
                    <div class="module-table-body">
                        <table style="font-size:16px;line-height:24px;">
                        	<tbody>
                                <tr>
                                    <th><?php echo $r['name']; ?></th>
                                </tr>
<?php
        $key=0;
        foreach( $articles as $row ){
            $key+=1;
?>
                                <tr class="<?php echo ( ($key%2)==0 )?'even':'odd'; ?>">
                                    <td><pre style="margin:0;"><?php echo $row; ?></pre></td>
                                </tr>
<?php
        }
?>
                            </tbody>
                        </table>
                        <div class="grid_12" style="text-align:center;margin-bottom:10px;">
                            <input type="button" class="submit-green" value="回到上頁" onclick="javascript: location.href='<?php echo url('.'); ?>';" />
                            <input type="button" class="submit-blue" value="編輯「<?php echo $data['name']; ?>」" onclick="javascript: location.href='<?php echo url('./edit/'.$data['id'].'.html'); ?>';" />
                        </div>
                    </div> <!-- End .module-body -->
                </div> <!-- End .module -->
<?php
    }
?>

        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->
                
<?php
include('layout_admin/tpl_footer.php');
?>