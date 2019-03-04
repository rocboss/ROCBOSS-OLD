<?php
/**
 * The authentication middleware for web module.
 * @author ROC <i@rocs.me>
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * Run
     *
     * @param array $params
     * @return mixed
     */
    public function run(array $params)
    {
        $force = isset($params[0]) ? $params[0] : true;
        $headers = getAllHeader();

        // If pass, return true.
        if (!empty($headers['x-authorization'])) {
            $verify = Auth::verify($headers['x-authorization']);
            if ($verify === 1) {
                // 二次校验用户 claimToken
                if (!empty(app()->get('claimToken'))) {
                    $user = (new \service\UserService())->getUserBaseInfo(app()->get('uid'));
                    if ($user && $user['claim_token'] == app()->get('claimToken')) {
                        app()->set('user', $user);

                        return true;
                    }

                    // Otherwise halt.
                    app()->halt([
                        'code' => 401,
                        'msg'  => '[401 Unauthorized]. You are forced to go offline.'
                    ]);

                    app()->stop();
                }
                return true;
            }
        }
        if ($force) {
            // Otherwise halt.
            app()->halt([
                'code' => 401,
                'msg'  => '[401 Unauthorized].'.app()->get('auth.error')
            ]);

            app()->stop();
        }
    }
}
