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
                        <tr class="odd" style="border-bottom:1px solid #ccc;border-top:1px solid #ccc;">
                            <td style="vertical-align:top;width:150px;">
                            <span style="color: #ff0000">*</span>
                            章節標題(中)
                            </td>
                            <td>
                            <input class="input-medium" name="name" type="text" value="<?php echo $data['name']; ?>" />
                            </td>
                        </tr>
                        <tr style="border-bottom:1px solid #ccc;border-top:1px solid #ccc;">
                            <td style="vertical-align:top;">
                            最大節
                            </td>
                            <td>
                            <?php echo $data['max_verse']; ?>
                            <!-- <input class="input-short" name="max_verse" type="text" value="<?php echo $data['max_verse']; ?>" /> -->
                            </td>
                        </tr>
                        </table>
                    </div>
                </div>
                <div class="module">
                    <h2><span>章節內文</span></h2>
                    <div class="module-table-body">
                        <table>
<?php $key=0; ?>
<?php foreach( $verses as $verse ){ $key+=1; ?>
<?php       if( in_array($verse['stype_id'], array('a','b','c','d','e','f')) ){ ?>
                        <tr class="<?php echo ( ($key%2)==0 )?'even':'odd'; ?>" style="border-bottom:1px solid #ccc;border-top:1px solid #ccc;">
                            <td style="vertical-align:top;">
                            <b><?php echo $verse['stype_name']; ?></b>
                            </td>
                            <td>
                            <input class="input-short" name="verses[<?php echo $verse['id']; ?>]" type="text" value="<?php echo htmlspecialchars($verse['name']); ?>" />
                            <b>關連經文</b>
                            <input class="input-short" name="relates[<?php echo $verse['id']; ?>]" type="text" value="<?php echo htmlspecialchars($verse['relate']); ?>" />
                            </td>
                        </tr>
<?php       } ?>
<?php       if( in_array($verse['stype_id'], array('g')) ){ ?>
                        <tr class="<?php echo ( ($key%2)==0 )?'even':'odd'; ?>" style="border-bottom:1px solid #ccc;border-top:1px solid #ccc;">
                            <td style="vertical-align:top;">
                            <?php echo $data['book_name']; ?> <?php echo $verse['chapter_id']; ?>:<?php echo $verse['verse_id']; ?>
                            </td>
                            <td>
                            <input class="input-long" name="verses[<?php echo $verse['id']; ?>]" type="text" value="<?php echo htmlspecialchars($verse['name']); ?>" />
                            </td>
                        </tr>
<?php       } ?>
<?php       if( in_array($verse['stype_id'], array('h')) ){ ?>
                        <tr class="<?php echo ( ($key%2)==0 )?'even':'odd'; ?>" style="border-bottom:1px solid #ccc;border-top:1px solid #ccc;">
                            <td style="vertical-align:top;">
                            <?php echo $data['book_name']; ?> <?php echo $verse['chapter_id']; ?>:<?php echo $verse['verse_id']; ?>
                            </td>
                            <td>
                            <?php echo $verse['stype_name']; ?>
                            </td>
                        </tr>
<?php       } ?>
<?php } ?>

                        <tr style="border-bottom:1px solid #ccc;border-top:1px solid #ccc;">

                            <td style="vertical-align:top;">

                            


                            </td>
                            <td>
                            <input type="hidden" name="" value="cancel" class="hidden-cancel">
                            <input class="submit-green" name="commit" value="送出" type="submit" />
                            <input class="submit-gray" name="" value="重設" type="reset" />&nbsp;<input class="submit-gray" onclick="$('.hidden-cancel').attr('name', 'cancel');this.form.submit();" name="" value="取消" type="button" />
                            </td>
                        </table>
                    </div>
                </div>
                </form>


        		<div style="clear: both;"></div>
            </div> <!-- End .grid_12 -->
                
<?php
include('layout_admin/tpl_footer.php');
?>