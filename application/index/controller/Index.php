<?php
namespace app\index\controller;

use app\admin\model\Banner;
use app\admin\model\Like;
use app\admin\model\User;
use think\Controller;
use think\Request;

class Index extends Controller
{
//    protected $middleware = [
//        'Auth' 	=> ['except' 	=> ['index'] ],
//        'Hello' => ['only' 	=> ['index'] ],
//    ];
  	
  	public function test(Request $request)
    {
        header('location:http://www.baidu.com');
//        header("HTTP/1.1 201 Created");
//        file_put_contents("./test.txt",var_export($request->param(),true));
//        exit();
    }

    public function index(Request $request)
    {
        $learn = new \app\admin\model\Learn();
        $life = new \app\admin\model\Life();
        //每页数据
        if(!empty(input('get.num'))){
            $num = input('get.num');
        }else{
            $num = 20;
        }
        $page = max(1,input('get.page'));
        $limit = ($page-1) * $num;

        $where['l.status'] = 1;

        $learnInfo = $learn->alias('l')->field('l.id,l.title,l.m_id,l.content,l.create_time,u.nickname,l.pic_url,l.like_num,l.page_view')->join('blog_user u','l.uid=u.id')->where($where)->order('l.create_time desc')->limit($limit,$num)->select()->toArray();
        $counts = $learn->where(['status'=>1])->count();
        foreach($learnInfo as &$val){
            $val['create_time'] = date('Y-m-d',strtotime($val['create_time']));
        }
        //轮播
        $banner = new Banner();
        $banners = $banner->where(['status'=>1])->select()->toArray();

        //置顶
        $learnTop= $learn->field('id,pic_url,m_id,title,create_time,page_view,m_id')->where(['status'=>1,'is_top'=>1])->order(['page_view'=>'desc'])->select()->toArray();
        $lifeTop = $life->field('id,pic_url,m_id,title,create_time,page_view')->where(['status'=>1,'is_top'=>1])->order(['page_view'=>'desc'])->select()->toArray();
        $top = array_merge($learnTop,$lifeTop);
        //特别推荐
        $learnEsp = $learn->field('id,pic_url,title,m_id')->where(['especially_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $lifeEsp = $life->field('id,pic_url,title,m_id')->where(['especially_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $result = array_merge($learnEsp,$lifeEsp);
        //推荐
        $learnRec = $learn->field('id,pic_url,title,create_time,m_id')->where(['is_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $lifeRec = $life->field('id,pic_url,title,create_time,m_id')->where(['is_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $recommend = array_merge($learnRec,$lifeRec);
        //点击排行
        $learnClick= $learn->field('id,pic_url,title,create_time,page_view,m_id')->where(['status'=>1])->order(['page_view'=>'desc'])->limit(1,3)->select()->toArray();
        $lifeClick = $life->field('id,pic_url,title,create_time,page_view,m_id')->where(['status'=>1])->order(['page_view'=>'desc'])->limit(1,3)->select()->toArray();
        $clickTop = array_merge($learnClick,$lifeClick);
        array_multisort(array_column($clickTop,'page_view'),SORT_DESC,$clickTop);
        $this->assign(['learnInfo'=>$learnInfo,'counts'=>$counts,'page'=>$page,'num'=>$num,'result'=>$result,'recommend'=>$recommend,'clickTop'=>$clickTop,'top'=>$top,'banner'=>$banners]);
        return $this->fetch();
    }


    /**
     * 学无止境内容
     */
    public function learnContent($id)
    {
        $learn = new \app\admin\model\Learn();
        //增加浏览量
        $learn->where(['id'=>$id,'status'=>1])->setInc('page_view',1);
        //数据查下
        $learnresult = $learn->alias('l')->field('l.*,u.nickname')->join('blog_user u','l.uid=u.id')->where(['l.status'=>1])->find($id);
        //上一篇
        $before = $learn->where([['id','<',$id],['status','=',1]])->find();
        //下一篇
        $next = $learn->where([['id','>',$id],['status','=',1]])->find();


        //特别推荐
        $life = new \app\admin\model\Life();
        $learnEsp = $learn->field('id,pic_url,title,m_id')->where(['especially_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $lifeEsp = $life->field('id,pic_url,title,m_id')->where(['especially_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $result = array_merge($learnEsp,$lifeEsp);
        //推荐
        $learnRec = $learn->field('id,pic_url,title,create_time,m_id')->where(['is_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $lifeRec = $life->field('id,pic_url,title,create_time,m_id')->where(['is_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $recommend = array_merge($learnRec,$lifeRec);
        //点击排行
        $learnClick= $learn->field('id,pic_url,title,create_time,page_view,m_id')->where(['status'=>1])->order(['page_view'=>'desc'])->limit(1,3)->select()->toArray();
        $lifeClick = $life->field('id,pic_url,title,create_time,page_view,m_id')->where(['status'=>1])->order(['page_view'=>'desc'])->limit(1,3)->select()->toArray();
        $clickTop = array_merge($learnClick,$lifeClick);
        array_multisort(array_column($clickTop,'page_view'),SORT_DESC,$clickTop);


        $this->assign(['learnresult'=>$learnresult,'before'=>$before,'next'=>$next,'result'=>$result,'recommend'=>$recommend,'clickTop'=>$clickTop]);
        return $this->fetch();
    }


    /**
     * 学无止境点赞
     */
    public function learnLike($id)
    {
        if(empty(session('uid',''))){
            return $this->error('请先登录');
        }
        $like = new Like();
        $isLike = $like->where(['uid'=>session('uid'),'m_id'=>2,'l_id'=>$id])->find();
        if(!empty($isLike)){
            return $this->error('请不要重复点赞');
        }
        $learn = new \app\admin\model\Learn();
        //增加点赞
        $learn->where(['id'=>$id,'status'=>1])->setInc('like_num',1);
        //保存
        $like->l_id = $id;
        $like->uid = session('uid');
        $like->m_id = 2;
        $like->save();
        //数据查询
        $learnresult = $learn->alias('l')->field('l.*,u.nickname')->join('blog_user u','l.uid=u.id')->where(['l.status'=>1])->find($id);
        //上一篇
        $before = $learn->where([['id','<',$id],['status','=',1]])->find();
        //下一篇
        $next = $learn->where([['id','>',$id],['status','=',1]])->find();


        //特别推荐
        $life = new \app\admin\model\Life();
        $learnEsp = $learn->field('id,pic_url,title,m_id')->where(['especially_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $lifeEsp = $life->field('id,pic_url,title,m_id')->where(['especially_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $result = array_merge($learnEsp,$lifeEsp);
        //推荐
        $learnRec = $learn->field('id,pic_url,title,create_time,m_id')->where(['is_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $lifeRec = $life->field('id,pic_url,title,create_time,m_id')->where(['is_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $recommend = array_merge($learnRec,$lifeRec);
        //点击排行
        $learnClick= $learn->field('id,pic_url,title,create_time,page_view,m_id')->where(['status'=>1])->order(['page_view'=>'desc'])->limit(1,3)->select()->toArray();
        $lifeClick = $life->field('id,pic_url,title,create_time,page_view,m_id')->where(['status'=>1])->order(['page_view'=>'desc'])->limit(1,3)->select()->toArray();
        $clickTop = array_merge($learnClick,$lifeClick);
        array_multisort(array_column($clickTop,'page_view'),SORT_DESC,$clickTop);


        $this->assign(['learnresult'=>$learnresult,'before'=>$before,'next'=>$next,'result'=>$result,'recommend'=>$recommend,'clickTop'=>$clickTop]);
        return $this->fetch('learn_content');
    }

    /**
     * 慢生活内容
     */
    public function lifeContent($id)
    {
        $life = new \app\admin\model\Life();
        //增加浏览量
        $life->where(['id'=>$id,'status'=>1])->setInc('page_view',1);
        //数据查下
        $liferesult = $life->alias('l')->field('l.*,u.nickname')->join('blog_user u','l.uid=u.id')->where(['l.status'=>1])->find($id);
        //上一篇
        $before = $life->where([['id','<',$id],['status','=',1]])->find();
        //下一篇
        $next = $life->where([['id','>',$id],['status','=',1]])->find();


        //特别推荐
        $learn = new \app\admin\model\Learn();
        $learnEsp = $learn->field('id,pic_url,title,m_id')->where(['especially_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $lifeEsp = $life->field('id,pic_url,title,m_id')->where(['especially_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $result = array_merge($learnEsp,$lifeEsp);
        //推荐
        $learnRec = $learn->field('id,pic_url,title,create_time,m_id')->where(['is_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $lifeRec = $life->field('id,pic_url,title,create_time,m_id')->where(['is_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $recommend = array_merge($learnRec,$lifeRec);
        //点击排行
        $learnClick= $learn->field('id,pic_url,title,create_time,page_view,m_id')->where(['status'=>1])->order(['page_view'=>'desc'])->limit(1,3)->select()->toArray();
        $lifeClick = $life->field('id,pic_url,title,create_time,page_view,m_id')->where(['status'=>1])->order(['page_view'=>'desc'])->limit(1,3)->select()->toArray();
        $clickTop = array_merge($learnClick,$lifeClick);
        array_multisort(array_column($clickTop,'page_view'),SORT_DESC,$clickTop);


        $this->assign(['liferesult'=>$liferesult,'before'=>$before,'next'=>$next,'result'=>$result,'recommend'=>$recommend,'clickTop'=>$clickTop]);
        return $this->fetch();
    }

    /**
     * 慢生活点赞
     */
    public function lifeLike($id)
    {
        if(empty(session('uid'))){
            return $this->error('请先登录');
        }
        $like = new Like();
        $isLike = $like->where(['uid'=>session('uid'),'m_id'=>3,'l_id'=>$id])->find();
        if(!empty($isLike)){
            return $this->error('请不要重复点赞');
        }
        $life = new \app\admin\model\Life();
        //增加点赞
        $life->where(['id'=>$id,'status'=>1])->setInc('like_num',1);
        //保存
        $like->l_id = $id;
        $like->uid = session('uid');
        $like->m_id = 3;
        $like->save();
        //数据查询
        $liferesult = $life->alias('l')->field('l.*,u.nickname')->join('blog_user u','l.uid=u.id')->where(['l.status'=>1])->find($id);
        //上一篇
        $before = $life->where([['id','<',$id],['status','=',1]])->find();
        //下一篇
        $next = $life->where([['id','>',$id],['status','=',1]])->find();


        //特别推荐
        $learn = new \app\admin\model\Learn();
        $learnEsp = $learn->field('id,pic_url,title,m_id')->where(['especially_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $lifeEsp = $life->field('id,pic_url,title,m_id')->where(['especially_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $result = array_merge($learnEsp,$lifeEsp);
        //推荐
        $learnRec = $learn->field('id,pic_url,title,create_time,m_id')->where(['is_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $lifeRec = $life->field('id,pic_url,title,create_time,m_id')->where(['is_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $recommend = array_merge($learnRec,$lifeRec);
        //点击排行
        $learnClick= $learn->field('id,pic_url,title,create_time,page_view,m_id')->where(['status'=>1])->order(['page_view'=>'desc'])->limit(1,3)->select()->toArray();
        $lifeClick = $life->field('id,pic_url,title,create_time,page_view,m_id')->where(['status'=>1])->order(['page_view'=>'desc'])->limit(1,3)->select()->toArray();
        $clickTop = array_merge($learnClick,$lifeClick);
        array_multisort(array_column($clickTop,'page_view'),SORT_DESC,$clickTop);


        $this->assign(['liferesult'=>$liferesult,'before'=>$before,'next'=>$next,'result'=>$result,'recommend'=>$recommend,'clickTop'=>$clickTop]);
        return $this->fetch('life_content');
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 搜索
     */
    public function search(Request $request)
    {
        $title = $request->param('keyboard');
        $learn = new \app\admin\model\Learn();
        //每页数据
        if(!empty(input('get.num'))){
            $num = input('get.num');
        }else{
            $num = 20;
        }
        $page = max(1,input('get.page'));
        $limit = ($page-1) * $num;
        if(empty($title)){
            $this->error('请输入关键字搜索');
        }
        $learnInfo = $learn->alias('l')->field('l.id,l.uid,l.title,l.m_id,l.content,l.create_time,l.pic_url,l.like_num,l.page_view')->where(['l.status'=>1])->where('l.title','like','%'.$title.'%')->order('l.create_time desc')->limit($limit,$num)->select()->toArray();
        $counts1 = $learn->where(['status'=>1])->count();

        foreach($learnInfo as &$val){
            $val['create_time'] = date('Y-m-d',strtotime($val['create_time']));
            $val['nickname'] = User::find($val['uid'])->nickname;
        }

        $life = new \app\admin\model\Life();
        $lifeInfo = $life->alias('l')->field('l.id,l.uid,l.title,l.m_id,l.content,l.create_time,l.pic_url,l.like_num,l.page_view')->join('blog_user u','l.uid=u.id')->where(['l.status'=>1])->where('title','like','%'.$title.'%')->order('l.create_time desc')->limit($limit,$num)->select()->toArray();
        $counts2 = $life->where(['status'=>1])->count();
        foreach($lifeInfo as &$val){
            $val['create_time'] = date('Y-m-d',strtotime($val['create_time']));
            $val['nickname'] = User::find($val['uid'])->nickname;
        }
        $learnInfo = array_merge($learnInfo,$lifeInfo);
        $counts = $counts1 + $counts2;

        //特别推荐
        $life = new \app\admin\model\Life();
        $learnEsp = $learn->field('id,pic_url,title,m_id')->where(['especially_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $lifeEsp = $life->field('id,pic_url,title,m_id')->where(['especially_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $result = array_merge($learnEsp,$lifeEsp);
        //推荐
        $learnRec = $learn->field('id,pic_url,title,create_time,m_id')->where(['is_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $lifeRec = $life->field('id,pic_url,title,create_time,m_id')->where(['is_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $recommend = array_merge($learnRec,$lifeRec);
        //点击排行
        $learnClick= $learn->field('id,pic_url,title,create_time,page_view,m_id')->where(['status'=>1])->order(['page_view'=>'desc'])->limit(1,3)->select()->toArray();
        $lifeClick = $life->field('id,pic_url,title,create_time,page_view,m_id')->where(['status'=>1])->order(['page_view'=>'desc'])->limit(1,3)->select()->toArray();
        $clickTop = array_merge($learnClick,$lifeClick);
        array_multisort(array_column($clickTop,'page_view'),SORT_DESC,$clickTop);
        $this->assign(['learnInfo'=>$learnInfo,'counts'=>$counts,'page'=>$page,'num'=>$num,'result'=>$result,'recommend'=>$recommend,'clickTop'=>$clickTop]);
        return $this->fetch();
    }

}
