<?php
APP::$systemConfigs['Debug'] = 0;          //0 or 1，0關閉，1開發模式，顯示詳細的輔助除錯訊息
APP::$systemConfigs['Production'] = 1;     //0 or 1，0關閉，1產品模式，系統錯誤會以溫柔、包裝過的方式呈現
                                //                          若關閉，系統會中斷執行，並以完整錯誤訊息呈現
APP::$systemConfigs['Cache'] = 1;          //0 or 1，0關閉，1啟用快取，啟用或關閉快取
APP::$systemConfigs['Timeout'] = '+60 min';// Session Destoryed Time, 設定整體的Session消滅時間
APP::$systemConfigs['Pagerows'] = 15;      // 每頁顯示筆數
?>