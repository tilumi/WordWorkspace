<?php
if( APP::$doctype != 'html' ){
    require('error/404.php');die;
}

APP::$pageTitle = '管理員群組';
APP::$mainTitle='管理員群組 Managers Groups';
APP::$mainName='群組';

$action = pos( APP::$params );
$registedAction = array(
    'index',
    'add',
    'edit',
    'delete',
    'view',
    'privileges',
    'm_edit',
    'm_delete',
    'm_priv',
    'active',
    'inactive',
    'normaluser',
    'superuser',
);
if( in_array( $action, $registedAction ) ){
    $action = array_shift(APP::$params);
}

$modelPath = APP::$handler.'_model.php';
if( file_exists($modelPath) ){ include( $modelPath ); }
$modelPath = APP::$prefix.'#managers_model.php';
if( file_exists($modelPath) ){ include( $modelPath ); }

if( in_array( $action , $registedAction ) ){
    //設定action的別名轉換
    switch( $action ){
        case 'chpasswd':
            $action='chpasswd';
            break;
    }
    //執行action
    $action();
}else{
    require('error/404.php');die;
}



$viewTpl = APP::$handler.'='.$action.'.php';
if( file_exists($viewTpl) ){ include( $viewTpl ); }

/******************************************************************************/

function index(){
    View::setTitle(APP::$mainTitle);
    
    //初始化
    $SESS = & $_SESSION['Pager'];
    if( ! isset($SESS[ APP::$prefix ][ APP::$app ]) ){
        $SESS[ APP::$prefix ][ APP::$app ] = array();
        $SESS[ APP::$prefix ][ APP::$app ]['pageRows'] = PAGEROWS;
        $SESS[ APP::$prefix ][ APP::$app ]['pageID'] = 1;
        $SESS[ APP::$prefix ][ APP::$app ]['search'] = array();
    }
    $session=& $SESS[ APP::$prefix ][ APP::$app ];
    
    //Pager
    $pageRows = $session['pageRows'];
    $pageID = $session['pageID'];
    if( isset($_GET['pageID']) && is_numeric($_GET['pageID']) ){
        $pageID=(int)$_GET['pageID'];
        $session['pageID']=$pageID;
    }
    
    //Search
    $form=Form::create('frmSearch', 'get', APP::$ME );
    
    $form->addElement('header', '', '內容檢索' );
    
    $form->addElement('text', 'name', APP::$mainName.'名稱', array('class'=>'input-long'));
    
    if( ACL::checkAuth('active') ){
        $options = array(
            ''=>'--- 選擇狀態 ---',
            '1'=>'顯示',
            '0'=>'隱藏',
        );
        $form->addElement('select', 'is_active', '顯示狀態', $options, array('class'=>'input'));
    }
    
    $buttons=Form::buttonsSearchForm( false );
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    //紀錄搜尋資料
    $submits=$form->getSubmitValues();
    if( count($submits)>0 ){
        $session['search']=$submits;
        $session['pageID'] = 1;
        redirect('.'); //重導洗掉GET參數
    }
    
    $submits = $session['search'];
    $form->setDefaults($submits);
    $form=Form::getHtml($form);
    
    //Search Message
    $searchInfo=array();
    foreach($submits as $key=>$value){
        if( $value==='' ){ continue; }
        if( $key=='name' ){ $searchInfo[]=APP::$mainName.'名稱含"<span>'.$value.'</span>"'; }
        if( $key=='is_active' ){ $_=array(0=>'隱藏',1=>'直接顯示'); $searchInfo[]='顯示狀態為"<span>'.$_[$value].'</span>"'; }
    }
    
    list($rows, $totalItems) = Groups::pagelist($submits, $pageID, $pageRows);
    
    APP::$appBuffer = array($rows,$totalItems,$pageID,$pageRows,$form,$searchInfo,$dignities);
}
function add(){
    APP::$pageTitle = '新增'.APP::$mainName;

    $form=Form::create('frmInsert', 'post', APP::$ME );
    
    $form->addElement('header', '', APP::$pageTitle );
    
    $form->addElement('text', 'name', '身分名稱', array('class'=>'input-short'));
    $form->addElement('text', 'info', '身分說明', array('class'=>'input-medium'));
    $form->addElement('text', 'sort', '排序', array('class'=>'input', 'value'=>1 ));
    
    if( ACL::checkAuth( array('action'=>'active') ) ){
        $options = array(
            '1'=>'啟用',
            '0'=>'停用',
        );
        $form->addElement('select', 'is_active', '啟用狀態', $options, array('class'=>'input-short'));
        $form->setDefaults( array('is_active'=>0 ) );
    }
    
    $buttons=Form::buttons();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $form->addRule('name', '名稱 必填', 'required', null, 'client');
    $form->addRule('name', '身分名稱 應 2~32 個字元', 'rangelength', array( 2,32 ), 'client');
    $form->addRule('info', '身分說明 必填', 'required', null, 'client');
    $form->addRule('sort', '排序 必填', 'required', null, 'client');
    $form->addRule('sort', '排序 必須是數字', 'numeric', null, 'client');

    $form->applyFilter('name', 'trim');
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = Groups::add($submits); 
            if( $errmsg === true ){
                $userid=$_SESSION['admin']['userid'];
                APP::syslog($userid.' 新增'.APP::$mainName.': '.$submits['userid'].' 成功', APP::$prior['info'], 'managers' );
                redirect( '.' , APP::$mainName.'已新增成功' , 'success' );
            }
            $userid=$_SESSION['admin']['userid'];
            APP::syslog($userid.' 新增'.APP::$mainName.': '.$submits['userid'].' 發生錯誤。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
            redirect( '.' , $errmsg , 'error' );
        }
    }
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);
}
function edit(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = Groups::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    APP::$pageTitle='編輯'.APP::$mainName.'：'.$data['name'];
    
    $form=Form::create('frmUpdate', 'post', APP::$ME );
    
    $form->addElement('header', '', APP::$pageTitle );
    
    $form->addElement('hidden', 'id');
    $form->addElement('text', 'name', APP::$mainName.'名稱', array('class'=>'input-short'));
    $form->addElement('text', 'info', APP::$mainName.'說明', array('class'=>'input-medium'));
    $form->addElement('text', 'sort', '排序', array('class'=>'input'));
    
    if( ACL::checkAuth( array('action'=>'active') ) ){
        $radio=array();
        $radio[]=&HTML_QuickForm::createElement('radio', 'is_active', '', ' 啟用', '1');
        $radio[]=&HTML_QuickForm::createElement('radio', 'is_active', '', ' 停用', '0');
        $form->addGroup($radio, '', '啟用狀態', ' ');
        $form->setDefaults( array('is_active'=>0 ) );
    }
    
    $buttons=Form::buttons();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $form->addRule('name', '名稱 必填', 'required', null, 'client');
    $form->addRule('name', '身分名稱 應 2~32 個字元', 'rangelength', array( 2,32 ), 'client');
    $form->addRule('info', '身分說明 必填', 'required', null, 'client');
    $form->addRule('sort', '排序 必填', 'required', null, 'client');
    $form->addRule('sort', '排序 必須是數字', 'numeric', null, 'client');

    $form->applyFilter('name', 'trim');
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = Groups::edit($submits); 
            if( $errmsg === true ){
                $userid=$_SESSION['admin']['userid'];
                APP::syslog($userid.' 編輯'.APP::$mainName.': '.$submits['userid'].' 成功', APP::$prior['info'], 'managers' );
                redirect( '.' , APP::$mainName.'已編輯成功' , 'success' );
            }
            $userid=$_SESSION['admin']['userid'];
            APP::syslog($userid.' 編輯'.APP::$mainName.': '.$submits['userid'].' 失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
            redirect( '' , $errmsg , 'error' );
        }
    } 
    $form->setDefaults($data);
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);
}
function view(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = Groups::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    APP::$pageTitle='檢視'.APP::$mainName.'資訊：'.$data['name'];
    $privs=Groups::loadFullACLs($id);
    
    $form=getACLsForm( '檢視'.APP::$mainName.'權限', $privs );
    
    $privs_html=Form::getHtml($form, 'rollcalls');
    
    APP::$appBuffer = array($data, $privs_html);
}
function getACLsForm( $header='' , $privs ){
    $form=Form::create('frmPrivs', 'post', APP::$ME );
    $form->addElement('header', '', $header );
    
    $privsType=array(
        'allow'=>'允許',
        'deny'=>'拒絕',
        'neutral'=>'不給予',
    );
    $privsClassName=array(
        'allow'=>'submit-green',
        'neutral'=>'submit-gray',
        'deny'=>'submit-red',
    );
    $privsHelp=array(
        'allow'=>'預設允許使用，但可被更改',
        'deny'=>'不允許使用，且不得被更改',
        'neutral'=>'預設不允許使用，但可被更改',
    );
    
    $text_indent=str_repeat('&nbsp; ', 2);
    $style='width:80px;';
    $form->addElement('html', '圖例：');
    foreach( $privsClassName as $key=>$pcn ){
        $form->addElement('button', '', $privsType[$key], array('class'=>$pcn, 'style'=>$style));
        $form->addElement('html', $privsHelp[$key].' &nbsp; ');
    }
    $form->addElement('html', '<div style="height:20px;"></div>');
    
    //pr($privs);die;
    foreach( $privs as $priv ){
        if( $priv['type']==='header' ){
            $form->addElement('html', '<div style="font-size:16px;color:red;font-weight:bold;padding:0;margin:0px 0 0 0;">'.$priv['name'].'</div>'."\n");
            continue;
        }
        
        //加入標題
        $form->addElement('html', $text_indent.'<span style="font-size:12px;"><strong>'.$priv['name'].'</strong></span><br>');
        //$form->addElement('html', '<div style="float:left;height:20px;line-height:20px;font-weight:bold;margin:10px 0 0 0;">'.$priv['name'].'： &nbsp;</div>'."\n");
        $form->addElement('html', $text_indent.$text_indent);
        foreach( $priv['methods'] as $name=>$value ){
            $elements=array();
            if( $data['type']!='normal' ){
                $elements[] = &HTML_QuickForm::createElement('button', 'button', $name, array(
                        'class'=>$privsClassName[ $value ].' ',
                        'style'=>'margin-top:8px;width:80px;',
                    )
                );
            }else{
                $elements[] = &HTML_QuickForm::createElement('button', 'button', $data['member_name'], array('class'=>$attendClassName[ $data['status'] ].' '.$type, 'id'=>'btn-'.$data['id'], 'onclick'=>$js_onclick, 'style'=>$style));
            }
            $form->addGroup($elements, '', '', '');
        }
        $form->addElement('html', '<div style="height:20px;">&nbsp;</div>');
        
    }
    
    return $form;
}
function privileges(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = Groups::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    if( $data['is_super_user']=='1' ){
        redirect( '.' , '不能變更這個使用者的權限' , 'attention' );
    }
    
    APP::$pageTitle='設定'.APP::$mainName.'權限：'.$data['name'];
    
    APP::load( 'vendor', 'Symfony'.DS.'yaml'.DS.'sfYaml' );
    $acls=sfYaml::load( dirname(__FILE__).DS.'config'.DS.'privileges.yml' );
    
    $privs=Groups::loadPrivileges($data['id']);
    $form=getPrivilegesForm( '設定'.APP::$mainName.'權限: &nbsp; '.$data['name'], $data, $acls, $privs );
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        //pr($submits);die;
        if( $form->validate() ){
            $errmsg = Groups::setPrivileges($submits); 
            if( $errmsg === true ){
                $userid=$_SESSION['admin']['userid'];
                APP::syslog($userid.' 變更'.APP::$mainName.'權限: '.$data['userid'].' 成功', APP::$prior['info'], 'managers' );
                redirect( '.' , APP::$mainName.'權限已設定成功' , 'success' );
            }
            $userid=$_SESSION['admin']['userid'];
            APP::syslog($userid.' 變更'.APP::$mainName.'權限: '.$data['userid'].' 失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
            redirect( '.' , $errmsg , 'error' );
        }
    }
    $default=array('userid'=>$data['id']) + $privs;
    $form->setDefaults( $default ); 
    
    $form=Form::getHtml($form, 'rollcalls');
    
    APP::$appBuffer = array($form);
}
function getPrivilegesForm( $header='' , $userdata=array() , $privs=array(), $settings=array() ){
    $form=Form::create('frmPrivileges', 'post', APP::$ME );
    $form->addElement('header', '', $header );
    
    $form->addElement('hidden', 'userid', $userdata['id']);

    $privsType=array(
        'allow'=>'允許',
        'deny'=>'拒絕',
        'neutral'=>'不給予',
    );
    $privsClassName=array(
        'allow'=>'submit-green',
        'neutral'=>'submit-gray',
        'deny'=>'submit-red',
    );
    $privsHelp=array(
        'allow'=>'預設允許使用，但可被更改',
        'deny'=>'不允許使用，且不得被更改',
        'neutral'=>'預設不允許使用，但可被更改',
    );
    
    $style='width:80px;';
    $form->addElement('html', '<div>圖例：</div>');
    foreach( $privsClassName as $key=>$pcn ){
        $form->addElement('button', '', $privsType[$key], array('class'=>$pcn, 'style'=>$style));
        $form->addElement('html', $privsHelp[$key].' &nbsp; ');
    }
    $form->addElement('html', '<div style="height:20px;clear:both;"></div>');
    
    $i=0;
    foreach($privs as $key=>$priv){
        $name=$priv['name'];
        if( isset($priv['type']) && $priv['type']==='header' ){
            $form->addElement('html', '<span style="font-size:14px;color:red;"><strong>'.$name.'</strong></span>');
            $form->addElement('html', '<div style="clear:both;height:10px;"></div>');
            continue;
        }
        
        $app='';
        if( isset($priv['app']) && !empty($priv['app']) ){
            $app=$priv['app'];
        }
        
        $text_indent=str_repeat('&nbsp; ', 2);
        $methods=$priv['methods'];
        $checkbox=array();
        $setup=array();
        $represent=array();
        $js_onclick="javascript: changeStatus(this);";
        //加入標題
        $form->addElement('html', $text_indent.'<span style="font-size:12px;"><strong>'.$name.'</strong></span>');
        //附加快捷按鈕
        if( $app !== 'main' ){
            $form->addElement('html', $text_indent.'<a href="javascript:void(0);" onclick="javascript: setStatus(\'area-'.$key.'\', \'allow\'); ">全允許</a>');
            $form->addElement('html', str_repeat('&nbsp; ', 1).'<a href="javascript:void(0);" onclick="javascript: setStatus(\'area-'.$key.'\', \'deny\'); ">全拒絕</a>');
            $form->addElement('html', str_repeat('&nbsp; ', 1).'<a href="javascript:void(0);" onclick="javascript: setStatus(\'area-'.$key.'\', \'neutral\'); ">全不給予</a>');
        }
        //附加說明
        if( $app === 'main' ){
            $form->addElement('html', $text_indent.'這是系統賦予的基本權限，不能關閉');
        }
        $form->addElement('html', '<div style="clear:both;height:10px;"></div>');
        foreach( $methods as $priv_name=>$actions ){
            $i+=1;
            if( $app == 'main' ){
                //主系統為基本權限，必須提供，因此不需列為選項
                //$checkbox[]=&HTML_QuickForm::createElement('advcheckbox', $actions[0], '', $priv_name, array('disabled', 'checked'), array('allow', 'allow'));
                $checkbox[]=&HTML_QuickForm::createElement('button', '', $priv_name, array('class'=>$privsClassName['allow'].' area-'.$key, 'id'=>'btn-'.$i, 'onclick'=>"alert('這是系統基本賦予的權限，不能關閉')", 'style'=>$style));
                $setup[]=&HTML_QuickForm::createElement('hidden', $actions[0], 'allow', array('id'=>'current-'.$i) );
                $represent[]=&HTML_QuickForm::createElement('hidden', $actions[0], implode(',', $actions) );
                continue;
            }
            $action=pos($actions);
            
            $auth_type='neutral';
            if( $settings['priv:'.$app][$action]==='allow' ){ $auth_type='allow'; }
            if( $settings['priv:'.$app][$action]==='deny' ){ $auth_type='deny'; }
            if( $settings['priv:'.$app][$action]==='neutral' ){ $auth_type='neutral'; }
            
            //$checkbox[]=&HTML_QuickForm::createElement('advcheckbox', $actions[0], '', $priv_name, array('class'=>'priv_'.$key,'onclick'=>$js_onclick), array('deny', 'allow'));
            $checkbox[]=&HTML_QuickForm::createElement('button', '', $priv_name, array('class'=>$privsClassName[$auth_type].' area-'.$key, 'id'=>'btn-'.$i, 'onclick'=>$js_onclick, 'style'=>$style));
            $setup[]=&HTML_QuickForm::createElement('hidden', $actions[0], $auth_type, array('id'=>'current-'.$i) );
            $represent[]=&HTML_QuickForm::createElement('hidden', $actions[0], implode(',', $actions) );
        }
        $form->addElement('html', $text_indent.$text_indent);
        $form->addGroup($checkbox, '', '<b>'.$name.'</b>: &nbsp;', ' ');
        $form->addGroup($setup, 'priv:'.$app.'', '', '');
        $form->addGroup($represent, 'represent['.$app.']', '', '');
        $form->addElement('html', '<div style="clear:both;height:10px;"></div>');
    }
    $form->addElement('html', '<div style="height:40px;"></div>');
    $buttons=Form::buttons();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    return $form;
}
function getNoJsPrivilegesForm( $header='' , $userdata=array() , $contents=array() ){
    $form=Form::create('frmPrivileges', 'post', APP::$ME );
    
    $form->addElement('header', '', $header );
    
    $form->addElement('hidden', 'userid', $userdata['id']);
    foreach($contents as $key=>$priv){
        $name=$priv['name'];
        if( isset($priv['type']) && !empty($priv['type']) ){
            $form->addElement('html', '<span style="font-size:14px;color:red;"><strong>'.$name.'</strong></span>');
            $form->addElement('html', '<div style="clear:both;height:10px;"></div>');
            continue;
        }
        
        $app='';
        if( isset($priv['app']) && !empty($priv['app']) ){
            $app=$priv['app'];
        }
        
        $methods=$priv['methods'];
        $checkbox=array();
        $represent=array();
        $i=0;
        foreach( $methods as $priv_name=>$actions ){
            if( $app == 'main' ){
                //主系統為基本權限，必須提供，因此不需列為選項
                $checkbox[]=&HTML_QuickForm::createElement('advcheckbox', $actions[0], '', $priv_name, array('disabled', 'checked'), array('allow', 'allow'));
                $represent[]=&HTML_QuickForm::createElement('hidden', $actions[0], implode(',', $actions) );
                continue;
            }
            $js_onclick='';
            if( $i==0 ){
                $js_onclick ="javascript: if( this.checked ){ ";
                $js_onclick.="$('.priv_{$key}').each( function(){ this.checked='checked'; } );";
                $js_onclick.="}else{";
                $js_onclick.="$('.priv_{$key}').each( function(){ this.checked=''; } );";
                $js_onclick.="}";
            }
            $checkbox[]=&HTML_QuickForm::createElement('advcheckbox', $actions[0], '', $priv_name, array('class'=>'priv_'.$key,'onclick'=>$js_onclick), array('deny', 'allow'));
            $represent[]=&HTML_QuickForm::createElement('hidden', $actions[0], implode(',', $actions) );
            $i+=1;
        }
        $form->addGroup($checkbox, 'priv:'.$app.'', '<b>'.$name.'</b>: &nbsp;', ' ');
        $form->addGroup($represent, 'represent['.$app.']', '', '');
        $form->addElement('html', '<div style="clear:both;"></div>');
    }
    $buttons=Form::buttons();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    return $form;
}
function delete(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = Groups::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    APP::$pageTitle='刪除'.APP::$mainName.'：'.$data['name'];
    
    $form=Form::create('frmDelete', 'post', APP::$ME );
    
    $form->addElement('header', '', '您確定要刪除'.APP::$mainName.' '.$data['userid'].' ?' );
    
    $form->addElement('hidden', 'id');
    $form->addElement('static', 'name', '名稱', array('class'=>'input-medium'));
            
    $buttons=Form::buttonsNoReset();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $form->applyFilter('name', 'trim');
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = Groups::delete($submits); 
            if( $errmsg === true ){
                $userid=$_SESSION['admin']['userid'];
                APP::syslog($userid.' 刪除'.APP::$mainName.': '.$submits['userid'].' 成功', APP::$prior['info'], 'managers' );
                redirect( '.' , APP::$mainName.'已刪除' , 'success' );
            }
            $userid=$_SESSION['admin']['userid'];
            APP::syslog($userid.' 刪除'.APP::$mainName.': '.$submits['userid'].' 失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
            redirect( '.' , $errmsg , 'error' );
        }
    } 
    $form->setDefaults($data);
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);
}
function m_edit(){
    $form=Form::create('frmList', 'post', ME );
    $submits = $form->getSubmitValues();
    
    $type=$submits['mode'];
    
    $allowed_type=array(
        'active'=>'已啟用',
        'inactive'=>'已停用',
    );
    if( ! array_key_exists($type, $allowed_type) ){
        redirect( '.' , '不允許這樣的操作' , 'attention' );
    }
    
    $items=$submits['items'];
    if( count($items)<1 ){
        redirect('.', '尚未選擇執行目標，您必須先選擇執行的項目', 'error');
    }
    switch( $type ){
        case 'active':
            $errmsg = Groups::setActive($items);
            break;
        case 'inactive':
            $errmsg = Groups::setInactive($items);
            break;
    }
    if( $errmsg === true ){
        $userid=$_SESSION['admin']['userid'];
        APP::syslog($userid.' 指定的'.count($items).'位'.APP::$mainName.' '.$allowed_type[$type].' id:'.implode(',',$submits['items']), APP::$prior['info'], 'managers' );
        redirect('.', '指定的 '.count($items).' 位'.APP::$mainName.$allowed_type[$type] , 'success');
    }
    $userid=$_SESSION['admin']['userid'];
    APP::syslog($userid.' 指定的'.count($items).'位'.APP::$mainName.' '.$allowed_type[$type].'失敗 id:'.implode(',',$submits['items']).'。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
    redirect('.', $errmsg , 'error');
}
function m_delete(){
    $form=Form::create('frmMultiple', 'post', array('action'=>'m_delete') );
    $submits = $form->getSubmitValues();
    
    if( isset($submits['commit']) ){
        $num=count($submits['ids']);
        $errmsg = Groups::delete($submits); 
        if( $errmsg === true ){
            $userid=$_SESSION['admin']['userid'];
            APP::syslog($userid.' 指定的'.count($items).'位'.APP::$mainName.' 已刪除 id:'.implode(',',$submits['items']), APP::$prior['info'], 'managers' );
            redirect( '.' , '指定的 '.$num.' 位'.APP::$mainName.'已刪除' , 'success' );
        }
        $userid=$_SESSION['admin']['userid'];
        APP::syslog($userid.' 指定的'.count($items).'位'.APP::$mainName.' 已刪除失敗 id:'.implode(',',$submits['items']).'。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
        redirect('.', $errmsg , 'error');
    }
    if( !isset($submits['items']) || count($submits['items'])<1 ){
        redirect('.', '尚未選擇執行目標，您必須先選擇執行的項目', 'error');
    }
    $items=$submits['items'];
    $rows=Model::fetchById( $items );
    $form=Form::get( 'm_delete' , '以下'.APP::$mainName.'都將被刪除，是否確認：', $rows );
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);
}
function m_priv(){
    $form=Form::create('frmList', 'post', ME );
    $submits = $form->getSubmitValues();
    
    $type=$submits['mode'];
    
    $allowed_type=array(
        'normal_user'=>'已設定為一般管理員',
        'super_user'=>'已設定為超級管理員',
    );
    if( ! array_key_exists($type, $allowed_type) ){
        redirect( '.' , '不允許這樣的操作' , 'attention' );
    }
    
    $items=$submits['items'];
    if( count($items)<1 ){
        redirect('.', '尚未選擇執行目標，您必須先選擇執行的項目', 'error');
    }
    switch( $type ){
        case 'normal_user':
            $errmsg = Groups::setNormalUser($items);
            break;
        case 'super_user':
            $errmsg = Groups::setSuperUser($items);
            break;
    }
    if( $errmsg === true ){
        $userid=$_SESSION['admin']['userid'];
        APP::syslog($userid.' 指定的'.count($items).'位'.APP::$mainName.' '.$allowed_type[$type].' id:'.implode(',',$submits['items']), APP::$prior['info'], 'managers' );
        redirect('.', '指定的 '.count($items).' 位'.APP::$mainName.$allowed_type[$type] , 'success');
    }
    $userid=$_SESSION['admin']['userid'];
    APP::syslog($userid.' 指定的'.count($items).'位'.APP::$mainName.' '.$allowed_type[$type].'失敗 id:'.implode(',',$submits['items']).'。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
    redirect('.', $errmsg , 'error');
}
function active(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = Groups::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    if( $errmsg = Groups::setActive($id) ){
        $userid=$_SESSION['admin']['userid'];
        APP::syslog($userid.' '.APP::$mainName.' '.$data['name'].' 已設定為啟用', APP::$prior['info'], 'managers' );
        redirect( '.' , APP::$mainName.' '.$data['name'].' 已設定為啟用' , 'success' );
    }
    $userid=$_SESSION['admin']['userid'];
    APP::syslog($userid.' '.APP::$mainName.' '.$data['name'].' 設定為啟用失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
    redirect( '.' , $errmsg , 'error' );
}
function inactive(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = Groups::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    $errmsg = Groups::setInactive($id);
    if( $errmsg === true ){
        $userid=$_SESSION['admin']['userid'];
        APP::syslog($userid.' '.APP::$mainName.' '.$data['name'].' 已設定為停用', APP::$prior['info'], 'managers' );
        redirect( '.' , APP::$mainName.' '.$data['name'].' 已設定為停用' , 'success' );
    }
    $userid=$_SESSION['admin']['userid'];
    APP::syslog($userid.' '.APP::$mainName.' '.$data['name'].' 設定為停用失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
    redirect( '.' , $errmsg , 'error' );
}

?>