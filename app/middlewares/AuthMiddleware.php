<?php
/**
 * The authentication middleware for web module.
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
        $headers = getAllHeader();

        // If pass, return true.
        if (!empty($headers['x-authorization'])) {
            $verify = Auth::verify($headers['x-authorization']);
            if ($verify === 1) {
                return true;
            }
        }

        // Otherwise halt.
        app()->halt([
            'code' => 401,
            'msg'  => '[401 Unauthorized].'.app()->get('auth.error')
        ], 401);

        exit;
    }
}
