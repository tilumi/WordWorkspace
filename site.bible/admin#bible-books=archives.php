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
                            <input type="button" class="submit-blue" value="編輯<?php echo APP::$mainName; ?>" onclick="javascript: location.href='<?php echo url('./edit/'.$data['urn'].'.html'); ?>';" />
                        </div>
                    </div> <!-- End .module-body -->
                </div> <!-- End .module -->

        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->
                
<?php
include('layout_admin/tpl_footer.php');
?>