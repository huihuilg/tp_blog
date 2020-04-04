<?php
namespace app\index\controller;

use app\admin\model\Banner;
use app\admin\model\Like;
use app\admin\model\User;
use think\Controller;
use think\Request;

class Index extends Controller
{

    const VERSION = "2.0";
    const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
    const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
    const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";
    const USER_INFO = 'https://graph.qq.com/user/get_user_info?access_token=%s&oauth_consumer_key=%s&openid=%s';
    private $APP_ID = 101544609;
    private $APP_Key = '778dcbddc5147be3a33cc1f17a290d6d';
    private $callback = 'http://sunny.huihuil.cn/index/index/qqaction';

//    protected $middleware = [
//        'Auth' 	=> ['except' 	=> ['index'] ],
//        'Hello' => ['only' 	=> ['index'] ],
//    ];
    public function test(Request $request)
    {
//        header("HTTP/1.1 201 Created");

    }

    /**
     * qq登录回调
     */
    public function qqLogin()
    {
        $appid = $this->APP_ID;
        $callback = $this->callback;
        $scope = 'get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo,check_page_fans,add_t,add_pic_t,del_t,get_repost_list,get_info,get_other_info,get_fanslist,get_idolist,add_idol,del_idol,get_tenpay_addr';
        //-------生成唯一随机串防CSRF攻击
        $state = md5(uniqid(rand(), TRUE));
        session('state',$state) ;
        //-------构造请求参数列表
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $appid,
            "redirect_uri" => $callback,
            "state" => $state,
            "scope" => $scope
        );
        $login_url =  $this->combineURL(self::GET_AUTH_CODE_URL, $keysArr);
        header("Location:$login_url");
    }
    public function qqaction(Request $request)
    {
        $state = session('state') ;;
        //--------验证state防止CSRF攻击
        if(!$state || $request->get('state') != $state){
            echo 'state 验证错误';exit();
        }

        //-------请求参数列表
        $keysArr = array(
            "grant_type" => "authorization_code",
            "client_id" => $this->APP_ID,
            "redirect_uri" => urlencode($this->callback),
            "client_secret" => $this->APP_Key,
            "code" => $_GET['code']
        );
        //------构造请求access_token的url
        $token_url = $this->combineURL(self::GET_ACCESS_TOKEN_URL, $keysArr);
        $response = $this->get_contents($token_url);
        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);

            if(isset($msg->error)){
                echo '错误码：'.$msg->error.'错误描述：'.$msg->error_description;exit();
            }
        }
        echo $response;
        $params = array();
        parse_str($response, $params);
//        session('access_token',$params["access_token"]) ;
//        echo 'access_token是：'.$params["access_token"];
        $openId = $this->get_openid();
        $url = sprintf(self::USER_INFO,$params["access_token"],$this->APP_ID,$openId);
        $result = json_decode($this->get_contents($url),1);

        $user = new User();
        $isExist = $user->where(['user_name'=>$result['nickname']])->find();
        if($isExist){
            cookie('user_name',$isExist->user_name);
            session('uid',$isExist->id);
            $this->redirect('/');
        }else{
            $user->user_name = trim($result['nickname']);
            $user->sex = $result['gender_type'];
            $user->province = $result['province'];
            $user->city = $result['city'];
            $user->user_pic = $result['figureurl_2'];
            $user->age = $result['year'];
            $user->qq_openid = $openId;
            $user->save();
            cookie('user_name',$user->user_name);
//            cookie('uid',$isLogin->id);
            session('uid',$user->id);
            $this->redirect('/');
        }
//        dump($result);
    }
    public function get_openid(){

        //-------请求参数列表
        $keysArr = array(
            "access_token" => session('access_token')
        );

        $graph_url = $this->combineURL(self::GET_OPENID_URL, $keysArr);
        $response = $this->get_contents($graph_url);

        //--------检测错误是否发生
        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
        }

        $user = json_decode($response);
        if(isset($user->error)){
            echo '错误码：'.$user->error.'错误描述：'.$user->error_description;exit();
//            $this->error->showError($user->error, $user->error_description);
        }
        //------记录openid
//        session('openid',$user->openid);
        return $user->openid;
    }
    /**
     * combineURL
     * 拼接url
     * @param string $baseURL   基于的url
     * @param array  $keysArr   参数列表数组
     * @return string           返回拼接的url
     */
    public function combineURL($baseURL,$keysArr){
        $combined = $baseURL."?";
        $valueArr = array();

        foreach($keysArr as $key => $val){
            $valueArr[] = "$key=$val";
        }

        $keyStr = implode("&",$valueArr);
        $combined .= ($keyStr);

        return $combined;
    }

    /**
     * get_contents
     * 服务器通过get请求获得内容
     * @param string $url       请求的url,拼接后的
     * @return string           请求返回的内容
     */
    public function get_contents($url){
        if (ini_get("allow_url_fopen") == "1") {
            $response = file_get_contents($url);
        }else{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $response =  curl_exec($ch);
            curl_close($ch);
        }

        //-------请求为空
        if(empty($response)){
            $this->error->showError("50001");
        }

        return $response;
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
