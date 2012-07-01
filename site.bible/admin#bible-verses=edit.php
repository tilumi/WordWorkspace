<?php
include('layout_admin/tpl_header.php');
include('layout_admin/helper.blocks.php');
list( $form , $data , $verses ) = APP::$appBuffer;
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

<?php //echo $formrender->getFormHtml($form); ?>
<script type="text/javascript">
//<![CDATA[
function validate_frmUpdate(frm) {
  var value = '';
  var errFlag = new Array();
  var _qfGroups = {};
  _qfMsg = '';

  value = frm.elements['name'].value;
  if (value == '' && !errFlag['name']) {
    errFlag['name'] = true;
    _qfMsg = _qfMsg + '\n - 標題 必填';
  }

  value = frm.elements['name'].value;
  if (value != '' && value.length > 255 && !errFlag['name']) {
    errFlag['name'] = true;
    _qfMsg = _qfMsg + '\n - 標題至多255個字';
  }

  if (_qfMsg != '') {
    _qfMsg = '以下欄位輸入有誤\n' + _qfMsg;
    _qfMsg = _qfMsg + '\n\n請更正以上欄位';
    alert(_qfMsg);
    return false;
  }
  return true;
}
//]]>
</script>


                <form action="/bride3/site.bible/administrator/bible/verses/edit/01:001.html" method="post" name="frmUpdate" id="frmUpdate" onsubmit="try { var myValidator = validate_frmUpdate; } catch(e) { return true; } return myValidator(this);">
                <input name="id" type="hidden" value="01:001" />
                <div class="module">
                    <h2><span>本章資訊</span></h2>
                    <div class="module-table-body">
                        <table>
                        <tr style="border-bottom:1px solid #ccc;border-top:1px solid #ccc;">
                            <td style="vertical-align:top;width:150px;">
                            <span style="color: #ff0000">*</span>
                            章節標題(中)
                            </td>
                            <td>
                            <input class="input-medium" name="name" type="text" value="神創造天地" />
                            </td>
                        </tr>
                        <tr style="border-bottom:1px solid #ccc;border-top:1px solid #ccc;">
                            <td style="vertical-align:top;">
                            最大節數
                            </td>
                            <td>
                            <input class="input-short" name="max_verse" type="text" value="0" />
                            </td>
                        </tr>
                        </table>
                    </div>
                </div>
                <div class="module">
                    <h2><span>章節內文維護</span></h2>
                    <div class="module-table-body">
                        <table>
                        <tr style="border-bottom:1px solid #ccc;border-top:1px solid #ccc;">
                            <td style="vertical-align:top;">
                            小標
                            </td>
                            <td>
                            <input class="input-medium" name="name[01:001:001a]" type="text" value="　神的創造" />
                            關連經文
                            <input class="input-short" name="relative[01:001:001a]" type="text" />
                            </td>
                        </tr>
                        <tr style="border-bottom:1px solid #ccc;border-top:1px solid #ccc;">
                            <td style="vertical-align:top;">
                            <?php echo $data['book_name']; ?> 1:1
                            </td>
                            <td>
                            <input class="input-long" name="name[01:001:001g]" type="text" value="起初，　神創造天地。" />
                            </td>
                        </tr>
                        </table>
                    </div>
                </div>
                </form>


<?php echo $form; ?>
          
        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->
                
<?php
include('layout_admin/tpl_footer.php');
?>