<?php
class APP{
    static $SESSION=array(); //連結至 $_SESSION
    /* Controller */
    static $pageTitle=''; //頁面標題
    
    static $mainTitle=''; //應用程式名稱
    static $mainName=''; //程式關鍵字
    /* Database */
    static $mdb; //資料庫操作元件
    
    /* Config */
    static $systemConfigs; //系統設定
    static $databaseConfigs; //資料庫設定
    static $layoutsConfigs; //layout顯示設定
    
    static $routing=array();
    static $params=array();
    static $handler=''; //紀錄本次執行，總管負責的程式
    static $prefix=''; //網址前綴詞
    static $appBuffer=''; //action執行完畢的結果回傳
    
    /* Syslog */
    static $prior = array(
        'emergency'=>'Emergency',
        'alert'=>'Alert',
        'critical'=>'Critical',
        'error'=>'Error',
        'warning'=>'Warning',
        'notice'=>'Notice',
        'info'=>'Info',
        'debug'=>'Debug'
    );
    function syslog($message, $prior='Notice', $type='MESSAGE'){
        $mdb=self::$mdb;
        
        $userid='<--SYSTEM-->';
        if( isset($_SESSION['administrator']['userid']) )
            $userid=$_SESSION['administrator']['userid'];
        if( ! class_exists('AuthComponent') ){
            LoadComponents('Auth');
        }
        $ip=AuthComponent::getUserClientIP();
        
        $fields=array();
        $fields['id']=$mdb->quote( uniqid('LOG' ), 'text' );
        $fields['plugin']=$mdb->quote( 'syslog' , 'text' );
        if( !empty($type) ) $fields['type']=$mdb->quote( strtoupper($type) , 'text' );
        if( !empty($prior) ) $fields['prior']=$mdb->quote( $prior , 'text' );
        if( !empty($userid) ) $fields['userid']=$mdb->quote( $userid , 'text' );
        if( !empty($ip) ) $fields['ip']=$mdb->quote( $ip , 'text' );
        if( !empty($message) ) $fields['name']=$mdb->quote( $message , 'text' );
    
    	$tb='syslog';$fs=array();$vs=array();
    	foreach( $fields as $f=>$v ){ $fs[]=$f; $vs[]=$v; }
    	$fs[]='created'; $vs[]='NOW()';
    	
    	$sql=sprintf("INSERT INTO $tb ( %s ) VALUES ( %s )",implode(',',$fs),implode(',',$vs));
    	
    	//echo $sql;exit();
    	$res=APP::$mdb->exec($sql);
    	if(MRDB::isError())
    		errmsg('Syslog Error');
    }

}

class Model{
    static $relation=array();
    static $useTable='';
    static $plugin='';
    static $mask;
    
    static $masterModel=''; //記錄誰將成為主要Model，紀錄 Model Class Name, ex. MainModel
    static $masterTable=''; 
    static $masterConfigs=array(); //記錄主要Model的各項設定
    
    function insert( $fields , $useTable='' ){
        if( empty($useTable) ){ $useTable=Model::$masterTable; }
        
        //pr($fields);die;
    	$fs=array();$vs=array();
    	foreach( $fields as $f=>$v ){ $fs[]="`".$f."`"; $vs[]=self::quote($v, 'text'); }
    	
    	$sql=sprintf("INSERT INTO ".$useTable." ( %s ) VALUES ( %s )",implode(',',$fs),implode(',',$vs));
    	
    	return Model::execute($sql);
    }
    function inserts( $rows , $useTable='' , $quotes=array() ){
        if( empty($useTable) ){ $useTable=Model::$masterTable; }
        
        $defineQuotes=false;
        if( is_array($quotes) && count($quotes)>0 ){
            $defineQuotes=true;
        }
    	$fs=array();
    	$first=true;
        foreach( $rows as $fields ){
            
            $vs=array();
        	foreach( $fields as $f=>$v ){
                if( $first ){ $fs[]="`".$f."`"; }
                if( $defineQuotes ){
                    $type='text';
                    if( isset($quotes[$f]) && !empty($quotes[$f]) ){ $type=$quotes[$f]; }
                    $vs[]=Model::quote( $v, $quotes[$f] );
                    continue;
                }
                $vs[]=Model::quote( $v, 'text');
            }
        	$first=false;
        	
        	$values[]='('.implode(',',$vs).')';
        }
    	
    	$sql=sprintf("INSERT INTO ".$useTable." ( %s ) VALUES %s",implode(',',$fs),implode(',',$values));
    	
    	return Model::execute($sql);
    }
    function update( $fields , $identify='id' , $useTable='' ){
        if( empty($useTable) ){ $useTable=Model::$masterTable; }
        
        if( is_string($identify) ){
            if( empty($identify) ){ die('The identify field could not empty.'); }
            $identify=array($identify);
        }
        $where_list=array();
        if( is_array($identify) ){
            foreach($identify as $ide){
                $where_list[]=$ide.'='.$fields[$ide];
                unset($fields[$ide]);
            }
        }
        
    	$fs=array();$vs=array();
        foreach( $fields as $f=>$v ){
            if( is_numeric($f) ){ //如果 $field 為純數字，表示 $value 直接成為條件
                $fs[]="`".implode('`=`', explode('=', $v) )."`";
                continue;
            }
            $fs[]="`".$f."`".'='.self::quote($v, 'text');
        }
    	
    	if(count($where_list)<1){ die('The identify field could not be empty.'); }
    	$sql=sprintf("UPDATE ".$useTable." SET %s WHERE %s",implode(',',$fs), implode(' AND ',$where_list) );
    	
    	return Model::execute($sql);
    }
    function query($sql){
        $result=APP::$mdb->query($sql);
        if( APP::$mdb->isError() )
            Model::query_error($sql);
        return $result;
    }
    function exec($sql){
        return Model::execute($sql);
    }
    function execute($sql){
        $result=APP::$mdb->exec($sql);
        if( APP::$mdb->isError() )
            Model::query_error($sql);
        if( $result!==false ){
            return true;
        }
        return false;
    }
    function numRows($sql){
        $res=$sql;
        if( is_string($sql) ){
            $res=APP::$mdb->query($sql);
            if( APP::$mdb->isError() )
                Model::query_error($sql);
        }
        $rows=APP::$mdb->numRows($res);
        return $rows;
    }
    function fetchAll($sql){
        $res=$sql;
        if( is_string($sql) ){
            $res=APP::$mdb->query($sql);
            if( APP::$mdb->isError() )
                Model::query_error($sql);
        }
        $rows=APP::$mdb->fetchAll($res);
        return $rows;
    }
    function fetchRow($sql){
        $res=$sql;
        if( is_string($sql) ){
            $res=APP::$mdb->query($sql);
            if( APP::$mdb->isError() )
                Model::query_error($sql);
        }
        $row=APP::$mdb->fetchRow($res);
        return $row;
    }
    function fetchOne($sql){
        $res=$sql;
        if( is_string($sql) ){
            $res=APP::$mdb->query($sql);
            if( APP::$mdb->isError() )
                Model::query_error($sql);
        }
        $col=APP::$mdb->fetchOne($res);
        return $col;
    }
    function quote($value, $type = null, $quote = true){
        return APP::$mdb->quote($value, $type, $quote);
    }
    function escape($value){
        return APP::$mdb->escape($value);
    }

    function getOffsetStart($pageID=1, $pageRows=PAGEROWS ){
        $offsetStart = ($pageID-1) * $pageRows ;
        return $offsetStart;
    }
    
    /**** Error Function ****/
    function query_error( $sql ){
        echo '<META CONTENT="text/html; charset=utf-8" HTTP-EQUIV="Content-Type">';
        $backtrace=debug_backtrace();
        $err=$backtrace[1];
        $_link=& APP::$mdb->_link[ APP::$mdb->_active_profile ];
        $msg ='<p style="font-size:15px;color:black;font-weight:normal;"><b>'.$err['file'].' Line '.$err['line'].'</b></p>';
        $msg.='<p style="font-size:13px;color:black;font-weight:normal;"><b>'.$err['class'].'::'.$err['function'].'() Complain:</p>';
        $msg.='<p style="font-size:13px;color:black;font-weight:bold;">Error: <span style="color:red;">'.$sql.'</span></p>';
        $msg.='<p style="font-size:13px;color:black;font-weight:normal;"><b>Message:</b> '.mysql_errno($_link).' '.mysql_error($_link).'</p>';
        $msg.=debugBacktrace();
        die( $msg );
    }
    function connect_error( $func , $mdb ){
        echo '<META CONTENT="text/html; charset=utf-8" HTTP-EQUIV="Content-Type">';
        if( PRODUCTION==1 ){ redirect( 500 ); }
        die( "Database Connection Error: " . $mdb->getMessage() . '<br>' . $mdb->getDebugInfo() );
    }
}

class View{
    static $Code=200;
    static $cacheLifeTime=-1; //render時的cache存活時間，-1時表示使用layout cache的預設值
    static $layoutConfigs=array(); //輸出頁面的設定資料
    
    function setTitle( $pageTitle ){
        self::$layoutConfigs['title']=$pageTitle;
        return true;
    }
    function setHeader( $name, $value ){ //設定Layout標頭<head>
        if( strpos($name, '.')!==false ){ list($name, $key)=explode('.', $name); }
        
        $_keys = array(
            'http_meta','http_metas','sitename','title','meta','metas','stylesheets','javascripts','stylesheet','javascript',
            'has_layout','layout','template',
        );
        if( ! in_array($name, $_keys) ){
            errmsg('不支援這個屬性設定：'.$name);
        }
        if( in_array($name, array('http_meta','meta','stylesheet','javascript')) ){
            $name.='s';
        }
        
        $_appends = array('http_metas','metas');
        if( in_array($name, $_appends) ){
            self::$layoutConfigs[$name][$key] = $value;
            return true;
        }
        //以下屬性用疊加的方式設定參數
        $_appends = array('stylesheets','javascripts');
        if( in_array($name, $_appends) ){
            if( is_string($value) ){
                self::$layoutConfigs[$name][] = $value;
                return true;
            }
            self::$layoutConfigs[$name]=array_merge( self::$layoutConfigs[$name], $value );
            return true;
        }
        if( $name == 'has_layout' ){
            if( $value ){
                self::$layoutConfigs[$name]=true;
                return true;
            }
            self::$layoutConfigs[$name]=false;
            return true;
        }
        self::$layoutConfigs[$name]=$value;
        return true;
    }
    /**** Head tag utilities start ****/
    function link( $params, $absolute_src=false ){
        if( is_string($params) ){
            $params=array('href'=>$params);
        }
        $_default = array(
            'rel' => 'stylesheet',
            'type' => 'text/css',
            'media' => 'screen'
        );
        $_data = $_default + $params;
        $rel=$_data['rel'];unset($_data['rel']);
        $href=$_data['href'];unset($_data['href']);
        $type=$_data['type'];unset($_data['type']);
        $media=$_data['media'];unset($_data['media']);
        $prefix=self::_attrs_to_str( $_data );
        
        if( ! $absolute_src ){
            $href=self::layout_url($href);
        }
        
        return '<link rel="'.$rel.'" type="'.$type.'" href="'.$href.'" media="'.$media.'"'.$prefix.'>';
    }
    function script( $params, $absolute_src=false ){
        if( is_string($params) ){
            $params=array('src'=>$params);
        }
        $_default = array(
            'type' => 'text/javascript',
        );
        $_data = $_default + $params;
        $type=$_data['type'];unset($_data['type']);
        $src=$_data['src'];unset($_data['src']);
        $prefix=self::_attrs_to_str( $_data );
        
        if( ! $absolute_src ){
            $src=self::layout_url($src);
        }
        
        return '<script type="'.$type.'" src="'.$src.'"'.$prefix.'></script>';
    }
    function meta( $params ){
        $name='';
        if( isset($params['name']) ){ $name='name="'.$params['name'].'" '; unset($params['name']); }
        $httpEquiv='';
        if( isset($params['httpEquiv']) ){ $httpEquiv='http-equiv="'.$params['httpEquiv'].'" '; unset($params['httpEquiv']); }
        $content='';
        if( isset($params['content']) ){ $content=$params['content']; unset($params['content']); }
        $prefix=self::_attrs_to_str( $params );
        return '<meta '.$name.$httpEquiv.'content="'.$content.'"'.$prefix.'>';
    }
    /**** Head tag utilities End ****/
    
    function include_http_metas( $return=false ){
        $contents='';
        $httpMetas=View::$layoutConfigs['http_metas'];
        if( is_array($httpMetas) && count($httpMetas)>0 ){
            foreach( $httpMetas as $http_meta=>$value ){
                if( empty($value) ) continue;
                $contents.='<meta http-equiv="'.$http_meta.'" content="'.$value.'">'."\n";
            }
        }
        $metas=View::$layoutConfigs['metas'];
        if( is_array($metas) && count($metas)>0 ){
            foreach( $metas as $meta=>$value ){
                if( empty($value) ) continue;
                if( in_array( $meta , array('title','sitename') ) ){ continue; }
                if( $meta=='language' ) $meta='content-'.$meta;
                $contents.='<meta name="'.$meta.'" content="'.$value.'">'."\n";
            }
        }
        
        if( $return ) return $content;
        echo $contents;
    }
    function include_title( $return=false ){
        $pageTitle = APP::$pageTitle;
        
        $contents='';
        $metas=View::$layoutConfigs;
        $sitename='';
        if( isset($metas['sitename']) && !empty($metas['sitename']) ){
            $sitename=$metas['sitename'];
        }
        $title='';
        if( isset($metas['title']) && !empty($metas['title']) ){
            $title=$metas['title'];
        }
        if( !empty($pageTitle) ){
            if( !empty($title) ){
                $title_replaced=str_replace( '<%pageTitle%>', $pageTitle, $title );
                //如果沒有設置replace tag, 則直接以設定值取代
                if( $title_replaced==$title ){
                    $title=$pageTitle;
                }else{
                    $title=$title_replaced;
                }
            }else{
                $title=$pageTitle;
            }
        }
        $docTitle =$title;
        $docTitle.=( !empty($title) && !empty($sitename) )?' - ':'';
        $docTitle.=$sitename;
        
        $contents.='<title>'.$docTitle.'</title>'."\n";
        if( $return ) return $content;
        echo $contents;
    }
    function include_stylesheets( $return=false ){
        $contents='';
        $_css=View::$layoutConfigs['stylesheets'];
        $_link = array(
            'rel' => 'stylesheet',
            'type' => 'text/css',
            'media' => 'screen'
        );
        foreach($_css as $url){
            if( substr($url,0,4)=='http' ){
                //網址的狀況
                $absolute_url=true;
                $_link['href']=$url;
                $contents.=self::link($_link, $absolute_url)."\n";
                continue;
            }
            if( substr($url,0,1)=='/' ){
                //視為根目錄路徑，表示要走程式化路徑 (由程式產生)
                $absolute_url=true;
                $_link['href']=$url;
                $contents.=self::link($_link, $absolute_url)."\n";
                continue;
            }
            //預設路徑為 layout 的 js資料夾
            $_link['href']='/css/'.$url;
            $contents.=self::link($_link)."\n";
        }
        if( $return ) return $content;
        echo $contents;
    }
    function include_javascripts( $return=false ){
        $contents='';
        $_js=View::$layoutConfigs['javascripts'];
        foreach($_js as $url){
            if( substr($url,0,4)=='http' ){
                //網址的狀況
                $absolute_url=true;
                $contents.=self::script($url, $absolute_url)."\n";
                continue;
            }
            if( substr($url,0,1)=='/' ){
                //視為根目錄路徑，表示要走程式化路徑 (由程式產生)
                $absolute_url=true;
                $contents.=self::script($url, $absolute_url)."\n";
                continue;
            }
            //預設路徑為 layout 的 js資料夾
            $url='/js/'.$url;
            $contents.=self::script($url)."\n";
        }
        if( $return ) return $content;
        echo $contents;
    }
    function include_extra_headers(){
    
    }

    /**** Body tag utilities start ****/
    function a( $href , $name='' , $attrs=array() ){
        return self::anchor( $href , $name='' , $attrs=array() );
    }
    function anchor( $href , $name='' , $attrs=array() ){
        $href_abs=self::url($href);
        if( empty($name) ){ $name=$href_abs; }
        $prefix=self::_attrs_to_str( $attrs );
        return '<a href="'.$href_abs.'"'.$prefix.'>'.$name.'</a>';
    }
    function img( $src , $params=array() ){
        $_default=array();
        $_data = $params + $_default ;
        $prefix=self::_attr_to_str( $_data );
        
        $src_abs=self::image_url($src);
        
        return '<img src="'.$src_abs.'"'.$prefix.' />';
    }
    /**** Head tag utilities End ****/
    function js_url( $src ){ return self::layout_url(APP::$routing['prefix'], $src); }
    function css_url( $src ){ return self::layout_url(APP::$routing['prefix'], $src); }
    function layout_url( $href='' ){ return layout_url(APP::$routing['prefix'], $href); }
    function image_url( $src ){ return image_url($src); }
    function url( $href ){ return url($href); }
    protected function _attrs_to_str( $attrs ){
        $prefix='';
        foreach( $attrs as $key=>$value ){
            $prefix.=' '.$key.'="'.$value.'"';
        }
        return $prefix;
    }

}

class Form{
    static $pears=array();
    
    static $form;
    static $formInputs=array();
    
    /*****  Below Only For PEAR::QuickForm  *****/
    
    function create( $name='frm', $method='post' , $action='' ){
        if( CACHE ){
            //APP::$dataCache->setLifeTime(300);
            //$form = APP::$dataCache->call( 'Form::createFormObject', $name, $method, $action );
            /* 無法Cache: unserialize failed */
            $form = Form::createFormObject( $name, $method, $action );
        }else{
            $form = Form::createFormObject( $name, $method, $action );
        }
        return $form;
    }
    function createFormObject( $name, $method, $action ){
        /*
        LoadVendors("formbuilder/class.form");
        
        $form = new FormBuilder($name);
        $form->setAttributes(array(
            "method" => $method,
            "action" => $action,
            "jsIncludesPath" => WEBROOT .'vendors/formbuilder/includes/',
        ));
        */
        APP::load('pear', 'HTML/QuickForm');
        APP::load('pear', 'HTML/QuickForm/advmultiselect');
        
        $form=new HTML_QuickForm($name, $method, $action );
        return $form;
    }
    function buttons( $hasName=true ){
        $buttons=array();
        if( $hasName ){
            $buttons[] = &HTML_QuickForm::createElement('submit', 'commit', '送出', array('class'=>'submit-green'));
            $buttons[] = &HTML_QuickForm::createElement('reset' , '',       '重設', array('class'=>'submit-gray'));
            //取消鍵使用button不使用submit的原因，是因為QuickForm在送出時會進行require檢查，造成取消之前還要先填好必填欄位
            $buttons[] = &HTML_QuickForm::createElement('html', '<input type="hidden" name="" value="cancel" class="hidden-cancel">');
            $buttons[] = &HTML_QuickForm::createElement('button', '', '取消', array('class'=>'submit-gray','onclick'=>"$('.hidden-cancel').attr('name', 'cancel');this.form.submit();"));
            return $buttons;
        }
        $buttons[] = &HTML_QuickForm::createElement('submit', '', '送出', array('class'=>'submit-green'));
        $buttons[] = &HTML_QuickForm::createElement('reset' , '', '重設', array('class'=>'submit-gray'));
        $buttons[] = &HTML_QuickForm::createElement('html', '<input type="hidden" name="" value="cancel" class="hidden-cancel">');
        $buttons[] = &HTML_QuickForm::createElement('button', '', '取消', array('class'=>'submit-gray','onclick'=>"$('.hidden-cancel').attr('name', 'cancel');this.form.submit();"));
        return $buttons;
    }
    function buttonsNoReset( $hasName=true ){
        $buttons=array();
        if( $hasName ){
            $buttons[] = &HTML_QuickForm::createElement('submit', 'commit', '送出', array('class'=>'submit-green'));
            //取消鍵使用button不使用submit的原因，是因為QuickForm在送出時會進行require檢查，造成取消之前還要先填好必填欄位
            $buttons[] = &HTML_QuickForm::createElement('html', '<input type="hidden" name="" value="cancel" class="hidden-cancel">');
            $buttons[] = &HTML_QuickForm::createElement('button', '', '取消', array('class'=>'submit-gray','onclick'=>"$('.hidden-cancel').attr('name', 'cancel');this.form.submit();"));
            return $buttons;
        }
        $buttons[] = &HTML_QuickForm::createElement('submit', '', '送出', array('class'=>'submit-green'));
        $buttons[] = &HTML_QuickForm::createElement('html', '<input type="hidden" name="" value="cancel" class="hidden-cancel">');
        $buttons[] = &HTML_QuickForm::createElement('button', '', '取消', array('class'=>'submit-gray','onclick'=>"$('.hidden-cancel').attr('name', 'cancel');this.form.submit();"));
        return $buttons;
    }
    function buttonsNoCancel( $hasName=true ){
        $buttons=array();
        if( $hasName ){
            $buttons[] = &HTML_QuickForm::createElement('submit', 'commit', '送出', array('class'=>'submit-green'));
            $buttons[] = &HTML_QuickForm::createElement('reset' , '',       '重設', array('class'=>'submit-gray'));
            return $buttons;
        }
        $buttons[] = &HTML_QuickForm::createElement('submit', '', '送出', array('class'=>'submit-green'));
        $buttons[] = &HTML_QuickForm::createElement('reset' , '', '重設', array('class'=>'submit-gray'));
        return $buttons;
    }
    function buttonsSubmitOnly( $hasName=true ){
        $buttons=array();
        if( $hasName ){
            $buttons[] = &HTML_QuickForm::createElement('submit', 'commit', '送出', array('class'=>'submit-green'));
            return $buttons;
        }
        $buttons[] = &HTML_QuickForm::createElement('submit', '', '送出', array('class'=>'submit-green'));
        return $buttons;
    }
    function buttonsSearchForm( $hasName=true ){
        $buttons=array();
        if( $hasName ){
            $buttons[] = &HTML_QuickForm::createElement('submit', 'commit', '送出', array('class'=>'submit-green'));
            $buttons[] = &HTML_QuickForm::createElement('reset' , '',       '重設', array('class'=>'submit-gray'));
            $buttons[] = &HTML_QuickForm::createElement('button', '',       '清除', array('class'=>'submit-gray', 'onclick'=>"javascript: former.clear('#'+this.form.id)"));
            return $buttons;
        }
        $buttons[] = &HTML_QuickForm::createElement('submit', '', '送出', array('class'=>'submit-green'));
        $buttons[] = &HTML_QuickForm::createElement('reset' , '', '重設', array('class'=>'submit-gray'));
        $buttons[] = &HTML_QuickForm::createElement('button', '', '清除', array('class'=>'submit-gray', 'onclick'=>"javascript: former.clear('#'+this.form.id)"));
        return $buttons;
    }
    
    /*****  Below Only For PEAR::QuickForm Renderer  *****/
    
    function getHtml( $form , $template='default' ){
        marktime('AppExecute', 'Start Renderring <span style="color:orange;">Form::getHTML( form_object, '.$template.' )</span>' );
        APP::load('pear', 'HTML/QuickForm/Renderer/Default');
        
        $method='_'.$template;
        if( method_exists( 'Form' , $method ) ){
            $renderer = self::$method();
        }
        
        $form->accept($renderer);
        
        $html=$renderer->toHtml();
        marktime('AppExecute', '<span style="color:orange;">Form::getHTML( form_object, '.$template.' )</span> Rendered' );
        return $html;
    }
    function getHtmlCode( $form , $template='default' ){
        $html='';
        $html.='<textarea style="width:700px;height:400px;">';
        $html.=htmlspecialchars( self::getHtml($form) );
        $html.='</textarea>';
        return $html;
    }
    function _privileges(){
        $renderer = new HTML_QuickForm_Renderer_Default();
        
        $headerTemplate='
                <div class="module">
                     <h2><span>{header}</span></h2>
                        
                         <div class="module-body">'."\n";
        $formTemplate = '
                     <form{attributes}>
                     {hidden}
                     {content}
                         </div> <!-- End .module-body -->
                     </form>
                </div>  <!-- End .module -->'."\n";
        $elementTemplate = '
                        <div style="float:left;height:30px;white-space:nowrap;">
                            <!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required -->
                            {label}
                            {element}
                            <!-- BEGIN error --><span class="notification-input ni-error">{error}</span><!-- END error -->
                            &nbsp;&nbsp;&nbsp;&nbsp;
                        </div>'."\n";
        $groupTemplate = '
                        <fieldset>
                            <legend><!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required -->
                            {label}</legend>
                            <ul>
                                <li>{element}</li>
                            </ul>
                            <!-- BEGIN error --><span class="notification-input ni-error">{error}</span><!-- END error -->
                        </fieldset>'."\n";
        $requiredNoteTemplate = "{requiredNote}";
        
        $renderer->setElementTemplate($elementTemplate);
        $renderer->setElementTemplate($groupTemplate, 'radio');
        $renderer->setElementTemplate($groupTemplate, 'checkbox');
        $renderer->setFormTemplate($formTemplate);
        $renderer->setHeaderTemplate($headerTemplate);
        $renderer->setRequiredNoteTemplate($requiredNoteTemplate);
        
        return $renderer;
    }
    function _search(){
        $renderer = new HTML_QuickForm_Renderer_Default();
        
        $headerTemplate=''."\n";
        $formTemplate = '
                     <form{attributes}>
                     {hidden}
                     {content}
                     </form>
                 <!-- End .module -->'."\n";
        $elementTemplate = '
                        <div style="float:left;height:30px;white-space:nowrap;">
                            <!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required -->
                            {label}
                            {element}
                            <!-- BEGIN error --><span class="notification-input ni-error">{error}</span><!-- END error -->
                            &nbsp;&nbsp;&nbsp;&nbsp;
                        </div>'."\n";
        $groupTemplate = '
                        <fieldset>
                            <legend><!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required -->
                            {label}</legend>
                            <ul>
                                <li>{element}</li>
                            </ul>
                            <!-- BEGIN error --><span class="notification-input ni-error">{error}</span><!-- END error -->
                        </fieldset>'."\n";
        $requiredNoteTemplate = "{requiredNote}";
        
        $renderer->setElementTemplate($elementTemplate);
        $renderer->setElementTemplate($groupTemplate, 'radio');
        $renderer->setElementTemplate($groupTemplate, 'checkbox');
        $renderer->setFormTemplate($formTemplate);
        $renderer->setHeaderTemplate($headerTemplate);
        $renderer->setRequiredNoteTemplate($requiredNoteTemplate);
        
        return $renderer;
    }
    function _rollcalls(){
        $renderer = new HTML_QuickForm_Renderer_Default();
        
        $headerTemplate='
                    <div class="module">
                    <h2><span>{header}</span></h2>
                    
                        <div class="module-body">'."\n";
        $formTemplate = '
                    <form{attributes}>
                    {hidden}
                    {content}
                        </div> <!-- End .module-body -->
                    </div>  <!-- End .module -->
                </form>
                '."\n";
        $elementTemplate = '
                            <!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required -->
                            {element}
                            <!-- BEGIN error --><span class="notification-input ni-error">{error}</span><!-- END error -->
                        '."\n";
        $groupTemplate = '
                        <fieldset>
                            <legend><!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required -->
                            {label}</legend>
                            <ul>
                                <li> {element} </li>
                            </ul>
                            <!-- BEGIN error --><span class="notification-input ni-error">{error}</span><!-- END error -->
                        </fieldset>'."\n";
        $requiredNoteTemplate = "{requiredNote}";
        
        $renderer->setElementTemplate($elementTemplate);
        $renderer->setElementTemplate($groupTemplate, 'radio');
        $renderer->setElementTemplate($groupTemplate, 'checkbox');
        //$renderer->setGroupTemplate('{content}', '');
        //$renderer->setGroupElementTemplate(' {label} {element}', '');
        $renderer->setFormTemplate($formTemplate);
        $renderer->setHeaderTemplate($headerTemplate);
        $renderer->setRequiredNoteTemplate($requiredNoteTemplate);
        
        return $renderer;
    }
    function _default(){
        $renderer = new HTML_QuickForm_Renderer_Default();
        
        $headerTemplate='
                <div class="module">
                    <h2><span>{header}</span></h2>
                        
                        <div class="module-table-body">
                        <table>'."\n";
        $formTemplate = '
                    <form{attributes}>
                    {hidden}
                    {content}
                        </table>
                        </div> <!-- End .module-body -->
                    </form>
                </div>  <!-- End .module -->'."\n";
        $elementTemplate = '
                        <tr style="border-bottom:1px solid #ccc;border-top:1px solid #ccc;">
                            <td style="vertical-align:top;">
                            <!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required -->
                            {label}
                            </td>
                            <td>
                            {element}
                            <!-- BEGIN error --><span class="notification-input ni-error">{error}</span><!-- END error -->
                            </td>
                        </tr>'."\n";
        $groupTemplate = '
                        <fieldset>
                            <legend><!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required -->
                            {label}</legend>
                            <ul>
                                <li> {element} </li>
                            </ul>
                            <!-- BEGIN error --><span class="notification-input ni-error">{error}</span><!-- END error -->
                        </fieldset>'."\n";
        $requiredNoteTemplate = " {requiredNote} ";
        
        $renderer->setElementTemplate($elementTemplate);
        $renderer->setElementTemplate($groupTemplate, 'radio');
        $renderer->setElementTemplate($groupTemplate, 'checkbox');
        //$renderer->setGroupTemplate('{content}', '');
        //$renderer->setGroupElementTemplate(' {label} {element}', '');
        $renderer->setFormTemplate($formTemplate);
        $renderer->setHeaderTemplate($headerTemplate);
        $renderer->setRequiredNoteTemplate($requiredNoteTemplate);
        
        return $renderer;
    }
}
?>