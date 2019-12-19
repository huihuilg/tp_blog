<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/15
 * Time: 17:01
 */

namespace app\admin\model;


use think\Model;
use think\model\concern\SoftDelete;

class User extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
}