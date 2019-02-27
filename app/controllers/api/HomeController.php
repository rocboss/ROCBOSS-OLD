<?php
namespace api;

use BaseController;
use model\UserModel;

/**
 * HomeController
 */
class HomeController extends BaseController
{
    /**
     * This is a demo method.
     * 
     * @method index
     * @return array
     */
    protected function index()
    {
        // Add Log
        app()->log()->info('IndexLog', [
            'data' => 'index',
        ]);

        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => 'version: '.\Batio::VERSION,
        ];
    }

    /**
     * This is a demo method for model.
     *
     * @method test
     * @return array
     */
    protected function test()
    {
        // You should configure your database and create a table first.
        $userModel = new UserModel();
        // Get records
        $records = $userModel->dump([
            'id[>]' => 1,
            'LIMIT' => [0, 10],
        ]);

        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => [
                'records' => $records,
                'token' => (string) \Auth::getToken('12'),
            ],
        ];
    }

    /**
     * This is a demo method for middleware of authentication.
     * 
     * @method user
     * @return array
     */
    protected function user()
    {
        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => [
                'uid' => 1,
                'user_name' => 'Jack',
                'user_age' => 18,
            ]
        ];
    }

    /**
     * DEMO JOB
     *
     * @return array
     */
    protected function demoJob()
    {
        return [
            'time' => time(),
        ];
    }
}
