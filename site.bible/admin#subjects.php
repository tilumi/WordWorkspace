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
    'sbman2flyfish',
);
if( in_array( $action, $registedAction ) ){
    $action = array_shift(APP::$params);
}

APP::$pageTitle = '禮拜主題';
APP::$mainTitle = '禮拜主題 Subjects';
APP::$mainName = '禮拜主題';
$menu_id = 4;

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
    
    //APP::load('vendor', 'Services_JSON');
    list($rows, $totalItems) = Subjects::pagelist($submits, $pageID, $pageRows);
    
    APP::$appBuffer = array( $rows, $totalItems, $pageID, $pageRows, $form, $searchInfo );
}
function getShortScript($id,$short=true,$saperate=", ",$p="-"){
    //傳入主題id，回傳簡式經文格式(串成單一字串)
    //參數：分隔符號、是否使用簡寫書名、節連接詞
    
    if($short){
        $sql ="SELECT CONCAT(t.short,b.chapter,':',b.section,'$p',b.section+s.plus) as script, s.plus,b.book,b.chapter,b.section";
    }else{
        $sql ="SELECT CONCAT(t.book,b.chapter,':',b.section,'$p',b.section+s.plus) as script, s.plus,b.book,b.chapter,b.section";
    }
    $sql.=" FROM subject_script s";
    $sql.=" LEFT JOIN bible b ON s.section=b.id";
    $sql.=" LEFT JOIN bible_short t ON t.id=b.book";
    $sql.=" WHERE s.sbj_id='$id' AND b.title='' ORDER BY s.id";
    $qid=db_query($sql);
    $r=db_fetch_row(&$qid);
    
    $link_start="";
    $link_stop="";
    if($link){
        $link_start ='<a target="_blank" href="'.sprintf($url_tpl,$r["book"],$r["chapter"],$r["section"],$r["plus"]).'">';
        $link_stop="</a>";

    }

    if($r["plus"]==0){
        list($script,)=explode($p,$r["script"]);
    }else{
        $script=$r["script"];
    }
    $script=$link_start.$script.$link_stop;
    while($r=db_fetch_row(&$qid)){
        $link_start="";
        $link_stop="";
        if($link){
            $link_start ='<a target="_blank" href="'.sprintf($url_tpl,$r["book"],$r["chapter"],$r["section"],$r["plus"]).'">';
            $link_stop="</a>";
        }
        if($r["plus"]==0){
            list($sc,)=explode($p,$r["script"]);
        }else{
            $sc=$r["script"];
        }
        $script.=$saperate.$link_start.$sc.$link_stop;
    }
    return $script;
}

function add(){
    APP::$pageTitle='新增'.APP::$mainName;
    View::setTitle(APP::$pageTitle);
    
    APP::load('model', 'yeartopics');
    APP::load('model', 'subjects-wtypes');
    APP::load('model', 'weekly');
    
    $form=Form::create('frmInsert', 'post', APP::$ME );
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = Subjects::add($submits); 
            if( $errmsg === true ){
                redirect( '.' , APP::$mainName.'已新增成功' , 'success' );
            }
            redirect( '.' , $errmsg , 'error' );
        }
    }
    
    $years = Yeartopics::getList();
    $wtypes = SubjectsWtypes::getList();
    
    $wday=(int)date('w');
    $data['worshiped']=date('Y-m-d');
    if( in_array( $wday, array(2,3,4)) ){
        $data['worshiped']=date('Y-m-d', strtotime('+3 days' , Weekly::getWeekDay1('', true)) );
        $data['wtype_id']='WedDay';
    }
    if( in_array( $wday, array(5,6)) ){
        $data['worshiped']=Weekly::getWeekDay1('+7 days');
        $data['wtype_id']='LordDay';
    }
    if( in_array( $wday, array(0,1)) ){
        $data['worshiped']=Weekly::getWeekDay1();
        $data['wtype_id']='LordDay';
    }
    
    APP::$appBuffer = array( $form , $years , $wtypes , $data );
}
function edit(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = Subjects::findById($id);
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
    
    $radio=array();
    $radio[]=&HTML_QuickForm::createElement('radio', 'is_active', '', ' 直接顯示', '1');
    $radio[]=&HTML_QuickForm::createElement('radio', 'is_active', '', ' 隱藏', '0');
    $form->addGroup($radio, '', '顯示狀態', ' ');

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
            $errmsg = Subjects::edit($submits); 
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
    $data = Subjects::findById($id);
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
            $errmsg = Subjects::delete($submits); 
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
    $data = Subjects::findById($id);
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
            $errmsg = Subjects::setActive($items);
            break;
        case 'inactive':
            $errmsg = Subjects::setInactive($items);
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
        $errmsg = Subjects::delete($submits); 
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
    $rows=Subjects::findById( $items );
    
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
    $data = Subjects::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    if( $errmsg = Subjects::setActive($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'「'.$data['name'].'」已設定顯示' , 'success' );
    }
    redirect( '.' , $errmsg , 'error' );
}
function inactive(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = Subjects::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    $errmsg = Subjects::setInactive($id);
    if( $errmsg === true ){
        redirect( '.' , '指定的'.APP::$mainName.'「'.$data['name'].'」已設定隱藏' , 'success' );
    }
    redirect( '.' , $errmsg , 'error' );
}
function sbman2flyfish(){
/*
A 聖經全書
B 書卷-書卷（含單一書卷）
C 書卷#章-章（含單一章）
D 書卷#章:節-節（含單一節）
E （特殊）書卷#章:節-章:節（同書卷）
F （特殊）書卷#章:節-書卷#章:節（跨書卷）
*/
    APP::load('model', 'weekly');
    
    mysql_select_db('sbman');
    $sql ="SELECT s.*,CONCAT(y.year,' ',y.topic_zh) as year_topic FROM subject s";
    $sql.=" LEFT JOIN yeartopic y ON y.year=s.year";
    $sql.=" WHERE 1<>2";
    $rows=Model::fetchAll($sql);
    
    $data=array();
    $a_data=array();
    $v_data=array();
    $a_notfound=array();
    $i=0;
    $v_i=0;
    $a_i=0;
    foreach( $rows as $key=>$r ){
        $i+=1;
        if( $i>50 ){ continue; }
        mysql_select_db('sbman');
        $id = $r['id'];
        $sql =" SELECT *, CONCAT(t.short,b.chapter,':',b.section) as script";
        $sql.=" , CONCAT(t.book,b.chapter,':',b.section) as script_full";
        $sql.=" FROM subject_script s";
        $sql.=" LEFT JOIN bible b ON s.section=b.id";
        $sql.=" LEFT JOIN bible_short t ON t.id=b.book";
        $sql.=" WHERE s.sbj_id='$id' AND b.title='' ORDER BY s.id";
        $scripts=Model::fetchAll($sql);
        $rows[$key]['scripts_count']=count($scripts);
        $rows[$key]['scripts']=$scripts;
        
        
        $sql ="SELECT n.id,i.series_name as sid,d.name_zh as name,d.name_kr as song_kr,d.name_en as song_en FROM subject_anthem a";
        $sql.=" LEFT JOIN songs n ON n.id=a.song_id";
        $sql.=" LEFT JOIN songs_no i ON i.songs_id=a.song_id";
        $sql.=" LEFT JOIN songs_data d ON d.id=n.current_data";
        $sql.=" WHERE a.sbj_id='$id' ORDER BY a.id";
        $anthems=Model::fetchAll($sql);
        $rows[$key]['anthems_count']=count($anthems);
        $rows[$key]['anthems']=$anthems;
        foreach( $anthems as $k=>$a ){
            mysql_select_db('flyfish');
            $sql ="SELECT * FROM songs WHERE name=".Model::quote($a['name'], 'text');
            $_=Model::fetchRow($sql);
            $rows[$key]['anthems'][$k]['id']=$_['id'];
            $rows[$key]['anthems'][$k]['std_id']=pos(explode(' ',$_['std_id']));
        }
        //pr($rows);
        
        
        $data[$key]['id']=uniqid('Subject');
        
        $verses=array();
        foreach( $rows[$key]['scripts'] as $k=>$s ){
            $v_i+=1;
            $verses[$k]['name']=$s['script_full'];
            $verses[$k]['short']=$s['script'];
            $verses[$k]['key']='D:'.$s['bk'].'#'.$s['ch'].':'.$s['sc'];
            if( $s['plus'] > 0 ){
                $verses[$k]['key'].='-'.($s['sc']+$s['plus']);
                $verses[$k]['name'].='-'.($s['sc']+$s['plus']);
                $verses[$k]['short'].='-'.($s['sc']+$s['plus']);
            }
            $verses[$k]['type']='本文';
            $verses[$k]['info']='';
            
            $v_data[$v_i]=$verses[$k];
            $v_data[$v_i]['id']=uniqid('Verse');
            $v_data[$v_i]['sort']=$k;
            $v_data[$v_i]['subject_id']=$data[$key]['id'];
            $v_data[$v_i]['verse_type']='D';
            $v_data[$v_i]['verse_key']=$verses[$k]['key'];
            unset($v_data[$v_i]['key']);
            $v_data[$v_i]['verse_id']=sprintf('%02d:%03d:%03dg', $s['bk'],$s['ch'],$s['sc']);
            $v_data[$v_i]['verse_plus']=$s['plus'];
            //$v_data[$v_i]['created']=date('Y-m-d H:i:s');
            //pr($v_data);die;
            
            //補充只在 json 中才記的內容
            
        }
        //pr($verses);
        $anthems=array();
        foreach( $rows[$key]['anthems'] as $k=>$s ){
            $a_i+=1;
            
            $anthems[$k]['name']=$s['name'];
            $anthems[$k]['std_id']=$s['std_id'];
            $anthems[$k]['song_id']=$s['id'];
            
            $a_data[$a_i]=$anthems[$k];
            $a_data[$a_i]['id']=uniqid('Anthem');
            $a_data[$a_i]['sort']=$k;
            $a_data[$a_i]['subject_id']=$data[$key]['id'];
            $a_data[$a_i]['type']='';
            $a_data[$a_i]['info']='';
            //$a_data[$a_i]['created']=date('Y-m-d H:i:s');
            
            if( empty($s['id']) ){
                $alter='';
                switch($s['name']){
                    case '地球上立起攝理燈台':
                        $alter='在各國中插上攝理燭臺';
                        break;
                    case '期待的心情':
                        $alter='等待的心情';
                        break;
                    case '攝理的榮光':
                        $alter='攝理的榮光 明亮之清晨';
                        break;
                    case '攝理史上奔跑的ＭＳ':
                        $alter='攝理上奔跑的我們';
                        break;
                    case '三十階段層層話語':
                        $alter='三十個論成約話語';
                        break;
                    case '無條件':
                        $alter='無條件(1)';
                        break;
                    case '你腳步請對我停留':
                        $alter='你腳步請對我停留(1)';
                        break;
                    case '讚美聲音':
                        $alter='感謝聲音';
                        break;
                    case '我的主最寶貴':
                        $alter='我的主最寶貴(1)';
                        break;
                }
                if( ! empty($alter) ){
                    mysql_select_db('flyfish');
                    $sql ="SELECT * FROM songs WHERE name=".Model::quote($alter, 'text');
                    $_=Model::fetchRow($sql);
                    $_['id']=$_['id'];
                    $_['std_id']=pos(explode(' ',$_['std_id']));
                    $s=$_;

                    $anthems[$k]['std_id']=$s['std_id'];
                    $anthems[$k]['song_id']=$s['id'];
                    
                    $a_data[$a_i]=$anthems[$k];
                    $a_data[$a_i]['id']=uniqid('Anthem');
                    $a_data[$a_i]['sort']=$k;
                    $a_data[$a_i]['subject_id']=$data[$key]['id'];
                    $a_data[$a_i]['type']='';
                    $a_data[$a_i]['info']='';
                    //$a_data[$a_i]['created']=date('Y-m-d H:i:s');
                    
                }
                
                if( empty($s['id']) ){
                    //$a_notfound[$a_i]=$a_data[$a_i];
                }
                
            }
        }
        
        $data[$key]['year']=$r['year'];
        $data[$key]['week']=$r['week'];
        $data[$key]['wday']=$r['wday'];
        $data[$key]['worshiped']=Weekly::getDate($r['year'], $r['week'], $r['wday']);
        if( $r['wday']==='0' ){
            $data[$key]['wtype_id']='LordDay';
            $data[$key]['wtype_name']='主日禮拜';
        }
        if( $r['wday']==='3' ){
            $data[$key]['wtype_id']='WedDay';
            $data[$key]['wtype_name']='週三禮拜';
        }
        $name_zh=str_replace(PHP_EOL, ' / ', $r['sbj_zh']);
        $name_kr=str_replace(PHP_EOL, ' / ', $r['sbj_kr']);
        $data[$key]['name']=$name_zh;
        $data[$key]['name_zh']=$name_zh;
        $data[$key]['name_zh_unfold']=$r['sbj_zh'];
        $data[$key]['name_kr']=$name_kr;
        $data[$key]['name_kr_unfold']=$r['sbj_kr'];
        $data[$key]['verses_count']=count($verses);
        $data[$key]['verses']=json_encode($verses);
        $data[$key]['anthems_count']=count($anthems);
        $data[$key]['anthems']=json_encode($anthems);
        $data[$key]['extra']=$r['extra'];
        $data[$key]['info']=$r['excerpt'];
        $data[$key]['is_active']='1';
        $data[$key]['created']=date('Y-m-d H:i:s');
        
/*        if( $i>=100 ){
            mysql_select_db('flyfish');
            Model::inserts($data, 'subjects');
            Model::inserts($v_data, 'subjects_verses');
            Model::inserts($a_data, 'subjects_anthems');
            $data=array();
            $a_data=array();
            $v_data=array();
            $i=0;
            $v_i=0;
            $a_i=0;
        }*/
    }
    //pr($a_notfound);die;
    pr($v_data);
    pr($a_data);
    pr($data);die;
    mysql_select_db('flyfish');
    Model::inserts($data, 'subjects');
    Model::inserts($v_data, 'subjects_verses');
    Model::inserts($a_data, 'subjects_anthems');
    markquery_report();
    echo '完成';die;

}

?>