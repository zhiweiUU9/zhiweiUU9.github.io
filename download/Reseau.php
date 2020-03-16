<?php
namespace app\app\controller;

class Reseau extends Base
{
    //案件统计
    public function cageCount()
    {


        $userid = $this->adminUser;

        if($userid['user_type'] == 2 || $userid['user_type'] == 3 || $userid['user_type'] == 6 ||$userid['user_type'] == 7){
            //如果是镇级，判断是哪个镇
            $where['id'] = $userid['zhen_id'];

        }

        $zhen = ['索镇','唐山镇','马桥镇','果里镇','新城镇','田庄镇','荆家镇','起凤镇','少海街道'];
        for ($i = 0;$i<count($zhen);$i++){

            $where['name'] = $zhen[$i];

            $aa[] = db('village')->where($where)->value('id');

        }

        foreach ($aa as $k=>$v){
            if (!empty($v)){

                $arr[] = db('anjian')->where(['ssz'=>$v])->count();
            }else{
                $arr[] = 0;
            }
        }


        return setReturn(200,'查询成功',$arr);
    }

    //案件列表
    public function cageList()
    {
        $userid = $this->adminUser;
        if($userid['user_type'] == 2 || $userid['user_type'] == 3 || $userid['user_type'] == 6 ||$userid['user_type'] == 7){
            //如果是镇级，判断是哪个镇
            $where['ssz'] = $userid['zhen_id'];
        }

        $list = db('anjian')->field('id,sn,wtlx,dzms,create_time')->where($where)->order('id desc')->paginate(getlimit(input('get.')));
        $list = $list->toArray();

        foreach ($list['data'] as $k=>&$v){
            $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $v['wtlx'] = db('dict')->where(['label'=>$v['wtlx'],'type'=>'wtlx'])->value('value');
        }
        return setReturn(200,'查询成功',$list);
    }

    //督办件
    public function cageList2()
    {

        $userid = $this->adminUser['id'];
        $where['userid'] = $userid;
        $where = [];

        $data = db('anjian_guanzhu')->alias('g')
            ->field('a.id,a.create_time,a.sn,a.wtlx,a.dzms')
            ->join('ht_anjian a','a.sn = g.sn','right')
            ->join('ht_dict d','type=wtlx and d.label = a.wtlx','left')
            ->paginate(getlimit(input('get.')));

        $data = $data->toArray();


        foreach ($data['data'] as $l=>$b){
            $data['data'][$l]['create_time'] = date('Y-m-d H:i:s',$b['create_time']);
            $data['data'][$l]['wtlx'] = db('dict')->where(['label'=>$b['wtlx'],'type'=>'wtlx'])->value('value');
        }

        return setReturn(200,'查询成功',$data);
    }

    //超时件
    public function cageList3()
    {
        $user_type = $this->adminUser['user_type'];
        if ($user_type == 4){
            $where = [];
        }elseif ($user_type == 3){

            $where2['ssz'] = $this->adminUser['zhen_id'];
        }


        $max_time = db('anjian_log')->field('sn,create_time')->where($where)->select();

        $aa = [];

        foreach ($max_time as $k=>$v){

            if ($v['create_time'] < strtotime('+8 hour',$v['create_time'])){
                $aa[] = $v['sn'];
            }
        }

        $aa = array_unique($aa);
        if (!empty($aa)){
            $bb = [];
            foreach ($aa as $k=>$v){
                $bb[] = db('anjian')->field('id,sn,wtlx,dzms,create_time')->where(['sn'=>$v])->where($where2)->find();
            }
        }
        $bb =array_filter($bb);

        if (!empty($bb)){
            foreach ($bb as $a=>$s){
                $time = time()-(8*3600);

                $cs = $time-$s['create_time'];
                if ($cs <= 0){
                    $dd = '正常';
                }else{
                    $dd = '超时:'.time2string($cs);
                }

                $bb[$a]['create_time'] = date('Y-m-d H:i:s',$s['create_time']);
                $bb[$a]['cssj'] = $dd;
                $bb[$a]['wtlx'] = db('dict')->where(['type'=>'wtlx'])->value('value');
            }
        }

        return setReturn(200,'查询时间',$bb);

    }

    //案件详情
    public function cageDetails()
    {
        $id = input('get.id');
        $anjianInfo = db('anjian')->field('id,sn,wtly,wtlx,dlmc,xlmc,wtms,dzms,create_time')->where(['id'=>$id])->find();
        $anjianInfo['wtly'] = db('dict')->where(['label'=>$anjianInfo['wtly'],'type'=>'wtly'])->value('value');
        $anjianInfo['wtlx'] = db('dict')->where(['label'=>$anjianInfo['wtlx'],'type'=>'wtlx'])->value('value');
        $anjianInfo['dlmc'] = db('dict')->where(['label'=>$anjianInfo['dlmc'],'type'=>'big_type'])->value('value');
        $anjianInfo['xlmc'] = db('dict')->where(['label'=>$anjianInfo['xlmc'],'type'=>'small_type'])->value('value');
        $anjianInfo['state'] = db('anjian_log')->where(['sn'=>$anjianInfo['sn']])->value('stage');
        $anjianInfo['create_time'] = date('Y-m-d',$anjianInfo['create_time']);

        $aa = db('file')->field('filepath,create_time')->where(['pid'=>$anjianInfo['id'],'table'=>'anjian'])->select();
        if (!empty($aa)){
            foreach ($aa as $k=>$v){
                $aa[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            }
        }
        $ht =  ((int)$_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http') . '://';
        $ff = $ht . $_SERVER['HTTP_HOST'];
        if (!empty($aa)){
            foreach ($aa as $k=>&$v){
                $v['filepath'] = $ff.$v['filepath'];
            }
        }

        $anjianInfo['file'] = $aa;

        return setReturn(200,'查询成功',$anjianInfo);
    }

    //高发问题
    public function gaoFa(){
        if (isPost()){
            $arr = input('post.');

            $userid = $this->adminUser;
            if($userid['user_type'] == 2 || $userid['user_type'] == 3 || $userid['user_type'] == 6 ||$userid['user_type'] == 7){
                //如果是镇级，判断是哪个镇
                $where['ssz'] = $userid['zhen_id'];
            }


            if (!empty($arr['start_time'])){

                $start_time = strtotime($arr['start_time']);
                $end_time = strtotime($arr['end_time']);

            }else{

                $start_time=mktime(0,0,0,date('m'),date('d'),date('Y'));
                $end_time=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            }

            $where['create_time'] = ['between',[$start_time,$end_time]];

            $arr = db('anjian')
                ->alias('a')
                ->join('dict d1','d1.label=a.wtlx and d1.type="wtlx"')
//                ->join('dict d2','d2.label=a.dlmc and d2.type="big_type"')
//                ->join('dict d3','d3.label=a.xlmc and d3.type="small_type"')
//                ->field('wtlx,dlmc,xlmc,state,d1.value as wtlx,d2.value as big_type,d3.value as small_type,a.create_time')
                ->field('wtlx,state,d1.value as wtlx,a.create_time')
                ->where($where)
                ->select();

            $data = [];
            $result = [];
            if (!empty($arr)) {
                foreach ($arr as $k => $v) {

                    $wtlx_name[] = $v['wtlx'];
//                    $bb = (int)$v['wtlx'];
//                    $zz = (int)$v['dlmc'];
//                    $qq = (int)$v['xlmc'];
//                    $data[$bb]['type'] = $v['wtlx'] . '(' .$v['big_type'] . '(' .$v['small_type'] .')'.')';
                    if (!isset($data[$v['wtlx']])){

                        $data[$v['wtlx']]['type'] = $v['wtlx'];
                        $data[$v['wtlx']]['sbs'] = 0;
                        $data[$v['wtlx']]['las'] = 0;
                        $data[$v['wtlx']]['pqs'] = 0;
                        $data[$v['wtlx']]['czs'] = 0;
                        $data[$v['wtlx']]['hcs'] = 0;
                        $data[$v['wtlx']]['jas'] = 0;
                    }


                    if ($v['state'] >= 10) {

                        $data[$v['wtlx']]['sbs'] += 1;

                    }
                    if ($v['state'] >= 20) {
                        $data[$v['wtlx']]['las'] += 1;

                    }
                    if ($v['state'] >= 25) {
                        $data[$v['wtlx']]['pqs'] += 1;

                    }
                    if ($v['state'] >= 75) {
                        $data[$v['wtlx']]['czs'] += 1;

                    }
                    if ($v['state'] >= 50) {
                        $data[$v['wtlx']]['hcs'] += 1;

                    }
                    if ($v['state'] >= 100) {
                        $data[$v['wtlx']]['jas'] += 1;

                    }
                }
                $dict = db('dict')->where(['type'=>'wtlx'])->column('value');
                $data = array_values($data);


                foreach ($data as $v){
                    $hh[] = $v['type'];
                }

                for ($i = 0;$i<count($dict);$i++){

                    if (!in_array($dict[$i],$hh)){
                        $ff = [
                            'type'=>$dict[$i],
                            'sbs' =>0,
                            'las'=>0,
                            'pqs'=>0,
                            'czs'=>0,
                            'hcs'=>0,
                            'jas'=>0,
                        ];
                        array_push($data,$ff);
                    }
                }


//                $wtlx_name = array_unique($wtlx_name);
//                foreach($wtlx_name as $k=>$v){
//                    foreach($arr as $k1=>$v1){
//                        if($v == $v1['wtlx']){
//                            $yy[$k] += 1;
//                        }
//                    }
//                }
//                 die();
//                 $result = [];
//                 if (!empty($data)){
//                     foreach ($data as $k => $v) {

//                         foreach ($v as $k1=>$v1) {

//                             foreach ($v1 as $k2=>$v2) {
// //                                dump($data[$k][$k1][$k2]['jas']);
// //                                for ($i = 0;$i<count($yy);$i++){
// //
// //                                    $data[$k][$k1][$k2]['jal'] = round($data[$k][$k1][$k2]['jas'] / $yy[$i] *100,2).'%';
// //                                }
//                                 $result[] = $v2;
//                             }
//                         }
//                     }
//                 }
                foreach($data as $k=>&$v){
                    if($v['jas'] == 0){
                        $v['jal'] = 0 . '%';
                    }else{
                        $v['jal'] = round($v['jas'] / $v['sbs'] *100 ,2) . '%';
                    }
                     
                }
 
                // $yy = array_values($yy);
                // foreach ($result as $k=>$v){
                //     for ($i = 0;$i<count($yy);$i++){

                //         $result[$k]['jal'] = round($v['jas'] / $yy[$i] *100,2).'%';
                //     }
                // }
            }else{
                $dict = db('dict')->where(['type'=>'wtlx'])->column('value');
                for ($i = 0;$i<count($dict);$i++){
                    $data[$i] = [
                        'type'=>$dict[$i],
                        'sbs' =>0,
                        'las'=>0,
                        'pqs'=>0,
                        'czs'=>0,
                        'hcs'=>0,
                        'jas'=>0,
                        'jal' =>0 .'%',
                    ];
                }
            }


            return setReturn(200,'查询成功',$data);
        }


    }

    //办理进度
    public function schedule()
    {
        $id = input('get.id');
        $list = db('anjian')->field('id,sn')->where(['id'=>$id])->find();
        $log_list = db('anjian_log')->where(['sn'=>$list['sn']])->order('id asc')->select();

        foreach ($log_list as $k=>$v){
            $time = time()-(8*3600);
            $cs = $time-$v['create_time'];
            if ($cs <= 0){
                $dd = '正常';
            }else{
                $dd = '超时:'.time2string($cs);
            }
            $log_list[$k]['cs_time'] = $dd;
            $log_list[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $log_list[$k]['create_by'] = db('user')->where(['id'=>$v['create_by']])->value('name');
            $log_list[$k]['state'] = db('dict')->where(['label'=>$v['state'],'type'=>'anjianState'])->value('value');
        }
        return setReturn(200,'查询成功',$log_list);
    }

    //经纬度
    public function getjw()
    {
        $id = input('id');
        $list = db('anjian')->where(['id'=>$id])->field('lng,lat')->find();
        return setReturn(200,'查询成功',$list);
    }

    //反馈情况
    public function getfk(){
        $id = input('id');
        $list = db('anjian')->where(['id'=>$id])->field('id,pyr,content,sn')->select();
        foreach ($list as $k=>$v){
            $list[$k]['time'] = db('anjian_log')->where(['sn'=>$v['sn']])->value('create_time');
        }
        foreach ($list as $k=>$v){
            if (!empty($v['time'])){
                $list[$k]['time'] = date('Y-m-d H:i',$v['time']);
            }
        }
        return setReturn(200,'查询成功',$list);
    }

    //反馈情况详细信息
    public function getfkd()
    {
        $id = input('id');
        $list = db('anjian')->where(['id'=>$id])->field('id,pyr,content,sn')->find();
        $list['create_time'] = db('anjian_log')->where(['sn'=>$list['sn']])->value('create_time');
        $list['aa'] = db('anjian_log')->where(['sn'=>$list['sn']])->value('create_time');
        if (!empty($list['create_time'])){
            $list['create_time'] = date('Y-m-d',$list['create_time']);
            $list['hfts'] = time()-$list['aa'];
            $list['hfts'] = time2string($list['hfts']);
        }
        $list['create_by'] = db('anjian_log')->where(['sn'=>$list['sn']])->value('create_by');
        $list['name'] = db('user')->where(['id'=>$list['create_by']])->value('name');
        $list['stage'] = db('anjian_log')->where(['sn'=>$list['sn']])->value('stage');
        $list['filepath'] = db('file')->where(['pid'=>$list['id'],'table'=>'anjian','type'=>'3'])->value('filepath');

        return setReturn(200,'查询成功',$list);
    }

    //统计分析
    public function tjfx()
    {
        if (isPost()){
            $arr = input('post.');


            if($this->adminUser['user_type'] == 2 || $this->adminUser['user_type'] == 3 || $this->adminUser['user_type'] == 6 ||$this->adminUser['user_type'] == 7){
                $where['ssz'] = $this->adminUser['zhen_id'];
            }


            if (!empty($arr['start_time'])){
                $start_time = strtotime($arr['start_time']);
                $end_time = strtotime($arr['end_time']);
            }else{
                $start_time=mktime(0,0,0,date('m'),date('d'),date('Y'));
                $end_time=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            }
            $where['create_time'] = ['between',[$start_time,$end_time]];

            $type = $arr['type'];
            if ($type == 'xd' || $type == 'xz' || $type =='xy'){
                $arr = db('anjian')
                    ->alias('a')
                    ->join('dict d1','d1.label=a.wtlx and d1.type="wtlx"')
                    ->field('wtlx,state,d1.value as wtlx,a.create_time')
                    ->where($where)
                    ->select();

            }elseif ($type =='yd'|| $type == 'yz' || $type =='yy'){
                $arr = db('anjian')
                    ->alias('a')
                    ->join('dict d1','d1.label=a.wtly and d1.type="wtly"')
                    ->field('wtly,state,d1.value as wtly,a.create_time')
                    ->where($where)
                    ->select();
            }

            if (!empty($arr)) {

                foreach($arr as $k=>$v){
                    //上报数
                    if ($v['state'] >= 10) {
                        $arr[$k]['num'] = 1;
                    }
                    //立案数
                    if ($v['state'] >= 20) {
                        $arr[$k]['num'] = 2;
                    }
                    //派遣数
                    if ($v['state'] >= 25) {
                        $arr[$k]['num'] = 3;
                    }
                    //处置数
                    if ($v['state'] >= 75) {
                        $arr[$k]['num'] = 4;
                    }
                    //核查数
                    if ($v['state'] >= 50) {
                        $arr[$k]['num'] = 5;
                    }
                    //结案数
                    if ($v['state'] >= 100) {
                        $arr[$k]['num'] = 6;
                    }
                    if ($type == 'xd' || $type == 'xz' || $type =='xy'){
                        $nyy[] = $v['wtlx'];
                    }elseif ($type =='yd'|| $type == 'yz' || $type =='yy'){
                        $nyy[] = $v['wtly'];
                    }
                }

                $nyy = array_unique($nyy);
                if ($type == 'xd' || $type == 'xz' || $type =='xy'){
                    foreach($nyy as $k=>$v){
                        $yy[$k]['name'] = $v;

                        foreach($arr as $k1=>$v1){

                            if($v == $v1['wtlx']){
                                $yy[$k]['cont'][$k1] = $v1;
                            }
                        }
                    }
                }elseif ($type =='yd'|| $type == 'yz' || $type =='yy'){
                    foreach($nyy as $k=>$v){
                        $yy[$k]['name'] = $v;

                        foreach($arr as $k1=>$v1){

                            if($v == $v1['wtly']){
                                $yy[$k]['cont'][$k1] = $v1;
                            }

                        }
                    }
                }



                foreach($yy as $k=>$v){

                    for($i=1;$i<7;$i++){
                        $yy[$k]['mm'][$i] =0;

                        foreach($v['cont'] as $k1=>$v1){

                            if($i == $v1['num']){
                                $yy[$k]['mm'][$i]++;
                            }

                        }

                    }
                    unset($yy[$k]['cont']);
                    $co = count($v['cont']);
                    $yw = $yy[$k]['mm'][6];

                    $yy[$k]['mm'][7] = round($yw / $co *100,2).'%';

                }

                foreach ($yy as $k=>$v){
                    $cc[] = $v['name'];
                }

                if ($type == 'xd' || $type == 'xz' || $type =='xy'){
                    $dict = db('dict')->where(['type'=>'wtlx'])->column('value');
                }elseif ($type =='yd'|| $type == 'yz' || $type =='yy'){
                    $dict = db('dict')->where(['type'=>'wtly'])->column('value');
                }

                for ($i = 0;$i<count($yy);$i++){
                    if (!in_array($dict[$i],$cc)){
                        $dd = [
                            'name'=>$dict[$i],
                            'mm'=>[
                                1=>0,
                                2=>0,
                                3=>0,
                                4=>0,
                                5=>0,
                                6=>0,
                                7=>0 . '%',
                            ],

                        ];
                        array_push($yy,$dd);
                    }
                }
                $yy = array_values($yy);
            }

            if ($yy == null){
                if ($type == 'xd' || $type == 'xz' || $type =='xy'){
                    $dict = db('dict')->where(['type'=>'wtlx'])->column('value');
                }elseif ($type =='yd'|| $type == 'yz' || $type =='yy'){
                    $dict = db('dict')->where(['type'=>'wtly'])->column('value');
                }
                for ($i = 0;$i<count($dict);$i++){
                    $yy[$i] = [
                        'name'=>$dict[$i],
                        'mm'=>[
                            1=>0,
                            2=>0,
                            3=>0,
                            4=>0,
                            5=>0,
                            6=>0,
                            7=>0 . '%',
                        ],
                    ];
                }

            }
        }

        return setReturn(200,'查询成功',$yy);
    }

    //统计分析时间轴
    public function tjcount()
    {
        if (isPost()){
            $arr = input('post.');

            if($this->adminUser['user_type'] == 2 || $this->adminUser['user_type'] == 3 || $this->adminUser['user_type'] == 6 ||$this->adminUser['user_type'] == 7){
                $where['ssz'] = $this->adminUser['zhen_id'];
            }


            if (!empty($arr['start_time'])){
                $start_time = strtotime($arr['start_time']);
                $end_time = strtotime($arr['end_time']);
                //开始时间大于结束时间，开始时间变更今天日期
                if ($start_time > $end_time){
                    $start_time=mktime(0,0,0,date('m'),date('d'),date('Y'));
                }

                if ($arr['time_type'] == '日'){
                    $st = date('d',$start_time);
                    $ed = date('d',$end_time);
                    if ($st != $ed){
                        $as = $ed-$st;
                    }
                }elseif ($arr['time_type'] == '月'){
                    $st = date('m',$start_time);
                    $ed = date('m',$end_time);
                    if ($st == $ed){
                        $as =1;
                    }else{
                        $as = $ed+1-$st;
                    }
                }elseif ($arr['time_type'] == '季度'){
                    $st = date('m',$start_time);
                    $ed = date('m',$end_time);
                    if ($st < $ed && $st != $ed){
                        $bs = $ed+1-$st;
                        $as = 0;
                        for ($i = 1;$i<=$bs;$i++){

                            if ($i % 3 == 0){
                                $as ++;
                            }
                        }
                    }
                }



            }


            if ($arr['time_type'] == '日'){
                for ($i = 1;$i<=$as+1;$i++){
                    if ($i !=1){
                        $start_time = $start_time +1*24*60*60;
                        $where['create_time'] = SearchTime($start_time,$end_time,false);
                    }else{
                        $where['create_time'] = SearchTime($start_time,$end_time,false);
                    }
                    $count[$i]['time'] = date('Y-m-d',$start_time) . '-' . date('Y-m-d',$end_time);
                    $count[$i]['sst'] = db('anjian')->where($where)->column('state');
                }
            }elseif ($arr['time_type'] == '月'){
                if ($as == 1){
                    $where['create_time'] = SearchTime($start_time,$end_time);
                    $count[0]['time'] = date('Y-m-d',$start_time) . '-' . date('Y-m-d',$end_time);
                    $count[0]['sst'] = db('anjian')->where($where)->column('state');
                }else{

                    for ($i = 1;$i<=$as;$i++){
                        //先获取第二月的开始时间
                        $ii = $i-1;
                        if($i == 1 ) {
                            $start_time1 = date('Y-m-d H:i:s', $start_time);
                        }
                        else{
                            $start_time1 = date('Y-m-1 00:00:00',strtotime("+" . $ii . " month",$start_time));
                        }

                        $eed=date('Y-m-d H:i:s',strtotime("$start_time1 +1 month -1 second"));
                        if(strtotime($eed) > $end_time){
                            $end_time1 = date('Y-m-d 23:59:59',$end_time);
                        }else{
                            $end_time1 = $eed;
                        }
                        $where['create_time'] = SearchTime($start_time1,$end_time1);
                        $count[$i]['time'] = $start_time1.'-' . $end_time1;
                        $count[$i]['sst'] = db('anjian')->where($where)->column('state');

                    }
                }

            }elseif ($arr['time_type'] == '季度'){

                if ($as ==0){
                    $season = ceil((date('n'))/3);
                    $kaishi = date('Y-m-d H:i:s', mktime(0, 0, 0,$season*3-3+1,1,date('Y')));
                    $jieshu = date('Y-m-d H:i:s', mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),date('Y')));
                    $where['create_time'] = SearchTime($kaishi,$jieshu);
                    $count[0]['time'] = $kaishi . '-' . $jieshu;
                    $count[0]['sst'] = db('anjian')->where($where)->column('state');
                }else{
                    for ($i = 1;$i<=$as;$i++){

                        $ii = ($i-1)*3;
                        if($i == 1 ) {
                            $start_time1 = date('Y-m-d H:i:s', $start_time);
                        }
                        else{
                            $start_time1 = date('Y-m-1 00:00:00',strtotime("+" . $ii . " month",$start_time));
                        }

                        $eed=date('Y-m-d H:i:s',strtotime("$start_time1 +3 month -1 second"));

                        if(strtotime($eed) > $end_time){
                            $end_time1 = date('Y-m-d 23:59:59',$end_time);
                        }else{
                            $end_time1 = $eed;
                        }
                        $where['create_time'] = SearchTime($start_time1,$end_time1);
                        $count[$i]['time'] = $start_time1.'-' . $end_time1;
                        $count[$i]['sst'] = db('anjian')->where($where)->column('state');

                    }
                }
            }


            foreach ($count as $k=>$v){

                $count[$k]['data'][0] = 0;
                $count[$k]['data'][1] = 0;
                $count[$k]['data'][2] = 0;
                $count[$k]['data'][3] = 0;
                $count[$k]['data'][4] = 0;
                $count[$k]['data'][5] = 0;

                foreach ($v['sst'] as $index => $item) {

                    if ($item >= 10) {
                        $count[$k]['data'][0] += 1;
                    }
                    //立案数
                    if ($item >= 20) {
                        $count[$k]['data'][1] += 1;
                    }
                    //派遣数
                    if ($item >= 25) {
                        $count[$k]['data'][2] += 1;
                    }
                    //处置数
                    if ($item >= 75) {
                        $count[$k]['data'][3] += 1;
                    }
                    //核查数
                    if ($item >= 50) {
                        $count[$k]['data'][4] += 1;
                    }
                    //结案数
                    if ($item >= 100) {
                        $count[$k]['data'][5] += 1;
                    }
                }
            }


            $data = [];
            foreach ($count as $k=>$v){

                $data[$k]['name'] = $v['time'];
                $data[$k]['mm'] = $v['data'];
            }

            return setReturn(200,'',$data);
        }

    }

    //同比-环比
    public function tbcount()
    {
        if (isPost()){
            $post = input('post.');
            if(empty($post['page'])) $post['page'] = 1;
            if (!empty($post['start_time'])){
                $start_time = strtotime($post['start_time']);
                $end_time = strtotime($post['end_time']);
            }else{
                //当天日期
                $start_time=mktime(0,0,0,date('m'),date('d'),date('Y'));
                $end_time=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            }

            //今年当前选择的时间段
            $where['a.create_time'] = SearchTime($start_time,$end_time,false);
            if ($post['type'] == 'tb'){

                //去年当前选择的时间段
                $lastyear_start = strtotime($post['start_time']. "-1 year");
                $lastyear_end = strtotime($post['end_time']. "-1 year");
                $where2['create_time'] = SearchTime($lastyear_start,$lastyear_end,false);
            }elseif ($post['type'] == 'hb'){
                //上一月当前选择的时间段
                $lastyear_start = strtotime($post['start_time']. "-1 month");
                $lastyear_end = strtotime($post['end_time']. "-1 month");
                $where2['a.create_time'] = SearchTime($lastyear_start,$lastyear_end,false);
            }


            if (!empty($post['zhenid'])){
                $where3['v1.id'] = $post['zhenid'];
            }
            //获取今年的数据

            $list = db('anjian')->alias('a')
                ->field('COUNT(a.id) num,a.state,a.ssc,v.name')
                ->join('ht_village v','v.id=a.ssc')
                ->where(['v.type'=>1])
                ->where($where)
                ->group('a.ssc,a.state')
//                ->where($where3)
                ->select();

            $list2 = db('anjian')->alias('a')
                ->field('COUNT(a.id) num,a.state,a.ssc,v.name')
                ->join('ht_village v','v.id=a.ssc')
                ->where(['v.type'=>1])
                ->where($where2)
//                ->where($where3)
                ->group('a.ssc,a.state')
                ->select();

            $data = [];
            $count = db('village')->where(['type'=>1,'fid'=>['gt',1]])->count();
            $cun = db('village')->alias('v')
                ->join('village v1','v1.id=v.fid')
                ->where(['v.type'=>1,'v.fid'=>['gt',1]])->where($where3)->page($post['page'],10)->column('v.id,"桓台县" as xian,v.name as cun,v1.name as zhen');
//                ->where(['v.type'=>1,'v.fid'=>['gt',1]])->where($where3)->page($post['page'],10)->select(false);

//            halt($cun);
            foreach ($list as $k=>$v){
                //今年上报
                if(empty($sbs[$v['ssc']])) $sbs[$v['ssc']] = 0;
                $sbs[$v['ssc']] += $v['num'];

                //今年立案
                if($v['state'] > 10 && $v['state'] != 30) {
                    if(empty($las[$v['ssc']])) $las[$v['ssc']] = 0;
                    $las[$v['ssc']] += $v['num'];
                }
                //派遣数
                if($v['state'] >= 25 && $v['state'] != 30) {
                    if(empty($pqs[$v['ssc']])) $pqs[$v['ssc']] = 0;
                    $pqs[$v['ssc']] += $v['num'];
                }
                //作废数
                if($v['state'] = 35) {
                    if(empty($zfs[$v['ssc']])) $zfs[$v['ssc']] = 0;
                    $zfs[$v['ssc']] += $v['num'];
                }

            }

            //去年
            foreach ($list2 as $k=>$v){
                //去年上报
                if(empty($sbs2[$v['ssc']])) $sbs2[$v['ssc']] = 0;
                $sbs2[$v['ssc']] += $v['num'];

                //去年立案
                if($v['state'] > 10 && $v['state'] != 30) {
                    if(empty($las2[$v['ssc']])) $las2[$v['ssc']] = 0;
                    $las2[$v['ssc']] += $v['num'];
                }
                //去年派遣数
                if($v['state'] >= 25 && $v['state'] != 30) {
                    if(empty($pqs2[$v['ssc']])) $pqs2[$v['ssc']] = 0;
                    $pqs2[$v['ssc']] += $v['num'];
                }
                //去年作废数
                if($v['state'] = 35) {
                    if(empty($zfs2[$v['ssc']])) $zfs2[$v['ssc']] = 0;
                    $zfs2[$v['ssc']] += $v['num'];
                }

            }

            foreach ($cun as $k => $v) {
                $arr = [$v['xian'],$v['zhen'],$v['cun'],(int)$sbs[$k],(int)$sbs2[$k]];
                if((int)$sbs2[$k] == 0 && (int)$sbs[$k] != 0) {
                    $arr[] = 100 . '%';
                }elseif((int)$sbs[$k] == 0){
                    $arr[] = 0 . '%';
                } else {
                    $arr[] =  round(((int)$sbs[$k] - (int)$sbs2[$k]) / (int)$sbs2[$k] ,4) * 100 . '%';
                }

                $arr[] = (int) $las[$k];
                $arr[] = (int) $las2[$k];
                if((int)$las2[$k] == 0 && (int)$las[$k] != 0) {
                    $arr[] =  100 . '%';
                }elseif((int)$las[$k] == 0){
                    $arr[] = 0 . '%';
                } else {
                    $arr[] =  round(((int)$las[$k] - (int)$las2[$k]) / (int)$las2[$k] ,4) * 100 . '%';
                }

                $arr[] = (int) $pqs[$k];
                $arr[] = (int) $pqs2[$k];
                if((int)$pqs2[$k] == 0 && (int)$pqs[$k] != 0) {
                    $arr[] =  100 . '%';
                }elseif((int)$pqs[$k] == 0){
                    $arr[] = 0 . '%';
                } else {
                    $arr[] =  round(((int)$pqs[$k] - (int)$pqs2[$k]) / (int)$pqs2[$k] ,4) * 100 . '%';
                }

                $arr[] = (int) $zfs[$k];
                $arr[] = (int) $zfs2[$k];
                if((int)$zfs2[$k] == 0 && (int)$zfs[$k] != 0) {
                    $arr[] =  100 . '%';
                }elseif((int)$zfs[$k] == 0){
                    $arr[] = 0 . '%';
                } else {
                    $arr[] =  round(((int)$zfs[$k] - (int)$zfs2[$k]) / (int)$zfs2[$k] ,4) * 100 . '%';
                }
                $data[] = $arr;
            }


            return setReturn(200,'',['total'=>$count,'data'=>$data]);
        }

    }

}