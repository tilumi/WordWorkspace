<?php
include('layout_admin/tpl_header.php');
include('layout_admin/helper.blocks.php');
list( $data , $subjects ) = APP::$appBuffer;
$mainTitle = APP::$mainTitle;
$mainName = APP::$mainName;
?>
            <!-- Form elements -->    
            <div class="grid_12">

<p>
<?php echo View::anchor('/', '管理首頁'); ?>
 »
<?php echo View::anchor('..', '禮拜主題 Subjects'); ?>
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
                                    <th class="header">年度</th>
                                    <th class="header"><?php echo APP::$mainName; ?>(中)</th>
                                    <th class="header"><?php echo APP::$mainName; ?>(韓)</th>
                                    <th class="header">最後更新</th>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo $data['year']; ?>
                                    </td>
                                    <td>
                                        <?php echo $data['name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $data['name_kr']; ?>
                                    </td>
                                    <td>
                                        <?php echo ($data['updated']!=='0000-00-00 00:00:00')? substr($data['updated'],0,16) :'(從未)'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="4">
                                        附註
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <?php echo $data['article']; ?>
                                        <?php if( empty($data['article']) ){ echo '（目前沒有內容）'; } ?>
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
                <div class="module">
                	<h2><span>各週主題經文</span></h2>
                    
                    <div class="module-table-body">
                        <table class="">
                        	<thead>
                                <tr>
                                    <th class="header" style="width: 170px">日期</th>
                                    <th class="header" style="width: 40px;">年</th>
                                    <th class="header" style="width: 30px;">週</th>
                                    <th class="header" style=""></th>
                                    <th style="width: 70px"></th>
                                </tr>
                            </thead>
                        	<tbody>
<?php
    for( $i=1;$i<=55;$i++ ){
        $key=$data['year'].'-'.$i;
        $lordday_ts=strtotime(Weekly::getDate($data['year'], $i));
        if( $i>50 && date('Y',$lordday_ts)!==$data['year'] ){ continue; }
        if( isset( $subjects[$key] ) ){
            $lordday=$subjects[$key]['LordDay'];
            $others=$subjects[$key]['others'];
            
            $json_verses=json_decode($lordday['verses']);
            //pr($verses);
            $_=array();
            foreach( $json_verses as $v ){
                $v_data=Subjects::parseVerseKey($v->key);
                
                //$url ='http://bible.jbride.cc';
                $url ='/'.$v_data['book_name'].'/'.$v_data['part2'].'.html';
                
                $_[] = '<a href="'.url('_'.$url).'" target="_blank">'.$v->name.'</a>';
                //$_[] = $v->name;
            }
?>
                                <tr>
                                    <th>
                                        <b><?php echo date('Y年n月j日', $lordday_ts); ?>
                                        主日</b>
                                    </th>
                                    <th>
                                        <?php echo $data['year']; ?>
                                    </th>
                                    <th>
                                        <?php echo $i; ?>
                                    </th>
                                    <th>
                                        
                                    </th>
                                    <th>
                                        
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <span style="font-weight:bold;font-size:24px;font-family:標楷體;">
                                            <?php echo $lordday['name']; ?>
                                        </span>
                                        <?php
                                        if( count($_) > 0 ){
                                            echo '<br>';
                                            echo '經文: ';
                                            echo implode(' , ', $_);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        
                                    </td>
                                </tr>
                                <?php
                                foreach( $others as $o ){
                                ?>
                                <tr class="odd">
                                    <td colspan="4">
                                <?php
                                    $padding = str_repeat('&nbsp; ', 4);
                                    echo $padding;
                                    echo $o['wtype_name'];
                                    $date_tpl='Y年n月j日';
                                    echo ' - ';
                                    echo '<b>';
                                    echo $o['name'];
                                    echo '</b>';
                                    echo ' ';
                                    echo '（'.date($date_tpl, strtotime($o['worshiped'])).'）';
                                    
                                    $json_verses=json_decode($o['verses']);
                                    //pr($verses);
                                    $_=array();
                                    foreach( $json_verses as $v ){
                                        $v_data=Subjects::parseVerseKey($v->key);
                                        
                                        //$url ='http://bible.jbride.cc';
                                        $url ='/'.$v_data['book_name'].'/'.$v_data['part2'].'.html';
                                        
                                        $_[] = '<a href="'.url('_'.$url).'" target="_blank">'.$v->name.'</a>';
                                        //$_[] = $v->name;
                                    }
                                    if( count($_) > 0 ){
                                        echo '<br>';
                                        echo $padding;
                                        //echo '經文: ';
                                        echo implode(' , ', $_);
                                    }
                                ?>
                                    </td>
                                    <td>
                                        
                                    </td>
                                </tr>
                                <?php
                                }
                                ?>
<?php
        }else{
?>
                                <tr>
                                    <th>
                                        <b><?php echo date('Y年n月j日', $lordday_ts); ?>
                                        主日</b>
                                    </th>
                                    <th>
                                        <?php echo $data['year']; ?>
                                    </th>
                                    <th>
                                        <?php echo $i; ?>
                                    </th>
                                    <th>
                                        
                                    </th>
                                    <th>
                                        
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        （沒有資料）
                                    </td>
                                    <td>
                                        
                                    </td>
                                </tr>
<?php
        }
?>
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

        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->
                
<?php
include('layout_admin/tpl_footer.php');
?>