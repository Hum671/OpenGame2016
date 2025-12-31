<?php
namespace app\http\middleware;

use app\lib\exception\TokenException;

class Auth
{
    // 接口中间件
    public function handle($request, \Closure $next)
    {
        if ($request->url() == '/api/datas/list') {
            if (!empty($request->header()['token'])) {
                $id = parseJwt($request->header()['token']);
                $request->userId = $id;
            }
            return $next($request);
        }
        if (empty($request->header()['token'])) {
            throw new TokenException();
        }

        $id = parseJwt($request->header()['token']);
        if ($id == 0) {
            throw new TokenException();
        }

        $request->userId = $id;
        return $next($request);
    }
}
