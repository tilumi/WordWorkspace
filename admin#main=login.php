<?php
include('layout_admin/tpl_header.php');
list( $form ) = APP::$appBuffer;
?>

        
        
        
  <!--[if lt IE 7]>
  <div style='border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 75px; position: relative;'>
    <div style='position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;'><a href='#' onclick='javascript:this.parentNode.parentNode.style.display="none"; return false;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-cornerx.jpg' style='border: none;' alt='Close this notice'/></a></div>
    <div style='width: 640px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;'>
      <div style='width: 75px; float: left;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg' alt='Warning!'/></div>
      <div style='width: 275px; float: left; font-family: Arial, sans-serif;'>
        <div style='font-size: 14px; font-weight: bold; margin-top: 12px;'>您正在使用過時的瀏覽器上網</div>
        <div style='font-size: 12px; margin-top: 6px; line-height: 12px;'>為了給您更好的上網體驗，也為了屬天歷史的資訊安全，請在右邊選一套您喜歡的瀏覽器上網吧!</div>
      </div>
      <div style='width: 75px; float: left;'><a href='http://www.firefox.com' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-firefox.jpg' style='border: none;' alt='Get Firefox 3.5'/></a></div>
      <div style='width: 75px; float: left;'><a href='http://www.browserforthebetter.com/download.html' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-ie8.jpg' style='border: none;' alt='Get Internet Explorer 8'/></a></div>
      <div style='width: 73px; float: left;'><a href='http://www.apple.com/safari/download/' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-safari.jpg' style='border: none;' alt='Get Safari 4'/></a></div>
      <div style='float: left;'><a href='http://www.google.com/chrome' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-chrome.jpg' style='border: none;' alt='Get Google Chrome'/></a></div>
    </div>
  </div>
  <![endif]-->

			<div class="prefix_2 grid_8 suffix_2">

                <?php echo redirect_message(); ?>
                
                <div class="module">
                     <h2><span>使用你的帳號登入</span></h2>
                        
                     <div class="module-body">
                        
<script type="text/javascript">
//<![CDATA[
function validate_frmLogin(frm) {
  var value = '';
  var errFlag = new Array();
  var _qfGroups = {};
  _qfMsg = '';

  value = frm.elements['userid'].value;
  if (value == '' && !errFlag['userid']) {
    errFlag['userid'] = true;
    _qfMsg = _qfMsg + '\n - 管理者名稱必填';
  }

  value = frm.elements['password'].value;
  if (value == '' && !errFlag['password']) {
    errFlag['password'] = true;
    _qfMsg = _qfMsg + '\n - 密碼必填';
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
                        <form method="post" action="<?php echo APP::$ME; ?>" class="login" name="frmLogin" id="frmLogin" onsubmit="try { var myValidator = validate_frmLogin; } catch(e) { return true; } return myValidator(this);"> 
                            
                            <fieldset>
                            <p>
                                <label style="width:50px;">管理者</label>
                                <input name="userid" class="input-medium" type="text" autocomplete="off">
                            </p>
                            <p>
                                <label style="width:50px;">密碼</label>
                                <input name="password" class="input-medium" type="password">
                            </p>
                            <p>
                                <input name="remember" value="no" type="hidden">
                            	<input name="remember" id="remember" value="auto" type="checkbox"><label for="remember">兩週內記得我的登入，直到我登出 ( 公用電腦切勿使用 )</label>
                            </p>

                                <input class="submit-green" value="送出" type="submit"> 
                            </fieldset>
                        </form>
                        
                        <ul>
                            <li><a href="<?php echo url( '/forgot_password.html' ); ?>">忘記密碼</a></li>
                        </ul>
                        


                        
                        
                     </div> <!-- End .module-body -->
                </div> <!-- End .module -->
            </div> <!-- End .grid_6 -->
				
           

<?php
include('layout_admin/tpl_footer.php');
?>