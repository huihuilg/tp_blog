<?php
/**
 * Created by PhpStorm.
 * User: hui
 * Date: 2019/1/25
 * Time: 20:53
 */

namespace app\admin\model;


use think\Model;
use think\model\concern\SoftDelete;

class Banner extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
}