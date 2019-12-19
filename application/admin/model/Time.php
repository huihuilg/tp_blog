<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/18
 * Time: 9:49
 */

namespace app\admin\model;


use think\Model;
use think\model\concern\SoftDelete;

class Time extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
}