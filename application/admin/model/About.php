<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/15
 * Time: 19:24
 */

namespace app\admin\model;


use think\Model;
use think\model\concern\SoftDelete;

class About extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
}