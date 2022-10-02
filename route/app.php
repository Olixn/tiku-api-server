<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::group('api',function () {
    Route::group('v1',function () {
        Route::get('cron','admin/cron');
        Route::get('auth','auth/index');
        Route::get('tips','chaoxing/tips');
        Route::get('enc','chaoxing/enc');
        Route::post('cx','chaoxing/queryAnswer');
        Route::post('save_one','chaoxing/saveOneQuestion');
        Route::post('save','chaoxing/saveAllQuestion');
    });
});


Route::group('out',function () {
    Route::post('add','admin/save');
    Route::get('get','admin/outPut');
    Route::resource("active",'active');
});