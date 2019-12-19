<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/17
 * Time: 18:01
 */

namespace app\admin\model;


use think\Model;
use think\model\concern\SoftDelete;

class Life extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
}