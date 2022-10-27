<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // Method 1
        // 如果不被允许的路由
        if ($exception instanceof MethodNotAllowedHttpException || $exception instanceof NotFoundHttpException)
        {
            if (!($request->ajax() || $request->wantsJson()))
            {
                $request_str = request()->path();
                if(strpos($request_str, "admin/") === 0)
                {
                    return redirect('/admin/404');
//                    return response()->view('admin.errors.404');
                }
                else if(strpos($request_str, "staff/") === 0)
                {
                    return redirect('/staff/404');
//                    return response()->view('admin.errors.404');
                }
                else return response()->view('frontend.errors.404');
            }
        }

        return parent::render($request, $exception);

//        // Method 2
//        switch($exception)
//        {
//            //使用类型运算符 instanceof 判断异常(实例)是否为 ModelNotFoundException
//            case ($exception instanceof ModelNotFoundException):
//                //进行异常处理
//                return $this->renderException($exception);
//                break;
//
//            default:
//                return parent::render($request, $exception);
//        }
    }

    //处理异常
    protected function renderException($e)
    {

        switch ($e){

            case ($e instanceof ModelNotFoundException):

                //自定义处理异常，此处我们返回一个404页面
                return view('frontend.errors.404');
                break;

            default:

                //如果异常非ModelNotFoundException，我们返回laravel默认的错误页面
                return (new SymfonyDisplayer(config('app.debug')))
                    ->createResponse($e);

        }

    }

}
