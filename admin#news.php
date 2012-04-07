<?php
if( APP::$doctype != 'html' ){
    require('error/404.php');die;
}

$action = pos( APP::$params );
$registedAction = array(
    'index',
    'add',
    'edit',
    'delete',
    'archives',
    'm_edit',
    'm_delete',
    'active',
    'inactive',
);
if( in_array( $action, $registedAction ) ){
    $action = array_shift(APP::$params);
}

APP::$pageTitle = '新聞中心';
APP::$mainTitle = '新聞中心 News';
APP::$mainName = '公告';

$modelPath = APP::$handler.'_model.php';
if( file_exists($modelPath) ){ include( $modelPath ); }

if( in_array( $action , $registedAction ) ){
    //設定action的別名轉換
    switch( $action ){
        case 'change-passwd':
            $action='changepwd';
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
    View::setTitle(APP::$pageTitle);
    
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
    $form=Form::create('frmSearch', 'post', APP::$ME );
    $form->addElement('header', '', '內容檢索' );
    $form->addElement('text', 'name', '標題', array('class'=>'input-long'));
    $form->addElement('text', 'author', '作者', array('class'=>'input-long'));
    
    $options = array(
        ''=>'--- 選擇狀態 ---',
        '1'=>'顯示',
        '0'=>'隱藏',
    );
    $form->addElement('select', 'is_active', '顯示狀態', $options, array('class'=>'input'));
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
        if( $key=='name' ){ $searchInfo[]='<u>標題</u> 含 "<span>'.$value.'</span>" '; }
        if( $key=='author' ){ $searchInfo[]='<u>作者</u> 含 "<span>'.$value.'</span>" '; }
        if( $key=='is_active' ){ $_=array(0=>'隱藏',1=>'直接顯示'); $searchInfo[]='<u>顯示狀態</u> 為 "<span>'.$_[$value].'</span>" '; }
    }
    
    list($rows, $totalItems) = News::pagelist($submits, $pageID, $pageRows);
    
    APP::$appBuffer = array( $rows, $totalItems, $pageID, $pageRows, $form, $searchInfo );
}
function add(){
    APP::$pageTitle='新增'.APP::$mainName;
    View::setTitle(APP::$pageTitle);
    
    $form=Form::create('frmInsert', 'post', APP::$ME );
    
    $form->addElement('header', '', $header );
    
    $options = array(
        'language'=>'tw',
        'format'=>'Y-m-d H:i',
        'minYear'=>date('Y')-5,
        'maxYear'=>date('Y')+5,
        'year'=>date('Y')
    );
    $form->addElement('date', 'published', '發佈日期', $options, array('class'=>'input'));
    $form->setDefaults( array('published'=>date('Y-m-d H:i') ) );
    
    $form->addElement('text', 'name', '標題', array('class'=>'input-medium'));
    //$form->addElement('text', 'urn', '網址URN (Unique Resource Name): 請填入標題的英譯文句，由系統自動轉換為網址，SEO 用', array('class'=>'input-medium'));
    
    $options = array(
        '1'=>'直接顯示',
        '0'=>'暫時隱藏',
    );
    $form->addElement('select', 'is_active', '顯示狀態', $options, array('class'=>'input-short'));
    $form->setDefaults( array('is_active'=>0 ) );

    $form->addElement('textarea', 'article', '內文', array('cols'=>90, 'rows'=>30, 'class'=>'wysiwyg'));
    
    $buttons=Form::buttons();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $form->addRule('published', '發佈日期 必填', 'required', null, 'client');
    $form->addRule('name', '標題 必填', 'required', null, 'client');
    $form->addRule('name', '標題至多255個字', 'maxlength', 255, 'client');
    $form->addRule('urn', 'URN至多128個字', 'maxlength', 128, 'client');
    $form->addRule('is_active', '啟用狀態 必填', 'required', null, 'client');
    
    $form->applyFilter('name', 'trim');
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = News::add($submits); 
            if( $errmsg === true ){
                redirect( '.' , APP::$mainName.'已新增成功' , 'success' );
            }
            redirect( '.' , $errmsg , 'error' );
        }
    }
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array( $form );
}
function edit(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = News::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    APP::$pageTitle='編輯'.APP::$mainName.'：'.$data['name'];
    View::setTitle(APP::$pageTitle);
    
    $form=Form::create('frmUpdate', 'post', APP::$ME );
    
    $form->addElement('header', '', $header );
    
    $form->addElement('hidden', 'id');
    $options = array(
        'language'=>'tw',
        'format'=>'Y-m-d H:i',
        'minYear'=>date('Y')-5,
        'maxYear'=>date('Y')+5,
        'year'=>date('Y')
    );
    $form->addElement('date', 'published', '發佈日期', $options, array('class'=>'input'));
    $form->setDefaults( array('published'=>date('Y-m-d H:i') ) );
    
    $form->addElement('text', 'name', '標題', array('class'=>'input-medium'));
    //$form->addElement('text', 'urn', '網址URN (Unique Resource Name): 請填入標題的英譯文句，由系統自動轉換為網址，SEO 用', array('class'=>'input-medium'));
    
    $options = array(
        '1'=>'直接顯示',
        '0'=>'暫時隱藏',
    );
    $form->addElement('select', 'is_active', '顯示狀態', $options, array('class'=>'input-short'));

    $form->addElement('textarea', 'article', '內文', array('cols'=>90, 'rows'=>30, 'class'=>'wysiwyg'));
    
    $buttons=Form::buttons();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $form->addRule('published', '發佈日期 必填', 'required', null, 'client');
    $form->addRule('name', '標題 必填', 'required', null, 'client');
    $form->addRule('name', '標題至多255個字', 'maxlength', 255, 'client');
    $form->addRule('urn', 'URN至多128個字', 'maxlength', 128, 'client');
    $form->addRule('is_active', '啟用狀態 必填', 'required', null, 'client');
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = News::edit($submits); 
            if( $errmsg === true ){
                redirect( '.' , APP::$mainName.'已編輯成功' , 'success' );
            }
            redirect( '.' , $errmsg , 'error' );
        }
    } 
    $form->setDefaults($data);
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array( $form );
}
function delete(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = News::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    APP::$pageTitle='刪除'.APP::$mainName.'確認：'.$data['name'];
    View::setTitle(APP::$pageTitle);
    
    $form=Form::create('frmDelete', 'post', APP::$ME );
    
    $form->addElement('header', '', '您確定要刪除'.APP::$mainName.' '.$data['name'].' ?' );
    
    $form->addElement('hidden', 'id');
    $form->addElement('static', 'name', '<b>標題名稱</b>');
    
    $html ='<p><img src="'.layout_url('admin', '/images/icons/edit-delete-3.png').'"></p>';
    $html.='<p style="color:red;"><b>將會被刪除，請確認？</b></p>';
    $form->addElement('html', '<div style="margin:10px;">'.$html.'</div>' );
    
    $buttons=Form::buttonsNoReset();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $form->applyFilter('name', 'trim');
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = News::delete($submits); 
            if( $errmsg === true ){
                redirect( '.' , APP::$mainName.'已刪除' , 'success' );
            }
            redirect( '.' , $errmsg , 'error' );
        }
    } 
    $form->setDefaults($data);
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array( $form );
}
function archives( $id=null ){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = News::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    APP::$pageTitle='檢視'.APP::$mainName.'：'.$data['name'];
    View::setTitle(APP::$pageTitle);
    
    APP::$appBuffer = array( $data );
}
function m_edit(){
    $form=Form::create('frmList', 'post', ME );
    $submits = $form->getSubmitValues();
    
    $type=$submits['mode'];
    
    $allowed_type=array(
        'active'=>'已設定顯示',
        'inactive'=>'已設定隱藏',
    );
    if( ! array_key_exists($type, $allowed_type) ){
        redirect( '.' , '不允許這樣的操作' , 'attention' );
    }
    if( !isset($submits['items']) || count($submits['items'])<1 ){
        redirect( '.', '尚未選擇執行目標，您必須先選擇項目', 'error');
    }
    
    $items=$submits['items'];
    switch( $type ){
        case 'active':
            $errmsg = News::setActive($items);
            break;
        case 'inactive':
            $errmsg = News::setInactive($items);
            break;
    }
    if( $errmsg === true ){
        redirect('.', '指定的 '.count($items).' 項'.APP::$mainName.$allowed_type[$type] , 'success');
    }
    redirect('.', $errmsg , 'error');
}
function m_delete(){
    $form=Form::create('frmMultiple', 'post', array('action'=>'m_delete') );
    
    APP::$pageTitle='批次刪除'.APP::$mainName.'確認';
    View::setTitle(APP::$pageTitle);
    
    $submits = $form->getSubmitValues();
    if( isset($submits['commit']) ){
        $submits = $form->getSubmitValues();
        $num=count($submits['ids']);
        $errmsg = News::delete($submits); 
        if( $errmsg === true ){
            redirect( '.' , '指定的 '.$num.' 項'.APP::$mainName.'已刪除' , 'success' );
        }
        redirect('.', $errmsg , 'error');
    }
    if( isset($submits['cancel']) ){
        redirect( '.' , '使用者取消' , 'info' );
    }
    if( !isset($submits['items']) || count($submits['items'])<1 ){
        redirect( '.', '尚未選擇執行目標，您必須先選擇項目', 'error');
    }
    $items=$submits['items'];
    $rows=News::findById( $items );
    
    $form=Form::create('frmMDelete', 'post', APP::$ME );
    $form->addElement('header', '', '以下'.APP::$mainName.'都將刪除，是否確認：' );
    $form->addElement('hidden', 'action', 'delete');
    $i=0;
    foreach( $rows as $item ){
        $i+=1;
        $form->addElement('hidden', 'ids[]', $item['id']);
        $form->addElement('static', '', APP::$mainName.' '.$i.'.', $item['name']);
    }
    
    $buttons=Form::buttonsNoReset();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array( $form );
}
function active(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = News::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    if( $errmsg = News::setActive($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'「'.$data['name'].'」已設定顯示' , 'success' );
    }
    redirect( '.' , $errmsg , 'error' );
}
function inactive(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = News::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    $errmsg = News::setInactive($id);
    if( $errmsg === true ){
        redirect( '.' , '指定的'.APP::$mainName.'「'.$data['name'].'」已設定隱藏' , 'success' );
    }
    redirect( '.' , $errmsg , 'error' );
}


?>