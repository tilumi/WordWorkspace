<?php
class Blocks{
    function mainTitle($mainTitle){
        $html ='';
        $html.='<h1 class="pageTitle">'.$mainTitle.'</h1>';
        return $html;
    }
    function pageInfo( $pageID, $pageRows, $currRows, $totalItems ){
        $html ='';
        $html.='第<span>'.$pageID.'</span>頁 [ 一頁<span>'.$pageRows.'</span>筆，';
        $html.='本頁<span>'.$currRows.'</span>筆，';
        $html.='共<span>'.ceil($totalItems/$pageRows).'</span>頁/<span>'.$totalItems.'</span>筆 ]';
        return $html;
    }
    function itemsChecker(){
        $html ='';
        $html.='<span>選取: </span> ';
        $html.='<span><a href="javascript: void(0);" onclick="javascript: checker.all();">全選</a></span> ';                
        $html.='<span><a href="javascript: void(0);" onclick="javascript: checker.none();">清除</a></span> ';
        $html.='<span><a href="javascript: void(0);" onclick="javascript: checker.reverse();">反轉</a></span> ';
        return $html;
    }
    function searchInfo($searchInfo){
        $html ='';
        $html.='檢索範圍: &nbsp;<span class="search-info">';
        if( count($searchInfo)>0 ){
            $html.=implode(', ', $searchInfo).'的內容';
        }else{
            $html.='全部';
        } 
        $html.='</span>';
        return $html;
    }
    function render( $_pageID, $totalItems, $params=array() ){
        APP::load('pear', 'Pager');
        
        $perPage=PAGEROWS;
        if( isset($params['perPage']) ){
            $perPage=$params['perPage'];
            unset($params['perPage']);
        }
        $pageID=1;
        if( is_numeric($_pageID) ){
            $pageID=(int)$_pageID;
        }
    	$_default = array(
            'mode' => 'Sliding',
            'perPage' => $perPage,
            'delta' => 2,
            'totalItems' => $totalItems,
            'httpMethod' => 'GET',
            'currentPage' => $pageID,
/*
            'linkClass' => 'pager',
            'altFirst' => 'First',
            'altPrev '=> 'Prev',
            'altNext' => 'Next',
            'altLast' => 'Last',
            'separator' => '<span>|</span>',
            'spacesBeforeSeparator' => 1,
            'spacesAfterSeparator' => 1,
            'useSessions' => false,
            'firstPagePre'     => '',
            'firstPagePost' => '',
            'lastPagePre' => '',
            'lastPagePost' => '',
            'firstPageText' => '<span><img src="'.layout_url('admin', '/images/arrow-stop-180-small.gif').'" alt="First" width="12" height="9"> First</span>',
            'lastPageText' => '<span><img src="'.layout_url('admin', '/images/arrow-180-small.gif').'" alt="Previous" width="12" height="9"> Prev</span>',
            'prevImg' => '<span>Next <img src="'.layout_url('admin', '/images/arrow-000-small.gif').'" alt="Next" width="12" height="9"></span>',
            'nextImg' => '<span>Last <img src="'.layout_url('admin', '/images/arrow-stop-000-small.gif').'" alt="Last" width="12" height="9"></span>',
            'altPage' => 'Page',
            'clearIfVoid' => true,
            'append' => false,
            'path' => '',
            'fileName' => ME.'?pageID=%d',
            'urlVar' => '',
*/
        );
        $params = $params + $_default;
        $pager = Pager::factory($params);
        
        //Calculate Template
        $template='';
        //$url=ME.'?pageID=%d';
        $url=url( array('params'=>array('page'=>'%d') ) );
        
        $first=''; $prev='';
        if( ! $pager->isFirstPage() ){
            /**** First ****/
            $firstText='第一頁';
            $first='<a href="'.sprintf($url,1).'" class="button"><span><img src="'.layout_url('admin', '/images/arrow-stop-180-small.gif').'" alt="First" width="12" height="9"> '.$firstText.'</span></a>'."\n";
            /**** Previous ****/
            $prevText='上一頁';
            $prevPage = $pager->getPreviousPageID();
            if( $prevPage!==false ){
                $prev='<a href="'.sprintf($url,$prevPage).'" class="button"><span><img src="'.layout_url('admin', '/images/arrow-180-small.gif').'" alt="Previous" width="12" height="9"> '.$prevText.'</span></a>'."\n";
            }
        }
        $next=''; $last='';
        if( ! $pager->isLastPage() ){
            /**** Last ****/
            $lastText='最後一頁';
            $lastPage=$pager->numPages();
            $last='<a href="'.sprintf($url,$lastPage).'" class="button last"><span>'.$lastText.' <img src="'.layout_url('admin', '/images/arrow-stop-000-small.gif').'" alt="Last" width="12" height="9"></span></a>'."\n";
            /**** Next ****/
            $nextText='下一頁';
            $nextPage = $pager->getNextPageID();
            if( $nextPage!==false ){
                $next='<a href="'.sprintf($url,$nextPage).'" class="button"><span>'.$nextText.' <img src="'.layout_url('admin', '/images/arrow-000-small.gif').'" alt="Next" width="12" height="9"></span></a>'."\n";
            }
        }
        $maxPage = $pager->numPages();
        $currentPage = $pager->getCurrentPageID();
        $deltaRange = $pager->getPageRangeByPageId( $currentPage );
        $middlePage = array();
        for( $i=$deltaRange[0];$i<=$deltaRange[1];$i++ ){
            if( $i==$currentPage ){
                $middlePage[] = '<span class="current">'.$i.'</span>'."\n";
                continue;
            }
            $middlePage[] = '<a href="'.sprintf($url,$i).'">'.$i.'</a>'."\n";
        }
        $middle  = '<div class="numbers">'."\n";
        $middle .= '<span>Page:</span>'."\n";
        if( $deltaRange[0] > 1 ){
            $middle .= '<a href="'.sprintf($url,1).'">1</a> &nbsp;...&nbsp; '."\n";
        }
        $middle .= implode( '<span>|</span>'."\n" , $middlePage );
        if( $deltaRange[1] < $maxPage ){
            $middle .= ' &nbsp;...&nbsp; <a href="'.sprintf($url,$maxPage).'">'.$maxPage.'</a>'."\n";
        }
        $middle .= '</div>'."\n";
        
        $template  = $first;
        $template .= $prev;
        $template .= $middle;
        $template .= $next;
        $template .= $last;
        
        return $template;
    }
    function getDeltaRange( $currentPage , $delta , $pageRange ){
        //計算前後位移量
        $forwardItems = floor( ($delta-1)/2 );
        $backwardItems = ceil( ($delta-1)/2 );
        
        //計算currentPage前面的空間
        $forwardSpace = $currentPage-$pageRange[0];
        $forwardLack = ( $forwardSpace < $forwardItems );
        //計算currentPage後面的空間
        $backwardSpace = $pageRange[1]-$currentPage;
        $backwardLack = ( $backwardSpace < $backwardItems );
        //如果前後空間都不夠，pageRange就是整體Range
        if( $forwardLack && $backwardLack ){
            return $pageRange;
        }
        //若前方空間不足
        if( $forwardLack ){
            //計算溢出量，加到 backwordItems 中
            $overflow = $forwardItems - $forwardSpace ;
            $backwardItems += $overflow ;
            $endPage = $currentPage + $backwardItems;
            if( $endPage > $pageRange[1] ){
                $endPage = $pageRange[1];
            }
            return array( 1 , $endPage );
        }
        //若後方空間不足
        if( $backwardLack ){
            //計算溢出量，加到 forwordItems 中
            $overflow = $backwardItems - $backwardSpace ;
            $forwardItems += $overflow ;
            $startPage = $currentPage - $forwardItems;
            if( $startPage < $pageRange[0] ){
                $startPage = $pageRange[0];
            }
            return array( $startPage , $pageRange[1] );
        }
    }
}
?>