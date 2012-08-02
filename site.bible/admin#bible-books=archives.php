<?php
include('layout_admin/tpl_header.php');
include('layout_admin/helper.blocks.php');
list( $data , $chapters ) = APP::$appBuffer;
$mainTitle = APP::$mainTitle;
$mainName = APP::$mainName;
?>
            <!-- Form elements -->    
            <div class="grid_12">

<p>
<?php echo View::anchor('/', '管理首頁'); ?>
 »
<?php echo View::anchor('..', '聖經維護 Bible'); ?>
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
                    	<form name="frmList" action="<?php echo ME; ?>" method="post">
                    	<input name="mode" type="hidden" value="">
                        <table class="">
                        	<tbody>
                                <tr>
                                    <th class="header">新舊約</th>
                                    <th class="header"><?php echo APP::$mainName; ?>型別</th>
                                    <th class="header"><?php echo APP::$mainName; ?>名稱(中)</th>
                                    <th class="header"><?php echo APP::$mainName; ?>簡稱(中)</th>
                                    <th class="header"><?php echo APP::$mainName; ?>名稱(韓)</th>
                                    <th class="header"><?php echo APP::$mainName; ?>簡稱(韓)</th>
                                    <th class="header"><?php echo APP::$mainName; ?>名稱(英)</th>
                                    <th class="header"><?php echo APP::$mainName; ?>簡稱(英)</th>
                                    <th class="header">最後更新</th>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo ($data['testament']==='OT')?'舊約':'新約'; ?>
                                    </td>
                                    <td>
                                        <?php echo $data['category_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $data['name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $data['short']; ?>
                                    </td>
                                    <td>
                                        <?php echo $data['name_kr']; ?>
                                    </td>
                                    <td>
                                        <?php echo $data['short_kr']; ?>
                                    </td>
                                    <td>
                                        <?php echo $data['name_en']; ?>
                                    </td>
                                    <td>
                                        <?php echo $data['short_en']; ?>
                                    </td>
                                    <td>
                                        <?php echo ($data['updated']!=='0000-00-00 00:00:00')? substr($data['updated'],0,16) :'(從未)'; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="">
                        	<tbody>
                                <tr>
                                    <th class="header" colspan="3" style="width:50%;">簡介</th>
                                    <th class="header" colspan="3" style="width:50%;">摘要</th>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-size:15px;">
                                        <?php echo $data['info_html']; ?>
                                    </td>
                                    <td colspan="3" style="font-size:15px;">
                                        <?php echo $data['summary_html']; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="grid_12" style="text-align:center;margin-bottom:10px;">
                            <input type="button" class="submit-green" value="回到上頁" onclick="javascript: location.href='<?php echo url('.'); ?>';" />
                            <input type="button" class="submit-blue" value="編輯「<?php echo $data['name']; ?>」" onclick="javascript: location.href='<?php echo url('./edit/'.$data['urn'].'.html'); ?>';" />
                        </div>
                        <table style="font-size:16px;line-height:24px;">
                        	<tbody>
<?php
    $chaps = $chapters;
    $max=$data['max_chapter'];
    $blockChaps=10;
    $columsNum=3;
    
    $rows = ceil( $max / ($blockChaps*$columsNum) ); //將要產生的區塊數，10章1區
    $blocks = array();
    $chap=pos($chaps);
    for( $i=1;$i<=$rows;$i++ ){
        echo "<tr>";
        for( $k=1;$k<=$columsNum;$k++ ){
            $index = ($i-1)*$columsNum + $k;
            $ch_start=( ($index-1)*$blockChaps + 1 );
            $ch_end=( $index*$blockChaps < $max ) ? ($index*$blockChaps) : $max;
            echo '<th style="width:33%;">';
            if( $ch_start <= $max ){
                echo $ch_start.' ~ '.$ch_end;
            }
            echo '</th>';
        }
        echo "</tr>\n";
        echo "<tr>";
        for( $j=1;$j<=$columsNum;$j++ ){
            $html='';
            $index = ($i-1)*$columsNum + $j;
            $ch_start=( ($index-1)*$blockChaps );
            $items = 0;
            
            $html.='<ul>'."\n";
            $chap=$chaps[ $ch_start+$items ];
            while( $chap && ($items < $blockChaps) ){
                $name='';
                if( ! empty($chap['name']) ){ $name = ' &nbsp; '.$chap['name']; }
                
                $unit='章';
                if( $chap['book_id']==19 ){ $unit='篇'; }
                
                $html.='    <li style="line-height:16px;">第 '.$chap['chapter_id'].' '.$unit.$name.'</li>'."\n";
                
                $items += 1;
                $chap=$chaps[ $ch_start+$items ];
            }
            $html.='</ul>'."\n";
            
            echo '<td>'."\n";
            echo $html;
            echo '</td>'."\n";
        }
        echo "</tr>";
    }
?>
                            </tbody>
                        </table>
                        <div class="grid_12" style="text-align:center;margin-bottom:10px;">
                            <input type="button" class="submit-green" value="回到上頁" onclick="javascript: location.href='<?php echo url('.'); ?>';" />
                            <input type="button" class="submit-blue" value="「<?php echo $data['name']; ?>」卷章管理" onclick="javascript: location.href='<?php echo url('./chapters/'.$data['urn'].'.html'); ?>';" />
                        </div>
                    </div> <!-- End .module-body -->
                </div> <!-- End .module -->

        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->
                
<?php
include('layout_admin/tpl_footer.php');
?>