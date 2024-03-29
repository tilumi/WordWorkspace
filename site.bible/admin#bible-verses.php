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

APP::$pageTitle = '章節經文';
APP::$mainTitle = '章節經文 Bible Verses';
APP::$mainName = '章節';
$menu_id = 2;

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


APP::$appBuffer['menu_id']=$menu_id;
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
    $form->addElement('text', 'book_name', '書卷名稱', array('class'=>'input-long'));
    $form->addElement('text', 'chapter_id', '章次', array('class'=>'input-long'));
    $form->addElement('text', 'name', '章節標題', array('class'=>'input-long'));
    
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
        if( $key=='book_name' ){ $searchInfo[]='<u>書卷名稱</u> 含 "<span>'.$value.'</span>" '; }
        if( $key=='chapter_id' ){ $searchInfo[]='<u>章次</u> 為 "<span>'.$value.'</span>" '; }
        if( $key=='name' ){ $searchInfo[]='<u>章節標題</u> 含 "<span>'.$value.'</span>" '; }
        //if( $key=='summary' ){ $_=array(0=>'隱藏',1=>'直接顯示'); $searchInfo[]='<u>顯示狀態</u> 為 "<span>'.$_[$value].'</span>" '; }
    }
    
    list($rows, $totalItems) = BibleVerses::pagelist($submits, $pageID, $pageRows);
    //BibleVerses::updateAllHTML();
/*    
    //計算各章的最大節
    $sql="SELECT c.id, MAX(b.verse_id) as max FROM `bible_chapters` c JOIN cuv b ON b.book_id=c.book_id AND b.chapter_id=c.chapter_id WHERE b.stype_id IN ('g','h') GROUP BY c.book_id, c.chapter_id";
    $rrows=Model::fetchAll($sql);
    foreach( $rrows as $r ){
        $update=array();
        $update['id']=$r['id'];
        $update['max_verse']=$r['max'];
        Model::update($update, 'id', 'bible_chapters');
    }
    */
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
    
    $radio=array();
    $radio[]=&HTML_QuickForm::createElement('radio', 'is_active', '', ' 直接顯示', '1');
    $radio[]=&HTML_QuickForm::createElement('radio', 'is_active', '', ' 隱藏', '0');
    $form->addGroup($radio, '', '顯示', ' ');
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
            $errmsg = BibleVerses::add($submits); 
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
    $data = BibleVerses::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $verses = BibleVerses::getVerses($id);
    
    APP::load('vendor', 'ckeditor/ckeditor');
    
    APP::$pageTitle='編輯'.APP::$mainName.'：'.$data['name'];
    View::setTitle(APP::$pageTitle);
    
    $form=Form::create('frmUpdate', 'post', APP::$ME );
    
    $form->addElement('header', '', $header );
    
    $form->addElement('hidden', 'id');
    
    $form->addElement('text', 'name', APP::$mainName.'標題(中)', array('class'=>'input-medium'));
    $form->addElement('text', 'max_verse', '最大節數', array('class'=>'input-medium'));
    
    $buttons=Form::buttons();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $form->addRule('name', '標題 必填', 'required', null, 'client');
    $form->addRule('name', '標題至多255個字', 'maxlength', 255, 'client');
    $form->addRule('urn', 'URN至多128個字', 'maxlength', 128, 'client');
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = BibleVerses::edit($submits); 
            if( $errmsg === true ){
                redirect( '.' , APP::$mainName.'已編輯成功' , 'success' );
            }
            redirect( '.' , $errmsg , 'error' );
        }
    } 
    $form->setDefaults($data);
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array( $form , $data , $verses );
}
function delete(){
    $urn = pos(APP::$params);
    if( empty($urn) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = BibleVerses::findByUrn($urn);
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
            $errmsg = BibleVerses::delete($submits); 
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
function archives(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = BibleVerses::findById($id);
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
            $errmsg = BibleVerses::setActive($items);
            break;
        case 'inactive':
            $errmsg = BibleVerses::setInactive($items);
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
        $errmsg = BibleVerses::delete($submits); 
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
    $rows=BibleVerses::findById( $items );
    
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
    $data = BibleVerses::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    if( $errmsg = BibleVerses::setActive($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'「'.$data['name'].'」已設定顯示' , 'success' );
    }
    redirect( '.' , $errmsg , 'error' );
}
function inactive(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = BibleVerses::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    $errmsg = BibleVerses::setInactive($id);
    if( $errmsg === true ){
        redirect( '.' , '指定的'.APP::$mainName.'「'.$data['name'].'」已設定隱藏' , 'success' );
    }
    redirect( '.' , $errmsg , 'error' );
}


?>