<?php
/**
 * Created by PhpStorm.
 * Author: wgl
 * Time:2019-01-16 10:42
 */

namespace freamwork;

use freamwork\Log;

/**
 * 异常处理类
 * Class ExceptionHandler
 * @package freamwork
 */
class ExceptionHandler {

    /**
     * 启动
     */
    public static function run() {
        //程序执行时异常终止错误捕获处理函数注册
        register_shutdown_function('\freamwork\ExceptionHandler::fatalError');
        //错误捕获自定义处理函数注册
        set_error_handler('\freamwork\ExceptionHandler::appError');
        //异常捕获自定义处理函数注册
        set_exception_handler('\freamwork\ExceptionHandler::appException');
    }

    /**
     * 异常捕获
     * @param $exception
     * @throws \Exception
     */
    public static function appException($exception) {
        // 发送404信息
        header('HTTP/1.1 404 Not Found');
        header('Status:404 Not Found');
        self::log($exception);
        self::dump();
    }

    /**
     * 错误捕获
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @throws \Exception
     */
    public static function appError($errno, $errstr, $errfile, $errline) {
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                ob_end_clean();
                $errorStr = "$errstr " . $errfile . " 第 $errline 行.";
                Log::error("[$errno] " . $errorStr);
                self::dump();
                break;
            default:
                $errorStr = "[$errno] $errstr " . $errfile . " 第 $errline 行.";
                Log::error("[$errno] " . $errorStr);
                self::dump();
                break;
        }
    }

    /**
     * 异常终止
     * @throws \Exception
     */
    public static function fatalError() {
        if ($e = error_get_last()) {
            switch ($e['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    self::log($e);
                    self::dump();
                    break;
            }
        }
    }

    /**
     * 关闭调试模式下打印错误
     */
    public static function dump() {
        echo ":(-出现了错误";
    }

    /**
     * 记录日志
     * @param $exception
     * @throws \Exception
     */
    public static function log($exception) {
        Log::error($exception->getMessage()
            . ' File:' . $exception->getFile()
            . '(' . $exception->getLine() . ')'
            . "\n"
            . $exception->getTraceAsString());
    }

}